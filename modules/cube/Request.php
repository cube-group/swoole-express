<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:23
 */

namespace cube;

use log\Log;
use utils\DynamicClass;
use utils\Utils;

/**
 * Class Request.
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 * @package cube
 */
final class Request
{
    /**
     * @var \swoole_http_request
     */
    protected $instance;
    /**
     * user ip
     * @var string
     */
    public $ip = '';
    /**
     * request protocol
     * @var string
     */
    public $protocol = 'http';
    /**
     * request host
     * @var string
     */
    public $host = '';
    /**
     * http refer
     * @var string
     */
    public $refer = '';
    /**
     * router string
     * @var string
     */
    public $path = '';
    /**
     * original http/https url
     * @var string
     */
    public $baseUrl = '';
    /**
     * cookie instance
     * @var object
     */
    private $cookie;
    /**
     * post instance
     * @var object
     */
    private $post;
    /**
     * body instance
     * @var object
     */
    private $body;
    /**
     * query instance
     * @var object
     */
    private $get;
    /**
     * file array.
     * @var array
     */
    private $files;
    /**
     *  session instance
     * @var object
     */
    private $session;
    /**
     * /router/:id/:name,$params['id']
     * @var DynamicClass
     */
    private $params;
    /**
     * $req->assist->key
     * @var DynamicClass
     */
    private $assist;
    /**
     * current router filter string.
     * @var string
     */
    public $route;
    /**
     * it's true after exec App::redirect('').
     * @var bool
     */
    public $redirected = false;

    /**
     * Request constructor.
     */
    public function __construct($instance)
    {
        $this->instance = $instance;

        $this->initCoreInfo();
        $this->assist = new DynamicClass();

        echo $this->path . "\n";
    }

    /**
     * init core info.
     *
     */
    private function initCoreInfo()
    {
        //common.
        $this->host = $this->instance->server['http_host'];
        $this->ip = $this->instance->server['remote_addr'];
        $this->referer = @$this->instance->server['http_referer'];
        $this->path = Utils::pathFilter($this->instance->server['request_uri']);
        $this->headers = $this->instance->server;
        $this->baseUrl = $this->protocol . '://' . $this->host . $this->uri;

        $this->cookie = new DynamicClass($this->instance->cookie);
        $this->get = new DynamicClass($this->instance->get);
        $this->post = new DynamicClass($this->instance->post);
        $this->body = $this->instance->rawContent();
        $this->files = $this->instance->files;
    }

    /**
     * get request headers
     * array(11) {
     * 'query_string' =>
     * string(3) "e=1"
     * 'request_method' =>
     * string(3) "GET"
     * 'request_uri' =>
     * string(8) "/a/b/c/d"
     * 'path_info' =>
     * string(8) "/a/b/c/d"
     * 'request_time' =>
     * int(1482315314)
     * 'request_time_float' =>
     * double(1482315314.8282)
     * 'server_port' =>
     * int(8777)
     * 'remote_port' =>
     * int(62493)
     * 'remote_addr' =>
     * string(9) "127.0.0.1"
     * 'server_protocol' =>
     * string(8) "HTTP/1.1"
     * 'server_software' =>
     * string(18) "swoole-http-server"
     * }
     * @return array
     */
    public function headers()
    {
        return $this->instance->server;
    }

    /**
     * redirect the request path.
     *
     * @param $value string
     * @throws \Exception
     */
    public static function redirect($value)
    {
        //coming soon.
    }

    /**
     * set session instance.
     * @param $session
     */
    public function session($session)
    {
        $this->session = $session;
    }

    /**
     * set params instance.
     * @param $params
     */
    public function params($params)
    {
        $this->params = new DynamicClass($params);
    }


    public function __get($name)
    {
        // TODO: Implement __get() method.

        switch ($name) {
            case 'get':
            case 'post':
            case 'body':
            case 'cookie':
            case 'files':
            case 'params':
            case 'session':
                return $this->$name;
        }
    }
}