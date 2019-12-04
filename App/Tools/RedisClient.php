<?php

namespace App\Tools;

use EasySwoole\Component\Singleton;

class RedisClient
{
    use Singleton;

    private $redis=null;

    private function __construct()
    {
        $this->init();
    }

    private function getRedisConf($name='')
    {
        
    }

    //创建redis对象
    private function init()
    {
        $this->redis=new \Redis();

        $config=[
            'host'=>'183.136.232.236',
            'port'=>6379,
            'timeout'=>5,
            'auth'=>'wanghan123',
            'database'=>12
        ];

        $host=$config['host'];
        $port=$config['port'];
        $timeout=$config['timeout'];

        $this->redis->connect($host,$port,$timeout);

        $this->redis->auth($config['auth']);

        $this->redis->select($config['database']);

        return true;
    }

    //更换链接
    public function conn($name)
    {

    }

    public function get($key)
    {
        if (empty($key)) return null;

        return $this->redis->get($key);
    }

    public function set($key,$value=null)
    {
        if (empty($key)) return null;

        return $this->redis->set($key,$value);
    }

    public function hget($key,$field)
    {
        if (empty($key)) return null;

        return $this->redis->hget($key,$field);
    }

    public function del($key)
    {
        if (empty($key)) return null;

        return $this->redis->del($key);
    }

    public function expire($key,$s)
    {
        if (empty($key)) return null;

        return $this->redis->expire($key,$s);
    }




}