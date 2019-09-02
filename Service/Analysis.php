<?php
namespace Service;
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/8
 * Time: 16:50
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 * 牌型分析
 */

class Analysis
{
    /**
     * 筛选
     * @param array $lai
     * @param array $zhong
     * @param array $wan
     * @param array $tiao
     * @param array $tong
     * @param array $feng
     */
    public function index(array $lai, array $zhong, array $wan, array $tiao, array $tong, array $feng)
    {

    }

    public function equal_value($value)
    {
        $keyArr    = [];
        $resultKey = [];

        foreach ($value as $k => $v) {
            if(in_array($v, $keyArr)){
                $resultKey[] = array_search($v, $keyArr);
                $resultKey[] = $k;
            } else {
                $keyArr[] = $v;
            }
        }
    }
}
