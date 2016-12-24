<?php

namespace favicon;

/**
 * Class Favicon.
 * @package favicon
 */
class Favicon
{
    public static function create()
    {
        return [
            '/favicon.ico',
            function ($req, $res, $next) {
                if (defined('PUB_DIR')) {
                    $res->download(PUB_DIR . 'favicon.ico');
                } else {
                    $res->send('error');
                }
            }
        ];
    }
}