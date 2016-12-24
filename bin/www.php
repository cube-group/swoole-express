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
require __DIR__ . '/../modules/cube/App.php';

//initialize the cube framework.
\cube\App::init([
    'debug' => 1,
    'error_report' => 1,
    'base_dir' => __DIR__ . '/../',
    'time_zone' => 'Asia/Shanghai',
    'ssl_cert_file' => '',
    'ssl_key_file' => '',
    'modules' => [
        'modules/session/autoload.php',
        'modules/favicon/autoload.php',
    ]
]);

?>