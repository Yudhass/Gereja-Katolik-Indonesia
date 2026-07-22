<?php

require_once dirname(__FILE__) . '/Router.php';
require_once dirname(__FILE__) . '/Logger.php';

class App
{
    private $router;

    public function __construct()
    {
        // Inisialisasi router
        $this->router = new Router();
        
        // Set custom error handler
        $this->setupErrorHandling();
    }
    
    /**
     * Setup Error & Exception Handling
     */
    private function setupErrorHandling()
    {
        // Set error handler untuk PHP errors
        set_error_handler(array($this, 'handleError'));
        
        // Set exception handler untuk uncaught exceptions
        set_exception_handler(array($this, 'handleException'));
        
        // Set shutdown function untuk fatal errors
        register_shutdown_function(array($this, 'handleShutdown'));
    }
    
    /**
     * Handle PHP Errors
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        // Jangan handle error yang di-suppress dengan @
        if (!(error_reporting() & $errno)) {
            return false;
        }

        Logger::phpError($errno, $errstr, $errfile, $errline);
        
        $errorTypes = array(
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        );
        
        $errorType = isset($errorTypes[$errno]) ? $errorTypes[$errno] : 'Unknown Error';
        
        $this->showErrorPage(array(
            'errorTitle' => 'PHP ' . $errorType,
            'errorType' => $errorType . ' (Code: ' . $errno . ')',
            'errorMessage' => $errstr,
            'errorFile' => $errfile,
            'errorLine' => $errline,
            'stackTrace' => debug_backtrace()
        ));
        
        return true;
    }
    
    /**
     * Handle Exceptions
     */
    public function handleException($exception)
    {
        Logger::exception($exception);

        $this->showErrorPage(array(
            'errorTitle' => 'Uncaught Exception',
            'errorType' => get_class($exception),
            'errorMessage' => $exception->getMessage(),
            'errorFile' => $exception->getFile(),
            'errorLine' => $exception->getLine(),
            'stackTrace' => $exception->getTrace()
        ));
    }
    
    /**
     * Handle Fatal Errors (Shutdown Function)
     */
    public function handleShutdown()
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
            Logger::phpError($error['type'], $error['message'], $error['file'], $error['line']);

