<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 */
use cube\App;
use session\Session;

$app = App::Router();

//session parser middleware.
$app->on(Session::create());

//add common middleware.
$app->on(function ($req, $res, $next) {
    $next();
});

//add virtual router.
$app->on('/user', 'router/user.php');
$app->on('/upload', 'router/upload.php');


//add router middleware.
$app->on('/redirect', function ($req, $res, $next) {
    $res->redirect('/upload/');
});


//add router middleware.
$app->on('/', function ($req, $res, $next) {
    $res->render('index');
});
