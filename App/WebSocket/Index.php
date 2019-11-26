<?php
namespace App\WebSocket;

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
        //{
        //   "content": {
        //                  "arg1": "1"
        //              },
        //              {
        //                  "arg2": "2"
        //              }
        //}

        $this->response()->setMessage('你发的参数是' . json_encode($this->caller()->getArgs()));
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