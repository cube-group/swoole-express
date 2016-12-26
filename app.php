<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 */
use cube\App;
use session\RedisSession;

$app = App::Router();

//session parser middleware.
//$app->on(RedisSession::create());

//add common middleware.
$app->on(\favicon\Favicon::create());

//add virtual router.
$app->on('/user', 'router/user.php');
$app->on(['up', '/upload'], 'router/upload.php');


//add router middleware.
$app->on('/redirect', function ($req, $res, $next) {
    $res->redirect('/upload/');
});


//add router middleware.
$app->on('/', function ($req, $res, $next) {
    $res->render('index');
});
