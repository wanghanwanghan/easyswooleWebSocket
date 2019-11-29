<?php
namespace App\SwooleTable;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;

class Aliance
{
    use Singleton;

    //联盟议事厅表名
    const ALIANCECHAT = 'AlianceChat';

    //创建联盟议事厅内存表
    public function createAssemblyHallSwooleTable()
    {
        $col=[
            // 联盟编号
            'alianceNum'=>[
                'type'=>TableManager::TYPE_INT,
                'size'=>1
            ],
        ];

        TableManager::getInstance()->add(self::ALIANCECHAT,$col);
    }





}