<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/16
 * Time: 下午6:57
 */

use cube\App;
use fs\FS;

$router = App::Router();

$router->on('/', function ($req, $res, $next) {
    if ($req->files && count($req->files) > 0) {
        $res->json(FS::saveUploadAsFile());
    } else if ($content = $req->body) {
        $res->json(FS::saveInputAsFile($content, $req->query->key));
    } else {
        $res->send('error');
    }
});