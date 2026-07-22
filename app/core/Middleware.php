<?php

/**
 * Base Middleware Class
 * Semua middleware harus extend class ini
 */
abstract class Middleware
{
    /**
     * Handle request
     * 
     * @return bool True jika allowed, False jika ditolak
     */
    abstract public function handle();
    
    /**
     * Redirect to specific route
     */
    protected function redirect($route, $message = null, $type = 'error')
    {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_message_type'] = $type;
        }
        
        header('Location: ' . BASEURL . $route);
        exit;
    }
    
    /**
     * Return 403 Forbidden
     */
    protected function forbidden($message = 'Access Forbidden')
    {
        http_response_code(403);
        
        // Show error page
        $errorData = array(
            'errorTitle' => 'Access Forbidden',
            'errorType' => '403 - Forbidden',
            'errorMessage' => $message,
            'errorFile' => __FILE__,
            'errorLine' => __LINE__,
            'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
            'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/',
            'stackTrace' => debug_backtrace()
        );
        
        extract($errorData);
        
        $errorView = dirname(__FILE__) . '/../views/errors/error.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<h1>403 Forbidden</h1><p>' . htmlspecialchars($message) . '</p>';
        }
        
        exit;
    }
}
