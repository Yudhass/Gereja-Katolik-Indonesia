<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Data - Dump & Die</title>
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

        .dd-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .dd-header-content {
            max-width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dd-title {
            font-size: 24px;
            font-weight: 700;
        }

        .dd-subtitle {
            font-size: 13px;
            opacity: 0.95;
            margin-top: 4px;
        }

        .dd-subtitle strong {
            font-weight: 700;
            background: rgba(255, 255, 255, 0.15);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .dd-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .dd-container {
            max-width: 100%;
            padding: 20px 30px;
        }

        .dd-item {
            background: #1e1e2e;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .dd-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #2d2d44;
            border-bottom: 1px solid #3d3d5c;
        }

        .dd-item-title {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
        }

        .dd-type-badge {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .dd-content {
            position: relative;
        }

        .dd-copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            z-index: 10;
        }

        .dd-copy-btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }

        .dd-copy-btn.copied {
            background: #28a745;
        }

        .dd-raw {
            background: #2d2d44;
            padding: 20px;
            font-family: "Courier New", monospace;
            font-size: 13px;
            line-height: 1.6;
            max-height: 600px;
            overflow: auto;
            color: #e0e0e0;
            white-space: pre-wrap;
            word-wrap: break-word;
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a2e;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        .dd-info {
            background: #2d2d44;
            padding: 15px 20px;
            text-align: center;
            color: #a0a0a0;
            font-size: 12px;
            border-top: 1px solid #3d3d5c;
        }
    </style>
</head>
<body>
    <div class="dd-header">
        <div class="dd-header-content">
            <div>
                <div class="dd-title">🐛 Debug Data - Dump & Die</div>
                <div class="dd-subtitle">
                    📍 Called from: <strong style="color: #ffd700;"><?php echo htmlspecialchars(basename($callerFile)); ?></strong> 
                    on line <strong style="color: #ffd700;"><?php echo $callerLine; ?></strong>
                </div>
                <div class="dd-subtitle" style="margin-top: 5px; font-size: 11px; opacity: 0.7;">
                    <?php echo htmlspecialchars($callerFile); ?>
                </div>
            </div>
            <div class="dd-count">
                <?php echo count($debugData); ?> Variable<?php echo count($debugData) > 1 ? 's' : ''; ?>
            </div>
        </div>
    </div>

    <div class="dd-container">
        <?php foreach ($debugData as $index => $data): ?>
        <div class="dd-item">
            <div class="dd-item-header">
                <div class="dd-item-title">Variable #<?php echo ($index + 1); ?></div>
                <div class="dd-type-badge"><?php echo strtoupper(gettype($data)); ?></div>
            </div>
            
            <div class="dd-content">
                <button class="dd-copy-btn" onclick="copyToClipboard(this, 'data-<?php echo $index; ?>')">
                    📋 Copy
                </button>
                <pre class="dd-raw" id="data-<?php echo $index; ?>"><?php 
                    // Format output untuk mudah di-copy
                    if (is_array($data) || is_object($data)) {
                        echo htmlspecialchars(print_r($data, true));
                    } else if (is_bool($data)) {
                        echo $data ? 'true' : 'false';
                    } else if (is_null($data)) {
                        echo 'NULL';
                    } else if (is_string($data)) {
                        echo '"' . htmlspecialchars($data) . '"';
                    } else {
                        echo htmlspecialchars($data);
                    }
                ?></pre>
            </div>
            
            <div class="dd-info">
                Type: <strong><?php echo gettype($data); ?></strong>
                <?php if (is_array($data)): ?>
                    | Items: <strong><?php echo count($data); ?></strong>
                <?php elseif (is_string($data)): ?>
                    | Length: <strong><?php echo strlen($data); ?> characters</strong>
                <?php elseif (is_object($data)): ?>
                    | Class: <strong><?php echo get_class($data); ?></strong>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        function copyToClipboard(button, elementId) {
            var element = document.getElementById(elementId);
            var text = element.innerText || element.textContent;
            
            // Create temporary textarea
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            
            // Select and copy
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            
            try {
                document.execCommand('copy');
                
                // Change button text
                var originalText = button.innerHTML;
                button.innerHTML = '✅ Copied!';
                button.className = 'dd-copy-btn copied';
                
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.className = 'dd-copy-btn';
                }, 2000);
            } catch (err) {
                alert('Failed to copy');
            }
            
            document.body.removeChild(textarea);
        }
        
        // Allow text selection
        document.querySelectorAll('.dd-raw').forEach(function(el) {
            el.style.cursor = 'text';
        });
    </script>
</body>
</html>
