<script src="<?= BASEURL; ?>assets/js/jquery.min.js"></script>
<script src="<?= BASEURL; ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASEURL; ?>assets/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="<?= BASEURL; ?>assets/plugins/notifications/js/lobibox.min.js"></script>
<script src="<?= BASEURL; ?>assets/plugins/notifications/js/notifications.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function() {
        <?php
        $flash = getFlashMessage();
        if ($flash):
            $alertType = 'default';
            $icon = 'bx bx-info-circle';

            // Map flash type to Lobibox type
            if ($flash['type'] == 'success') {
                $alertType = 'success';
                $icon = 'bx bx-check-circle';
            } elseif ($flash['type'] == 'error') {
                $alertType = 'error';
                $icon = 'bx bx-x-circle';
            } elseif ($flash['type'] == 'warning') {
                $alertType = 'warning';
                $icon = 'bx bx-error';
            } elseif ($flash['type'] == 'info') {
                $alertType = 'info';
                $icon = 'bx bx-info-circle';
            }
        ?>
            Lobibox.notify('<?php echo $alertType; ?>', {
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'center top',
                size: 'mini',
                icon: '<?php echo $icon; ?>',
                msg: '<?php echo htmlspecialchars($flash['message'], ENT_QUOTES); ?>',
                sound: false // Disable sound notification
            });
        <?php endif; ?>
    });
</script>
<!--app JS-->
<script src="<?= BASEURL; ?>assets/js/app.js"></script>
</body>

</html>