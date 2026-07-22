<?php

class Router
{
    private $routes = array();

    public function __construct()
    {
        // Routes akan di-load dari file routes.php
    }
    
    /**
     * Load routes dari file
     */
    public function loadRoutes()
    {
        // Pass $router instance to routes.php
        $router = $this;
        require_once dirname(__FILE__) . '/../routes/routes.php';
    }
    
    /**
     * Add GET route
     */
    public function get($path, $handler, $middlewares = array())
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }
    
    /**
     * Add POST route
     */
    public function post($path, $handler, $middlewares = array())
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }
    
    /**
     * Add PUT route
     */
    public function put($path, $handler, $middlewares = array())
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }
    
    /**
     * Add DELETE route
     */
    public function delete($path, $handler, $middlewares = array())
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }
    
    /**
     * Add route with any method
     */
    public function any($path, $handler, $middlewares = array())
    {
        $methods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH');
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler, $middlewares);
        }
    }
    
    /**
     * Add route
     */
    private function addRoute($method, $path, $handler, $middlewares = array())
    {
        $this->routes[] = array(
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        );
    }

    /**
     * Resolve route berdasarkan method dan URI
     */
    public function resolve($method, $uri)
    {
        foreach ($this->routes as $route) {
            // Cek metode HTTP
            if ($method !== $route['method']) {
                continue;
            }

            // Cek URL dengan parameter dinamis
            $pattern = $this->convertToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Hapus elemen pertama (full match)
                return array(
                    'handler' => $route['handler'],
                    'params' => $matches,
                    'middlewares' => isset($route['middlewares']) ? $route['middlewares'] : array()
                );
            }
        }

        return null; // Tidak ditemukan
    }

    /**
     * Convert route path ke regex pattern
     */
    private function convertToRegex($path)
    {
        // Normalize path - tambahkan leading slash jika tidak ada
        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        
        // Ubah {param} menjadi regex yang support:
        // - Angka dan huruf: a-zA-Z0-9
        // - Underscore: _
        // - Dash: - (untuk tanggal format YYYY-MM-DD dan nilai -1)
        // - Dot: . (untuk desimal dan ekstensi file)
        return '/^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_\-\.]+)', str_replace('/', '\/', $path)) . '$/';
    }
    
    /**
     * Get all registered routes (untuk debugging)
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
