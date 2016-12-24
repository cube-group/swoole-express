<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/13
 * Time: 下午12:20
 */

namespace redis;

use utils\Utils;

/**
 * Class RedisSync.
 * connect and operate redis by the sync mode.
 * @package modules\redis
 */
class RedisSync
{
    /**
     * redis connect config.
     * @var array
     */
    private $options;
    /**
     * redis connect instance.
     * @var \Redis
     */
    private $redis;

    /**
     * DataStore constructor.
     * array(
     *      'host'=>'localhost',
     *      'port'=>4396,
     *      'password'=>''
     * );
     * @param $options
     */
    public function RedisSync($options)
    {
        if ($ext = Utils::is_miss_ext('redis')) {
            throw new \Exception('Ext ' . $ext . ' is not exist!');
        }

        $this->options = $options;
    }

    /**
     * close the redis connection.
     *
     * @return bool
     */
    public function close()
    {
        $this->redis = null;
        return true;
    }

    /**
     * get the redis db.
     *
     * $redis->model()->set('key','value');
     * $redis->model()->get('key');
     *
     * $redis->model()->setex('key',3600,'value');//1h TTL
     *
     * $redis->model()->setnx('key','value');//repeat-write
     *
     * $redis->model()->delete('key');
     * $redis->model()->delete(array('key1','key2','key3');
     *
     * $redis->model()->ttl('key');//get the life-cycle of the key
     *
     * $redis->model()->persist('key');//remove the key when its life-cycle is over,success return 1,failed return 0
     *
     * $redis->model()->mset(array('key1'=>'value1','key2'=>'value2'));
     *
     * $redis->model()->exists('key');//key is exist or not.
     *
     * $redis->model()->incr('key');//auto plus 1
     * $redis->model()->incrBy('key',10);//auto plus 10
     *
     * $redis->model()->decr('key');//Auto minus 1
     * $redis->model()->decrBy('key',10);//Auto minus 10
     *
     * @param $index int
     */
    public function model($index = 0)
    {
        if (!$this->redis) {
            try {
                $this->redis = new \Redis();
                $this->redis->connect($this->options["host"], $this->options["port"]);
                $this->redis->auth($this->options["password"]);

            } catch (\RedisException $e) {
                return FALSE;
            }
        }
        if ($this->options['index'] != $index) {
            $this->options['index'] = $index;
            $this->redis->select($index);
        }

        return $this->redis;
    }
}


/**
 * Class RedisAsync。
 * connect and operate redis by the async mode.
 * @package redis
 */
class RedisAsync
{
    /**
     * redis connect config.
     * @var array
     */
    private $options;
    /**
     * redis connect instance.
     * @var \swoole_redis
     */
    private $redis;

    /**
     * DataStore constructor.
     * array(
     *      'host'=>'localhost',
     *      'port'=>4396,
     *      'password'=>''
     * );
     * @param $options
     */
    public function RedisASync($options)
    {
        if ($ext = Utils::is_miss_ext('hiredis')) {
            throw new \Exception('Ext ' . $ext . ' is not exist!');
        }

        $this->options = $options;
    }

    /**
     * close the redis connection.
     *
     * @return bool
     */
    public function close()
    {
        $this->redis = null;
        return true;
    }

    /**
     * get the redis db.
     *
     * $redis->model()->set('key','value');
     * $redis->model()->get('key');
     *
     * $redis->model()->setex('key',3600,'value');//1h TTL
     *
     * $redis->model()->setnx('key','value');//repeat-write
     *
     * $redis->model()->delete('key');
     * $redis->model()->delete(array('key1','key2','key3');
     *
     * $redis->model()->ttl('key');//get the life-cycle of the key
     *
     * $redis->model()->persist('key');//remove the key when its life-cycle is over,success return 1,failed return 0
     *
     * $redis->model()->mset(array('key1'=>'value1','key2'=>'value2'));
     *
     * $redis->model()->exists('key');//key is exist or not.
     *
     * $redis->model()->incr('key');//auto plus 1
     * $redis->model()->incrBy('key',10);//auto plus 10
     *
     * $redis->model()->decr('key');//Auto minus 1
     * $redis->model()->decrBy('key',10);//Auto minus 10
     *
     * @param $index int
     */
    public function model($index = 0)
    {
        if (!$this->redis) {
            try {
                $this->redis = new \swoole_redis();
                $this->redis->connect($this->options["host"], $this->options["port"]);
                $this->redis->auth($this->options["password"]);

            } catch (\RedisException $e) {
                return FALSE;
            }
        }
        if ($this->options['index'] != $index) {
            $this->options['index'] = $index;
            $this->redis->select($index);
        }
        return $this->redis;
    }
}