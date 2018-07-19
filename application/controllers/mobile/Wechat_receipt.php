<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/config/eesee_config.php";
class Wechat_receipt extends Base_MobileController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('session_model');
        $this->filepath = get_instance()->config->config['log_path'];
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
        curl_setopt($ch,CURLOPT_SSLCERT,"application/config/cert_eesee/apiclient_cert.pem");
        curl_setopt($ch,CURLOPT_SSLKEY,"application/config/cert_eesee/apiclient_key.pem");


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

    public function getThirdOpenid(){
        $this->load->model("thirdOpenid_model");
        $code = $this->get_input('code');
        $state = $this->get_input('state');
        if($code != '' && $state != ''){

            $appid = EeseeConfig::APPID;
            $appsecret = EeseeConfig::APPSECRET;
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
            $ch = curl_init();                              //initialize curl handle
            curl_setopt($ch, CURLOPT_URL, $url);            //set the url
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
            $response = curl_exec($ch);
            curl_close($ch);
            $wx_userinfo = json_decode($response, true);
            if(isset($wx_userinfo['openid'])){
                $openid = $wx_userinfo['openid'];
            }else{
                parent::wechatAlert('请重新进入公众号');
            }
            $this->load->model("Thirdopenid_model");

            $sesseion = $this->session_model->getInfoBySId($state);
            $sid = $state;
            $in_data['user_id'] = $sesseion['user_id'];
            $in_data['openid'] = $openid;
            $in_data['type'] = 1;

            $openid_rs = $this->thirdOpenid_model->insertData($in_data);
            if($openid_rs === false){
                parent::wechatAlert('请重新公众号进入');
            }else{
                $bas_url = base_url().'resources/wechat/scan-qr-code/scan-qr-code.html?sid='.$sid;
                header('Location:'.$bas_url);
                exit();
            }

        }else{
            $this->load->model('club_model');
            $this->load->model('UserRedeem_model');
            $sid = $this->get_input('sid');
            if(empty($sid)){
                $sid = $this->post_input('sid');
            }
            $sessioninfo = $this->session_model->getInfoBySId($sid);
            if(empty($sessioninfo)){
                parent::wechatAlert('请登录！');
                exit();
            }
            $user_id = $sessioninfo['user_id'];
            $userinfo = $this->user_model->getInfoById($user_id);
            if(empty($userinfo)){
                parent::wechatAlert('用户不存在');
            }
            $clubInfo = $this->club_model->fetchOne(array('user_id'=>$user_id));
            if(!empty($clubInfo)){
                if($clubInfo['status'] != 1){
                    $url = base_url()."mobile/wechat/shop?sid={$sid}";
                    parent::wechatAlert('您的店铺还未通过审核', $url);
                    return;
                }
            }
            else{
                //取用户兑奖信息
                $userRedeemInfo = $this->UserRedeem_model->fetchOne(array('user_id'=>$user_id));
                if(empty($userRedeemInfo)){
                    $url = base_url().'resources/wechat/user-zc.html?sid='.$sid;
                    parent::wechatAlert('请补充兑奖人信息', $url);
                    return;
                }
                else{
                    if($userRedeemInfo['status'] == 0){
                        $url = base_url().'resources/wechat/user-info.html?sid='.$sid;
                        parent::wechatAlert('您的兑奖人信息还在审核中', $url);
                        return;
                    }
                }
            }
            $where = array('user_id'=>$user_id,'type' => 1);
            $thirdopenid = $this->thirdOpenid_model->fetchOne($where);
            if(!empty($thirdopenid)){
                $bas_url = base_url().'resources/wechat/scan-qr-code/scan-qr-code.html?sid='.$sid;
                header('Location:'.$bas_url);
                exit();
            }
            $appid = EeseeConfig::APPID;
            $re_url = base_url()."/mobile/wechat_receipt/getThirdOpenid";
            $redirect_uri = urlencode($re_url);
            // 网页授权
            $autourl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state={$sid}#wechat_redirect";
            header("location:$autourl");
            exit();
        }
    }

