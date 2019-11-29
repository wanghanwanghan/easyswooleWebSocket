<?php
namespace App\WebSocket;

use EasySwoole\Component\TableManager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

/**
 * Class Index
 *
 * 此类是默认的 websocket 消息解析后访问的 控制器
 *
 * @package App\WebSocket
 */
class Index extends Controller
{
    public function hello()
    {
        // {"class":"Index","action":"hello","content":{"alianceNum":1,"uid":22357}}

        $args=$this->caller()->getArgs();

        $uid=$args['uid'];
        $alianceNum=$args['alianceNum'];

        $fd=$this->caller()->getClient()->getFd();


        var_dump($uid,$alianceNum,$fd);
    }

    public function who()
    {
        $this->response()->setMessage('your fd is ' . $this->caller()->getClient()->getFd());
    }

    public function pushToAllUser()
    {
        $this->response()->setMessage('异步通知成功');

        $client = $this->caller()->getClient();

        // 异步推送, 这里直接 use fd也是可以的
        TaskManager::getInstance()->async(function () use ($client)
        {
            $server = ServerManager::getInstance()->getSwooleServer();

            foreach ($server->connections as $fd)
            {
                $fd=(int)$fd;

                $clientFd=(int)$client->getFd();

                //不推送给自己
                if ($fd===$clientFd) continue;

                $server->push($fd,"{$fd}号用户接通知啦".date('H:i:s'));
            }
        });
    }
}