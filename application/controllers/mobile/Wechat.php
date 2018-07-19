<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
// require_once('/var/www/api/Public/SDK/aliyun_sms/mns-autoloader.php');
require_once "application/third_party/CCPRestSmsSDK.php";
use AliyunMNS\Client;
use AliyunMNS\Topic;
use AliyunMNS\Constants;
use AliyunMNS\Model\MailAttributes;
use AliyunMNS\Model\SmsAttributes;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;
class Wechat extends Base_MobileController
{
    protected $filepath ='';
    protected $wx_base_url = '';
    protected $credits_url = '';
    function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
        $this->load->model('club_model');
        $this->load->model('area_model');
        $this->load->model('session_model');
        $this->load->model('ticket_order_model');
        $this->filepath = get_instance()->config->config['log_path_file'];
        if(isset(get_instance()->config->config['wx_base_url'])){
            $this->wx_base_url = get_instance()->config->config['wx_base_url'];
        }
        else{
            $this->wx_base_url = 'https://wxyan.bjzwhz.cn/';
        }
        $this->credits_url = get_instance()->config->config['credits_url'];
        
    }

    public function test()
    {

        $a[0]['name'] = 'separater';

        $a[1]['name'] = "profile";
        $a[1]['label'] = "用户设置";

        $a[2]['name'] = 'separater';

        $a[3]['name'] = "charge";
        $a[3]['label'] = "烟币";

        $a[4]['name'] = "point_shop";
        $a[4]['label'] = "商城";
        $a[4]['url'] = "";

        $a[5]['name'] = 'separater';

        $a[6]['name'] = "question";
        $a[6]['label'] = "公益票专卖申请";
        $a[6]['url'] = base_url()."resources/wechat/question.html";

        $a[7]['name'] = "shopzc";
        $a[7]['label'] = "零售店加盟";
        $a[7]['url'] = base_url()."resources/wechat/shopzc.html";

        $a[8]['name'] = "player_ranking";
        $a[8]['label'] = "零售店排行";
        $a[8]['url'] = base_url()."resources/wechat/day";

        $a[9]['name'] = 'separater';

        $a[10]['name'] = "nearby_boxing";
        $a[10]['label'] = "关于我们";
        $a[10]['url'] = base_url()."portal/about";

        $a[11]['name'] = 'separater';

        $a[12]['name'] = "setting";
        $a[12]['label'] = "设置";


//
//
//        $a = '[
//    {
//        "name": "separater"
//    },
//    {
//        "name": "profile",
//        "label": "用户设置"
//    },
//    {
//        "name": "separater"
//    },
//    {
//        "name": "charge",
//        "label": "烟币"
//    },
//    {
//        "name": "point_shop",
//        "label": "商城",
//        "url": ""
//    },
//    {
//        "name": "separater"
//    },
//    {
//        "name": "player_ranking",
//        "label": "零售店排行",
//        "url": base_url()."resources/wechat/day"
//    },
//    {
//        "name": "apply_for",
//        "label": "我要报名"
//    },
//    {
//        "name": "challenge",
//        "label": "赛事报名"
//    },
//    {
//        "name": "separater"
//    },
//    {
//        "name": "nearby_boxing",
//        "label": "关于我们",
//        "url": "http://47.92.37.141/portal/about"
//    },
// {
//        "name": "question",
//        "label": "公益票专卖申请",
//        "url": base_url()."resources/wechat/question.html"
//    },
// {
//        "name": "shopzc",
//        "label": "零售店加盟",
//        "url": base_url()."resources/wechat/shopzc.html"
//    },
//    {
//        "name": "separater"
//    },
//    {
//        "name": "setting",
//        "label": "设置"
//    }]';
        echo json_encode($a);
        exit;
//        $sid = $this->get_input('sid');//用户ID
//        $a = $this->session_model->getInfoBySId($sid);
//        var_dump($a);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://news.qq.com");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        //iconv('gb2312','utf-8//TRANSLIT//IGNORE', serialize($storeData));
        //iconv('gb2312','utf-8//TRANSLIT//IGNORE',$output);
        //$output = str_replace('charset=gb2312','charset=utf-8',$output);
        echo $output;
    }


    public function sendsms($telno, $number, $type)
    {
        /**
         * Step 1. 初始化Client
         */
        $this->endPoint = "https://1351069018860975.mns.cn-hangzhou.aliyuncs.com/"; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
        $this->accessId = "pybW3kxGx7PnUrdR";
        $this->accessKey = "adP3C61PmLZ3bRDqMkb2373k6Umvso";
        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
        /**
         * Step 2. 获取主题引用
         */
        $topicName = "sms.topic-cn-hangzhou";
        $topic = $this->client->getTopicRef($topicName);
        /**
         * Step 3. 生成SMS消息属性
         */
        // 3.1 设置发送短信的签名（SMSSignName）和模板（SMSTemplateCode）
        $batchSmsAttributes = new BatchSmsAttributes("意视科技", "SMS_90890039");
        // 3.2 （如果在短信模板中定义了参数）指定短信模板中对应参数的值
        $batchSmsAttributes->addReceiver($telno, array("number" => $number));
        //$batchSmsAttributes->addReceiver("YourReceiverPhoneNumber2", array("YourSMSTemplateParamKey1" => "value1"));
        $messageAttributes = new MessageAttributes(array($batchSmsAttributes));
        /**
         * Step 4. 设置SMS消息体（必须）
         *
         * 注：目前暂时不支持消息内容为空，需要指定消息内容，不为空即可。
         */
        $messageBody = "smsmessage";
        /**
         * Step 5. 发布SMS消息
         */
        $request = new PublishMessageRequest($messageBody, $messageAttributes);

        $ret = array();
        $ret['code'] = 1;
        try
        {
            $res = $topic->publishMessage($request);
            //error_log("SMS: ".$number. " ". $telno." ".json_encode($res). " ". $res->isSucceed());
            if ($res->isSucceed() > 0)
            {
                $ret['code'] = 0;
                $ret['messgeId'] =  $res->getMessageId();
            }
        }
        catch (MnsException $e)
        {

        }
        return $ret;
    }
    //中维短信
    //Demo调用
    //**************************************举例说明***********************************************************************
    //*假设您用测试Demo的APP ID，则需使用默认模板ID 1，发送手机号是13800000000，传入参数为6532和5，则调用方式为           *
    //*result = sendTemplateSMS("13800000000" ,array('6532','5'),"1");                                                                        *
    //*则13800000000手机号收到的短信内容是：【云通讯】您使用的是云通讯短信模板，您的验证码是6532，请于5分钟内正确输入     *
    //*********************************************************************************************************************
    public function zwsendsms($to,$datas,$tempId){
        //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        $accountSid= '8a216da86002167f01600abc950f0401';

        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken= 'b1752de3bede46b0aba241bf5cede326';

        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
         //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId='8a216da86002167f01600abc956a0407';

         //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP='app.cloopen.com';


        //请求端口，生产环境和沙盒环境一致
        $serverPort='8883';

        //REST版本号，在官网文档REST介绍中获得。
        $softVersion='2013-12-26';
        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信

        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        $data_code = [];
        if($result == NULL ) {
            $data_code['code'] = 2;
        }
        if($result->statusCode!=0) {
            $data_code['code'] = $result->statusCode;
            $data_code['statusMsg'] =  $result->statusMsg;
            //TODO 添加错误处理逻辑
        }else{
            $data_code['code'] = 0;
            //TODO 添加成功处理逻辑
        }

        return $data_code;
    }
    public function ajaxSend() {
        $mobile = $this->input->post('mobile');
        $zho = $this->input->post('id');
        // 인증코드생성 및 전화번호에 발송
        $this->load->model("verifyphone_model");

        $test = false;
        if ($test) {
            $authCode = '123456';
            $response = json_decode("{'code':0}");
        } else {
            $authCode = gen_rand_num(6);

          if($zho != ''){
              $response = $this->sendsms($mobile, $authCode, "SMS_62780209");
          }else{
              $response = $this->zwsendsms($mobile, array($authCode,'5'), "221151");
          }


            #$ch = curl_init();                              //initialize curl handle
            #curl_setopt($ch, CURLOPT_URL, $url);            //set the url
            #curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
            #$response = curl_exec($ch);
            #curl_close($ch);                                //close the curl handle
        }
        if($test || $response['code'] == 0) {
            $this->verifyphone_model->add_new_code($mobile, $authCode);
            $data['success'] = true;
            $data['code'] = $authCode;
        } else {
            if(array_key_exists('statusMsg', $response)){
                $data['success'] = false;
                $data['error'] = (string)$response['statusMsg'];
            }
            else{
                $data['success'] = false;
                $data['error'] = "短信发送失败";
            }
            file_put_contents(get_instance()->config->config['log_path_file'].'sendmsgError.log', var_export($response,true),FILE_APPEND|LOCK_EX);
        }
        echo json_capsule($data);
    }

    //零售店主
    // public function shop()
    // {
    //     $sid = $this->get_input('sid');//用户ID
    //     $sessionInfo = $this->session_model->getInfoBySId($sid);
    //     $userId = $sessionInfo['user_id'];

    //     if (empty($sessionInfo)) {
    //         parent::wechatAlert('请登录！');
    //     }
    //     $info = $this->club_model->fetchOne(array('user_id'=>$userId,'question'=>1));
    //     if (!empty($info)) {
    //         if ($info['status'] == 0) {
    //             $url = base_url().'resources/wechat/shopinfo.html?sid=' . $sid;
    //             parent::wechatAlert('店铺审核中，请耐心等待', $url);
    //         }
    //         if($info['status'] == 2){
    //             $url = base_url().'resources/wechat/shopinfo.html?sid=' . $sid;
    //             parent::wechatAlert('店铺等待票证中，请耐心等待', $url);
    //         }
    //         if ($info['status'] == 1) {
    //             // $url = base_url().'resources/wechat/shopinfo.html';
    //             // header("location:" . $url . "?sid=" . $sid);
    //             // exit;
    //             $url = base_url().'resources/wechat/lottery.html';
    //             header("location:" . $url . "?sid=" . $sid);
    //             die;
    //             //parent::wechatAlert('审核通过！');
    //         }
    //     }
    //     else{
    //         $flag = $this->club_model->deleteData(array('user_id'=>$userId));
    //     }
    //     $url = base_url().'resources/wechat/shopzc.html';
    //     header("location:" . $url . "?sid=" . $sid);
    // }
    public function shop()
    {
        $sid = $this->get_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];

        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $info = $this->club_model->fetchOne(array('user_id'=>$userId));
        if(empty($info)){
            $url = base_url().'resources/wechat/vc-shop-img-zc.html';
            header("location:" . $url . "?sid=" . $sid);
            return;
        }
        else{
            if($info['refuse'] == 1){
                $url = base_url().'resources/wechat/vc-shop-img-zc.html';
                header("location:" . $url . "?sid=" . $sid."&refuse=1");
                return;
            }
            elseif($info['status'] == 0 && $info['question'] == 1){
                $url = base_url().'resources/wechat/shopinfo.html?sid=' . $sid;
                parent::wechatAlert('店铺审核中，请耐心等待', $url);
            }
            elseif($info['status'] == 1){
                $url = base_url().'resources/wechat/lottery.html';
                header("location:" . $url . "?sid=" . $sid);
                return;
            }
            elseif($info['status'] == 2){
                $url = base_url().'resources/wechat/shopinfo.html?sid=' . $sid;
                parent::wechatAlert('您的店铺申请已经通过客户经理审核，正在办理代销证，请耐心等待', $url);
            }
            elseif($info['question'] == 0) {
                $url = base_url().'resources/wechat/vc-shop-img-zc.html';
                header("location:" . $url . "?sid=" . $sid);
                return;
            }
        }
    }
    //经理注册
    public function manager()
    {
        $sid = $this->get_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('manager_model');
        $info = $this->manager_model->fetchOne(array('consumer_userid'=>$userId));
        if(!empty($info)){
            if ($info['refuse'] == 1) {
                $url = base_url().'resources/wechat/managerzc.html';
                header("location:" . $url . "?sid=" . $sid.'&refuse=1');
            }
            elseif($info['status'] == 0){
                $url = base_url().'resources/wechat/managerinfo.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
            }
            elseif($info['status'] == 1){
                $url = base_url().'resources/wechat/manager-point.html?sid=' . $sid;
                header("location:" . $url);
            }
        }
        else{
            $url = base_url().'resources/wechat/managerzc.html';
            header("location:" . $url . "?sid=" . $sid);
        }
    }
    public function getManagerInfo(){
        $this->load->model('area_model');

        $sid = $this->post_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('manager_model');
        $info = $this->manager_model->fetchOne(array('consumer_userid'=>$userId));
        if(empty($info)){
            parent::output(7);
        }
        $info['manager_name'] = $info['name'];
        $area_id = $info['area_id'];
        if($area_id){
            $province = $this->area_model->fetchAll(array('id'=>$area_id));
            $info['sheng'] = $province[0]['name'];
        }
        parent::output($info);
    }
    // public function getCreditsByManager(){
    //     $sid = $this->post_input('sid');//用户ID
    //     if(empty($sid)){
    //         $sid = $this->get_input('sid');//用户ID
    //     }
    //     $rs = [];
    //     $sessionInfo = $this->session_model->getInfoBySId($sid);
    //     $userId = $sessionInfo['user_id'];
    //     if (empty($sessionInfo)) {
    //         parent::wechatAlert('请登录！');
    //     }
    //     $this->load->model('manager_model');
    //     $info = $this->manager_model->getCreditsByManager($userId);
    //     $total = 0;
    //     $lists = [];
    //     $dayTotal = 0;
    //     $start = date('Y-m-d 00:00:00');
    //     $end = date('Y-m-d 23:59:59');
    //     foreach ($info as $key => $value) {
    //         if($value['manager_credits'] == 0){
    //             continue;
    //         }
    //         $total += $value['manager_credits'];
    //         $lists[$key]['id'] = $value['id'];
    //         $lists[$key]['create_time'] = $value['update_date'];
    //         $lists[$key]['user_id'] = $value['user_id'];
    //         $lists[$key]['name'] = $value['name'];
    //         $lists[$key]['manager_name'] = $value['manager_name'];
    //         $lists[$key]['manager_credits'] = $value['manager_credits'];
    //         $lists[$key]['address'] = $value['city'].$value['address'];
    //         if($value['update_date']>= $start && $value['update_date'] <= $end){
    //             $dayTotal += $value['manager_credits'];
    //         }
    //     }
    //     $rs['total'] = $total;
    //     $rs['dayTotal'] = $dayTotal;
    //     $rs['lists'] = $lists;
    //     parent::output($rs);
    // }
    // public function getCreditsByManager(){
    //     $sid = $this->post_input('sid');//用户ID
    //     if(empty($sid)){
    //         $sid = $this->get_input('sid');//用户ID
    //     }
    //     $rs = [];
    //     $sessionInfo = $this->session_model->getInfoBySId($sid);
    //     $userId = $sessionInfo['user_id'];
    //     if (empty($sessionInfo)) {
    //         parent::wechatAlert('请登录！');
    //     }
    //     $this->load->model('manager_model');
    //     $manager_id = $this->manager_model->fetchOne(array('consumer_userid'=>$userId))['manager_id'];
    //     if(empty($manager_id)){
    //         parent::wechatAlert('缺少信息');
    //     }
    //     $info = $this->manager_model->getCreditsByManager($manager_id);
    //     $total = 0;
    //     $lists = [];
    //     $dayTotal = 0;
    //     $start = date('Y-m-d 00:00:00');
    //     $end = date('Y-m-d 23:59:59');
    //     foreach ($info as $key => $value) {
    //         if($value['manager_credits'] == 0){
    //             continue;
    //         }
    //         $total += $value['manager_credits'];
    //         $lists[$key]['id'] = $value['id'];
    //         $lists[$key]['create_time'] = $value['update_date'];
    //         $lists[$key]['user_id'] = $value['user_id'];
    //         $lists[$key]['name'] = $value['name'];
    //         $lists[$key]['manager_name'] = $value['manager_name'];
    //         $lists[$key]['manager_credits'] = $value['manager_credits'];
    //         $lists[$key]['address'] = $value['city'].$value['address'];
    //         if($value['update_date']>= $start && $value['update_date'] <= $end){
    //             $dayTotal += $value['manager_credits'];
    //         }
    //     }
    //     $time = time();
    //     $appkey = "h#jGD&kihy787867";
    //     $sign = md5($appkey.$time);
    //     $url  = "http://test.mall.eeseetech.cn/api/creditapi/gettodayconsume?timetmp={$time}&sign={$sign}&userid={$userId}";
    //     $ch = curl_init();                              //initialize curl handle
    //     curl_setopt($ch, CURLOPT_URL, $url);            //set the url
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
    //     $response = curl_exec($ch);
    //     curl_close($ch);
    //     $callinfo = json_decode($response, true);
    //     if(empty($callinfo)){
    //         $expend = 0;
    //     }else{
    //        $expend = $callinfo[0]['s_credits'];
    //     }
    //     $rs['expend'] =  $expend ;
    //     $rs['total'] = $total;
    //     $rs['dayTotal'] = $dayTotal;

    //     parent::output($rs);
    // }
    public function getCreditsByManager(){
        $sid = $this->post_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->get_input('sid');//用户ID
        }
        $rs = [];
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }

        $this->load->model('user_credits_model');
        //获取今日积分
        $user_id = $userId;
        $start = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $end = mktime(23,59,59,date("m"),date("d"),date("Y"));
        $today_credits = $this->user_credits_model->get_today_credits($user_id,$start,$end);
        $creditsToday = $today_credits;
        //获取用户总积分
        $info = $this->user_model->getInfoById($userId);
        $total = $info['point'];
        //获取今日消费积分
        $time = time();
        $appkey = "h#jGD&kihy787867";
        $sign = md5($appkey.$time);
        $url  = $this->credits_url."api/creditapi/gettodayconsume?timetmp={$time}&sign={$sign}&userid={$userId}";
        $ch = curl_init();                              //initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $url);            //set the url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
        $response = curl_exec($ch);
        curl_close($ch);
        $callinfo = json_decode($response, true);
        if(empty($callinfo)){
            $expend = 0;
        }else{
           $expend = $callinfo[0]['s_credits'];
        }
        if(empty($expend)){
            $expend = 0;
        }
        $rs['expend'] =  $expend ;
        $rs['total'] = (int)$total;
        $rs['dayTotal'] = (int)$creditsToday;

        parent::output($rs);
    }
    public function getShopByManager(){
        $rs = [];
        $sid = $this->post_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->get_input('sid');//用户ID
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('manager_model');
        $info = $this->manager_model->getShopByManager($userId);
        foreach ($info as $key => $value) {
            $info[$key]['address'] = $value['city'].$value['address'];
        }
        $rs = $info;
        parent::output($rs);
    }
    //市场经理注册
    public function area_manager()
    {
        $sid = $this->get_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->post_input('sid');//用户ID
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('Area_manager_model');
        $info = $this->Area_manager_model->fetchOne(array('user_id'=>$userId));
        if(!empty($info)){
            if ($info['status'] == 0) {
                $url = base_url().'resources/wechat/market-manager-info.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
            }
            elseif($info['status'] == 1){
                $url = base_url().'resources/wechat/market-manager-point.html?sid=' . $sid;
                header("location:" . $url);
            }
        }
        else{
            $url = base_url().'resources/wechat/market-manager-zc.html';
            header("location:" . $url . "?sid=" . $sid);
        }
    }

    //区域经理注册
    public function bazaar_manager()
    {
        $sid = $this->get_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->post_input('sid');//用户ID
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('Bazaar_manager_model');
        $info = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$userId));
        if(!empty($info)){
            if ($info['status'] == 0) {
                $url = base_url().'resources/wechat/area-manager-info.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
            }
            elseif($info['status'] == 1){
                $url = base_url().'resources/wechat/area-manager-point.html?sid=' . $sid;
                header("location:" . $url);
            }
        }
        else{
            $url = base_url().'resources/wechat/area-manager-zc.html';
            header("location:" . $url . "?sid=" . $sid);
        }
    }
    //添加市场经理
    public function addarea_Manager()
    {
        $this->load->model("verifyphone_model");
        $this->load->model("Area_manager_model");
        $this->load->model("session_model");

        $sid = $this->post_input('sid');//用户ID
        $name = $this->post_input('area_manager_name');//区域经理姓名
        $id_number = $this->post_input('id_number');//身份证号码
        $phone = $this->post_input('phone');//手机号
        $bazaar_phone = $this->post_input('bazaar_phone');//手机号1
        $bazaar_name = $this->post_input('bazaar_name');
        $code = $this->post_input('code');//验证码
        $area_id = $this->post_input('area_id');
        $city = $this->post_input('city');
        $address = $this->post_input('address');
        $area_code = $this->post_input('area_code');//区域编码
        if (empty($sid) || empty($name) || empty($id_number) || empty($phone) || empty($bazaar_phone)|| empty($bazaar_name) || empty($area_id) || empty($city) || empty($address) || empty($area_code)) {
            parent::output(100);
        }
        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            parent::output(701);
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            parent::output(801);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::output(102);
        }
        $user_id = $sessionInfo['user_id'];
        $recordId = $this->wechatBindApp($user_id,$phone);
        if(gettype($recordId) == 'string'){
            $info = $this->Area_manager_model->fetchOne(array('user_id'=>$recordId));
            if(!empty($info)){
                parent::output(1003);
            }
            $user_id = $recordId;
            //更新用户sid
            $updateData = [];
            $updateData['user_id'] = $user_id;
            $this->session_model->updateBySId($sid,$updateData);
        }
        //验证手机号码身份证是否被使用
        $info = $this->Area_manager_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info)){
            parent::output(19);
        }
        $info = $this->Area_manager_model->fetchOne(array('phone'=>$phone));
        if(!empty($info)){
            parent::output(3);
        }
        $info = $this->Area_manager_model->fetchOne(array('id_number'=>$id_number));
        if(!empty($info)){
            parent::output(13);
        }

        $data['type'] = 'area_manager';
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['bazaar_phone'] = $bazaar_phone;
        $data['bazaar_name'] = $bazaar_name;
        $data['id_number'] = $id_number;
        $data['area_manager_id'] = $phone;
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $user_id;
        $data['area_id'] = $area_id;
        $data['city'] = $city;
        $data['address'] = $address;
        $data['area_code'] = $area_code;
        
        if (!$this->Area_manager_model->insertData($data)) {
            parent::output(99);
        }

        $out = array();
        parent::output($out);
    }
    //添加区域经理
    public function add_bazaar_Manager()
    {
        $this->load->model("verifyphone_model");
        $this->load->model("manager_model");
        $this->load->model("session_model");

        $sid = $this->post_input('sid');//用户ID
        $name = $this->post_input('area_manager_name');//区域经理姓名
        $id_number = $this->post_input('id_number');//身份证号码
        $phone = $this->post_input('phone');//手机号
        $code = $this->post_input('code');//验证码
        $area_id = $this->post_input('area_id');
        $city = $this->post_input('city');
        $address = $this->post_input('address');
        $area_code = $this->post_input('area_code');//区域编码
        if (empty($sid) || empty($name) || empty($id_number) || empty($phone) || empty($code) || empty($area_id) || empty($city) || empty($address) || empty($area_code) ) {
            parent::output(100);
        }

        $this->load->model("verifyphone_model");
        $this->load->model("Bazaar_manager_model");

        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            parent::output(701);
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            parent::output(801);
        }

        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::output(102);
        }
        $user_id = $sessionInfo['user_id'];
        $recordId = $this->wechatBindApp($user_id,$phone);
        if(gettype($recordId) == 'string'){
            $info = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$recordId));
            if(!empty($info)){
                parent::output(1003);
            }
            $user_id = $recordId;
            //更新用户sid
            $updateData = [];
            $updateData['user_id'] = $user_id;
            $this->session_model->updateBySId($sid,$updateData);
        }
        //验证手机号码身份证是否被使用
        $info = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info)){
            parent::output(19);
        }
        $info = $this->Bazaar_manager_model->fetchOne(array('phone'=>$phone));
        if(!empty($info)){
            parent::output(3);
        }
        $info = $this->Bazaar_manager_model->fetchOne(array('id_number'=>$id_number));
        if(!empty($info)){
            parent::output(13);
        }
        $sql = "select * from tbl_session where session_id='{$sid}' order by create_date desc";
        $info = $this->Bazaar_manager_model->queryAll($sql);
        $user_id = $info[0]['user_id'];
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['id_number'] = $id_number;
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $user_id;
        $data['area_id'] = $area_id;
        $data['city'] = $city;
        $data['address'] = $address;
        $data['area_code'] = $area_code;
        if (!$this->Bazaar_manager_model->insertData($data)) {
            parent::output(99);
        }
        $out = array();
        parent::output($out);
    }
     public function getAreaManagerInfo(){
        $this->load->model('area_model'); 
        $sid = $this->post_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->get_input('sid');//用户ID
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('Area_manager_model');
        $info = $this->Area_manager_model->fetchOne(array('user_id'=>$userId));
        if(empty($info)){
            parent::output(7);
        }
        $info['area_manager_name'] = $info['name'];
        $area_id = $info['area_id'];
        if($area_id){
            $province = $this->area_model->fetchAll(array('id'=>$area_id));
            $info['sheng'] = $province[0]['name'];
        }
        parent::output($info);
    }

    public function getBazaarManagerInfo(){
        $this->load->model('area_model');
        $sid = $this->post_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->get_input('sid');//用户ID
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $this->load->model('Bazaar_manager_model');
        $info = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$userId));
        if(empty($info)){
            parent::output(7);
        }
        $info['area_manager_name'] = $info['name'];
        $area_id = $info['area_id'];
        if($area_id){
            $province = $this->area_model->fetchAll(array('id'=>$area_id));
            $info['sheng'] = $province[0]['name'];
        }
        parent::output($info);
    }
    //店铺ID
    public function shopid()
    {
        $sid = $this->get_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $userId = $sessionInfo['user_id'];
        $info = $this->club_model->getInfoByUserId($userId);
        if (empty($info)) {
            $url = base_url().'resources/wechat/shopzc.html?sid=' . $sid;
           // header("location:" . $url . "?sid=" . $sid);
           // exit;
            parent::wechatAlert('请注册零售店！', $url);
        }
        $url = base_url().'resources/wechat/shopinfo.html';
        header("location:" . $url . "?sid=" . $sid);
    }

    //公益票申请
    public function welfare()
    {
        $sid = $this->get_input('sid');//用户ID
        if (empty($sid)) {
            parent::wechatAlert('请登录！');
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $userId = $sessionInfo['user_id'];
        $url = base_url().'resources/wechat/lottery.html';
        header("location:" . $url . "?sid=" . $sid);
    }

    //销售排行
    public function salesRanking()
    {
        $sid = $this->get_input('sid');//用户ID
        if (empty($sid)) {
            parent::wechatAlert('请登录！');
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }
        $userId = $sessionInfo['user_id'];
        $url = base_url().'resources/wechat/rank-list.html';
        // header("location:" . $url);
        header("location:" . $url . "?sid=" . $sid);
    }


    //用户注册
    public function reg()
    {
        $sid = $this->get_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);

        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }

        $userId = $sessionInfo['user_id'];
        $info = $this->user_model->getInfoById($userId);
        if (!empty($info)) {
            if ($info['id_number'] && $info['username'] && $info['real_name']) {

                $url = base_url().'resources/wechat/peoinfo.html';
                header("location:" . $url . "?sid=" . $sid);
                exit;
                //parent::wechatAlert('已注册跳转用户详情页！');
            }
        }
        $url = base_url().'resources/wechat/peozc.html';
        header("location:" . $url . "?sid=" . $sid);
    }


    //我的ID
    public function myid()
    {
        $sid = $this->get_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);

        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }

        $userId = $sessionInfo['user_id'];
        $userInfo = $this->user_model->getInfoById($userId);
        if (empty($userInfo)) {
            parent::wechatAlert('还未注册！');
        } else {
            if (!$userInfo['id_number'] || !$userInfo['username'] || !$userInfo['real_name']) {
                $url = base_url().'resources/wechat/peozc.html?sid=' . $sid;
                //header("location:" . $url );
                parent::wechatAlert('请注册！', $url);
                exit;
            }
        }
        $url = base_url().'resources/wechat/peoinfo.html';
        header("location:" . $url . "?sid=" . $sid);
    }


    //我的积分
    public function mypoints()
    {
        echo '我的积分';
        exit;
    }

    //行业新闻
    public function news()
    {
//        $sid = $this->get_input('sid');//用户ID
//        $url=base_url().'resources/wechat/shopzc.html';
//        header("location:" . $url . "?sid=" . $sid);
//        $url = ' https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzI4Mzk0NDEzNg==&scene=124#wechat_redirect';
        $url = base_url().'/resources/wechat/news/information.html';
        header("location:" . $url);
    }


    public function caipiao()
    {
//        $sid = $this->get_input('sid');//用户ID
//        $url=base_url().'resources/wechat/shopzc.html';
//        header("location:" . $url . "?sid=" . $sid);
        $url = base_url().'/resources/wechat/news/activity.html';
        header("location:" . $url);
    }


    //建设中
    public function loading()
    {
        parent::wechatAlert('页面建设中……');
    }

    //关注事件
    public function subscribe()
    {
        $openId = $this->post_input('openId');//用户ID
        $nickname = $this->post_input('nickname');
        $headimgurl = $this->post_input('headimgurl');
        $unionid = $this->post_input('unionId');
        if (empty($openId) || empty($nickname)) { // || empty($headimgurl)) {
            $data['code'] = 500;
            $data['message'] = "错误:用户ID或者昵称为空!";
            echo json_encode($data);
            exit;
        }
        $userInfo = $this->user_model->fetchAll(array('weixin'=>$openId));
        $unionid_userInfo = $this->user_model->fetchAll(array('unionid'=>$unionid));
        if(empty($unionid_userInfo)){
            if(empty($userInfo)){
                //新增用户
                if (!$this->user_model->wechatInsert($openId, $nickname, $headimgurl,$unionid)) {
                    $res['code'] = 200;
                    $res['message'] = "数据插入错误！";
                    echo json_encode($res);
                    exit;
                }
                $res['code'] = 200;
                $res['message'] = "欢迎关注北京中维微信公众号\n<a href='".($this->wx_base_url)."/api2/wechat/oauth?token=WAf2NO2Wrdc=&mid=1001'>零售店注册点这里</a>";
                echo json_encode($res);
                exit;
            }
            else{
                //更新用户unionid
                $userInfo = $userInfo[0];
                $data['unionid'] = $unionid;
                if (!$this->user_model->update($userInfo['id'], $data)) {
                    $res['code'] = 200;
                    $res['message'] = "数据更新错误！";
                    echo json_encode($res);
                    exit;
                }
            }
        }
        else{
            if(empty($userInfo)){
                $unionid_userInfo = $unionid_userInfo[0];
                $data['weixin'] = $openId;
                $data['username'] = $openId;
                if (!$this->user_model->update($unionid_userInfo['id'], $data)) {
                    $res['code'] = 200;
                    $res['message'] = "数据更新错误！";
                    echo json_encode($res);
                    exit;
                }
                else{
                    $res['code'] = 200;
                    $res['message'] = "欢迎关注北京中维微信公众号\n<a href='".($this->wx_base_url)."/api2/wechat/oauth?token=WAf2NO2Wrdc=&mid=1001'>零售店注册点这里</a>";
                    echo json_encode($res);
                    exit;
                }
            }
        }
        $userInfo = $userInfo[0];
        $data['nickname'] = $nickname;
        $data['avatar_url'] = $headimgurl;
        $data['login_date'] = date("Y-m-d H:i:s");
        if (!$this->user_model->update($userInfo['id'], $data)) {
            $res['code'] = 200;
            $res['message'] = "数据更新错误！";
            echo json_encode($res);
            exit;
        }
        $res['code'] = 200;
        $res['message'] = "欢迎关注北京中维微信公众号\n<a href='".($this->wx_base_url)."/api2/wechat/oauth?token=WAf2NO2Wrdc=&mid=1001'>零售店注册点这里</a>";
        echo json_encode($res);
        exit;
    }


    //oauth,授权跳转
    public function oauth()
    {

        $data = $this->decrypt($_REQUEST['data'], '2020star');
        $data = preg_replace('/[^[:print:]]/', '', $data);
        $url = urldecode($_REQUEST['url']);
        $openId = $data;
        // $openId = 'oGpqZ0ZwYIWohX2mcBOcMGQWum-0';

        $userInfo = $this->user_model->getInfoByWeixin($openId);
        if(empty($userInfo)){
            $wexinInfo = $this->getUserInfo($openId);
            $wexinInfo = json_decode($wexinInfo,true);
            $unionid = $wexinInfo['unionid'];
            $userInfo = $this->user_model->fetchAll(array('unionid'=>$unionid));
            if(empty($userInfo)){
                exit('用户不存在');
            }
            $userInfo = $userInfo[0];
        }

        $sessionInfo = $this->session_model->getByUser($userInfo['id']);

        if (empty($sessionInfo)) {
            $id = $this->session_model->insert($userInfo['id']);
            if (empty($id)) {
                parent::output(12);
            }
            $sessionInfo = $this->session_model->get($id);
        } else {
            $et = time() + 30 * 60;
            $updata['expire_date'] = date("Y-m-d H:i:s", $et);
            $this->session_model->updateBySId($sessionInfo['session_id'], $updata);
        }


//        $out = array(
//            'session' => array(
//                'sid' => $session['session_id'],
//                'uid' => $userInfo['id']
//            ),
//            'user' => $userInfo
//        );
//        parent::output($out);

        header("location:" . $url . "?sid=" . $sessionInfo['session_id']);
    }
    //取微信用户unionid
    public function getUserInfo($openId = ''){
        $this->load->model('Token_model');
        $token = $this->Token_model->getAccessToken();
        $openid = 'oGpqZ0ZwYIWohX2mcBOcMGQWum-0';
        //获取用户详情
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}";
        $res = $this->Token_model->post($url);
        return $res;
    }
   //跳转积分商城
    public function credits_shop(){
        $sid = $this->get_input('sid');//用户ID
        $sessionInfo = $this->session_model->getInfoBySId($sid);

        if (empty($sessionInfo)) {
            parent::wechatAlert('请登录！');
        }

        $userId = $sessionInfo['user_id'];
        $userInfo = $this->user_model->getInfoById($userId);
        if (empty($userInfo)) {
            parent::wechatAlert('还未注册！');
        } else {

            $club_info = $this->club_model->fetchOne(array('user_id'=>$userId));

            if(isset($club_info['status']) && $club_info['status'] >= 0){
                $sid = $this->encrypt($sid, '2026star');
                $url = $this->credits_url.'mobile/pointmall?appId=56&sid=' . $sid;
                header("location:" . $url );
                exit;
            }

            parent::wechatAlert('非零售户不能进入商城！');

        }
    }
    //积分商城来取积分信息
    public function get_user_info(){
        $data = $this->decrypt($_REQUEST['sid'], '2026star');
        $sid = preg_replace('/[^[:print:]]/', '', $data);
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(10);
        }
        $userId = $sessionInfo['user_id'];

        $userInfo = $this->user_model->getInfoById($userId);

        if (empty($userInfo)) {
            parent::output(10);
        }
        parent::output($userInfo);

    }
    //积分商城call_deduct_credits
    public function call_deduct_credits(){

        $userid = $this->get_input('userid');
        $credits = $this->get_input('credits');
        $sign = $this->get_input('sign');
        $timetmp = $this->get_input('timetmp');
        if($timetmp == ''){
            parent::output(905);
        }
        if($userid == ''){
            parent::output(10);
        }
        if($credits == ''){
            parent::output(906);
        }

        $appkey = "h#jGD&kihy787867";
        $signs = MD5($appkey.$timetmp);
        if($sign != $signs){
            parent::output(903);
        }
        $userInfo = $this->user_model->getInfoById($userid);
        if (empty($userInfo)) {
            parent::output(10);
        }
        $user_re = $this->user_model->updateDeductCredits($userid,$credits);
        $fp =  $this->filepath."deduct_credits_".date("Y-m-d",time()).".log";
        /* $fp =  "D:/add_credits_".date("Y-m-d",time()).".log";*/
        if($user_re){
            $contents = '{"club_userid:"'.$userid.',"credits:-"'.$credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;

            file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
        }else{
            $contents = '{"club_userid:"'.$userid.',"credits:"'.$credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;

            file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
        }
        $re = [];
        parent::output($re);

    }
    public function wechat_mypoints()
    {
        $openId = $this->post_input('openId');//用户ID

        if (empty($openId)) {
            $data['code'] = 500;
            $data['message'] = "参数错误！";
            echo json_encode($data);
            exit;
        }
        $userInfo = $this->user_model->getInfoByWeixin($openId);
        if (empty($userInfo)) {
            $data['code'] = 500;
            $data['message'] = "用户不存在！";
            echo json_encode($data);
            exit;
        }
        $data['code'] = 200;
        $data['message'] = "您的积分：" . $userInfo['point'] . "\n<a href='".($this->wx_base_url)."/api2/wechat/oauth?token=WAf2NO2Wrdc=&mid=2004'>立即兑换</a>";
        echo json_encode($data);
        exit;
    }

    public function wechat_keywords()
    {
        $data['code'] = 200;
        $data['message'] = "欢迎关注北京中维微信公众号\n<a href='".($this->wx_base_url)."/api2/wechat/oauth?token=WAf2NO2Wrdc=&mid=1001'>零售店注册点这里</a>";
        echo json_encode($data);
        exit;
    }


    //api接口
    public function updateUser()
    {
        $sid = $this->post_input('sid');//用户ID
        $idNumber = $this->post_input('idNumber');//身份证号
        $username = $this->post_input('phone');//手机号
        $realName = $this->post_input('realName');//手机号
        $code = $this->post_input('code');//验证码

        if (empty($sid) || empty($idNumber) || empty($username) || empty($code) || empty($realName)) {
            parent::output(100);
        }

        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(102);
        }

        $userId = $sessionInfo['user_id'];

        $userInfo = $this->user_model->getInfoByName($username);
        if (!empty($userInfo)) {
            parent::output(3);
        }

        $userInfo = $this->user_model->getInfoByNumber($idNumber);

        if (!empty($userInfo)) {
            parent::output(13);
        }

        $data['id_number'] = $idNumber;
        $data['username'] = $username;
        $data['real_name'] = $realName;
        if (!$this->user_model->update($userId, $data)) {
            parent::output(99);
        }

        $out = array();
        parent::output($out);
    }
    //添加客户经理
    public function addManager()
    {
        $this->load->model("verifyphone_model");
        $this->load->model("manager_model");
        $this->load->model("session_model");

        $sid = $this->post_input('sid');//用户ID
        $manager_name = $this->post_input('manager_name');//客户经理姓名
        $id_number = $this->post_input('id_number');//身份证号码
        $area_managername = $this->post_input('area_managername');//区域经理姓名
        $area_managerid = $this->post_input('area_managerid');//区域经理身份证号码
        empty($area_managername) ? $area_managername = '暂时去掉' : '';
        empty($area_managerid) ? $area_managerid = '暂时去掉' : '';
        $phone = $this->post_input('phone');//手机号
        $code = $this->post_input('code');//验证码
        $area_id = $this->post_input('area_id');
        $city = $this->post_input('city');
        $address = $this->post_input('address');
        $area_code = $this->post_input('area_code');//区域编码


        if (empty($sid) || empty($manager_name) || empty($id_number) || empty($phone) || empty($area_managername) || empty($area_managerid) || empty($area_id) || empty($city) || empty($address) || empty($area_code) ) {
            parent::output(100);
        }
        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            parent::output(701);
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            parent::output(801);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::output(102);
        }
        $user_id = $sessionInfo['user_id'];
        $recordId = $this->wechatBindApp($user_id,$phone);
        if(gettype($recordId) == 'string'){
            $info = $this->manager_model->fetchOne(array('consumer_userid'=>$recordId));
            if(!empty($info)){
                parent::output(1003);
            }
            $user_id = $recordId;
            //更新用户sid
            $updateData = [];
            $updateData['user_id'] = $user_id;
            $this->session_model->updateBySId($sid,$updateData);
        }
        //验证手机号码身份证是否被使用
        $info = $this->manager_model->fetchOne(array('consumer_userid'=>$user_id));
        if(!empty($info)){
            parent::output(19);
        }
        $info = $this->manager_model->fetchOne(array('phone'=>$phone));
        if(!empty($info)){
            parent::output(3);
        }
        $info = $this->manager_model->fetchOne(array('id_number'=>$id_number));
        if(!empty($info)){
            parent::output(13);
        }
        $sql = "select * from tbl_session where session_id='{$sid}' order by create_date desc";
        $info = $this->manager_model->queryAll($sql);

        $user_id = $info[0]['user_id'];
        $data['type'] = 'manager';
        $data['name'] = $manager_name;
        $data['phone'] = $phone;
        $data['id_number'] = $id_number;
        $data['manager_id'] = $phone;
        $data['sid'] = $sid;
        $data['area_managername'] = $area_managername;
        $data['area_managerid'] = $area_managerid;
        $data['area_id'] = $area_id;
        $data['city'] = $city;
        $data['address'] = $address;
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['consumer_userid'] = $user_id;
        $data['area_code'] = $area_code;
        $this->load->model("consumer_model");
        $info = $this->consumer_model->getInfoByPhone($data['phone']);
        if(!empty($info)){
            parent::output(3);
            die;
        }
        if (!$this->consumer_model->insert($data)) {
            parent::output(99);
        }

        $out = array();
        parent::output($out);
    }
    //添加消费者
    public function addUser()
    {
        $sid = $this->post_input('sid');//用户ID
        $name = $this->post_input('realName');//客户经理姓名
        $id_number = $this->post_input('idNumber');//身份证号码
        $phone = $this->post_input('phone');//手机号
        $code = $this->post_input('code');//验证码
        if (empty($sid) || empty($name) || empty($id_number) || empty($phone) || empty($code)) {
            parent::output(100);
        }
        $this->load->model("verifyphone_model");

        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            parent::output(701);
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            parent::output(801);
        }
        $this->load->model("consumer2_model");
        $sql = "select * from tbl_session where session_id='{$sid}' order by create_date desc";
        $info = $this->consumer2_model->queryAll($sql);
        $user_id = $info[0]['user_id'];

        $data['user_id'] = $user_id;
        $data['type'] = 'consumer';
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['id_number'] = $id_number;
        $data['sid'] = $sid;
        $data['create_date'] = date('Y-m-d H:i:s');
        
        $info = $this->consumer2_model->getInfoByPhone($data['phone']);
        if(!empty($info)){
            parent::output(3);
            die;
        }
        if (!$this->consumer2_model->insert($data)) {
            parent::output(99);
        }

        $out = array();
        parent::output($out);
    }

    public function updateShop()
    {
        $this->load->model('Common_model');

        $sid = $this->post_input('sid');//用户ID
        $name = $this->post_input('name');//店主姓名
        $yan_code = $this->post_input('yan_code');//烟草编号
        $id_number = $this->post_input('id_number');//身份证号
        $area_id = $this->post_input('area_id');//省份
        $city = $this->post_input('city');//城市
        $address = $this->post_input('address');//地址
        $phone = $this->post_input('phone');//手机号
        $code = $this->post_input('code');//验证码
        $manager_name = $this->post_input('manager_name');//客户经理
        $manager_id = $this->post_input('manager_id_number');//客户经理身份证证号
        $view_name = $this->post_input('view_name');//小店名称
        $area_code = $this->post_input('area_code');//区域编码
        if (empty($sid) || empty($name) || empty($yan_code) || empty($id_number) || empty($area_id) || empty($city) || empty($address) || empty($phone) || empty($code) || empty($manager_name) || empty($manager_id) || empty($area_code) ) {
            parent::output(100);
        }
        if (empty($phone) || empty($code)) {                // 输入参数错误
            $data['success'] = false;
            $data['message'] = '输入参数错误！';
            echo json_capsule($data);
            return;
        }
        $this->load->model("verifyphone_model");

        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            parent::output(701);
        }

        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            parent::output(801);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(102); 
        }
        $userId = $sessionInfo['user_id'];
        $recordId = $this->wechatBindApp($userId,$phone);
        if(gettype($recordId) == 'string'){
            $clubinfo = $this->club_model->fetchOne(array('user_id'=>$recordId));
            if(!empty($clubinfo)){
                parent::output(1003);
            }
            $userId = $recordId;
            //更新用户sid
            $updateData = [];
            $updateData['user_id'] = $userId;
            $this->session_model->updateBySId($sid,$updateData);
        }
        $clubinfo = $this->club_model->getInfoByUserId($userId);
       
        if (!empty($clubinfo) && $clubinfo['question'] == 1) {
            parent::output(15);
        }
        $info = $this->club_model->getInfoByYancode($yan_code);
        if (!empty($info) && $info['question'] == 1) {
            parent::output(14);
        }
        $info = $this->club_model->getInfoByNumber($id_number);
        if (!empty($info) && $info['question'] == 1) {
            parent::output(13);
        }
        $info = $this->club_model->getInfoByphone($phone);
        if (!empty($info) && $info['question'] == 1) {
            parent::output(3);
        }
        $data['user_id'] = $userId;
        $data['id_number'] = $id_number;
        $data['area_id'] = $area_id;
        $data['city'] = $city;
        $data['address'] = $address;
        $data['phone'] = $phone;
        $data['manager_name'] = $manager_name;
        $data['manager_id'] = $manager_id;
        $data['name'] = $name;
        $data['view_name'] = $view_name;
        $data['yan_code'] = $yan_code;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['area_code'] = $area_code;
        $data['question'] = 0;
        //获取身份证照片
        $this->Common_model->setTable('tbl_image');
        $images = $this->Common_model->fetchAll(array('user_id'=>$userId));
        if(!empty($images)){
            foreach ($images as $key => $value) {
                if($value['type'] == 'id_number'){
                    $data['id_number_image'] = $value['data'];
                }
            }
        }
        if(empty($clubinfo)){
            if (!$this->club_model->wxinsert($data)) {
                parent::output(99);
            }
        }
        else{
            $where = array('user_id'=>$userId);
            $this->club_model->updateData($data,$where);
        }
        $out = array();
        parent::output($out);
    }

    public function api_audit()
    {
        $this->load->model('auditconfig_model');
        $out = $this->auditconfig_model->getConfig(1);
        foreach ($out as $n => $v) {
            $out[$n]['values'] = explode('|', $v['values']);
        }
        parent::output($out);
    }

    //烟草问卷
    public function api_auditSave()
    {

        $sid = $this->post_input('sid');//用户ID
        if (empty($sid)) {
            //parent::output(100);
            parent::wechatAlert('参数错误！');
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            //parent::output(102);
            parent::wechatAlert('请登录！');
        }
        $userId = $sessionInfo['user_id'];

        $this->load->model('audit_model');
        $this->load->model('auditconfig_model');
        $auditInfo = $this->audit_model->getByUserid($userId);
        if (!empty($auditInfo)) {
            //已经存在申请
            //parent::output(17);
            // parent::wechatAlert('已申请！');
            //删除之前添加的问卷，重新添加
            $flag = $this->audit_model->deleteData(array('user_id'=>$userId));
        }

        $answer = $this->post_input('answer');
        if (empty($answer) || !is_array($answer)) {
            //parent::output(100);
            parent::wechatAlert('参数错误！');
        }

        $data['kind'] = 1;
        $data['status'] = 0;
        $data['is_marked'] = 0;
        $data['user_id'] = $userId;
        $num = 1;
        foreach ($answer as $n => $v) {
            $config = $this->auditconfig_model->getById($n);
            $data['attribute' . ($num)] = $v . "|" . $config['attr_label'];
            $num++;
        }
        $this->audit_model->wechat_insert($data);
        //parent::output(array());
        //添加之后将店铺问卷状态置为1
        $flag = $this->club_model->updateData(array('question'=>1,'refuse'=>0),array('user_id'=>$userId));
        // parent::wechatAlert('申请成功！');
        $url = base_url().'resources/wechat/shopinfo.html?sid=' . $sid;
        parent::wechatAlert('注册成功，店铺审核中！' ,$url);
    }

    public function api_shopid()
    {
        $sid = $this->post_input('sid');//用户ID
        if (empty($sid)) {
            parent::output(100);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
       // var_dump($sessionInfo);die();
        if (empty($sessionInfo)) {
            parent::output(102);
        }
        $userId = $sessionInfo['user_id'];
        $info = $this->club_model->getInfoByUserId($userId);
        $info['id_number_image'] = json_decode($info['id_number_image'],true);
       // var_dump($info);die();
        if (empty($info)) {
            parent::output(16);
        }
        parent::output($info);
    }

    public function api_myid()
    {
        $sid = $this->post_input('sid');//用户ID
        if (empty($sid)) {
            parent::output(100);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(102);
        }
        $userId = $sessionInfo['user_id'];
        $userInfo = $this->user_model->getInfoById($userId);
        if (empty($userInfo)) {
            parent::output(10);
        }
        parent::output($userInfo);
    }

    public function api_salesRanking()
    {
        $this->load->model('session_model');
        $this->load->model('user_model');
        $this->load->model('club_model');
        $this->load->model('area_model');
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        $type = $this->post_input('type'); //按月 按周
        $pageIndex = $this->post_input('pageIndex'); //分页
        $entryNum = $this->post_input('entryNum'); //条数
        if(empty($sid) || empty($type)){
            parent::output(100);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::output(10);
        }
        $user_id = $sessionInfo['user_id'];
        $userInfo = $this->user_model->getInfoById($user_id);
        if(empty($userInfo)){
            parent::output(10);
        }
        $clubInfo = $this->club_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($clubInfo)){
            $areaInfo = $this->area_model->fetchAll(array('id'=>$clubInfo['area_id']));
            $province = $areaInfo[0]['name'];
        }
        else{
            $province = '0';
        }
        $start = '1970-01-01 00:00:00';
        if($type == 'week'){
            $start = date('Y-m-d H:i:s',strtotime('Sunday -6 days'));
        }
        elseif($type == 'month'){
            $start = date('Y-m-1 00:00:00');
        }
        elseif($type == 'day'){
            $start = date('Y-m-d 00:00:00');
        }
        $end = date('Y-m-d H:i:s');
        $data = [];
        $data['pageIndex'] = 1;
        $data['entryNum'] = 20;
        $data['province'] = $province;
        if(!empty($pageIndex) && !empty($entryNum)){
            $data['pageIndex'] = $pageIndex;
            $data['entryNum'] = $entryNum;
        }
        $tbl = '';
        if($province_id = $clubInfo['area_id']){
            $tbl = "tbl_ticket_order_".$province_id;
        }
        else{
            parent::output(903);
        }
        $info = $this->club_model->salesRanking($start,$end,$data,$tbl);        
        foreach ($info as $key => $value) {
            $info[$key]['avatar_url'] = $this->user_model->getInfoById($value['user_id'])['avatar_url'];
            empty($info[$key]['avatar_url']) ? $info[$key]['avatar_url'] = base_url() . $this->config->item('default_avatar') : '';
        }
        foreach ($info as $key => $value) {
            $info[$key]['avatar_url'] = $this->user_model->getInfoById($value['user_id'])['avatar_url'];
            empty($info[$key]['avatar_url']) ? $info[$key]['avatar_url'] = base_url() . $this->config->item('default_avatar') : '';
        }
        parent::output($info);
    }

    public function city_list()
    {
        $provinceId = $this->input->get('province_id');
        if (empty($provinceId)) {
            $rslt['error'] = 1;
            echo json_encode($rslt);
        }

        $this->load->model('area_model');
        $cityList = $this->area_model->getCityList($provinceId);

        $rslt['error'] = 0;
        $rslt['result'] = $cityList;
        echo json_encode($rslt);
    }

    public function province_list()
    {
        $this->load->model('area_model');
        $provinceList = $this->area_model->getProvinceList();
        $rslt['error'] = 0;
        $rslt['result'] = $provinceList;
        echo json_encode($rslt);
    }
   public function get_ticket(){
       $sid = $this->post_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->get_input('sid');//用户ID
        }
        $rs = [];
       $price = $this->post_input('price');//几元票
       $arr = [];
       $sessionInfo = $this->session_model->getInfoBySId($sid);
       if (empty($sessionInfo)) {
           parent::output(102);
       }

       $userId = $sessionInfo['user_id'];
       $clubinfo = $this->club_model->getInfoByUserId($userId);
       if(empty($clubinfo)){
            parent::output(16);
       }
       $province_id = $clubinfo['area_id'];
       if($price == 0){
           $this->load->model('porder_model');
           /*设置分省
            */
            $order_num = 'tbl_order_num_'.$province_id;
            $this->porder_model->set_order_num($order_num);
            /*设置分省
            */
           $tickets = $this->porder_model->getTicketInfo($province_id);
           if(empty($tickets)){
               parent::output($arr);
           }
           foreach ($tickets as $key => $value){
               $arr[$key]['id'] = $value->id;
               $arr[$key]['title'] = $value->title;
               $arr[$key]['price'] = $value->count_price;
               $arr[$key]['description'] = $value->description;
               $arr[$key]['inventory'] = $value->inventory;
           }
           parent::output($arr);
       }else{
           $price = sprintf("%.2f",$price);
           $this->load->model('ticket_model');
           $tickets = $this->ticket_model->getTicketInfo($price,$province_id);
           if(empty($tickets)){
               // $rslt['code'] = 1;
               // $rslt['data'] = [];
               // echo json_encode($rslt);
                parent::output($arr);
               return;
           }else{
               foreach ($tickets as $key => $value){
                   $arr[$key]['id'] = $value['id'];
                   $arr[$key]['title'] = $value['title'];
                   $arr[$key]['price'] = $value['count_price'];
                   $arr[$key]['description'] = $value['description'];
                   $arr[$key]['inventory'] = $value['inventory'];
               }
               parent::output($arr);
           }

       }

   }
   //获取积分
    public function get_credits(){
        $sid = $this->post_input('sid');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(102);
        }

        $userId = $sessionInfo['user_id'];
        $credits_arr['credits'] = [];
        $info = $this->user_model->getInfoById($userId);
        if(isset($info['point']) && $info['point']){
            $credits_arr['credits']  = $info['point'];
        }else{
            $credits_arr['credits']  = 0;
        }
        parent::output($credits_arr);
    }
   public function apply_welfare_ticket(){
        // [{"id":"32","num":10,"name":"美梦成真"},{"id":"36","num":1,"name":"北京印象"},{"id":"37","num":1,"name":"蓝玫瑰"}]
        $this->load->model('ticket_model');
        $this->load->model('area_model');
        $this->load->model('consumer_model');
        $this->load->model("Common_model");
       $data = $this->post_input('data');
       if(empty($data)){
            $data = $this->get_input('data');
       }
       $sid = $this->post_input('sid');
       if(empty($data) || empty($sid)){
            parent::output(100);
       }
       $data = json_decode($data);
       $sessionInfo = $this->session_model->getInfoBySId($sid);
       if (empty($sessionInfo)) {
           parent::output(102);
       }

       $userId = $sessionInfo['user_id'];
       $order_arr = [];
       $order_arr['user_id'] = $userId;
       $info = $this->club_model->getInfoByUserId($userId);
       if (empty($info)) {
           parent::output(16);
       }
       if($info['status'] != 1){
           parent::output(901);
       }
       //校验库存是否足够
       $ids = [];
       $nums = [];
       foreach ($data as $key => $value) {
           $ids[] = $value->id;
           $nums[$value->id] = $value->num;
       }
       $ids = implode(',', array_values($ids));
       $where = " id in ({$ids})";
       $ticketInfo = $this->ticket_model->fetchAll($where);
       $str = '';
       foreach ($ticketInfo as $key => $value) {
            if($value['inventory'] < $nums[$value['id']]){
                $str .= $value['title'].',';
            }
       }
       if(!empty($str)){
            $status = array(
               'status' => array(
                   'succeed' => 0,
                   'error_code' =>404,
                   'error_desc' => substr($str, 0,-1).' 库存不足'
               )
           );
           echo json_capsule($status);
           exit();
       }
       //$first_order = $this->porder_model->get_first_order($userId);
       //取消首单限制
       /*if($info['order_status'] == 1){
           $status = array(
               'status' => array(
                   'succeed' => 0,
                   'error_code' =>405,
                   'error_desc' => "请确定收货首单"
               )
           );
           echo json_capsule($status);
           exit();
       }*/

       $province = $this->area_model->getProvince($info['area_id']);
       $order_arr['area'] = $province;
       $order_arr['name'] = $info['name'];
       $order_arr['city'] = $info['city'];


       $order_arr['address'] = $info['address'];
       $order_arr['phone'] = $info['phone'];
       $order_arr['pay_status'] = 0;
       $order_arr['area_code'] = $info['area_code'];
       $trade_no = '';
       
       list($t1, $t2) = explode(' ', microtime());
       $milisec = (int)sprintf('%.0f', (floatval($t1)) * 1000);
       $milisec = sprintf("%03d", $milisec);

       if($info['area_id'] > 9){
           $area_num = $info['area_id'];
       }else{
           $area_num = '0'.$info['area_id'];
       }
       $trade_no = nownum() . $milisec . gen_rand_num(4).$userId.$area_num;

       $order_arr['trade_no'] = $trade_no;
       if(!empty($data)){
           $num_arr = [];
           $total_money = 0;
           //取票券信息
           $province_id = $info['area_id'];
           $ticketInfos = $this->ticket_model->fetchAll(array('province_id'=>$province_id));
           $temp = [];
           foreach ($ticketInfos as $key => $value) {
                $temp[$value['id']] = $value;
           }
           $ticketInfos = $temp;
           $insertData = [];
           foreach ($data as $key => $value) {
               foreach ($value as $key2 => $value2) {
                   if(empty($value2)){
                        parent::output(907);
                   }
               }
               $ticket_id = $value->id;
               $ticket_num = $value->num;
               $total_money += $ticketInfos[$ticket_id]['count_price'] * ($ticket_num);
               $insertData[$key]['trade_no'] = $trade_no;
               $insertData[$key]['ticket_id'] =  $ticket_id;
               $insertData[$key]['ticket_num'] = $ticket_num;
               $insertData[$key]['ticke_money'] = $ticketInfos[$ticket_id]['count_price'] * ($ticket_num);
           }
           $flag = $this->ticket_order_model->insertBatch($insertData,$info['area_id']);
           //配置总金额和实付金额
           $this->Common_model->setTable('tbl_ticket_config');
           $filters = [];
           $filters['area_id'] = $province_id;
           $filters['valid'] = 1;
           $ticketConfig = $this->Common_model->fetchOne($filters);
           if(empty($ticketConfig)){
                parent::output(1010);
           }
           $order_arr['total_money'] = $ticketConfig['total_money'];
           $order_arr['relmoney'] = $ticketConfig['relmoney'];
           $order_status = $info['order_status'];
           switch ($province_id) {
                case '14':
                   if($order_status == 1 || $order_status == 2){
                        $order_arr['total_money'] = $ticketConfig['total_money'];
                        $order_arr['relmoney'] = $ticketConfig['first_total_money'] + $ticketConfig['relmoney'];
                    }
                    elseif($order_status == 0){
                        $order_arr['total_money'] = $ticketConfig['first_total_money'];
                        $order_arr['relmoney'] = $ticketConfig['first_relmoney'];
                    }
                   break;
                case '7':
                   if($order_status == 0){
                        $order_arr['total_money'] = $ticketConfig['first_total_money'];
                        $order_arr['relmoney'] = $ticketConfig['first_relmoney'];
                   }
                   break;
                default:
                   # code...
                   break;
           }
           $order_arr['get_credits'] = round($total_money*1/100);
           $order_arr['create_date'] = date("Y-m-d H:i:s",time());
           $re =  $this->ticket_order_model->insertData($order_arr,$info['area_id']);
           //库存数量处理
           $display_order = [];
           foreach ($data as $key => $value) {
               $display_order[$value->id] = $value->num;
           }        
            $ids = implode(',', array_keys($display_order));
            $sql = "UPDATE tbl_ticket SET inventory = CASE id ";
            foreach ($display_order as $id => $ordinal) {
                $sql .= sprintf("WHEN %s THEN inventory - %s ", $id, $ordinal);
            }
            $sql .= " END WHERE id IN ($ids)";
            $flag = $this->ticket_model->updateInventory($sql);
            //判断是不是首单，是首单更新店铺order_status为1
           if($info['order_status'] == 0){
               $club_flag = $this->club_model->updateData(array('order_status' => 1),array('user_id' => $userId));
               $fp =  $this->filepath."update_club_".date("Y-m-d",time()).".log";
               if(!$club_flag){
                   $contents = '{"club_userid:"'.$userId.',"order_status:1","date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }else{
                   $contents = '{"club_userid:"'.$userId.',"order_status:1","date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }
           }elseif ($info['order_status'] == 1 || $info['order_status'] == 2){
               $club_flag = $this->club_model->updateData(array('order_status' => 3),array('user_id' => $userId));
               $fp =  $this->filepath."update_club_".date("Y-m-d",time()).".log";
               if(!$club_flag){
                   $contents = '{"club_userid:"'.$userId.',"order_status:3","date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }else{
                   $contents = '{"club_userid:"'.$userId.',"order_status:3","date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                   file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
               }
           }
           if($re == null){
               parent::output(902);
           }else{
               $out = array();
               parent::output($out);
           }
           if(!$flag){
                parent::output(1002);
           }
       }else{
           parent::output(903);
       }
   }
   public function get_order_list(){
       $sid = $this->post_input('sid');//用户ID
        if (empty($sid)) {
           $sid = $this->get_input('sid');//用户ID
       }
       $status = $this->post_input('status');
       $pageIndex = $this->post_input('pageIndex');//分页
       $entryNum = $this->post_input('entryNum');//条数
       if(empty($pageIndex)){
            $primitive = 1;
       }
       $sessionInfo = $this->session_model->getInfoBySId($sid);
       if (empty($sessionInfo)) {
           parent::output(102);
       }
       $userId = $sessionInfo['user_id'];
       $order_arr = [];
       $order_arr['user_id'] = $userId;

       $info = $this->club_model->getInfoByUserId($userId);
       if (empty($info)) {
           parent::output(16);
       }
       if($info['status'] != 1){
           parent::output(901);
       }
       if($status == 'wait' || $status == 'payed'){
            $where = array(
               'user_id' => $userId,
               'order_status!='=> 2
           );
       }
       else{
            $where = array(
               'user_id' => $userId,
               'order_status'=> 2
           );
       }
       // if($status == 'wait'){
       //     $where = array(
       //         'user_id' => $userId,
       //         'order_status'=> 1
       //     );
       // }elseif($status == 'payed'){
       //     $where = array(
       //         'user_id' => $userId,
       //         'order_status'=> 0
       //     );
       // }else{
       //     $where = array(
       //         'user_id' => $userId,
       //         'order_status>'=> 1
       //     );
       // }

       $data = [];
        $data['pageIndex'] = 1;
        $data['entryNum'] = 10;
        if(!empty($pageIndex) && !empty($entryNum)){
            $data['pageIndex'] = $pageIndex;
            $data['entryNum'] = $entryNum;
        }
       $result = $this->ticket_order_model->get_order_list($where,$data,$status,$info['area_id']);
       $num = $this->ticket_order_model->get_order_list_num($where,$info['area_id']);
       $Lists = [];
       if(empty($result)){
            $Lists['length'] = $num;
            $Lists['lists'] = [];
           parent::output($Lists);
       }

       foreach ($result as $key=>$item) {
           $Lists[$key]['id'] = $item['id'];
           $Lists[$key]['trade_no'] = $item['trade_no'];
           $Lists[$key]['money'] =  $item['total_money'];
           $Lists[$key]['credits'] = $item['get_credits'];
           $Lists[$key]['getwelfare'] = $item['get_credits'];
           $Lists[$key]['status'] = $item['order_status'] == 2?$item['order_status']:$item['pay_status'];
           $Lists[$key]['create_date'] = $item['create_date'];
           $Lists[$key]['update_date'] = $item['update_date'];
           $Lists[$key]['relmoney'] = $item['relmoney'];
           $order_num = $this->ticket_order_model->getInfoByTradeno($item['trade_no'],$info['area_id']);
           $detail = [];
           foreach ($order_num as $ky => $value){
               $detail[$ky]['title'] = $value->title;
               $detail[$ky]['ticket_num'] = $value->ticket_num;
               $detail[$ky]['price'] = $value->count_price;
           }
           $Lists[$key]['detail'] = $detail; 
       }
       foreach ($Lists as $key => $value) {
           if($value['status'] == 1){
                $Lists[$key]['delivery'] = true;
           }
           elseif($value['status'] == 0){
                $Lists[$key]['delivery'] = false;
           }
       }
       $clubInfo = $info;
       $province_id = $clubInfo['area_id'];
       //计算备注
       foreach ($Lists as $key => $value) {
            $Lists[$key]['note'] = '';
           switch ($province_id) {
               case '7':
                    if($Lists[$key]['relmoney'] == 0){
                        $Lists[$key]['note'] = '首单暂时不用付款';
                    }
                   break;
               case '14':
                   if($value['money'] == 600 ){
                        $Lists[$key]['note'] = '首单600暂时不用付款';
                   }
                   elseif($value['relmoney'] == 3600 ){
                        $Lists[$key]['note'] = '第二单3600包含首单600';
                   }
                   break;
               default:
                   # code...
                   break;
           }
       }
       //计算返佣
       foreach ($Lists as $key => $value) {
            $Lists[$key]['rebate'] = '0.00';
           switch ($province_id) {
               case '7':
                    if($Lists[$key]['relmoney'] == 0){
                        $Lists[$key]['rebate'] = '0.00';
                    }
                    else{
                        $Lists[$key]['rebate'] =  (string)number_format(($Lists[$key]['money'] - $Lists[$key]['relmoney']),2,'.','');
                    }
                   break;
               case '14':
                    $Lists[$key]['rebate'] = '0.00';
                   break;
               default:
                   # code...
                   break;
           }
       }
       foreach ($Lists as $key => $value) {
           $Lists[$key]['money'] =  (string)number_format($value['money'],2,'.','');
           $Lists[$key]['relmoney'] =  (string)number_format($value['relmoney'],2,'.','');
       }
       if(isset($primitive) && $primitive == 1){
            parent::output($Lists);
       }
       else{
            $rs['length'] = $num;
            $rs['lists'] = $Lists;
            parent::output($rs);
       }
       
   }
    public function get_order_update(){
        $sid = $this->post_input('sid');
        $id = $this->post_input('id');
        $this->load->model('consumer_model');
        $this->load->model('porder_model');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(102);
        }
        if($id == ''){
            parent::output(903);
        }

        $userId = $sessionInfo['user_id'];
        $order_arr = [];
        $order_arr['user_id'] = $userId;

        $info = $this->club_model->getInfoByUserId($userId);
        if (empty($info)) {
            parent::output(16);
        }
        if($info['status'] != 1){
            parent::output(901);
        }

        $where = array(
            'id'    => $id,
            'user_id' => $userId,
            'order_status'=> 1
        );

        $data['order_status'] = 2;
        $data['update_date'] = date('Y-m-d H:i:s');
        $managerinfo = $this->consumer_model->getInfoByManagerid($info['manager_id']);



        if(!empty($managerinfo)){
            $manager_user_id = $managerinfo['consumer_userid'];
            $area_manager_user_id = $managerinfo['area_user_id'];
            $bazaar_user_id = $managerinfo['bazaar_user_id'];

        }else{
            $manager_user_id = '';
            $area_manager_user_id = '';
            $bazaar_user_id = '';
        }
        //判断是否为首单 是更新club order_status为2
        if($info['order_status'] == 1){
            $club_flag = $this->club_model->updateData(array('order_status' => 2),array('user_id' => $userId));
            $fps =  $this->filepath."update_club_".date("Y-m-d",time()).".log";
            if(!$club_flag){
                $contents = '{"club_userid:"'.$userId.',"order_status:2","date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                file_put_contents($fps,$contents,FILE_APPEND|LOCK_EX);
            }else{
                $contents = '{"club_userid:"'.$userId.',"order_status:2","date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                file_put_contents($fps,$contents,FILE_APPEND|LOCK_EX);
            }
        }elseif($info['order_status'] == 3){
            $club_flag = $this->club_model->updateData(array('order_status' => 4),array('user_id' => $userId));
            $fps =  $this->filepath."update_club_".date("Y-m-d",time()).".log";
            if(!$club_flag){
                $contents = '{"club_userid:"'.$userId.',"order_status:4","date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                file_put_contents($fps,$contents,FILE_APPEND|LOCK_EX);
            }else{
                $contents = '{"club_userid:"'.$userId.',"order_status:4","date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                file_put_contents($fps,$contents,FILE_APPEND|LOCK_EX);
            }
        }





        $order_info = $this->ticket_order_model->get_order_info_by_id($id,$info['area_id']);
        if(empty($order_info)){
            parent::output(903);
        }
        $result = $this->ticket_order_model->get_order_update($data,$where,$info['area_id']);
        $total_money = round($order_info['total_money']);
        $address = $order_info['area'].$order_info['city'].$order_info['address'];
        $trade_no = $order_info['trade_no'];
        $create_date = $order_info['create_date'];
     /*店铺积分记录
      * */
        $club_data['trade_no'] = $trade_no;
        $club_data['user_id'] = $userId;
        $club_data['create_date'] = $create_date;
        $club_data['credits'] = round($total_money*1/100);
        $club_data['type'] = 1;
        $club_data['status'] = 1;
        $club_data['add_time'] = time();
        $club_user_credits = round($total_money*1/100);

        /*客户经理
         * */

        $manager_user_id = $manager_user_id;
        $manager_credits =  round($total_money*1/200);
        $manager_data['trade_no'] = $trade_no;
        $manager_data['user_id'] = $manager_user_id;
        $manager_data['create_date'] = $create_date;
        $manager_data['credits'] = $manager_credits;
        $manager_data['type'] = 2;
        $manager_data['status'] = 1;
        $manager_data['add_time'] = time();
        $manager_data['address'] = $address;
        $manager_data['name'] = $info['name'];
       
        /*市场经理
         * */
        $area_manager_user_id = $area_manager_user_id;
        $area_manager_credits = round($total_money*1/300);
        $area_manager_data['trade_no'] = $trade_no;
        $area_manager_data['user_id'] = $area_manager_user_id;
        $area_manager_data['create_date'] = $create_date;
        $area_manager_data['credits'] =  $area_manager_credits;
        $area_manager_data['type'] = 4;
        $area_manager_data['status'] = 1;
        $area_manager_data['add_time'] = time();
       # $area_manager_data['address'] = $address;
        $area_manager_data['name'] = $info['manager_name'];

        /*区域经理
         * */
        $bazaar_user_id = $bazaar_user_id;
        $bazaar_credits = round($total_money*1/300);
        $bazaar_data['trade_no'] = $trade_no;
        $bazaar_data['user_id'] = $bazaar_user_id;
        $bazaar_data['create_date'] = $create_date;
        $bazaar_data['credits'] =  $bazaar_credits;
        $bazaar_data['type'] = 5;
        $bazaar_data['status'] = 1;
        $bazaar_data['add_time'] = time();
        $bazaar_data['name'] = $managerinfo['area_name'];

        if($result <= 0){
            parent::output(902);
        }else{
            $this->load->model("user_credits_model");
            $user_re = $this->user_model->updateCredits($userId,$club_user_credits);
            $fp =  $this->filepath."add_credits_".date("Y-m-d",time()).".log";
           /* $fp =  "D:/add_credits_".date("Y-m-d",time()).".log";*/
            if($user_re){
                $contents = '{"club_userid:"'.$userId.',"credits:"'.$club_user_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;

                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                $this->user_credits_model->insertData($club_data);
            }else{
                $contents = '{"club_userid:"'.$userId.',"credits:"'.$club_user_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                $club_data['status'] = 0;
                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                $this->user_credits_model->insertData($club_data);

            }

            if($manager_user_id != null){
                $manager_re = $this->user_model->updateCredits($manager_user_id,$manager_credits);
                if($manager_re){
                    $contents = '{"manager:"'.$manager_user_id.',"credits:"'.$manager_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                    $this->user_credits_model->insertData($manager_data);
                    file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                }else{
                    $contents = '{"manager:"'.$manager_user_id.',"credits:"'.$manager_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                    $manager_data['status'] = 0;
                    $this->user_credits_model->insertData($manager_data);
                    file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                }
            }


            if($area_manager_user_id != ''){
                $area_manager_re = $this->user_model->updateCredits($area_manager_user_id,$area_manager_credits);
                if($area_manager_re){
                    $contents = '{"area_manager:"'.$area_manager_user_id.',"credits:"'.$area_manager_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                    $this->user_credits_model->insertData($area_manager_data);
                    file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                }else{
                    $contents = '{"area_manager:"'.$area_manager_user_id.',"credits:"'.$area_manager_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                    $area_manager_data['status'] = 0;
                    $this->user_credits_model->insertData($area_manager_data);
                    file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                }
            }

            if($bazaar_user_id != ''){
                $bazaar_manager_re = $this->user_model->updateCredits($bazaar_user_id,$bazaar_credits);
                if($bazaar_manager_re){
                    $contents = '{"bazaar_manager:"'.$bazaar_user_id.',"credits:"'.$bazaar_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                    $this->user_credits_model->insertData($bazaar_data);
                    file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                }else{
                    $contents = '{"bazaar_manager:"'.$bazaar_user_id.',"credits:"'.$bazaar_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                    $bazaar_data['status'] = 0;
                    $this->user_credits_model->insertData($bazaar_data);
                    file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                }
            }

            $re = [];
            parent::output($re);
        }

    }

    //积分来源
    public function get_credits_detail(){
        $sid = $this->post_input('sid');//用户ID
        $page = $this->post_input('pageIndex');
        $type = $this->post_input('type');
        if (empty($sid)) {
            parent::output(100);
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            parent::output(102);
        }
        $userId = $sessionInfo['user_id'];

        $this->load->model('porder_model');

        $data = [];
        if($type == 3){
            $time = time();
            $appkey = "h#jGD&kihy787867";
            $sign = md5($appkey.$time);
            $url  = $this->credits_url."api/creditapi/credits_detail?timetmp={$time}&sign={$sign}&userid={$userId}&page={$page}";
            $ch = curl_init();                              //initialize curl handle
            curl_setopt($ch, CURLOPT_URL, $url);            //set the url
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
            $response = curl_exec($ch);
            curl_close($ch);
            $callinfo = json_decode($response, true);
            if(!empty($callinfo)){
                foreach ($callinfo['lists'] as $key=>$value){
                    $data[$key]['trade_no'] = $value['trade_no'];
                    $data[$key]['credits'] = $value['credits'];
                    $data[$key]['update_date'] = $value['update_date'];
                    $data[$key]['product_title'] = $value['product_title'];
                    $data[$key]['num'] = $value['product_value'];
                    $data[$key]['type'] = 3;
                }
                $re['length'] = $callinfo['lenth'];
                $re['list']=$data;
                $re['type'] = (int)$type;
                parent::output($re);
            }else{
                $re = [];
                parent::output($re);
            }
        }


        $this->load->model("user_credits_model");

        $credits_detail = $this->user_credits_model->get_credits_lists($userId,1,$page);
        $len = $this->user_credits_model->get_credits_count($userId,1);
        $data = [];
            if(!empty($credits_detail)){
                foreach ($credits_detail as $key => $value){
                    if($value['type'] == 1 ){
                        $data[$key]['trade_no'] = $value['trade_no'];
                        $data[$key]['update_date'] = date("Y-m-d H:i:s",$value['add_time']);
                        $data[$key]['credits'] = $value['credits'];
                        $data[$key]['type'] = 1;
                    }
                    if($value['type'] == 2){
                        $data[$key]['name'] = $value['name'];
                        $data[$key]['credits'] = $value['credits'];
                        $data[$key]['address'] = $value['address'];
                        $data[$key]['update_date'] =date("Y-m-d H:i:s",$value['add_time']);
                        $data[$key]['type'] = 2;
                    }
                    if($value['type'] == 4){
                        $data[$key]['name'] = $value['name'];
                        $data[$key]['credits'] = $value['credits'];
                        $data[$key]['update_date'] = date("Y-m-d H:i:s",$value['add_time']);
                        $data[$key]['type'] = 4;
                    }
                    if($value['type'] == 5){
                        $data[$key]['name'] = $value['name'];
                        $data[$key]['credits'] = $value['credits'];
                        $data[$key]['update_date'] = date("Y-m-d H:i:s",$value['add_time']);;
                        $data[$key]['type'] = 5;
                    }
                    if($value['type'] == 6){
                        $data[$key]['name'] = $value['name'];
                        $data[$key]['credits'] = $value['credits'];
                        $data[$key]['update_date'] = date("Y-m-d H:i:s",$value['add_time']);;
                        $data[$key]['address'] = $value['address'];
                        $data[$key]['type'] = 6;
                    }
                    if($value['type'] == 7){
                        $data[$key]['name'] = $value['name'];
                        $data[$key]['credits'] = $value['credits'];
                        $data[$key]['update_date'] = date("Y-m-d H:i:s",$value['add_time']);;
                        $data[$key]['address'] = $value['address'];
                        $data[$key]['type'] = 7;
                    }
                }
            }
            $re['length'] = $len;
            $re['list'] = $data;
            $re['type'] = (int)$type;
            parent::output($re);

    }
    public function test_tmp(){
        $this->load->model('sendmsg_model');
        $re = $this->sendmsg_model-> passFtip('oyAK9wzag0ccx03XFk67o0_N5NWA','韩力','18312862310','韩力小店');
        var_dump($re);
    }
    public function getSid(){
        $rs = [];
        $this->load->model('user_model');
        $this->load->model('session_model');

        $unionid = $this->post_input('unionid');
        if(empty($unionid)){
            $unionid = $this->get_input('unionid');
        }
        $headimgurl = $this->post_input('headimgurl');
        if(empty($headimgurl)){
            $headimgurl = $this->get_input('headimgurl');
        }
        $nickname = $this->post_input('nickname');
        if(empty($nickname)){
            $nickname = $this->get_input('nickname');
        }
        if(empty($unionid) || empty($nickname)){
            $this->reply('缺少参数');
            return;
        }
        $userinfo = $this->user_model->fetchAll(array('unionid'=>$unionid));
        if(empty($userinfo)){
            //注册用户信息
            $user_id = $this->user_model->wechatInsert(md5(microtime()), $nickname, $headimgurl,$unionid);
            if (!$user_id) {
                $this->reply('数据插入错误！code:1');
                exit;
            }
            //处理sid
            if (!$this->session_model->insert($user_id)) {
                $this->reply('数据插入错误！code:2');
                exit;
            }
        }
        else{
            $user_id = $userinfo[0]['id'];
        }
        //取session
        $sessionInfo = $this->session_model->getByUser($user_id);
        if(empty($sessionInfo)){
            $rs['code'] = 1;
            $rs['msg'] = '缺少session信息';
            $rs['data'] = [];
            echo json_encode($rs);
            return;
        }
        $rs['code'] = 200;
        $rs['msg'] = '';
        $rs['data'] = array('sid'=>$sessionInfo['session_id']);
        echo json_encode($rs);
        return;
    }
    public function checkVersion(){
        $rs = [];
        $getversion = $this->post_input('version');
        if(empty($version)){
            $getversion = $this->get_input('version');
        }
        $basePath = "/var/www/download/zhongwei/apk/";
        // $basePath = "D:image/";
        $baseUrl = get_instance()->config->config['base_download_url']."zhongwei/apk/";
        $temp = scandir($basePath);
        if(sizeof($temp) == 2){
            $this->reply('目录下无apk');
            return;
        }
        $apkList = [];
        //取最新的文件
        $createTime = 0;
        $file = '';
        foreach ($temp as $key => $value) {
            if($value == '.' || $value == '..'){
                continue;
            }
            $tempTIme = filectime($basePath.$value);
            if(empty($createTime)){
                $createTime = $tempTIme;
                $file = $value;
            }
            else{
                if($tempTIme > $createTime){
                    $createTime = $tempTIme;
                    $file = $value;
                }
            }
        }
        //取文件版本号
        $version = '';
        if(strrpos($file, '_') !== false){
           $version = explode('_', substr($file, 0,strpos($file, '.')))[1]; 
        }
        //计算最新文件的MD5值
        $md5 = md5_file($basePath.$file);
        $downloadUrl = $baseUrl.$file;
        $rs['data'] = array('version'=>$version,'fileMd5'=>$md5,'downloadUrl'=>$downloadUrl);
        if($getversion == $version){
            $rs['data']['updata'] = 0;
        }
        elseif($getversion != $version){
            $rs['data']['updata'] = 1;
        }
        $this->success('成功',$rs['data']);
        return;
    }
    public function wechatBindApp($user_id,$phone){
        return true;
        $this->load->model('user_model');
        if(empty($phone)){
            return false;
        }
        $userInfo = $this->user_model->fetchOne(array('phone'=>$phone));
        $recordId = $userInfo['id'];
        if(empty($userInfo)){
            return true;
        }
        $userInfo = $this->user_model->fetchOne(array('id'=>$user_id));
        if(!empty($userInfo)){
            if(!empty($userInfo['weixin']) && empty($userInfo['phone'])){
                $flag =  $this->user_model->deleteData(array('id'=>$user_id));
                file_put_contents($this->filepath.'deleteUer.log', date("Y-m-d H:i:s").PHP_EOL.var_export($userInfo,true).PHP_EOL,FILE_APPEND|LOCK_EX);
                $updateData = [];
                $updateData['username'] = $userInfo['weixin'];
                $updateData['nickname'] = $userInfo['nickname'];
                $updateData['avatar_url'] = $userInfo['avatar_url'];
                $updateData['weixin'] = $userInfo['weixin'];
                $updateData['unionid'] = $userInfo['unionid'];
                $flag = $this->user_model->updateData($updateData,array('id'=>$recordId));
                return $recordId;
            }
        }
        else{
            return true;
        }
    }
}
