<?php
/**
 * Created by PhpStorm.
 * www is the facade of the project.
 * never never try to change the www.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:53
 */

//include all cube libs.
require __DIR__.'/../modules/cube/App.php';

//initialize the cube framework.
\cube\App::init([
    'base_dir' => __DIR__.'/../',
    'error_report' => 1,
    'time_zone' => 'Asia/Shanghai',
    'debug' => 1,
    'modules'=>[
        'modules/session/autoload.php',
        'modules/favicon/autoload.php',
    ]
]);

?>