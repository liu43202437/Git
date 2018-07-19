<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/config/WxPay.Config.php";
class Service extends Base_MobileController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('session_model');
        $this->filepath = get_instance()->config->config['log_path'];
    }

    public function create_session()
    {

        $code = $this->get_input('code');
        if($code != '') {

            $appid = WxPayConfig::APPID;
            $appsecret = WxPayConfig::APPSECRET;
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
            $ch = curl_init();                              //initialize curl handle
            curl_setopt($ch, CURLOPT_URL, $url);            //set the url
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
            $response = curl_exec($ch);
            curl_close($ch);
            $wx_userinfo = json_decode($response, true);

            $url_t = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $chs = curl_init();                              //initialize curl handle
            curl_setopt($chs, CURLOPT_URL, $url_t);            //set the url
            curl_setopt($chs, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
            // curl_setopt($chs, CURLOPT_GET, 1);
            $responses = curl_exec($chs);
            curl_close($chs);
            $return_infos = json_decode($responses, true);

            if (isset($wx_userinfo['openid'])) {
                $openid = $wx_userinfo['openid'];
            } else {
                parent::wechatAlert('请重新进入公众号');
            }

            $token = $return_infos['access_token'];
            $curl = "https://api.weixin.qq.com/customservice/kfsession/create?access_token={$token}";
            $post_data = ["kf_account" => "hl0669@sdasdas","openid"=>$openid];
           // $post_data = ["touser"=>$openid, "msgtype"=>"text", "text"=>["content"=>"Hello World"]];
            $rs = $this->curl_post_ssl($curl,json_encode($post_data));
            echo $this->transmitService($openid,"hl0669@sdasdas");
            exit();
        }else{
            $appid = WxPayConfig::APPID;
            $re_url = base_url()."/service/service/create_session";
            $redirect_uri = urlencode($re_url);
            // 网页授权
            $autourl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=12#wechat_redirect";
            header("location:$autourl");
            exit();
        }

    }
    private function transmitService($openid,$user)
    {
    $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
     $result = sprintf($xmlTpl, $openid, $user, time());
    return $result;
}

    /**
     *  array转xml
     */
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }
    //使用证书，以post方式提交xml到对应的接口url

    /**
     *   作用：使用证书，以post方式提交xml到对应的接口url
     */
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
        //curl_setopt($ch,CURLOPT_SSLCERT,"application/config/cert_eesee/apiclient_cert.pem");
        //curl_setopt($ch,CURLOPT_SSLKEY,"application/config/cert_eesee/apiclient_key.pem");


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
}