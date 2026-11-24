<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Info Gereja</title>
    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo/logo.webp" type="image/webp">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/guest.css?v=20261124" rel="stylesheet">
    <style>
        body {
            background: var(--bg-canvas);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }
        .error-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            max-width: 500px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .error-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 1.85rem;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: var(--primary);
            margin-bottom: 0.35rem;
        }
        .error-card h1 {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--ink);
            margin: 0 0 0.5rem;
        }
        .error-card p {
            font-size: 0.92rem;
            color: var(--ink-secondary);
            margin: 0 0 1.5rem;
        }
        .error-actions {
            display: flex;
            gap: 0.6rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-links {
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.45rem;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="error-badge" aria-hidden="true"><i class="bx bx-map-alt"></i></div>
        <div class="error-code">ERROR 404</div>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>Halaman yang Anda tuju tidak tersedia atau tautannya sudah berubah. Silakan kembali ke direktori utama.</p>
        <div class="error-actions">
            <a href="<?= BASEURL; ?>" class="gki-btn gki-btn-primary">
                <i class="bx bx-home" aria-hidden="true"></i>Kembali ke Beranda
            </a>
            <button type="button" onclick="history.back()" class="gki-btn gki-btn-outline">
                <i class="bx bx-arrow-back" aria-hidden="true"></i>Halaman Sebelumnya
            </button>
        </div>
        <div class="error-links">
            <a href="<?= BASEURL; ?>cari" class="gki-btn gki-btn-soft gki-btn-sm">Cari Gereja</a>
            <a href="<?= BASEURL; ?>jadwal" class="gki-btn gki-btn-soft gki-btn-sm">Jadwal Misa</a>
            <a href="<?= BASEURL; ?>maps" class="gki-btn gki-btn-soft gki-btn-sm">Peta Gereja</a>
        </div>
    </main>
</body>
</html>
