<?php
namespace app\common\utils;

use think\Config;

/**
 * 彩票工具类
 */
class LotteryUtils
{
   protected static $cpk_url = 'http://api.caipiaokong.cn/';//彩票控的接口连接
    protected static $jk_url = 'http://api.jiekouapi.com/';//彩票控的接口连接


    /**
   * =================通用接口=================
   */

    /**
     * 通用请求访问彩票接口
     * @access protected
     * @param string $action 执行方法
     * @param array $parameters 业务参数
     * @return array
     * @throws \Exception
     */
    protected static function call($action, $parameters)
    {
        $url = self::$cpk_url.$action.'/';
        $protocol = Config::get($parameters['name']);// 获取接口参数
        $parameters = array_merge($protocol, $parameters);
        $url .= '?'.http_build_query($parameters);
        $response = self::get($url);
        if (is_null($response)) {
            throw new \Exception("无效回复");
        }
        return json_decode($response,true);
    }

    /**
     * GET请求
     * @access private
     * @param string $url 地址
     * @param array $header 头部
     * @return string
     * @throws \Exception
     */
    protected static function get($url,$header = []){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }

    /**
     * =================北京28=================
     */

   /**
    * 采集北京28数据
    * @access public
    * @param integer $limit 一次获取多少
    * @return array|false
    */
   public static function getBjxy28($limit = 10)
   {
       $data =[
           "name"=>'bjklb',//通过北京快乐8
           "num"=>$limit,
       ];
        try{
            $result = self::call('lottery',$data);
            if(empty($result)||isset($result['status'])){
                return false;
            }
            $newList = [];
            foreach ($result as $k=>$v){
                $newList[$k] = [
                    'issue'=>$k,
                    'numbers'=>self::bjklb_to_bjxy28($v['number']),
                    'time'=>strtotime($v['dateline'])
                ];
            }
            return $newList;
        } catch (\Exception $e){
            return false;
        }
   }
    /**
     * 通过北京快乐8计算北京幸运28
     * 北京快乐8每期开奖共开出20个数字，幸运28将这20个开奖号码按照由小到大的顺序依次排列；
     * 取其1-6位开奖号码相加，和值的末位数作为幸运 28开奖第一个数值；
     * 取其7-12位开奖号码相加，和值的末位数作为幸运28开奖第二个数值，
     * 取其13-18位开奖号码相加，和值的末位数作为幸运 28开奖第三个数值；
     * 三个数值相加即为幸运28最终的开奖结果。
     * @param string $numbers
     * @return array|false
     */
    protected static function bjklb_to_bjxy28($numbers){
        $data = explode(',',$numbers);
        if(count($data) == 21) array_pop($data);
        if(count($data) !== 20) return false;
        sort($data);
        $aNumber = array_chunk($data,6);
        $newData = [];
        foreach($aNumber as $k => $v){
            if($k <= 2){
                $newData[] = substr(array_sum($v),-1);
            }
        }
        $str = implode(',',$newData);
        return $str;
    }

    /**
     * =================加拿大28=================
     */
    public static function getJndxy28($limit = 10){
        $data = [
            'name'=>'jndklb',
            'num'=>$limit,
        ];
        try{
            $result = self::call('lottery',$data);
            if(empty($result) || isset($result['status'])){
                return false;
            }
            $newList = [];
            foreach ($result as $k=>$v){
                $newList[$k] = [
                    'issue'=>$k,
                    'numbers'=>self::jndklb_to_jndxy28($v['number']),
                    'time'=>strtotime($v['dateline'])
                ];
            }
            return $newList;
        } catch (\Exception $e){
            return false;
        }
    }
    /**
     * 通过加拿大快乐8计算PC28
     * 加拿大28每期开奖共开出20的号码，并且按照从小到大的顺序依次排列
     * 取其2、5、8、11、14、17位数进行相加，和值的末位作为幸运28开奖的第一位数值
     * 取其3、6、9、12、15、18位数进行相加，和值的末位作为幸运28开奖的第二位数值
     * 取其4、7、10、13、16、19位数进行相加，和值的末位作为幸运28开奖的第三位数值
     * 三个数值相加即为PC28最终的开奖结果。
     * @param string $numbers 开奖号码
     * @return array|false
     */
    protected static function jndklb_to_jndxy28($numbers){
        $data = explode(',',$numbers);
        if(count($data) == 21) array_pop($data);
        if(count($data) !== 20) return false;
        sort($data);
        $newData = [];
        $newData[0] = $data[1]+$data[4]+$data[7]+$data[10]+$data[13]+$data[16];
        $newData[1] = $data[2]+$data[5]+$data[8]+$data[11]+$data[14]+$data[17];
        $newData[2] = $data[3]+$data[6]+$data[9]+$data[12]+$data[15]+$data[18];
        foreach($newData as $k => $v){
            $newData[$k] = substr($v,-1);
        }
        $str = implode(',',$newData);
        return $str;

    }

