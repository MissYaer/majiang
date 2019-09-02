<?php
namespace Service;
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/7
 * Time: 11:55
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 */

class Majiang{

    public static $random_cookie; // random room

    const BRAND = [
        'wan1', 'wan2', 'wan3', 'wan4', 'wan5', 'wan6', 'wan7', 'wan8', 'wan9', //一九万
        'tong1', 'tong2', 'tong3', 'tong4', 'tong5', 'tong6', 'tong7', 'tong8', 'tong9',//一九筒
        'tiao1', 'tiao2', 'tiao3', 'tiao4', 'tiao5', 'tiao6', 'tiao7', 'tiao8', 'tiao9',//一九条
        'dong', 'nan', 'xi', 'bei', 'zhong', 'facai', 'bai' // 东南西北中发白
    ];
    // 风
    const FENG = [
        'dong', 'nan', 'xi', 'bei', 'fa', 'bai'
    ];
    // 人
    const PEOPLE = [
        'east'  => 0,
        'south' => 1,
        'west'  => 2,
        'north' => 3,
        'lai'   => 4
    ];

    const MAJIANG_PEOPLE = [
        0 => 'east',
        1 => 'south',
        2 => 'west',
        3 => 'north',
    ];

    const LIST_BRAND = [];

    /**
     * 创建游戏
     * @param null $random_cookie
     * @return string
     */
    public function startGame($random_cookie = null){
        $redis = new \Redis();
        $redis->connect('172.17.0.5',6379);

        // TODO test
        $redis->delete($random_cookie);
        $redis->delete($random_cookie.self::PEOPLE['east']);
        $redis->delete($random_cookie.self::PEOPLE['south']);
        $redis->delete($random_cookie.self::PEOPLE['west']);
        $redis->delete($random_cookie.self::PEOPLE['north']);

        $redis->delete($random_cookie.'_surplus_'.self::PEOPLE['east']);
        $redis->delete($random_cookie.'_surplus_'.self::PEOPLE['south']);
        $redis->delete($random_cookie.'_surplus_'.self::PEOPLE['west']);
        $redis->delete($random_cookie.'_surplus_'.self::PEOPLE['north']);

        // redis 写入数据
        foreach (self::BRAND as $v){
            for($i = 1; $i < 5; $i++){
                $redis->lPush($random_cookie,$v);
                $redis->lPush($random_cookie.'_surplus_'.self::PEOPLE['east'],$v);
                $redis->lPush($random_cookie.'_surplus_'.self::PEOPLE['south'],$v);
                $redis->lPush($random_cookie.'_surplus_'.self::PEOPLE['west'],$v);
                $redis->lPush($random_cookie.'_surplus_'.self::PEOPLE['north'],$v);
            }
        }
        self::$random_cookie = $random_cookie;
        $data['list'] = $this->licensing();
        $data['type'] = 'licensing_brand';
        return json_encode($data);
    }


    /**
     * 起牌
     * @return array
     */
    public function licensing(){
        $list_brand = self::LIST_BRAND;
        for ($a = 0; $a < 3; $a++){
            for ($i = 0; $i < 4; $i++){
                $list_brand['east'][] = $this->getBrand(self::PEOPLE['east']);
            }

            for ($i = 0; $i < 4; $i++){
                $list_brand['south'][] = $this->getBrand(self::PEOPLE['south']);
            }

            for ($i = 0; $i < 4; $i++){
                $list_brand['west'][] = $this->getBrand(self::PEOPLE['west']);
            }

            for ($i = 0; $i < 4; $i++){
                $list_brand['north'][] = $this->getBrand(self::PEOPLE['north']);
            }
        }

        // 再取一次
        $list_brand['east'][]  = $this->getBrand(self::PEOPLE['east']);
        $list_brand['south'][] = $this->getBrand(self::PEOPLE['south']);
        $list_brand['west'][]  = $this->getBrand(self::PEOPLE['west']);
        $list_brand['north'][] = $this->getBrand(self::PEOPLE['north']);
        $list_brand['east'][]  = $this->getBrand(self::PEOPLE['east']); // dong
        $lai                   = $this->getBrand(self::PEOPLE['lai']); // lai

        // sort brand
        $licensingService    = new Licensing(self::$random_cookie);
        $list_brand['east']  = $licensingService->sort($list_brand['east']);
        $list_brand['south'] = $licensingService->sort($list_brand['south']);
        $list_brand['west']  = $licensingService->sort($list_brand['west']);
        $list_brand['north'] = $licensingService->sort($list_brand['north']);
        $list_brand['lai']   = $lai;

        return $list_brand;
    }

    /**
     * 获取一张牌
     * @param $people
     * @param bool $lai
     * @return mixed|String
     */
    public function getBrand($people){
        $redis = new \Redis();
        $redis->connect('172.17.0.5',6379);

        // random get brand
        $len = $redis->lLen(self::$random_cookie);
        $random = rand(0,$len-1);
        $brand_one = $redis->lIndex(self::$random_cookie,$random);
        $redis->lRem(self::$random_cookie,$brand_one,1);
        $redis->lRem(self::$random_cookie.'_surplus_'.$people,$brand_one,1);


        if($people == self::PEOPLE['lai']){
            // 后一个作为赖子
            $brand_one = $this->createLai($brand_one);
            $redis->set(self::$random_cookie.'lai', $brand_one);
        } else {
            $redis->lPush(self::$random_cookie.$people,$brand_one);
        }

        return $brand_one;
    }

    /**
     * 创建赖子
     * @param $lai
     * @return mixed|string
     */
    private function createLai($lai){
        $str = substr($lai,0,-1);
        $is_routine = false;
        $rest = '';
        switch ($str){
            case 'tiao':
                $rest = substr($lai, -1);
                $is_routine = true;
                break;
            case 'tong':
                $rest = substr($lai, -1);
                $is_routine = true;
                break;
            case 'wan':
                $rest = substr($lai, -1);
                $is_routine = true;
                break;
            case 'zhon':
                $lai = 'facai';
                break;
            default:
                $key = array_search($lai, self::FENG);
                $lai = $key == (count($lai)-1) ? self::FENG[0] : self::FENG[$key + 1];
                break;
        }

        if($is_routine == true){
            $lai = intval($rest) + 1 == 10 ? $str.'1' : $str.(intval($rest)+1);
        }

        return $lai;
    }
}