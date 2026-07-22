<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #e2e5f2 0%, #caece3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(240, 147, 251, 0.08) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .error-content {
            position: relative;
            z-index: 1;
        }

        .error-code {
            font-size: 140px;
            font-weight: 700;
            background: linear-gradient(135deg, #de0f0f 0%, #8f081a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: shake 3s ease-in-out infinite;
        }

        @keyframes shake {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-5deg);
            }
            75% {
                transform: rotate(5deg);
            }
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .error-description {
            font-size: 18px;
            color: #718096;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .error-details {
            background: #fff5f5;
            border-left: 4px solid #de0f0f;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: left;
        }

        .error-details-title {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .error-details-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .error-details-item:last-child {
            margin-bottom: 0;
        }

        .error-details-label {
            font-weight: 600;
            color: #2d3748;
            min-width: 100px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-details-value {
            color: #c53030;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            flex: 1;
        }

        .method-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            background: linear-gradient(135deg, #de0f0f 0%, #8f081a 100%);
            color: white;
        }

        .alert-box {
            background: #fff5f5;
            border-left: 4px solid #f5576c;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }

        .alert-box-title {
            font-size: 16px;
            font-weight: 600;
            color: #c53030;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-box-content {
            font-size: 14px;
            color: #742a2a;
            line-height: 1.6;
        }

        .alert-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }

        .alert-box li {
            margin: 5px 0;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #de0f0f 0%, #8f081a 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
        }

        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
            transform: translateY(-2px);
        }

        .error-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .info-card {
            background: #f7f9fc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: left;
        }

        .info-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card-content {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
        }

        .contact-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            color: #667eea;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .error-container {
                padding: 40px 25px;
                border-radius: 15px;
            }

            .error-details {
                padding: 15px;
            }

            .error-details-title {
                font-size: 13px;
            }

            .error-details-item {
                flex-direction: column;
                font-size: 13px;
            }

            .error-details-label {
                min-width: auto;
                margin-bottom: 4px;
            }

            .error-details-value {
                font-size: 12px;
            }

            .error-code {
                font-size: 100px;
            }

            .error-icon {
                font-size: 60px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-description {
                font-size: 16px;
                margin-bottom: 25px;
            }

            .alert-box {
                padding: 15px;
            }

            .alert-box-title {
                font-size: 15px;
            }

            .alert-box-content {
                font-size: 13px;
            }

            .btn {
                padding: 12px 24px;
                font-size: 15px;
                width: 100%;
                justify-content: center;
            }

            .error-actions {
                flex-direction: column;
                gap: 12px;
            }

            .info-card {
                padding: 15px;
            }

            .info-card-title {
                font-size: 15px;
            }

            .info-card-content {
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .error-container {
                padding: 30px 20px;
            }

            .error-code {
                font-size: 80px;
            }

            .error-icon {
                font-size: 50px;
            }

            .error-title {
                font-size: 20px;
            }

            .error-description {
                font-size: 14px;
            }

            .alert-box {
                padding: 12px;
            }

            .alert-box-title {
                font-size: 14px;
            }

            .alert-box-content {
                font-size: 12px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            .error-info {
                margin-top: 30px;
                padding-top: 25px;
            }

            .info-card {
                padding: 12px;
            }

            .info-card-title {
                font-size: 14px;
            }

            .info-card-content {
                font-size: 12px;
            }

            .contact-info {
                font-size: 13px;
            }
        }

        /* Large screens */
        @media (min-width: 1200px) {
            .error-container {
                padding: 80px 60px;
            }

            .error-code {
                font-size: 160px;
            }

            .error-title {
                font-size: 36px;
            }

            .error-description {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">🔒</div>
            <div class="error-code">403</div>
            <h1 class="error-title">Akses Ditolak</h1>
            <p class="error-description">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>

            <div class="error-details">
                <div class="error-details-title">📋 Detail Request</div>
                <div class="error-details-item">
                    <div class="error-details-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                        URL:
                    </div>
                    <div class="error-details-value"><?= htmlspecialchars($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : '/'); ?></div>
                </div>
                <div class="error-details-item">
                    <div class="error-details-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                        Method:
                    </div>
                    <div class="error-details-value">
                        <span class="method-badge"><?= htmlspecialchars($_SERVER['REQUEST_METHOD'] ? $_SERVER['REQUEST_METHOD'] : 'GET'); ?></span>
                    </div>
                </div>
            </div>

            <div class="alert-box">
                <div class="alert-box-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Mengapa saya melihat halaman ini?
                </div>
                <div class="alert-box-content">
                    Anda tidak memiliki hak akses yang diperlukan karena:
                    <ul>
                        <li>Akun anda tidak bisa melakukan proses ini</li>
                        <li>Halaman ini hanya dapat diakses oleh pengguna dengan izin tertentu</li>
                    </ul>
                </div>
            </div>

            <div class="error-actions">
                <a href="<?= BASEURL; ?>" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Kembali ke Beranda
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Halaman Sebelumnya
                </a>
            </div>

            <div class="error-info">
                <div class="info-card">
                    <div class="info-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Butuh Akses?
                    </div>
                    <div class="info-card-content">
                        Jika Anda merasa seharusnya memiliki akses ke halaman ini, silakan hubungi administrator sistem untuk meminta izin akses yang diperlukan.
                        <div class="contact-info">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            Hubungi Administrator
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