    /**
     * =================北京赛车=================
     */
    /**
     * 采集北京赛车数据
     * @access public
     * @param integer $limit 一次获取多少
     * @return array|false
     */
    public static function getBjsc($limit = 10){
        $data = [
            'name'=>'bjpks',
            'num'=>$limit,
        ];
        try{
            $result = self::call('lottery',$data);
            if(empty($result) || isset($result['status'])){
                return false;
            }
            $newList = [];
            foreach ($result as $k=>$v){
                $newList[$k] = [
                    'issue'=>$k,
                    'numbers'=>$v['number'],
                    'time'=>strtotime($v['dateline'])
                ];
            }
            return $newList;
        } catch (\Exception $e){
            return false;
        }

    }
    /**
     * =================幸运飞艇=================
     */
    /**
     * 采集北京赛车数据
     * @access public
     * @param integer $limit 一次获取多少
     * @return array|false
     */
    public static function getXyft($limit = 10){
        $data = [
            'name'=>'xyft',
            'num'=>$limit,
        ];
        try{
            $result = self::call('lottery',$data);
            if(empty($result) || isset($result['status'])){
                return false;
            }
            $newList = [];
            foreach ($result as $k=>$v){
                $newList[$k] = [
                    'issue'=>$k,
                    'numbers'=>$v['number'],
                    'time'=>strtotime($v['dateline'])
                ];
            }
            return $newList;
        } catch (\Exception $e){
            return false;
        }

    }
    /**
     * =================重庆时时彩=================
     */
    /**
     * 采集重庆时时彩数据
     * @access public
     * @param integer $limit 一次获取多少
     * @return array|false
     */
    public static function getCqssc($limit = 10){
        $data = [
            'name'=>'cqssc',
            'num'=>$limit,
        ];
        try{
            $result = self::call('lottery',$data);
            if(empty($result) || isset($result['status'])){
                return false;
            }
            $newList = [];
            foreach ($result as $k=>$v){
                $newList[$k] = [
                    'issue'=>$k,
                    'numbers'=>$v['number'],
                    'time'=>strtotime($v['dateline'])
                ];
            }
            return $newList;
        } catch (\Exception $e){
            return false;
        }

    }
    /**
     * =================澳洲5=================
     */
    /**
     * 采集重庆时时彩数据
     * @access public
     * @param integer $limit 一次获取多少
     * @return array|false
     */
    public static function getAz5($limit = 10){
        $data = [
            'name'=>'az5',
            'num'=>$limit,
        ];
        try{
            $result = self::call2('hall/nodeService/api_request',$data);
            if(empty($result) || isset($result['status'])){
                return false;
            }
            $newList = [];
            foreach ($result['result']['data'] as $k=>$v){
                $newList[$k] = [
                    'issue'=>$v['gid'],
                    'numbers'=>$v['award'],
                    'time'=>strtotime($v['time'])
                ];
            }
            return $newList;
        } catch (\Exception $e){
            return false;
        }

    }
    /**
     * 通用请求访问彩票接口
     * @access protected
     * @param string $action 执行方法
     * @param array $parameters 业务参数
     * @return array
     * @throws \Exception
     */
    protected static function call2($action, $parameters)
    {
        $url = self::$jk_url.$action;
        $protocol = Config::get($parameters['name']);// 获取接口参数
//        $parameters = array_merge($protocol, $parameters);
        $url .= '?'.http_build_query($protocol);
        $response = self::get($url);
        if (is_null($response)) {
            throw new \Exception("无效回复");
        }

        return json_decode($response,true);
    }

}