//企业向个人付款
    private function payToUser($openid = '',$partner_trade_no,$desc='提现',$amount = 0)
    {
//微信付款到个人的接口
        if($openid == '' || $amount == 0){
            return;
        }
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        $params["mch_appid"]        = EeseeConfig::APPID;   //公众账号appid
        $params["mchid"]            = EeseeConfig::MCHID;   //商户号 微信支付平台账号
        $params["nonce_str"]        = 'eeseetech99'.time();   //随机字符串
        $params["partner_trade_no"] = $partner_trade_no;           //商户订单号
        $params["amount"]           = $amount;          //金额
        $params["desc"]             = $desc;            //企业付款描述
        $params["openid"]           = $openid;          //用户openid
        $params["check_name"]       = 'NO_CHECK';       //不检验用户姓名
        $params['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];   //获取IP
        $keyss = $appid = EeseeConfig::KEY;
        //生成签名
        $str = 'amount='.$params["amount"].'&check_name='.$params["check_name"].'&desc='.$params["desc"].'&mch_appid='.$params["mch_appid"].'&mchid='.$params["mchid"].'&nonce_str='.$params["nonce_str"].'&openid='.$params["openid"].'&partner_trade_no='.$params["partner_trade_no"].'&spbill_create_ip='.$params['spbill_create_ip'].'&key='.$keyss;
        //md5加密 转换成大写

        $sign = strtoupper(md5($str));

        $params["sign"] = $sign;//签名

        $xml = $this->arrayToXml($params);

        return $this->curl_post_ssl($url, $xml);
    }
    public function excReceipt()
    {
        $sid = $this->post_input('sid');
        $money = $this->post_input('money');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $this->load->model("thirdOpenid_model");
        if (empty($sessionInfo)) {
            parent::output(12);
        }

        $userId = $sessionInfo['user_id'];
        $this->load->model('club_model');
        if(!preg_match("/^[1-9]\d*$/",$money)){
            parent::output(909);
        }
        //取消限制为了亲属可以提现
        $info = $this->club_model->getInfoByUserId($userId);
        if (empty($info)) {
            $this->load->model("UserRedeem_model");
            $clan_info = $this->UserRedeem_model->fetchOne(array("user_id"=>$userId));
            if(empty($clan_info)){
                parent::output(16);
            }else{
                $info['name'] = $clan_info['name'];
                $info['id_number'] = $clan_info['phone'];//亲属用手机号码代替
            }
        }else{
            if($info['status'] != 1){
                parent::output(901);
            }
        }

        $userinfo = $this->user_model->getInfoById($userId);
        $userprice = $userinfo['prize'];
        if($userprice < $money ){
            parent::output(806);
        }
        $where = array('user_id'=>$userId,'type' => 1);
        $thirdopenid = $this->thirdOpenid_model->fetchOne($where);
        if(!empty($thirdopenid)){
            $openid = $thirdopenid['openid'];
        }else{
            parent::output(808);
        }

        $this->load->model("receipt_model");
        $start_day = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $end_day = mktime(23,59,59,date("m"),date("d"),date("Y"));
        $today_sum_money = $this->receipt_model->get_today_sum_money($userId,$start_day,$end_day);
        $today_sum_money = intval($today_sum_money/100);
        $today_sum_money= $today_sum_money+$money;
        if($today_sum_money > 10000){
            parent::output(803);
        }
        $data['amount'] = intval($money*100);
        $data['balance'] = intval(($userprice-$money)*100);
        $data['user_id'] = $userId;
        $data['add_time'] = time();
        $data['status'] = 0;
        $data['description'] = "提现";
        $data['number'] = $info['id_number'];
        $data['transfer_name'] = $info['name'];


        list($t1, $t2) = explode(' ', microtime());
        $milisec = (int)sprintf('%.0f', (floatval($t1)) * 1000);
        $milisec = sprintf("%03d", $milisec);

        $trade_no = nownum() . $milisec . gen_rand_num().$userId;

        $data['partner_trade_no'] = $trade_no;


       $order_id = $this->receipt_model->order_insert($data);

       if($order_id != false){
           $sql = "update tbl_user set prize=prize-{$money} where id={$userId}";
           $flag = $this->user_model->execSql($sql);
           if($flag === false){
               $fp =  $this->filepath."receipt_fail_add".date("Y-m-d",time()).".log";
               $contents = '"money":'.$money.'"openid":'.$openid.',"order_id":'.$order_id.PHP_EOL;
               file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               parent::output(902);
           }
           $partner_trade_no = $trade_no;
           $desc = $data['description'];
           $amount = $data['amount'];
           try{
               $wechat_rs = $this->payToUser($openid,$partner_trade_no,$desc,$amount);
               $wechat_rs = $this->xmlToArray($wechat_rs);
           }catch (Exception $e){
               $fp =  $this->filepath."receipt_fail_Exception".date("Y-m-d",time()).".log";
               $contents = json_encode($data).PHP_EOL;
               file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               parent::output(902);
           }


           $where = array("id" => $order_id);
           if($wechat_rs['return_code'] != "SUCCESS"){
               //失败后加回金额
               /* $error_code = array(
                    'NO_AUTH' => '没有该接口权限',
                    'AMOUNT_LIMIT' => '付款金额不能小于最低限额',
                    'PARAM_ERROR' => '参数错误',
                    'OPENID_ERROR' => 'Openid错误',
                    'SEND_FAILED' => '付款错误',
                    'NOTENOUGH' => '余额不足',
                    'SYSTEMERROR' => '系统繁忙，请稍后再试。',
                    'NAME_MISMATCH' => '姓名校验出错',
                    'SIGN_ERROR' => '签名错误',
                    'XML_ERROR' => 'Post内容出错',
                    'FATAL_ERROR'=> '两次请求参数不一致',
                    'FREQ_LIMIT' => '超过频率限制，请稍后再试。',
                    'MONEY_LIMIT' => '已经达到今日付款总额上限/已达到付款给此用户额度上限',
                    'CA_ERROR'=>'证书出错',
                    'V2_ACCOUNT_SIMPLE_BAN'=>'无法给非实名用户付款',
                    'PARAM_IS_NOT_UTF8' => '请求参数中包含非utf8编码字符',
                    'AMOUNT_LIMIT' => '付款失败，因你已违反《微信支付商户平台使用协议》，单笔单次付款下限已被调整为5元'
                );*/


               $sql = "update tbl_user set prize=prize+{$money} where id={$userId}";
               $flag = $this->user_model->execSql($sql);
               if($flag === false){
                   $fp =  $this->filepath."receipt_fail_add".date("Y-m-d",time()).".log";
                   $contents = json_encode($wechat_rs).'"openid":'.$openid.',"order_id":'.$order_id.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }

               $fail_data["openid"] = $openid;
               $fail_data["reason"] = "错误";
               $update_date['status'] = 3;
               $up_order_rs = $this->receipt_model->get_order_update($fail_data,$where);
               $fp =  $this->filepath."receipt_fail_return".date("Y-m-d",time()).".log";
               $content = json_encode($wechat_rs).'"openid":'.$openid.',"order_id":'.$order_id.PHP_EOL;
               file_put_contents($fp,$content,FILE_APPEND|LOCK_EX);

              parent::output(807);
           }
           if($wechat_rs['result_code'] == "SUCCESS") {
               $update_date['detail_id'] = $wechat_rs['payment_no'];
               $update_date['transfer_time'] = $wechat_rs['payment_time'];
               $update_date['openid'] = $openid;
               $update_date['status'] = 1;

               $up_order_rs = $this->receipt_model->get_order_update($update_date,$where);
               if($up_order_rs < 0 ){
                   $fp =  $this->filepath."receipt_fail_return".date("Y-m-d",time()).".log";
                   $contents = json_encode($update_date).'"openid":'.$openid.',"order_id":'.$order_id.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }
              $statuss = array();
               parent::output($statuss);

           }else{

               $sql = "update tbl_user set prize=prize+{$money} where id={$userId}";
               $flag = $this->user_model->execSql($sql);
               if($flag === false){
                   $fp =  $this->filepath."receipt_fail_add".date("Y-m-d",time()).".log";
                   $contents = json_encode($wechat_rs).'"openid":'.$openid.',"order_id":'.$order_id.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }
               $fail_data["openid"] = $openid;
               $fail_data["reason"] = $wechat_rs["err_code_des"];
               $update_date['status'] = 2;
               $up_order_rs = $this->receipt_model->get_order_update($fail_data,$where);

               $error_num = array(
                   'NO_AUTH'=>0,
                   'AMOUNT_LIMIT' => 1,
                   'PARAM_ERROR' => 2,
                   'OPENID_ERROR' => 3,
                   'SEND_FAILED' => 4,
                   'NOTENOUGH' => 5,
                   'SYSTEMERROR' => 6,
                   'NAME_MISMATCH' => 7,
                   'SIGN_ERROR' => 8,
                   'XML_ERROR' => 9,
                   'FATAL_ERROR'=> 10,
                   'FREQ_LIMIT' =>11,
                   'MONEY_LIMIT' => 12,
                   'CA_ERROR'=>13,
                   'V2_ACCOUNT_SIMPLE_BAN'=>14,
                   'PARAM_IS_NOT_UTF8' => 15,
                   'AMOUNT_LIMIT' => 16,
                   'SENDNUM_LIMIT'=>17
               );
               if($wechat_rs['err_code'] == 'V2_ACCOUNT_SIMPLE_BAN' ){
                   $status = array(
                       'status' => array(
                           'succeed' => 0,
                           'error_code' => $error_num[$wechat_rs['err_code']],
                           'error_desc' => "无法给非实名用户付款"
                       )
                   );
               }elseif($wechat_rs['err_code'] == 'NOTENOUGH'){
                   $status = array(
                   'status' => array(
                       'succeed' => 0,
                       'error_code' => $error_num[$wechat_rs['err_code']],
                       'error_desc' => "商家余额不足"
                   )
                   );
                   echo json_capsule($status);
                   exit();
               }elseif ($wechat_rs['err_code'] == 'SENDNUM_LIMIT'){
                   $status = array(
                       'status' => array(
                           'succeed' => 0,
                           'error_code' => $error_num[$wechat_rs['err_code']],
                           'error_desc' => "今日提现次数超出微信官方限制次数"
                       )
                   );
               } else{
                   $status = array(
                       'status' => array(
                           'succeed' => 0,
                           'error_code' => $error_num[$wechat_rs['err_code']],
                           'error_desc' => "系统繁忙，请稍后再试。". $error_num[$wechat_rs['err_code']]
                       )
                   );
               }
               echo json_capsule($status);
               $fp =  $this->filepath."receipt_fail_weixin_".date("Y-m-d",time()).".log";
               $contents = json_encode($wechat_rs).PHP_EOL;
               file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               exit();
           }
       }else{
           $fp =  $this->filepath."receipt_fail_".date("Y-m-d",time()).".log";
           $contents = json_encode($data).PHP_EOL;
           file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);

          parent::output(902);
       }


    }
    public function post_order_request(){
        $code = $_GET['code'];
        $order_id = $_GET['state'];
        // 获取openid
        if($code=='' || $order_id == ''){
            $bas_url = base_url().'resources/wechat/demo/withdraw-after.html?code=1&msg=非法请求';
            header('Location:'.$bas_url);
            exit();
        }
        $appid = EeseeConfig::APPID;
        $appsecret = EeseeConfig::APPSECRET;
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $ch = curl_init();                              //initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $url);            //set the url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
        $response = curl_exec($ch);
        curl_close($ch);
        $wx_userinfo = json_decode($response, true);



        if(isset($wx_userinfo['openid'])){
            $openid = $wx_userinfo['openid'];
        }else{
            $bas_url = base_url().'resources/wechat/demo/withdraw-after.html?code=1&msg=错误请求';
            header('Location:'.$bas_url);
            exit();
        }
        $this->load->model("session_model");
        $this->load->model("receipt_model");

        $order_info = $this->receipt_model->getOrderInfoById($order_id);

        $sesseion = $this->session_model->getByUser($order_info['user_id']);
        $sid = $sesseion['session_id'];
        $partner_trade_no = $order_info['partner_trade_no'];
        $desc = $order_info['description'];
        $amount = $order_info['amount'];
        $wechat_rs = $this->payToUser($openid,$partner_trade_no,$desc,$amount);
        $wechat_rs = $this->xmlToArray($wechat_rs);









    }
    //将XML转为array
    private function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

}
