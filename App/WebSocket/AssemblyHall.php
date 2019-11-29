<?php

namespace App\WebSocket;

use App\MysqlClient\MysqlConnection;
use App\Tools\Base64;
use EasySwoole\Mysqli\Client;
use EasySwoole\Mysqli\Config;
use EasySwoole\Socket\AbstractInterface\Controller;

class AssemblyHall extends Controller
{
    //联盟议事厅
    private function getMysqlConfig()
    {
        $assemblyHall=[
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

        $config = new Config([
            'host'          => $assemblyHall['host'],
            'port'          => $assemblyHall['port'],
            'user'          => $assemblyHall['username'],
            'password'      => $assemblyHall['password'],
            'database'      => $assemblyHall['database'],
            'timeout'       => 5,
            'charset'       => $assemblyHall['charset'],
        ]);

        $client = new Client($config);

        go(function () use ($client,$config)
        {
            //构建sql
            $client->queryBuilder()->get('user_list');

            //执行sql
            var_dump($client->execBuilder());
        });
    }

    public function getChatContent()
    {
        // {"class":"AssemblyHall","action":"getChatContent","content":{"uid":22357,"alianceNum":1}}

        $args=$this->caller()->getArgs();

        $uid=$args['uid'];

        $alianceNum=$args['alianceNum'];

        $fd=$this->caller()->getClient()->getFd();

        // 先取得最近的100条聊天记录
        $res=MysqlConnection::getInstance()->chatLimit($alianceNum,100);




        $res=Base64::decode(Base64::encode("今天上火了么？hello，123444"));


        var_dump($res);

        var_dump(Base64::encode("今天上火了么？hello，123444"));


    }




}