            $this->showErrorPage(array(
                'errorTitle' => 'Fatal Error',
                'errorType' => 'Fatal Error (Code: ' . $error['type'] . ')',
                'errorMessage' => $error['message'],
                'errorFile' => $error['file'],
                'errorLine' => $error['line'],
                'stackTrace' => debug_backtrace()
            ));
        }
    }
    
    /**
     * Show Error Page
     */
    private function showErrorPage($errorData)
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
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($errorMessage) . '</p>';
            echo '<p><strong>File:</strong> ' . htmlspecialchars($errorFile) . '</p>';
            echo '<p><strong>Line:</strong> ' . htmlspecialchars($errorLine) . '</p>';
        }
        
        exit;
    }

    public function run()
    {
        try {
            // Load routes first
            $this->router->loadRoutes();
            
            // Ambil metode HTTP (GET, POST, PUT, DELETE, dll.)
            $method = $_SERVER['REQUEST_METHOD'];

            // Ambil URI permintaan dan hilangkan base folder
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Normalize FOLDER_PROJECT - hapus trailing slash jika ada
            $folderProject = rtrim(FOLDER_PROJECT, '/');
            
            // Hilangkan base folder dari URI (case-insensitive, hanya path segment utuh)
            $requestUri = preg_replace('#^/' . preg_quote($folderProject, '#') . '($|/)#i', '/', $requestUri);
            
            // Jika URI kosong atau hanya slash, set sebagai '/'
            if (empty($requestUri)) {
                $requestUri = '/';
            }

            // Proses permintaan melalui router
            $route = $this->router->resolve($method, $requestUri);

            if ($route) {
                // Execute middlewares first
                $middlewares = isset($route['middlewares']) ? $route['middlewares'] : array();
                $this->executeMiddlewares($middlewares);
                
                list($controllerName, $methodName) = explode('@', $route['handler']);
                $params = $route['params'];

                $controllerFile = dirname(__FILE__) . '/../controllers/' . $controllerName . '.php';

                // Cek apakah file controller ada
                if (!file_exists($controllerFile)) {
                    $this->showErrorPage(array(
                        'errorTitle' => 'Controller Not Found',
                        'errorType' => '404 - File Not Found',
                        'errorMessage' => "Controller file not found: {$controllerName}.php",
                        'errorFile' => $controllerFile,
                        'errorLine' => 0,
                        'method' => $method,
                        'uri' => $requestUri,
                        'stackTrace' => debug_backtrace()
                    ));
                    return;
                }

                require_once $controllerFile;

                // Cek apakah kelas controller ada
                if (!class_exists($controllerName)) {
                    $this->showErrorPage(array(
                        'errorTitle' => 'Controller Class Not Found',
                        'errorType' => '500 - Class Not Found',
                        'errorMessage' => "Controller class not found: {$controllerName}",
                        'errorFile' => $controllerFile,
                        'errorLine' => 0,
                        'method' => $method,
                        'uri' => $requestUri,
                        'stackTrace' => debug_backtrace()
                    ));
                    return;
                }

                $controller = new $controllerName();

                // Cek apakah metode dalam controller ada
                if (!method_exists($controller, $methodName)) {
                    $this->showErrorPage(array(
                        'errorTitle' => 'Method Not Found',
                        'errorType' => '500 - Method Not Found',
                        'errorMessage' => "Method '{$methodName}' not found in controller '{$controllerName}'",
                        'errorFile' => $controllerFile,
                        'errorLine' => 0,
                        'method' => $method,
                        'uri' => $requestUri,
                        'stackTrace' => debug_backtrace()
                    ));
                    return;
                }

                // Panggil metode controller dengan parameter
                call_user_func_array(array($controller, $methodName), $params);
                return;
            }

            // Jika rute tidak ditemukan
            $this->showErrorPage(array(
                'errorTitle' => 'Route Not Found',
                'errorType' => '404 - Not Found',
                'errorMessage' => "No route found for {$method} {$requestUri}",
                'errorFile' => __FILE__,
                'errorLine' => __LINE__,
                'method' => $method,
                'uri' => $requestUri,
                'stackTrace' => debug_backtrace()
            ));
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    /**
     * Execute middlewares
     */
    private function executeMiddlewares($middlewares)
    {
        if (empty($middlewares)) {
            return;
        }
        
        foreach ($middlewares as $middleware) {
            // Parse middleware dengan parameter: "role:admin,manager"
            $middlewareName = $middleware;
            $params = array();
            
            if (strpos($middleware, ':') !== false) {
                list($middlewareName, $paramString) = explode(':', $middleware, 2);
                $params = explode(',', $paramString);
            }
            
            // Capitalize first letter
            $middlewareClass = ucfirst($middlewareName) . 'Middleware';
            $middlewarePath = dirname(__FILE__) . '/../middlewares/' . $middlewareClass . '.php';
            
            if (file_exists($middlewarePath)) {
                require_once $middlewarePath;
                
                // Create middleware instance dengan parameter di constructor
                if (!empty($params)) {
                    $middlewareInstance = new $middlewareClass($params);
                } else {
                    $middlewareInstance = new $middlewareClass();
                }
                
                // Execute middleware
                $middlewareInstance->handle();
            } else {
                $this->showErrorPage(array(
                    'errorTitle' => 'Middleware Not Found',
                    'errorType' => '500 - Middleware Not Found',
                    'errorMessage' => "Middleware file not found: {$middlewareClass}.php",
                    'errorFile' => $middlewarePath,
                    'errorLine' => 0,
                    'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
                    'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/',
                    'stackTrace' => debug_backtrace()
                ));
            }
        }
    }
}
