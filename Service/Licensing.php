<?php
namespace Service;

/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/8
 * Time: 11:02
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 * 理牌
 */

class Licensing{

    private $lai;

    private $redis;
    private $random_cookie;

    public function __construct($random_cookie)
    {
        $this->redis = new \Redis();
        $this->random_cookie = $random_cookie;
        $this->redis->connect('172.17.0.5',6379);
        $this->lai =  $this->redis->get($random_cookie.'lai');
    }

    /**
     * init sort
     * @param $data
     * @return array
     */
    public function sort($data){
        $tiao = $tong = $wan = $feng = $zhong = $lai = [];
        foreach ($data as $v){
            if($v == $this->lai){
                $lai[] = $this->lai;
                continue;
            }
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

        asort($wan);
        asort($tiao);
        asort($tong);
        asort($feng);
        $result = array_merge($lai,$zhong,$wan,$tiao,$tong,$feng);
        return $result;
    }

    /**
     * 牌型排序
     * @param $data
     * @param $random_cookie
     * @param $people
     * @return array
     */
    public function sortPay($data, $random_cookie, $people){
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

        $arr = $this->equal_value($data);
        $nothing = $this->no_chi($data, $arr); // can play brand

        if(!empty($nothing)){
            // get me have brand ??
            $resultData = $this->play_brand($nothing, $random_cookie, $people);
            $result['data'] = $resultData;
            $result['type'] = 'play_brand';
            $result['people'] = Majiang::MAJIANG_PEOPLE[$people];

            // 剔牌
            $this->diBrand($resultData,$people);
        } else {
            // 没有垃圾牌
            $is_can_gang = $this->is_can_gang($data);
            if($is_can_gang == true){
                // kang pai
                $this->kang($data, $random_cookie, $people);
            } else {
                // 严格算法

            }
        }

        return $result;
    }

    /**
     * 对子排序
     * @param $data
     * @return array
     */
    public function equal_value($data){
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
    public function getKey($arr, $value){
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
    public function no_chi($data, $arr){
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

    /**
     * @param $nothing
     * @param $random_cookie
     * @param $people
     * @return mixed
     */
    public function play_brand($nothing, $random_cookie, $people){
        $surplus = $this->redis->lRange($random_cookie.'_surplus_'.$people,0,-1);
        $lai = $this->redis->get($random_cookie.'lai');
        $min_count = 1;
        $min_value = '';
        foreach ($nothing as $v){
            if($v == 'zhong' || $v == $lai){
                continue;
            }
            // peng
            $count = $this->getPengValue($v, $surplus);

            // chi
            $count += $this->getChiValue($v, $surplus);

            if($count < $min_count){
                $min_count = $count;
                $min_value = $v;
            }
        }
        return $min_value;
    }

    /**
     * 可以碰到的概率
     * @param string $value
     * @param array $arr
     * @return int
     */
    public function getPengValue(string $value, array $arr){
        $count = 0;
        $valueCount = count($arr);
        foreach ($arr as $k => $v){
            if($v == $value){
                $count++;
            }
        }

        return $count / $valueCount;
    }

    /**
     * 可以吃的概率
     * @param string $value
     * @param array $arr
     * @return float|int
     */
    public function getChiValue(string $value, array $arr){
        $valueCount = count($arr);
        $value = substr($value,0,-1);
        $number = substr($value,-1,1);
        $count = 0;
        foreach ($arr as $k => $v){
            if($value.($number+1) == $v
                || $value.($number+2) == $v
                || $value.($number-1) == $v
                || $value.($number-2) == $v
            ){
                $count++;
            }
        }
        return $count / $valueCount;
    }

    /**
     * 剔牌
     * @param $brand
     * @param $people
     */
    public function diBrand($brand, $people){
        $this->redis->lRem($this->random_cookie.$people, $brand);
        for ($i = 0; $i < 4; $i++){
            if($i != $people){
                $this->redis->lRem($this->random_cookie.'_surplus_'.$i, $brand,1);
            }
        }
    }

    /**
     * 通知三家可以碰
     * @param $brand
     * @param $people
     */
    public function three_families($brand,$people){
        // 三家是否有人可以碰
        for ($i = 0; $i < 4; $i++){
            if($i != $people){
                $data = $this->redis->lRange($this->random_cookie.$i, 0,-1);
                $number = 0;
                foreach ($data as $k => $v) {
                    if($v == $brand){
                        $number++;
                        if($number == 2){
                            // TODO peng的算法待优化
                            $this->redis->lRem($this->random_cookie.$i,$v,2);
                            $data = [
                                'type'   => 'peng',
                                'people' => Majiang::MAJIANG_PEOPLE[$i],
                                'data'  => $v
                            ];
                            return $data;
                            break;
                        }
                    }
                }
            }
        }
        // 下家可以吃吗
        $next_people = $people == 3 ? 0 : $people++;

        $data = $this->redis->lRange($this->random_cookie.$next_people, 0,-1);
        $chi = [];
        foreach ($data as $v){
            $str = substr($v,0,-1);
            if($str == $brand){
                $chi[] = $v;
            }
        }

        if(!empty($chi)){
            foreach ($chi as $key => $value){

            }
        }

        return false;
    }

    public function is_can_gang($data){
        foreach ($data as $k => $v) {
            if($v == 'zhong'){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $people
     */
    public function kang($data, $random_cookie, $people){
        $this->redis->lRem($this->random_cookie.$people,'zhong',1);
        // 摸牌
        Majiang::getBrand(Majiang::MAJIANG_PEOPLE[$people]);

        $Hu = new Hu();

    }
}