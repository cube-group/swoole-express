<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午9:59
 */

namespace cube;

use utils\Utils;

/**
 * Class Router.
 * MiddleWare Saver.
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 * @package cube
 */
final class Router
{
    /**
     * middleWare stack.
     * @var MiddlewareArray
     */
    private $stack;
    /**
     * middleware layer filter.
     * @var string;
     */
    private $temp;

    /**
     * Connect constructor.
     * @param $filter string
     */
    public function __construct()
    {
        $this->stack = [];
        $this->temp = [];
    }

    /**
     * add middleWare.
     *
     * support php5.6...
     *
     * add common middleWare.
     * $router->on(function($req,$res,$next){});
     *
     * add router php fileName.
     * $router->on(['/filter','router/filter.php');
     *
     * add router middleware.
     * $router->on(['/filter',function($req,$res,$next){}]);
     *
     * add router middleWare.
     * $router->on('/filter',function($req,$res,$next){});
     *
     * @param $arg1 string|\Closure
     * @param $arg2 string|\Closure|Router
     * @param $object router ClassName or Instance.
     */
    public function on(...$args)
    {
        $len = count($args);
        if ($len >= 2) {
            if (is_string($args[1])) {
                $this->temp[] = $args[0];
                import($args[1]);
                array_pop($this->temp);
            } else {
                $this->stack[] = [$this->getAbsoluteFilter($args[0]), $args[1]];
            }
        } else {
            if (is_array($args[0]) && count($args[0]) == 2) {
                $this->stack[] = [$this->getAbsoluteFilter($args[0][0]), $args[0][1]];
            } else if (get_class($args[0]) == 'Closure') {
                $this->stack[] = $args[0];
            }
        }
    }

    /**
     * get the current short filter.
     * @return string
     */
    public function filter()
    {
        return $this->filter;
    }

    /**
     * get the test stack.
     * @return array
     */
    public function stack()
    {
        return $this->stack;
    }

    /**
     * fill the filter string.
     *
     * $routerFilter + $filter => $absoluteFilter
     * '/user/' + '/login' => '/user/login/'
     *
     * @param $filter string
     * @param $routerFilter string
     * @return string
     */
    private function getAbsoluteFilter($filter)
    {
        $parentAbsoluteFilter = '';
        foreach ($this->temp as $item) {
            $parentAbsoluteFilter .= substr(Utils::pathFilter($item), 0, -1);
        }
        return $parentAbsoluteFilter . Utils::pathFilter($filter);
    }
}