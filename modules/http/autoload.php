<?php

namespace http;

use utils\Utils;

if ($ext = Utils::is_miss_ext('curl')) {
    throw new \Exception('Ext ' . $ext . ' is not exist!');
}

/**
 * Class Http.
 * \http\Http
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 *
 * @package http
 */
final class Http
{
    private function __construct()
    {
    }

    /**
     * sync get the http/https request.
     *
     * @param $url
     * @param timeout
     * @param $CA
     * @return mixed
     */
    public static function getSync($url, $timeout = 15, $CA = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        if (Utils::isHTTPS($url)) {
            if ($CA) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_CAINFO, $CA); //CA root file
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            } else {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            }
        }
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * sync post http/https request.
     * @param $url
     * @param $data
     * @param timeout
     * @param $CA
     * @return mixed
     */
    public static function postSync($url, $data, $timeout = 15, $CA = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        if (Utils::isHTTPS($url)) {
            if ($CA) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_CAINFO, $CA); //CA root file
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            } else {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            }
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * sync post request by the special headers.
     *
     * @param $url string
     * @param $data object
     * @param null $headers array
     * @param $timeout integer
     * @return string
     */
    public static function postDataSync($url, $data, $header, $timeout = 15)
    {
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => $header,
                'content' => $data,
                'timeout' => $timeout
            ]
        ];

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * async get the content.
     * @param $url string
     * @param $headers array
     * @param $callback \Closure
     */
    public static function getAsync($url, $headers, $callback)
    {
        $info = parse_url($url);
        $host = $info['host'];
        $port = $info['scheme'] == 'https' ? 443 : 80;
        $base_path = $info['path'] . '?' . $info['query'] . ($info['fragment'] ? '#' . $info['fragment'] : '');

        $cli = new \swoole_http_client($host, $port, $port == 443);
        $cli->setMethod('GET');
        $cli->setHeaders($headers);
        $cli->get($base_path, function (\swoole_http_client $cli) use ($callback) {
            if ($cli && $cli->body) {
                $callback(null, $cli->body);
            } else {
                $callback('err', NULL);
            }
        });
    }

    /**
     * @param $url string
     * @param $obj array
     * @param $headers array
     * @param $callback \Closure
     * @return null
     */
    public static function postAsync($url, $obj, $headers, $callback)
    {
        $info = parse_url($url);
        $host = $info['host'];
        $port = $info['scheme'] == 'https' ? 443 : 80;
        $base_path = $info['path'] . '?' . $info['query'] . ($info['fragment'] ? '#' . $info['fragment'] : '');

        $cli = new \swoole_http_client($host, $port, $port == 443);
        $cli->setMethod('POST');
        $cli->setHeaders($headers);
        $cli->get($base_path, $obj, function (\swoole_http_client $cli) use ($callback) {
            if ($cli && $cli->body) {
                $callback(null, $cli->body);
            } else {
                $callback('err', NULL);
            }
        });
    }
}

?>