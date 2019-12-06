<?php
namespace App\Tools;

use EasySwoole\Component\Singleton;

class Sort
{
    use Singleton;

    //二维数组按照某一列排序
    public function arraySort1($array,$cond=['desc','id'])
    {
        if ($cond[0]=='asc')
        {
            $cond[0]='SORT_ASC';

        }else
        {
            $cond[0]='SORT_DESC';
        }

        $sort=['Rule'=>$cond[0],'SortKey'=>$cond[1]];

        $arrSort=[];

        foreach($array as $uniqid=>$row)
        {
            foreach($row as $key=>$value)
            {
                $arrSort[$key][$uniqid]=$value;
            }
        }

        array_multisort($arrSort[$sort['SortKey']],constant($sort['Rule']),$array);

        return $array;
    }





}
