<?php

class Logger
{
    private static $logDir = null;
    private static $logLevel = null;
    private static $initialized = false;

    private static $logLevels = array(
        'debug'     => 0,
        'info'      => 1,
        'notice'    => 2,
        'warning'   => 3,
        'error'     => 4,
        'critical'  => 5,
        'alert'     => 6,
        'emergency' => 7,
    );

    private static function init()
    {
        if (self::$initialized) {
            return;
        }

        $baseDir = dirname(__FILE__) . '/../..';
        $logPath = defined('LOG_PATH') ? LOG_PATH : 'storage/logs/';
        self::$logDir = $baseDir . '/' . ltrim($logPath, '/');

        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0755, true);
        }

        $level = defined('LOG_LEVEL') ? strtolower(LOG_LEVEL) : 'error';
        if (!isset(self::$logLevels[$level])) {
            $level = 'error';
        }
        self::$logLevel = self::$logLevels[$level];

        self::$initialized = true;
    }

    public static function write($level, $message, $context = array())
    {
        self::init();

        $level = strtolower($level);
        if (!isset(self::$logLevels[$level])) {
            $level = 'error';
        }

        if (self::$logLevels[$level] < self::$logLevel) {
            return;
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = isset($trace[2]) ? $trace[2] : (isset($trace[1]) ? $trace[1] : $trace[0]);
        $file = isset($caller['file']) ? $caller['file'] : 'unknown';
        $line = isset($caller['line']) ? $caller['line'] : 0;

        $logMessage = '[' . date('Y-m-d H:i:s') . '] '
            . '[' . strtoupper($level) . '] '
            . $message
            . ' | File: ' . $file
            . ':' . $line;

        if (!empty($context)) {
            $logMessage .= ' | Context: ' . print_r($context, true);
        }

        $logMessage .= PHP_EOL;

        $filename = 'app-' . date('Y-m-d') . '.log';
        $filePath = self::$logDir . '/' . $filename;

        @file_put_contents($filePath, $logMessage, FILE_APPEND | LOCK_EX);
    }

    public static function error($message, $context = array())
    {
        self::write('error', $message, $context);
    }

    public static function warning($message, $context = array())
    {
        self::write('warning', $message, $context);
    }

    public static function info($message, $context = array())
    {
        self::write('info', $message, $context);
    }

    public static function debug($message, $context = array())
    {
        self::write('debug', $message, $context);
    }

    public static function critical($message, $context = array())
    {
        self::write('critical', $message, $context);
    }

    public static function exception($exception, $extraContext = array())
    {
        $context = array(
            'exception_class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        );

        if (!empty($extraContext)) {
            $context = array_merge($context, $extraContext);
        }

        self::write('error', $exception->getMessage(), $context);
    }

    public static function phpError($errno, $errstr, $errfile, $errline)
    {
        $errorTypes = array(
            E_ERROR             => 'Fatal Error',
            E_WARNING           => 'Warning',
            E_PARSE             => 'Parse Error',
            E_NOTICE            => 'Notice',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
        );

        $typeName = isset($errorTypes[$errno]) ? $errorTypes[$errno] : 'Unknown';

        self::write('error', "[$typeName] $errstr", array(
            'errno' => $errno,
            'file' => $errfile,
            'line' => $errline,
        ));
    }
}

if (!function_exists('log_error')) {
    function log_error($message, $context = array())
    {
        Logger::error($message, $context);
    }
}

if (!function_exists('log_info')) {
    function log_info($message, $context = array())
    {
        Logger::info($message, $context);
    }
}

if (!function_exists('log_warning')) {
    function log_warning($message, $context = array())
    {
        Logger::warning($message, $context);
    }
}

if (!function_exists('log_exception')) {
    function log_exception($exception, $context = array())
    {
        Logger::exception($exception, $context);
    }
}
