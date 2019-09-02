<?php
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/12
 * Time: 17:03
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 */

/**
 * @param $arr
 * @param $value
 * @return bool|int|string
 */
function getKey($arr, $value){
    foreach ($arr as $k => $v){
        if($v == $value){
            return $k;
        }
    }
    return false;
}