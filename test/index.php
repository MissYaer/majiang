<?php
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/12
 * Time: 10:21
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 */

function init(){
    // 获取牌
    $redis = new Redis();
    $redis->connect('172.17.0.5',6379);
    $data = $redis->lRange('test_room0',0,-1);
    $sortData = sortPay($data);
    var_dump($sortData);exit;
    // var_dump($sortData);
    $arr = equal_value($data);
    $nothing = no_chi($data, $arr);

    if(empty($nothing)){
        strict_algorithm($data);
    }

}

/**
 * @param $data
 * @return array
 */
function sortPay($data){
    $tiao = $tong = $wan = $feng = $zhong = $lai = [];
    foreach ($data as $v){
        $str = substr($v,0,-1);
        switch ($str){
            case 'tiao':
                $tiao[] = $v;
                break;
            case 'tong':
                $tong[] = $v;
                break;
            case 'wan':
                $wan[] = $v;
                break;
            case 'zhon':
                $zhong[] = $v;
                break;
            default:
                $feng[] = $v;
        }
    }
    $data = [
        'tiao' => $tiao,
        'tong' => $tong,
        'wan'  => $wan,
        'zhon' => $zhong,
        'feng' => $feng
    ];
    return $data;
}


/**
 * 对子排序
 * @param $data
 * @return array
 */
function equal_value($data){
    $dan = $shuang = [];
    foreach ($data as $v){
        if (in_array($v, $dan)) {
            array_push($shuang, $v);
            array_push($shuang, $v);
            $key = getKey($dan,$v);
            unset($dan[$key]);
        } else if (in_array($v, $shuang)) {
            array_push($shuang, $v);
        } else {
            $dan[] = $v;
        }
    }
    return $dan;
}

/**
 * 获取指定数组的key
 * @param $arr
 * @param $value
 * @return int|string
 */
function getKey($arr, $value){
    foreach ($arr as $k => $v){
        if($v == $value){
            return $k;
        }
    }
    return false;
}

/**
 * nothing chi
 * @param $data
 * @param $arr
 * @return array
 */
function no_chi($data, $arr){
    $nothing = [];
    foreach ($arr as $v){
        $str = substr($v,0,-1);
        $number = substr($v,-1,1);
        if(in_array($str.($number+1), $data)
        || in_array($str.($number+2), $data)
        || in_array($str.($number-1), $data)
        || in_array($str.($number-2), $data)
        )
        {
            continue;
        } else {
            $nothing[] = $v;
        }
    }
    return $nothing;
}

// 严格算法
function strict_algorithm($data){

}

function have($data){
    // get history

}

init();