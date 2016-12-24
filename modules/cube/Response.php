<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:23
 */

namespace cube;

use engine\AngularEngine;
use engine\RaintplEngine;
use engine\ViewEngine;
use fs\FS;
use log\Log;
use utils\Utils;


/**
 * Class Response
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 * @package cube
 */
final class Response
{
    /**
     * @var \swoole_http_response
     */
    protected $instance;

    /**
     * Response constructor.
     * @param $instance \swoole_http_response
     */
    public function __construct($instance)
    {
        $this->instance = $instance;

        $this->instance->header('ServiceX', Config::get('name') . ' version:' . Config::get('version'));
    }

    /**
     * url location to the client.
     *
     * $res->location('https://github.com/cube-group/express-mvc');
     *
     * @param $path
     * @return $this
     */
    public function location($path)
    {
        $this->instance->header('Location', $path);
        return $this;
    }


    /**
     * set the cookie of the responder.
     * @param $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return $this
     */
    public function cookie($key, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        $this->instance->cookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * file download.
     *
     * $res->download('/usr/local/a.rar');
     *
     * @param $filename string
     */
    public function download($filename)
    {
        if (!is_file($filename)) {
            return FALSE;
        }

        // Parse Info / Get Extension
        $fsize = filesize($filename);
        $path_parts = pathinfo($filename);//返回文件路径的信息
        $ext = strtolower($path_parts["extension"]); //将字符串转化为小写
        // Determine Content Type
        switch ($ext) {
            case "ico":
                $ctype = "image/x-icon";
                break;
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "exe":
                $ctype = "application/octet-stream";
                break;
            case "zip":
                $ctype = "application/zip";
                break;
            case "doc":
                $ctype = "application/msword";
                break;
            case "xls":
                $ctype = "application/vnd.ms-excel";
                break;
            case "ppt":
                $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            default:
                $ctype = "application/force-download";
        }

        $this->instance->header("Pragma", "public"); // required 指明响应可被任何缓存保存
        $this->instance->header("Expires", "0");
        $this->instance->header("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $this->instance->header("Content-Type", $ctype);
        $this->instance->header("Content-Length", $fsize);
        $this->instance->sendfile($filename);
        return TRUE;
    }

    /**
     * send the simple string to the client.
     *
     * $res->send('hello world');
     *
     * @param $value
     */
    public function send($value)
    {
        $this->instance->status(200);
        $this->instance->end($value);

//        Log::log('Response send', $this->getTimer());
    }

    /**
     * send the json string to the client.
     *
     * $res->json(['hello world']);
     *
     * @param $value array
     */
    public function json($value)
    {
        $this->instance->status(200);
        $this->instance->end(json_encode($value));
    }


    /**
     * set the angularJS Object.
     *
     * @param $viewName string
     * @param $value object
     */
    public function angular($viewName, $value)
    {
        $engine = new AngularEngine();
        $data = $engine->render($viewName, $value);

        $this->instance->status(200);
        $this->instance->end($data);
    }

    /**
     * send the content to the client by the viewEngine.
     *
     * $res->render(new \engine\AngularEngine(),'center',['uid''adsfadsf']);
     *
     * @param viewName string
     * @param $value object
     * @param $engine ViewEngine
     */
    public function render($viewName, $value = null, $engine = null)
    {
        if (!$value) {
            $engine = new ViewEngine();
        } else if (!$engine) {
            $engine = new RaintplEngine();
        }
        $data = $engine->render($viewName, $value);

        $this->instance->status(200);
        $this->instance->end($data);
    }

    /**
     * redirect url.
     *
     * $
     * @param $value string
     */
    public function redirect($value)
    {
//        if ($value) {
//            if (Utils::is_url($value)) {
//                $this->statusCode(301)->location($value);
//                Log::log('Response redirect ' . $value, $this->getTimer());
//            } else {
//                App::redirect($value);
//            }
//        } else {
//            throw new \Exception('redirect value is illegal');
//        }
    }


    /**
     * Get the application run duration microtime.
     *
     * @return int
     */
    public function getTimer()
    {
        return intval((microtime(true) - constant('START_TIME')) * 1000);
    }
}