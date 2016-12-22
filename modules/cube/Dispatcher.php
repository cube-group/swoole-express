<?php

namespace cube;

/**
 * Class Dispatcher.
 *
 */
class Dispatcher
{
    /**
     * @var Request
     */
    private $req;
    /**
     * @var Response
     */
    private $res;
    /**
     * @var MiddlewareArray
     */
    private $stack;
    /**
     * next function instance.
     * @var \Closure
     */
    private $connect;



    /**
     * filter router string & router-path.
     *
     * demo: match
     * path: /user/
     * filter: /
     *
     * demo: not match
     * path: /user/
     * filter : /u
     *
     * demo: not match
     * path: /user or /user/ or /user/a/b
     * filter: /user/:id
     *
     * demo: match
     * path: /user/a
     * filter: /user/:id
     *
     * demo: match
     * path: /user/a/b
     * filter: /user/:id/:name
     *
     * demo: not match
     * path: /user/a/
     * filter: /user/:id/:name
     *
     * @param $absoluteFilter stringf
     * @param $req Request
     */
    private static function match($absoluteFilter, Request $req)
    {
        if (strpos($req->path, $absoluteFilter) === 0) {
            $req->route = $absoluteFilter;
            return true;
        } else if (strstr($absoluteFilter, ':') == true) {
            //get the params from the path.
            $path_stack = explode('/', $req->path);
            $filter_stack = explode('/', $absoluteFilter);
            //use strict.
            if (count($path_stack) == count($filter_stack)) {
                $params = [];
                foreach ($filter_stack as $key => $value) {
                    if ($value != '' && strstr($value, ':') == true) {
                        $params[explode(':', $value)[1]] = $path_stack[$key];
                    } else if ($value != $path_stack[$key]) {
                        //length equal but other value not equal
                        return false;
                    }
                }
                $req->params($params);
                $req->route = $absoluteFilter;
                return true;
            }
        }
        return false;
    }



    /**
     * Dispatcher constructor.
     * @param Request $req
     * @param Response $res
     * @param \cube\Router $router
     */
    public function __construct(Request $req, Response $res, Router $router)
    {
        $this->req = $req;
        $this->res = $res;

        //clone router->stack()
        $tempStack = $router->stack();
        $this->stack = new MiddlewareArray($tempStack);

        //connect module.
        $this->connect = function () {
            if ($item = $this->stack->current()) {
                $this->exec($item);
            } else {
                $this->res->render('404');
            }
        };

        $this->next();
    }


    /**
     * start the connect.
     *
     * @param $reset boolean
     * @throws \Exception
     */
    private function next()
    {
        if (!$this->stack) {
            throw new \Exception('App can not start!');
        }

        $nextFunction = $this->connect;
        $nextFunction();
    }


    /**
     * execute the middleWare.
     *
     * @param $middleWare array|\Closure
     */
    private function exec($middleware)
    {
        if (is_array($middleware)) {
            list($filter, $obj) = $middleware;

            if (self::match($filter, $this->req)) {
                if (get_class($obj) == 'Closure') {
                    //( $filter , function($req,$res,$next) )
                    $obj($this->req, $this->res, $this->connect);
                } else {
                    $this->next();
                }
            } else {
                $this->next();
            }

        } else if (get_class($middleware) == 'Closure') {
            //( function($req,$res,$next) )
            $middleware($this->req, $this->res, $this->connect);
        } else {
            $this->next();
        }
    }
}


/**
 * Class MiddlewareArray.
 * Package Array.
 * @package cube
 */
class MiddlewareArray
{
    private $value = [];
    private $index = 0;

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name;
    }

    public function push($value)
    {
        array_push($this->value, $value);
    }

    public function del($i)
    {
        unset($this->value[$i]);
    }

    public function current($value = null)
    {
        if ($value) {
            $this->value[$this->index] = $value;
        } else {
            return ($this->index < $this->length()) ? $this->value[$this->index] : false;
        }
    }

    public function execNext()
    {
        $this->value[$this->index][1]->next();
    }

    public function next()
    {
        $this->index++;
    }

    public function prev()
    {
        $this->index--;
    }

    public function reset()
    {
        $this->index = 0;
    }

    public function length()
    {
        return count($this->value);
    }
}
