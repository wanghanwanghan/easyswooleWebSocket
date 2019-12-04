<?php
namespace App\Tools;

use EasySwoole\Component\Singleton;

class FormatDate
{
    use Singleton;

    public function formatDate($timestamp)
    {
        $todaytimestamp = time();

        if(intval($todaytimestamp-$timestamp) < 3600)
        {
            return intval(($todaytimestamp-$timestamp)/60) .'分钟前';

        }elseif(intval($todaytimestamp-$timestamp) < 86400)
        {
            return intval(($todaytimestamp-$timestamp)/3600) .'小时前';

        }else
        {
            return date('n月j日',$timestamp);
        }
    }

}
