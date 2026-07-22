<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - <?php echo isset($errorTitle) ? htmlspecialchars($errorTitle) : 'Application Error'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #1a1a2e;
            color: #e0e0e0;
            min-height: 100vh;
            padding: 0;
        }

        .error-container {
            max-width: 100%;
            width: 100%;
        }

        .error-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .error-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .error-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .error-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .error-tabs {
            background: #16213e;
            padding: 0;
            border-bottom: 2px solid #0f3460;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .tab-buttons {
            display: flex;
            gap: 0;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .tab-button {
            padding: 15px 25px;
            background: transparent;
            border: none;
            color: #a0a0a0;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .tab-button:hover {
            color: #e0e0e0;
            background: rgba(255, 255, 255, 0.05);
        }

        .tab-button.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .error-body {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 40px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .error-message-box {
            background: #1e1e2e;
            border-left: 4px solid #f5576c;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .error-message-title {
            color: #f5576c;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .error-message-text {
            color: #e0e0e0;
            font-family: "Courier New", monospace;
            font-size: 15px;
            line-height: 1.6;
            word-break: break-word;
        }

        .code-preview {
            background: #1e1e2e;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .code-preview-header {
            background: #2d2d44;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #3d3d5c;
        }

        .code-file-path {
            color: #a0a0a0;
            font-size: 13px;
            font-family: "Courier New", monospace;
        }

        .code-line-number {
            color: #667eea;
            font-weight: 600;
            font-size: 13px;
        }

        .code-lines {
            overflow-x: auto;
        }

        .code-line {
            display: flex;
            padding: 0;
            font-family: "Courier New", monospace;
            font-size: 13px;
            line-height: 1.8;
            transition: background 0.2s;
        }

        .code-line:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .code-line-num {
            min-width: 60px;
            padding: 4px 15px;
            text-align: right;
            color: #666;
            background: #2d2d44;
            user-select: none;
            border-right: 1px solid #3d3d5c;
        }

        .code-line-content {
            padding: 4px 20px;
            flex: 1;
            color: #e0e0e0;
            white-space: pre;
        }

        .code-line.error-line {
            background: rgba(245, 87, 108, 0.15);
        }

        .code-line.error-line .code-line-num {
            background: #f5576c;
            color: white;
            font-weight: bold;
        }

        .stack-trace-container {
            background: #1e1e2e;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .stack-trace-item {
            border-bottom: 1px solid #2d2d44;
            transition: all 0.3s;
        }

        .stack-trace-item:last-child {
            border-bottom: none;
        }

        .stack-trace-header {
            padding: 20px;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: background 0.2s;
        }

        .stack-trace-header:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .stack-number {
            background: #667eea;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .stack-info {
            flex: 1;
        }

        .stack-function {
            color: #63b3ed;
            font-family: "Courier New", monospace;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stack-location {
            color: #a0a0a0;
            font-size: 13px;
            font-family: "Courier New", monospace;
        }

        .stack-file {
            color: #68d391;
        }

        .stack-line {
            color: #fbd38d;
        }

        .stack-details {
            padding: 0 20px 20px 67px;
            display: none;
        }

        .stack-trace-item.expanded .stack-details {
            display: block;
        }

        .stack-expand-icon {
            color: #667eea;
            font-size: 12px;
            transition: transform 0.3s;
        }

        .stack-trace-item.expanded .stack-expand-icon {
            transform: rotate(90deg);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #1e1e2e;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .info-card-title {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #2d2d44;
            font-size: 13px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #a0a0a0;
            font-weight: 600;
        }

        .info-value {
            color: #e0e0e0;
            font-family: "Courier New", monospace;
            word-break: break-all;
            text-align: right;
            max-width: 60%;
        }

        .request-data {
            background: #1e1e2e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .request-data-title {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .request-data-content {
            background: #2d2d44;
            padding: 15px;
            border-radius: 4px;
            font-family: "Courier New", monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            color: #e0e0e0;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #2d2d44;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <div class="error-icon">⚠️</div>
            <h1 class="error-title"><?php echo isset($errorTitle) ? htmlspecialchars($errorTitle) : 'Application Error'; ?></h1>
            <p class="error-subtitle"><?php echo isset($errorType) ? htmlspecialchars($errorType) : 'An error occurred'; ?></p>
        </div>

        <div class="error-tabs">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="switchTab('error-tab')">⚡ Error</button>
                <button class="tab-button" onclick="switchTab('stack-tab')">📋 Stack Trace</button>
                <button class="tab-button" onclick="switchTab('request-tab')">🌐 Request</button>
                <button class="tab-button" onclick="switchTab('environment-tab')">⚙️ Environment</button>
            </div>
        </div>

        <div class="error-body">
            <div id="error-tab" class="tab-content active">
                <?php if (isset($errorMessage) && !empty($errorMessage)): ?>
                <div class="error-message-box">
                    <div class="error-message-title">💥 Error Message</div>
                    <div class="error-message-text"><?php echo htmlspecialchars($errorMessage); ?></div>
                </div>
                <?php endif; ?>

                <?php if (isset($errorFile) && isset($errorLine)): ?>
                <div class="code-preview">
                    <div class="code-preview-header">
                        <span class="code-file-path">📄 <?php echo htmlspecialchars($errorFile); ?></span>
                        <span class="code-line-number">Line <?php echo $errorLine; ?></span>
                    </div>
                    <div class="code-lines">
                        <?php
                        if (file_exists($errorFile)) {
                            $lines = file($errorFile);
                            $startLine = max(1, $errorLine - 10);
                            $endLine = min(count($lines), $errorLine + 10);
                            
                            for ($i = $startLine; $i <= $endLine; $i++) {
                                $isErrorLine = ($i == $errorLine);
                                $lineClass = $isErrorLine ? 'code-line error-line' : 'code-line';
                                echo '<div class="' . $lineClass . '">';
                                echo '<span class="code-line-num">' . $i . '</span>';
                                echo '<span class="code-line-content">' . htmlspecialchars($lines[$i - 1]) . '</span>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="empty-state">Source file not readable</div>';
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-card-title">📍 Error Location</div>
                        <?php if (isset($errorFile)): ?>
                        <div class="info-row">
                            <span class="info-label">File:</span>
                            <span class="info-value"><?php echo htmlspecialchars(basename($errorFile)); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errorLine)): ?>
                        <div class="info-row">
                            <span class="info-label">Line:</span>
                            <span class="info-value"><?php echo htmlspecialchars($errorLine); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="info-card">
                        <div class="info-card-title">🌐 Request Info</div>
                        <?php if (isset($method)): ?>
                        <div class="info-row">
                            <span class="info-label">Method:</span>
                            <span class="info-value"><?php echo htmlspecialchars($method); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($uri)): ?>
                        <div class="info-row">
                            <span class="info-label">URI:</span>
                            <span class="info-value"><?php echo htmlspecialchars($uri); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="stack-tab" class="tab-content">
                <?php if (isset($stackTrace) && !empty($stackTrace)): ?>
                <div class="stack-trace-container">
                    <?php 
                    $traces = is_array($stackTrace) ? $stackTrace : debug_backtrace();
                    foreach ($traces as $index => $trace): 
                    ?>
                    <div class="stack-trace-item" onclick="toggleStack(this)">
                        <div class="stack-trace-header">
                            <div class="stack-number"><?php echo $index; ?></div>
                            <div class="stack-info">
                                <div class="stack-function">
                                    <?php 
                                    if (isset($trace['class'])) {
                                        echo htmlspecialchars($trace['class']) . htmlspecialchars($trace['type']);
                                    }
                                    echo isset($trace['function']) ? htmlspecialchars($trace['function']) . '()' : '{main}';
                                    ?>
                                </div>
                                <div class="stack-location">
                                    <?php if (isset($trace['file'])): ?>
                                    <span class="stack-file"><?php echo htmlspecialchars($trace['file']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($trace['line'])): ?>
                                    <span class="stack-line">:<?php echo $trace['line']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="stack-expand-icon">▶</span>
                        </div>
                        
                        <?php if (isset($trace['file']) && isset($trace['line']) && file_exists($trace['file'])): ?>
                        <div class="stack-details">
                            <div class="code-preview">
                                <div class="code-lines">
                                    <?php
                                    $lines = file($trace['file']);
                                    $startLine = max(1, $trace['line'] - 5);
                                    $endLine = min(count($lines), $trace['line'] + 5);
                                    
                                    for ($i = $startLine; $i <= $endLine; $i++) {
                                        $isErrorLine = ($i == $trace['line']);
                                        $lineClass = $isErrorLine ? 'code-line error-line' : 'code-line';
                                        echo '<div class="' . $lineClass . '">';
                                        echo '<span class="code-line-num">' . $i . '</span>';
                                        echo '<span class="code-line-content">' . htmlspecialchars($lines[$i - 1]) . '</span>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">No stack trace available</div>
                <?php endif; ?>
            </div>

            <div id="request-tab" class="tab-content">
                <?php if (!empty($_GET)): ?>
                <div class="request-data">
                    <div class="request-data-title">GET Parameters</div>
                    <div class="request-data-content">
                        <pre><?php echo htmlspecialchars(print_r($_GET, true)); ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($_POST)): ?>
                <div class="request-data">
                    <div class="request-data-title">POST Data</div>
                    <div class="request-data-content">
                        <pre><?php echo htmlspecialchars(print_r($_POST, true)); ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($_COOKIE)): ?>
                <div class="request-data">
                    <div class="request-data-title">Cookies</div>
                    <div class="request-data-content">
                        <pre><?php echo htmlspecialchars(print_r($_COOKIE, true)); ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <div class="request-data">
                    <div class="request-data-title">Headers</div>
                    <div class="request-data-content">
                        <pre><?php
                        $headers = array();
                        foreach ($_SERVER as $key => $value) {
                            if (strpos($key, 'HTTP_') === 0) {
                                $headers[str_replace('HTTP_', '', $key)] = $value;
                            }
                        }
                        echo htmlspecialchars(print_r($headers, true));
                        ?></pre>
                    </div>
                </div>
            </div>

            <div id="environment-tab" class="tab-content">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-card-title">🖥️ Server Info</div>
                        <div class="info-row">
                            <span class="info-label">PHP Version:</span>
                            <span class="info-value"><?php echo PHP_VERSION; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Server:</span>
                            <span class="info-value"><?php echo isset($_SERVER['SERVER_SOFTWARE']) ? htmlspecialchars($_SERVER['SERVER_SOFTWARE']) : 'Unknown'; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">OS:</span>
                            <span class="info-value"><?php echo PHP_OS; ?></span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-title">📂 Paths</div>
                        <div class="info-row">
                            <span class="info-label">Document Root:</span>
                            <span class="info-value"><?php echo isset($_SERVER['DOCUMENT_ROOT']) ? htmlspecialchars($_SERVER['DOCUMENT_ROOT']) : 'N/A'; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Script:</span>
                            <span class="info-value"><?php echo isset($_SERVER['SCRIPT_FILENAME']) ? htmlspecialchars(basename($_SERVER['SCRIPT_FILENAME'])) : 'N/A'; ?></span>
                        </div>
                    </div>
                </div>

                <?php if (isset($_SESSION) && !empty($_SESSION)): ?>
                <div class="request-data">
                    <div class="request-data-title">Session Data</div>
                    <div class="request-data-content">
                        <pre><?php echo htmlspecialchars(print_r($_SESSION, true)); ?></pre>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-button').forEach(function(btn) {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }

        function toggleStack(element) {
            if (element.className.indexOf('expanded') >= 0) {
                element.className = element.className.replace(' expanded', '');
            } else {
                element.className += ' expanded';
            }
        }
    </script>
</body>
</html>
