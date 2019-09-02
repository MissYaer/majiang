<?php
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/18
 * Time: 23:48
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 */

function init(){
    $brand = [
        'wan1',
        'wan1',
        'wan2',
        'wan2',
        'wan3',
        'wan3',
        'tong9',
        'tong8'
    ];
    $data = pengpengHu($brand);
    var_dump($data);
}

function pengpengHu(array $brand){
    $dan = $shuang = $dui = [];
    $laiNumber = 0;

    $redis = new \Redis();
    $redis->connect('172.17.0.5',6379);
    $lai = $redis->get('test_roomlai');

    foreach ($brand as $v){
        if (in_array($v, $dan)) {
            array_push($shuang, $v);
            array_push($shuang, $v);
            $key = getKey($dan,$v);
            unset($dan[$key]);
        } else if (in_array($v, $shuang)) {
            array_push($dui, $v);
        } else {
            if($v == $lai){
                $laiNumber++;
            }
            $dan[] = $v;
        }
    }
    var_dump(count($dan));
    if(count($dan) <= $laiNumber){
        return true;
    }
    return false;
}

function getKey($arr, $value){
    foreach ($arr as $k => $v){
        if($v == $value){
            return $k;
        }
    }
    return false;
}

init();