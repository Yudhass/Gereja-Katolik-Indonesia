/**
 * RFID Helper - Retry mechanism & Offline Queue untuk Scan RFID
 * Menangani koneksi putus-putus saat scan RFID
 */

var RfidHelper = (function() {
    'use strict';

    var QUEUE_KEY = 'rfid_scan_queue';
    var MAX_RETRIES = 3;
    var RETRY_DELAY = 2000; // ms

    function getQueue() {
        try {
            var raw = localStorage.getItem(QUEUE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveQueue(queue) {
        localStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
    }

    function addToQueue(item) {
        var queue = getQueue();
        queue.push(item);
        saveQueue(queue);
    }

    function removeFromQueue(index) {
        var queue = getQueue();
        queue.splice(index, 1);
        saveQueue(queue);
    }

    function clearQueue() {
        localStorage.removeItem(QUEUE_KEY);
    }

    /**
     * AJAX dengan retry otomatis
     * options: objek konfigurasi jQuery AJAX + onRetry, onQueue
     */
    function ajaxWithRetry(options) {
        var attempt = 0;
        var originalError = options.error || function() {};
        var originalSuccess = options.success || function() {};

        function doRequest() {
            var opts = $.extend({}, options, {
                success: function(data, textStatus, jqXHR) {
                    originalSuccess(data, textStatus, jqXHR);
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Jika error karena network/timeout, retry
                    var isNetworkError = !xhr.status || xhr.status === 0 || textStatus === 'timeout' || textStatus === 'error';
                    if (isNetworkError && attempt < MAX_RETRIES) {
                        attempt++;
                        if (typeof options.onRetry === 'function') {
                            options.onRetry(attempt, MAX_RETRIES);
                        }
                        setTimeout(doRequest, RETRY_DELAY * attempt);
                    } else {
                        originalError(xhr, textStatus, errorThrown);
                    }
                }
            });
            $.ajax(opts);
        }

        doRequest();
    }

    /**
     * Fetch linen data dengan retry dan queue jika gagal total
     * @param {string} rfid - RFID linen
     * @param {string} url - URL endpoint
     * @param {object} callbacks - {onSuccess, onError, onRetry}
     */
    function fetchLinenDataWithRetry(rfid, url, callbacks) {
        callbacks = callbacks || {};
        var loadingMsg = null;

        if (typeof Lobibox !== 'undefined') {
            loadingMsg = Lobibox.notify('info', {
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'top right',
                size: 'mini',
                icon: 'bx bx-loader-circle bx-spin',
                msg: 'Mencari data linen...',
                sound: false,
                delay: false
            });
        }

        ajaxWithRetry({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000, // 10 detik timeout
            onRetry: function(attempt, max) {
                if (typeof Lobibox !== 'undefined') {
                    Lobibox.notify('warning', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        size: 'mini',
                        icon: 'bx bx-wifi-off',
                        msg: 'Koneksi terputus, mencoba ulang (' + attempt + '/' + max + ')...',
                        sound: false,
                        delay: 3000
                    });
                }
                if (typeof callbacks.onRetry === 'function') {
                    callbacks.onRetry(attempt, max);
                }
            },
            success: function(response) {
                if (loadingMsg) loadingMsg.remove();
                if (typeof callbacks.onSuccess === 'function') {
                    callbacks.onSuccess(response);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                if (loadingMsg) loadingMsg.remove();

                var isNetworkError = !xhr.status || xhr.status === 0 || textStatus === 'timeout' || textStatus === 'error';
                if (isNetworkError) {
                    // Simpan ke queue untuk diproses nanti
                    addToQueue({
                        rfid: rfid,
                        url: url,
                        timestamp: new Date().toISOString(),
                        type: 'fetch_linen'
                    });
                    if (typeof Lobibox !== 'undefined') {
                        Lobibox.notify('error', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            size: 'mini',
                            icon: 'bx bx-wifi-off',
                            msg: 'Koneksi bermasalah. Scan RFID disimpan dalam antrian dan akan diproses otomatis saat koneksi pulih.',
                            sound: false,
                            delay: 8000
                        });
                    }
                } else {
                    if (typeof Lobibox !== 'undefined') {
                        var errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal mengambil data linen!';
                        Lobibox.notify('error', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            size: 'mini',
                            icon: 'bx bx-x-circle',
                            msg: errorMsg,
                            sound: false
                        });
                    }
                }

                if (typeof callbacks.onError === 'function') {
                    callbacks.onError(xhr, textStatus, errorThrown, isNetworkError);
                }
            }
        });
    }

    /**
     * Memproses antrian scan yang tertunda
     * @param {function} processor - function(queueItem, done) untuk memproses setiap item
     */
    function processQueue(processor) {
        var queue = getQueue();
        if (!queue.length) return;

        if (typeof Lobibox !== 'undefined') {
            Lobibox.notify('info', {
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'top right',
                size: 'mini',
                icon: 'bx bx-refresh',
                msg: 'Memproses ' + queue.length + ' scan tertunda...',
                sound: false
            });
        }

        var i = 0;
        function next() {
            if (i >= queue.length) {
                clearQueue();
                if (typeof Lobibox !== 'undefined') {
                    Lobibox.notify('success', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        size: 'mini',
                        icon: 'bx bx-check-circle',
                        msg: 'Antrian scan selesai diproses.',
                        sound: false
                    });
                }
                return;
            }
            var item = queue[i];
            processor(item, function(success) {
                if (success) {
                    i++;
                } else {
                    // Stop processing if failed, keep remaining in queue
                    var remaining = queue.slice(i);
                    saveQueue(remaining);
                    return;
                }
                next();
            });
        }
        next();
    }

    // Deteksi perubahan status online/offline
    $(window).on('online', function() {
        if (typeof Lobibox !== 'undefined') {
            Lobibox.notify('success', {
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'top right',
                size: 'mini',
                icon: 'bx bx-wifi',
                msg: 'Koneksi terhubung kembali.',
                sound: false
            });
        }
    });

    $(window).on('offline', function() {
        if (typeof Lobibox !== 'undefined') {
            Lobibox.notify('warning', {
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'top right',
                size: 'mini',
                icon: 'bx bx-wifi-off',
                msg: 'Koneksi terputus! Scan RFID akan disimpan dan diproses nanti.',
                sound: false,
                delay: 8000
            });
        }
    });

    // Public API
    return {
        ajaxWithRetry: ajaxWithRetry,
        fetchLinenDataWithRetry: fetchLinenDataWithRetry,
        processQueue: processQueue,
        addToQueue: addToQueue,
        getQueue: getQueue,
        clearQueue: clearQueue
    };
})();
