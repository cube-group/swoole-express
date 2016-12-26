<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:42
 */

namespace cube;

use log\Log;
use utils\Utils;

/**
 * Global load php method.
 * import will check the constant('BASE_DIR')
 *
 * @param $files file path string or array
 * @param $repeatLoad boolean
 */
function import($files, $repeatLoad = false)
{
    $arr = null;
    if (empty($files)) {
        return;
    } elseif (is_array($files)) {
        $arr = $files;
    } else {
        $arr = [$files];
    }
    $base_dir = defined('BASE_DIR') ? constant('BASE_DIR') : '';
    foreach ($arr as $file) {
        $file = $base_dir . $file;
        if (!strstr($file, '.php')) {
            $file .= '.php';
        }
        if (!is_file($file)) {
            continue;
        }

        $key = 'import-require-once-' . $file;
        if ($repeatLoad) {
            require $file;
        } else if (!isset($GLOBALS[$key])) {
            $GLOBALS[$key] = 1;
            require $file;
        }
    }
}


/**
 * Class App.
 * Cube HTTP Framework Facade Core Class.
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 * @package cube
 */
final class App
{
    /**
     * facade Router.
     * @var Router
     */
    private static $router = null;

    /**
     * Application GarbageCollection.
     */
    public static function close()
    {
        Log::flush();
    }

    /**
     * initialize the app.
     *
     * options:[
     *      'base_dir'=>'project dir',
     *      'time_zone'=>'zone',
     *      'time_limit'=>'set_time_limit',
     *      'error_report'=>'0/1',
     *      'debug'=>1
     * ]
     * @param $options array
     */
    public static function init($options)
    {
        if (self::$router) {
            throw new \Exception('App has been initialized!');
        }

        //load libs & modules.
        Config::init($options);

        //check php version.
        if (!Utils::is_legal_php_version('5.6.0')) {
            throw new \Exception('PHP VERSION IS LOW!');
        }
        //check swoole.
        if (Utils::is_miss_ext('swoole')) {
            throw new \Exception('SWOOLE MISS!');
        }

        //init the router.
        self::$router = new Router();

        //load logic code.
        import('app.php');

        if ($options['debug']) {
            var_dump(self::$router->stack());
        }

        //start the web server.
        $server = new \swoole_http_server('127.0.0.1', Config::get('core', 'server_port'));
        $server->on('request', function ($req, $res) {
            new Dispatcher(new Request($req), new Response($res), self::$router);
        });
        $server->set(array(
            'reactor_num' => 2,
            'worker_num' => 4,
            'log_level' => 0,
            'log_file' => BASE_DIR . 'log.log',
            'ssl_cert_file' => $options['ssl_cert_file'],
            'ssl_key_file' => $options['ssl_key_file'],
            'upload_tmp_dir' => BASE_DIR . Config::get('dir', 'tmp')
        ));
        $server->start();

        return $server;
    }


    /**
     * return the facade router.
     *
     * $app = App::app();
     * $app->on('/test',function($req,$res,$next){
     *      $next();
     * });
     *
     * @return Router
     */
    public static function Router()
    {
        return self::$router;
    }

    private function __construct()
    {
        //private
    }
}


/**
 * Class Config.
 * save the Application package.json object.
 * save the global values.
 */
final class Config
{
    /**
     * cube global config object.
     * @var array
     */
    private static $VALUE = null;


    /**
     * append the package.json object info.
     * all constant value.
     *options:[
     *      'base_dir'=>'project dir',
     *      'time_zone'=>'zone',
     *      'error_report'=>'0/1'
     * ]
     * @param $json array
     * @throws \Exception
     */
    public static function init($options)
    {
        if (!$options) {
            $options = [];
        }

        set_time_limit(0);
        error_reporting($options['error_report'] ? $options['error_report'] : 0);
        date_default_timezone_set($options['time_zone'] ? $options['time_zone'] : 'Asia/Shanghai');

        define('BASE_DIR', realpath($options['base_dir']) . '/');
        define('START_TIME', microtime(true));

        import([
            'modules/utils/autoload.php',
            'modules/fs/autoload.php',
            'modules/log/autoload.php',
            'modules/engine/autoload.php',
            'modules/cube/Request.php',
            'modules/cube/Response.php',
            'modules/cube/Router.php',
            'modules/cube/Dispatcher.php'
        ]);

        if ($options['modules']) {
            import($options['modules']);
        }

        if ($json = json_decode(file_get_contents($options['base_dir'] . 'package.json'), true)) {
            self::$VALUE = $json;
            define('VIEW_DIR', $options['base_dir'] . $json['dir']['view'] . '/');
            define('TMP_DIR', $options['base_dir'] . $json['dir']['tmp'] . '/');
            define('PUB_DIR', $options['base_dir'] . $json['dir']['pub'] . '/');
            $GLOBALS['CONFIG'] = $json;
        } else {
            throw new \Exception('config is error or null');
        }
    }

    /**
     * Get the package.json object children value.
     *
     * Config::get('dir','view');
     *
     * @param $arg1 string
     * @param string $arg2
     * @return object | null
     */
    public static function get($arg1, $arg2 = '')
    {
        if ($arg2) {
            return self::$VALUE[$arg1][$arg2];
        } else {
            return self::$VALUE[$arg1];
        }
    }
}

function onErrorHandler()
{
    if ($e = error_get_last()) {
        switch ($e['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                displayError(['msg' => $e['message'], 'level' => $e['type'], 'line' => $e['line'], 'file' => $e['file']]);
                break;
        }
    }
}

/**
 * Global Exception Handler.
 * @param Exception $e
 */
function onExceptionHandler(\Exception $e)
{
    displayError(['msg' => $e->getMessage(), 'level' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
}

function displayError($errors)
{
    var_dump($errors);
}

set_error_handler('cube\onErrorHandler');
set_exception_handler('cube\onExceptionHandler');
register_shutdown_function('cube\onErrorHandler');