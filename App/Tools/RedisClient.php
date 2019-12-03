<?php

namespace App\Tools;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;

class RedisClient
{
    use Singleton;

    private $redis=null;

    private function __construct()
    {
        //判断拓展安装没
        if (!extension_loaded('redis')) throw new \Exception('没有redis拓展');

        try
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
            $timeOut=$config['timeout'];

            $res=$this->redis->connect($host,$port,$timeOut);

        }catch (\Exception $e)
        {
            throw new \Exception('redis服务异常');
        }

        if ($res==null) throw new \Exception('redis对象生成异常');

        $this->redis->auth($config['auth']);

        $this->redis->select($config['database']);
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