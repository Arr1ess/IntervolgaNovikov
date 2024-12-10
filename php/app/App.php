<?php

namespace app;

use app\router\Response;
use app\router\Router;
use Exception;
use models\user;
use plugins\Pdiscount\Pdiscount;
use plugins\PProduct\PProduct;

class App
{
    private static App $instance;
    public static string $serverName;



    public static function getInstance(): App
    {
        if (isset(self::$instance)) {
            return self::$instance;
        } else {
            throw new Exception("App is not initialized");
        }
    }
    
    public function __construct(bool $debugMode = false, string $serverName = null)
    {
        self::$serverName = $serverName ?? $_SERVER['DOCUMENT_ROOT'];
        if (!$debugMode) {
            ini_set('display_errors', '0');
        }
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null && $this->isFatalError($error['type'])) {
                Response::error($error['message'])->send();
            }
        });

        spl_autoload_register(function ($className) {
            $className = str_replace('\\', '/', $className);
            $filePath = self::$serverName . "/$className.php";
        
            if (file_exists($filePath)) {
                require_once $filePath;
            } else {
                throw new Exception("Class file not found: $filePath");
            }
        });
        
        self::$instance = $this;


    }

    private function isFatalError(int $type): bool
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true);
    }

    public function run()
    {
        Router::dispatch();
    }
}
