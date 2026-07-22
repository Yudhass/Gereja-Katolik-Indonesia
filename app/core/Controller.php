<?php

class Controller
{
    public function view($view, $data = array())
    {
        $viewPath = dirname(__FILE__) . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            // Show proper error page
            $this->showErrorPage(array(
                'errorTitle' => 'View Not Found',
                'errorType' => '500 - View File Not Found',
                'errorMessage' => "View file not found: {$view}.php",
                'errorFile' => $viewPath,
                'errorLine' => 0,
                'stackTrace' => debug_backtrace()
            ));
            exit;
        }

        if (count($data)) {
            extract($data);
        }
        require_once $viewPath;
    }

    public function dd()
    {
        // Get all arguments
        $args = func_get_args();
        
        // Clear any output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Start new output buffer
        ob_start();
        
        // Prepare debug data
        $debugData = $args;
        
        // Load dd view
        $ddView = dirname(__FILE__) . '/../views/errors/dd.php';
        
        if (file_exists($ddView)) {
            require $ddView;
        } else {
            // Fallback jika view tidak ada
            echo '<!DOCTYPE html><html><head><title>Debug</title></head><body>';
            echo '<pre style="background: #1a1a2e; color: #e0e0e0; padding: 20px; font-family: monospace;">';
            foreach ($debugData as $index => $data) {
                echo "=== Variable #" . ($index + 1) . " ===\n";
                var_dump($data);
                echo "\n\n";
            }
            echo '</pre></body></html>';
        }
        
        // Flush output buffer
        ob_end_flush();
        
        die();
    }

    public function model($model)
    {
        $file = '../app/models/' . $model . '.php';

        // Cek apakah file model ada
        if (!file_exists($file)) {
            $this->showErrorPage(array(
                'errorTitle' => 'Model Not Found',
                'errorType' => '500 - Model File Not Found',
                'errorMessage' => "Model file not found: {$model}.php",
                'errorFile' => $file,
                'errorLine' => 0,
                'stackTrace' => debug_backtrace()
            ));
            exit;
        }

        // Memasukkan file model
        require_once $file;

        // Pastikan kelas model ada, kemudian buat instance objeknya
        if (class_exists($model)) {
            return new $model();
        } else {
            $this->showErrorPage(array(
                'errorTitle' => 'Model Class Not Found',
                'errorType' => '500 - Class Not Found',
                'errorMessage' => "Model class not found: {$model}",
                'errorFile' => $file,
                'errorLine' => 0,
                'stackTrace' => debug_backtrace()
            ));
            exit;
        }
    }

    // Custom redirect method
    public function redirect($route, $message = null, $type = 'success')
    {
        // Store the message in session to display it on the next page
        if ($message) {
            setFlashMessage($message, $type);
        }

        // Redirect to the specified route (prepend BASEURL to avoid relative path issues)
        header('Location: ' . BASEURL . ltrim($route, '/'));
        exit;
    }

    // Redirect back to the previous page
    public function redirectBack($message = null, $type = 'success')
    {
        if ($message) {
            setFlashMessage($message, $type);
        }

        // Redirect back to the previous page
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: ' . BASEURL . 'login');
        }
        exit;
    }

    // Get session value
    public function getSession($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    // Get and clear flash message
    public function getFlashMessage()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = array(
                'message' => $_SESSION['flash_message'],
                'type' => isset($_SESSION['flash_message_type']) ? $_SESSION['flash_message_type'] : 'success'
            );

            // Clear the flash message after retrieving
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_message_type']);

            return $message;
        }

        return null;
    }

    // Check if flash message exists
    public function hasFlashMessage()
    {
        return isset($_SESSION['flash_message']);
    }
    
    /**
     * Render view with template layout (PHP 5.2+ compatible)
     * 
     * @param string $view - Path to the view file
     * @param array $data - Data to pass to the view
     * @param string $layout - Layout template to use (default: 'layouts/master')
     * @return void
     */
    public function render($view, $data = array(), $layout = 'layouts/master')
    {
        $viewPath = dirname(__FILE__) . '/../views/' . $view . '.php';
        $layoutPath = dirname(__FILE__) . '/../views/' . $layout . '.php';
        
        // Check if view file exists
        if (!file_exists($viewPath)) {
            $this->showErrorPage(array(
                'errorTitle' => 'View Not Found',
                'errorType' => '500 - View File Not Found',
                'errorMessage' => "View file not found: {$view}.php",
                'errorFile' => $viewPath,
                'errorLine' => 0,
                'stackTrace' => debug_backtrace()
            ));
            exit;
        }
        
        // Extract data for view
        if (count($data)) {
            extract($data);
        }
        
        // Start output buffering to capture view content
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        // If layout exists, use it
        if (file_exists($layoutPath)) {
            // Make content available to layout
            if (!isset($data['content'])) {
                $data['content'] = $content;
            }
            
            // Re-extract data for layout (includes $content now)
            extract($data);
            
            require $layoutPath;
        } else {
            // No layout, just output the content
            echo $content;
        }
    }
    
    /**
     * Render view with custom template and sections (PHP 5.2+ compatible)
     * Similar to Blade's @extends and @section
     * 
     * @param string $view - Path to the view file
     * @param array $params - Parameters for the template
     * @return void
     */
    public function template($view, $params = array())
    {
        // Default template parameters
        $defaults = array(
            'layout' => 'layouts/master',
            'title' => 'Application',
            'navbar' => true,
            'footer' => true,
            'sidebar' => false,
            'css' => array(),
            'js' => array(),
            'styles' => '',
            'scripts' => '',
            'wrapperClass' => 'container',
            'content' => ''
        );
        
        // Merge with provided parameters
        $params = array_merge($defaults, $params);
        
        $viewPath = dirname(__FILE__) . '/../views/' . $view . '.php';
        
        // Check if view file exists
        if (!file_exists($viewPath)) {
            $this->showErrorPage(array(
                'errorTitle' => 'View Not Found',
                'errorType' => '500 - View File Not Found',
                'errorMessage' => "View file not found: {$view}.php",
                'errorFile' => $viewPath,
                'errorLine' => 0,
                'stackTrace' => debug_backtrace()
            ));
            exit;
        }
        
        // Extract parameters for view
        extract($params);
        
        // Capture view content
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        // Update content in params
        $params['content'] = $content;
        
        // Get layout path
        $layoutPath = dirname(__FILE__) . '/../views/' . $params['layout'] . '.php';
        
        // Render with layout if it exists
        if (file_exists($layoutPath)) {
            extract($params);
            require $layoutPath;
        } else {
            // No layout, output content directly
            echo $content;
        }
    }
    
    /**
     * Show Error Page
     */
    protected function showErrorPage($errorData)
    {
        // Clear any output buffer
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Set HTTP response code
        http_response_code(500);
        
        // Extract error data
        extract($errorData);
        
        // Add request info
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        
        // Load error view
        $errorView = dirname(__FILE__) . '/../views/errors/error.php';
        
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            // Fallback jika view tidak ada
            echo '<h1>Error</h1>';
            echo '<p><strong>Type:</strong> ' . htmlspecialchars($errorType) . '</p>';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($errorMessage) . '</p>';
            echo '<p><strong>File:</strong> ' . htmlspecialchars($errorFile) . '</p>';
            echo '<p><strong>Line:</strong> ' . htmlspecialchars($errorLine) . '</p>';
        }
        
        exit;
    }
}
