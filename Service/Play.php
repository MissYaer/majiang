<?php
namespace Service;
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/7
 * Time: 21:25
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 * 打牌类
 */

class Play{
    const NUMBER = 13; // 手牌小于13张

    public function __construct($data)
    {
        if(count($data) < self::NUMBER){
            $huService = new Hu();
        }
    }

    /**
     * 打牌
     * @param $data
     * @param $random_cookie
     * @param $people
     * @return string
     */
    public function play($data, $random_cookie, $people){
        $licensing = new Licensing($random_cookie);
        $data = $licensing->sortPay($data, $random_cookie, $people);
        return json_encode($data);
    }

    /**
     * 通知三家，是否可以碰
     * @param $brand
     * @param $random_cookie
     * @return string
     */
    public function three_families($brand, $random_cookie,$people){
        $licensing = new Licensing($random_cookie);
        $data = $licensing->three_families($brand,$people);
        return json_encode($data);
    }
}