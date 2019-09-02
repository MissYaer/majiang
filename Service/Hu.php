<?php
namespace Service;
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/7
 * Time: 21:32
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 * 胡牌检测（ 不包括分析牌的打法 ）
 */

class Hu
{
    private $lai;
    private $brand;

    public function __construct($random_cookie, $brand = null, $people)
    {
        $redis = new \Redis();
        $redis->connect('172.17.0.5',6379);
        $this->lai = $redis->get($random_cookie.'lai');

    }

    public function check($data){

    }

    /**
     * 碰碰胡乱将
     * @param array $brand
     * @return bool
     */
    public function pengpengHu(array $brand){
        $dan = $shuang = [];
        $lai = 0;
        foreach ($brand as $v){
            if (in_array($v, $dan)) {
                array_push($shuang, $v);
                array_push($shuang, $v);
                $key = getKey($dan,$v);
                unset($dan[$key]);
            } else if (in_array($v, $shuang)) {
                array_push($shuang, $v);
            } else {
                if($v == $this->lai){
                    $lai++;
                }
                $dan[] = $v;
            }
        }
        if(count($dan) <= $lai){
            return true;
        }
        return false;
    }
}