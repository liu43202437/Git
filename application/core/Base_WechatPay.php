<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/config/WxPay.Config.php";
class Base_WechatPay extends Base_AppController {

    const APPID = WxPayConfig::APPID;
    const SECRET = WxPayConfig::APPSECRET ;
    const MCHID = WxPayConfig::MCHID;
    const KEY = WxPayConfig::KEY;
    const CODEURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const OPENIDURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    const UNURL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const SEORDERURL = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const PAYTOPERSON = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    const REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    private $logPath = '';

    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->logPath = get_instance()->config->config['log_path_file'];
    }
       //生成签名
    public function getSign($arr){
        //去除空值
        $arr = array_filter($arr);
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        //按照键名字典排序
        ksort($arr);
        //生成url格式的字符串
       $str = $this->arrToUrl($arr) . '&key=' . self::KEY;
       return strtoupper(md5($str));
    }
    //获取带签名的数组
    public function setSign($arr){
        $arr['sign'] = $this->getSign($arr);;
        return $arr;
    }
    public function arrToUrl($arr){
        return urldecode(http_build_query($arr));
    }
    //验证签名
    public function chekSign($arr){
        $sign = $this->getSign($arr);
        if($sign == $arr['sign']){
            return true;
        }else{
            return false;
        }
    }
    //获取openid
    public function getOpenId(){
        if($this->session->openid == 'openid'){
            unset($_SESSION);   //清除内存中变量
            session_destroy();  //清除文件
        }
        // unset($_SESSION);   //清除内存中变量
        // session_destroy();  //清除文件
        $this->logs('openid.log', '1');
        if(!empty($this->session->openid)){
            return $this->session->openid;
        }else{
            //1.用户访问一个地址 先获取到code
            if(!isset($_GET['code'])){
                //print_r($_SERVER);
                $this->logs('openid.log', '2');
                $redurl = $this->curPageURL();
                $redurl = urlencode($redurl);
                $this->logs('openid.log', $redurl);
                $url = self::CODEURL . "appid=" .self::APPID ."&redirect_uri={$redurl}&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
                //构建跳转地址 跳转
                $this->logs('openid.log', $url);
                header("location:{$url}");
                die;
            }else{
                //2.根据code获取到openid
                //调用接口获取openid
                $this->logs('openid.log', '3');
                $openidurl = self::OPENIDURL . "appid=" . self::APPID . "&secret=".self::SECRET . "&code=" . $_GET['code'] . "&grant_type=authorization_code";
                $data = file_get_contents($openidurl);
                $arr = json_decode($data,true);
                if(empty($arr['openid'])){
                    $arr['openid'] = 'openid';
                }
                $this->session->set_userdata(array('openid'=>$arr['openid']));
                return $arr['openid'];             
            }
        }
    }
    //调用统一下单api
    public function unifiedOrder($oid,$total_fee = 1,$notify_url = '',$openid = ''){
        /**
         * 1.构建原始数据
         * 2.加入签名
         * 3.将数据转换为XML
         * 4.发送XML格式的数据到接口地址
         */
        $nowTime = time();
        $params = [
            'appid'=> self::APPID,
            'mch_id'=> self::MCHID,
            'nonce_str'=>md5(time()),
            'body'=>'公众号支付测试',
            'out_trade_no'=>$oid,
            'total_fee'=> $total_fee,
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],
            'notify_url'=> base_url().'hunan/passimeter/Socket/notify',
            'trade_type'=>'JSAPI',
            'time_start'=>date('YmdHis',$nowTime),   //超时时间
            'time_expire'=>date('YmdHis',$nowTime + 60*5),
            'product_id'=>$oid,
        ];
        if(empty($openid)){
            $params['openid'] = $this->getOpenId();
        }
        else{
            $params['openid'] = $openid;
        }
       $params = $this->setSign($params); 
       $xmldata = $this->ArrToXml($params);
       $resdata = $this->postXml(self::UNURL, $xmldata);
       $arr = $this->XmlToArr($resdata);
       return $arr;
    }
    //获取prepayid
    public function getPrepayId($oid,$total_fee,$notify_url,$openid){
        $arr = $this->unifiedOrder($oid,$total_fee,$notify_url,$openid);
        if(empty($arr['prepay_id'])){
            $this->reply($arr['return_msg']);
            die;
        }
        return $arr['prepay_id'];
    }
    //获取公众号支付所需要的json数据
    public function getJsParams($prepay_id){
        $params = [
            'appId' => self::APPID,
            'timeStamp' => (string)time(),
            'nonceStr' => md5(time()),
            'package' =>'prepay_id=' . $prepay_id,     
            'signType' =>'MD5',
     //       'paySign' => $this->getSign($params)
        ];
        $params['paySign'] = $this->getSign($params);
        return json_encode($params);
    }
     //调用查询订单接口
    public function sOrder($oid){
       //构建数据
        $params = [
            'appid'=> self::APPID,
            'mch_id'=> self::MCHID,
            'out_trade_no' => $oid,
            'nonce_str'=>md5(time()),
            'sign_type' => 'MD5'
        ];
       
       $params = $this->setSign($params); 
       $xmldata = $this->ArrToXml($params);
     
       $resdata = $this->postXml(self::SEORDERURL, $xmldata);
       $arr = $this->XmlToArr($resdata);
       return $arr;
    }
    //付款到个人
    public function payToPerson($oid,$openid,$amount){
        /**
         * 1.构建原始数据
         * 2.加入签名
         * 3.将数据转换为XML
         * 4.发送XML格式的数据到接口地址
         */
        $params = [
            'mch_appid'=> self::APPID,
            'mch_id'=> self::MCHID,
            'nonce_str'=>md5(time()),
            'partner_trade_no'=>$oid,
            'openid'=>$openid,
            'check_name'=>'NO_CHECK',
            'amount'=>$amount,
            'desc'=>'企业付款到个人',
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR']
        ];
        $params = $this->setSign($params); 
        $xmldata = $this->ArrToXml($params);
        $resdata = $this->curl_post_ssl(self::PAYTOPERSON, $xmldata);
        $arr = $this->XmlToArr($resdata);
        return $arr;
    }
    //退款
    public function refund($oid,$out_refund_no,$total_fee,$refund_fee){
        /**
         * 1.构建原始数据
         * 2.加入签名
         * 3.将数据转换为XML
         * 4.发送XML格式的数据到接口地址
         */
        $params = [
            'appid'=> self::APPID,
            'mch_id'=> self::MCHID,
            'nonce_str'=>md5(time()),
            'out_trade_no'=>$oid,
            'out_refund_no'=>$out_refund_no,
            'total_fee'=>$total_fee,
            'refund_fee'=>$refund_fee,
            'refund_desc'=>'少出票',
        ];
        $params = $this->setSign($params); 
        $xmldata = $this->ArrToXml($params);
        $resdata = $this->postXml(self::REFUND, $xmldata);
        $arr = $this->XmlToArr($resdata);
        return $arr;

    }
    public function curl_post_ssl($url, $vars, $second=30)
    {
        $ch = curl_init();
//超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

//以下两种方式需选择一种
        /******* 此处必须为文件服务器根目录绝对路径 不可使用变量代替*********/
        curl_setopt($ch,CURLOPT_SSLCERT,"application/config/cert_zhongwei/apiclient_cert.pem");
        curl_setopt($ch,CURLOPT_SSLKEY,"application/config/cert_zhongwei/apiclient_key.pem");


        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);

        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    //数组转xml
    public function ArrToXml($arr)
    {
            if(!is_array($arr) || count($arr) == 0) return '';

            $xml = "<xml>";
            foreach ($arr as $key=>$val)
            {
                    if (is_numeric($val)){
                            $xml.="<".$key.">".$val."</".$key.">";
                    }else{
                            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                    }
            }
            $xml.="</xml>";
            return $xml; 
    }
    public function XmlToArr($xml)
    {   
        if($xml == '') return '';
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);     
        return $arr;
    }
    
    public function logs($filename,$data){
        // file_put_contents('./application/logs/' . $filename, date("Y-m-d H:i:s").PHP_EOL.$data.PHP_EOL,FILE_APPEND|LOCK_EX);
        @file_put_contents('/mnt/nas/www/download/zhongwei/fulei/'.date('Y-m-d').'_'. $filename, date("Y-m-d H:i:s").PHP_EOL.$data.PHP_EOL,FILE_APPEND|LOCK_EX);
    }
    public function postXml($url,$postData=''){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    //获取post过来的数据
    public function getPost(){
        return file_get_contents('php://input');
    }
    public function curPageURL() 
    {
      $pageURL = 'http';
     
      if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
      {
        $pageURL .= "s";
      }
      $pageURL .= "://";
      $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
      return $pageURL;
    }
    public function jump($code = '',$url = ''){
        $url = base_url()."lottery-vending/index.html#/locked"."?openid={$openid}&machine_id={$machine_id}&msg={$code}";
        header("location:" . $url);
    }
    //字符串转 ASCII
    public function stringToASCII($str){
        $ascii = '';
        $arr = str_split($str);
        foreach ($arr as $key => $value) {
            $temp = bin2hex($value);
            $temp = strtoupper($temp);
            $ascii .= $temp;
        }
        return $ascii;
    }
    //ASCII转 字符串
    public function ASCIITostring($ascii){
        $str = '';
        $arr = str_split($ascii,2);
        foreach ($arr as $key => $value) {
            $temp = chr(hexdec($value));
            $temp = strtoupper($temp);
            $str .= $temp;
        }
        return $str;
    }
}
