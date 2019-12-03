<?php

namespace App\WebSocket;

use App\MysqlClient\MysqlConnection;
use App\SwooleTable\Aliance;
use App\Tools\RedisClient;
use EasySwoole\Component\TableManager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

class AssemblyHall extends Controller
{
    //获取聊天内容，顺便打开websocket
    public function getChatContent()
    {
        // {"class":"AssemblyHall","action":"getChatContent","content":{"uid":22357,"alianceNum":1}}

        $args=$this->caller()->getArgs();

        $uid=$args['uid'];

        $alianceNum=$args['alianceNum'];

        $fd=$this->caller()->getClient()->getFd();

        //加入内存表
        $this->createUidAndFdRelation($uid,$alianceNum,$fd);

        //取得最近的聊天记录
        $res=MysqlConnection::getInstance()->chatLimit($alianceNum,50);

        $res=$this->fillData($res);

        $this->response()->setMessage($res);
    }

    //处理客户端发送过来的消息
    public function getClientMsg()
    {
        // {"class":"AssemblyHall","action":"getClientMsg","content":{"uid":22357,"alianceNum":1,"content":"什么冬梅"}}

        $args=$this->caller()->getArgs();

        $uid=$args['uid'];

        $alianceNum=$args['alianceNum'];

        //用户输入内容
        $content=$args['content'];

        $client=$this->caller()->getClient();

        //异步mysql
        go(function () use ($uid,$alianceNum,$content)
        {
            MysqlConnection::getInstance()->insertOneChat($alianceNum,$uid,$content);
        });

        $server=ServerManager::getInstance()->getSwooleServer();

        foreach ($server->connections as $fd)
        {
            $clientFd=$client->getFd();

            $res=TableManager::getInstance()->get(Aliance::ALIANCECHATS)->get("{$fd}_num");

            if (!$res) $this->createUidAndFdRelation($uid,$alianceNum,$clientFd);

            if ($fd==$clientFd) continue;

            if ($res['alianceNum']!=$alianceNum) continue;

            var_dump($res);

            $res['content']=$content;
            $res['unixTime']=time();

            $res=$this->fillData([$res]);

            $server->push($fd,json_encode($res));
        }





        //异步推送
//        TaskManager::getInstance()->async(function () use ($client,$content,$alianceNum)
//        {
//            $server=ServerManager::getInstance()->getSwooleServer();
//
//            foreach ($server->connections as $fd)
//            {
//                $clientFd=$client->getFd();
//
//                //不推送给自己和不存在内存表中
//                $res=TableManager::getInstance()->get(Aliance::ALIANCECHATS)->get((string)$fd);
//
//                if ($fd==$clientFd || !$res || $res['alianceNum']!=$alianceNum) continue;
//
//                $res['content']=$content;
//                $res['unixTime']=time();
//
//                $server->push($fd,json_encode(AssemblyHall::getInstance()->fillData([$res])));
//            }
//        });
    }

    //建立uid和fd的关系，删除关系在onClose里
    private function createUidAndFdRelation($uid,$alianceNum,$fd)
    {
        $getObj=TableManager::getInstance()->get(Aliance::ALIANCECHATS);

        $getObj->set("{$fd}_num",['uid'=>$uid,'alianceNum'=>$alianceNum]);

        return true;
    }

    //根据uid添加用户名和头像
    public function fillData($data)
    {
        foreach ($data as &$one)
        {
            if (!isset($one['uid'])) continue;

            $one['name']=trim(RedisClient::getInstance()->hget($one['uid'],'name'));
            $one['avatar']=trim(RedisClient::getInstance()->hget($one['uid'],'avatar'));
        }
        unset($one);

        return $data;
    }




}