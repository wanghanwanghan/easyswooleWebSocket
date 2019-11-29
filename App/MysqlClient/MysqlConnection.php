<?php
namespace App\MysqlClient;

use EasySwoole\Component\Singleton;
use EasySwoole\Mysqli\Client;
use EasySwoole\Mysqli\Config;

class MysqlConnection
{
    use Singleton;

    private $assemblyHall=[
        'driver' => 'mysql',
        'host' => '183.136.232.236',
        'port' => '3306',
        'database' => 'aliance',
        'username' => 'chinaiiss',
        'password' => 'chinaiiss',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => null,
    ];

    private $config=null;

    private $client=null;

    private function setAlianceConfig()
    {
        if ($this->config!=null) return $this;

        $config = new Config([
            'host'          => $this->assemblyHall['host'],
            'port'          => $this->assemblyHall['port'],
            'user'          => $this->assemblyHall['username'],
            'password'      => $this->assemblyHall['password'],
            'database'      => $this->assemblyHall['database'],
            'timeout'       => 5,
            'charset'       => $this->assemblyHall['charset'],
        ]);

        $this->config=$config;

        return $this;
    }

    private function setAlianceClient()
    {
        if ($this->client!=null) return $this;

        $client = new Client($this->config);

        $this->client=$client;

        return $this;
    }

    //取得最近的n条聊天记录
    public function chatLimit($alianceNum,$num)
    {
        $this->setAlianceConfig();
        $this->setAlianceClient();

        $this->client->queryBuilder()
            ->orderBy('unixTime')
            ->limit($num)
            ->get('alianceChat'.$alianceNum);

        return $this->client->execBuilder();
    }




}
