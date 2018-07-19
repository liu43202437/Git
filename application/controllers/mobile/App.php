<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/third_party/CCPRestSmsSDK.php";
class App extends Base_MobileController
{
    protected $filepath ='';
    protected $wx_base_url = '';
    function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
        $this->load->model('club_model');
        $this->load->model('area_model');
        $this->load->model('session_model');
        $this->filepath = get_instance()->config->config['log_path_file'];
        if(isset(get_instance()->config->config['wx_base_url'])){
            $this->wx_base_url = get_instance()->config->config['wx_base_url'];
        }
        else{
            $this->wx_base_url = 'https://wxyan.bjzwhz.cn/';
        }

    }
    public function judeIdent(){
        $rs = [];
        $this->load->model('club_model'); 
        $this->load->model('manager_model');
        $this->load->model('area_manager_model');
        $this->load->model('session_model');
        $this->load->model("lottery_manager");

        //判断用户的身份
        $sid = $this->get_input('sid');
        if(empty($sid)){
            $sid = $this->post_input('sid');
        }
        $sessioninfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessioninfo)){
            $rs['code'] = 0;
            $rs['msg'] = 'sid不存在';
            $rs['data'] = [];
            echo json_encode($rs);
            return;
        }
        $userId = $sessioninfo['user_id'];
        //判断用户身份
        $clubinfo = $this->club_model->fetchOne(array('user_id'=>$userId,'refuse'=>0));
        empty($clubinfo) ? $rs['data']['club'] = false : $rs['data']['club'] = true;
        $manager_modelinfo = $this->manager_model->fetchOne(array('consumer_userid'=>$userId));
        empty($manager_modelinfo) ? $rs['data']['manager'] = false : $rs['data']['manager'] = true;
        $areaManagerInfo = $this->area_manager_model->fetchOne(array('user_id'=>$userId));
        empty($areaManagerInfo) ? $rs['data']['areaManager'] = false : $rs['data']['areaManager'] = true;
        $lottery_managerInfo=$this->lottery_manager->fetchOne(array('user_id'=>$userId,'refuse'=>0));
        empty($lottery_managerInfo) ? $rs['data']['lottery_manager'] = false : $rs['data']['lottery_manager'] = true;
        $rs['code'] = 200;
        $rs['msg'] = '成功';
        echo json_encode($rs);
        return;
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
    public function shop()
    {
        $this->load->model('session_model');
        $sid = $this->get_input('sid');//用户ID

        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::wechatAlert('请登录！');
        }
        $userId = $sessionInfo['user_id'];
        $info = $this->club_model->fetchOne(array('user_id'=>$userId,'question'=>1));
        if (!empty($info)) {
            if($info['refuse'] == 0){
                if ($info['status'] == 0) {
                $url = base_url().'resources/app/shopinfo.html?sid=' . $sid;
                parent::wechatAlert('店铺审核中，请耐心等待', $url);
                }
                if ($info['status'] == 1) {
                    $url = base_url().'resources/app/lottery.html';
                    header("location:" . $url . "?sid=" . $sid);
                    die;
                    //parent::wechatAlert('审核通过！');
                }
                if($info['status'] == 2){
                    $url = base_url().'resources/app/shopinfo.html?sid=' . $sid;
                    parent::wechatAlert('您的店铺申请已经通过客户经理审核，正在办理代销证，请耐心等待', $url);
                }
            }
            elseif($info['refuse'] == 1){
                $url = base_url().'resources/app/shopzc.html?refuse=1';
                header("location:" . $url . "&sid=" . $sid);
                return;
            } 
        }
        $url = base_url().'resources/app/shopzc.html';
        header("location:" . $url . "?sid=" . $sid);
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
            if($info['refuse'] == 0){
                if ($info['status'] == 0) {
                $url = base_url().'resources/app/managerinfo.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
                }
                elseif($info['status'] == 1){
                    $url = base_url().'resources/app/manager-point.html?sid=' . $sid;
                    header("location:" . $url);
                    return;
                }
            }
            else{
                $url = base_url().'resources/app/managerzc.html?refuse=1';
                header("location:" . $url . "&sid=" . $sid);
                return;
            }
        }
        else{
            $url = base_url().'resources/app/managerzc.html';
            header("location:" . $url . "?sid=" . $sid);
        }
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
            if($info['refuse'] == 0){
                if ($info['status'] == 0) {
                $url = base_url().'resources/app/market-manager-info.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
                }
                elseif($info['status'] == 1){
                    $url = base_url().'resources/app/market-manager-point.html?sid=' . $sid;
                    header("location:" . $url);
                    return;
                }
            }
            else{
                $url = base_url().'resources/app/market-manager-zc.html?refuse=1';
                header("location:" . $url . "&sid=" . $sid);
                return;
            } 
        }
        else{
            $url = base_url().'resources/app/market-manager-zc.html';
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
            if($info['refuse'] == 0){
                if ($info['status'] == 0) {
                $url = base_url().'resources/app/area-manager-info.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
                }
                elseif($info['status'] == 1){
                    $url = base_url().'resources/app/area-manager-point.html?sid=' . $sid;
                    header("location:" . $url);
                }
            }
            else{
                $url = base_url().'resources/app/area-manager-zc.html?refuse=1';
                header("location:" . $url . "&sid=" . $sid);
                return;
            }
        }
        else{
            $url = base_url().'resources/app/area-manager-zc.html';
            header("location:" . $url . "?sid=" . $sid);
        }
    }
    //访销经理注册
    public function lottery_manager()
    {
        $this->load->model('lottery_manager');
        $sid = $this->get_input('sid');//用户ID
        if(empty($sid)){
            $sid = $this->post_input('sid');//用户ID
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        $userId = $sessionInfo['user_id'];
        if (empty($sessionInfo)) {
            $this->reply('sid过期，请重新登录');
            return;
        }
        $info = $this->lottery_manager->fetchOne(array('user_id'=>$userId));
        if(!empty($info)){
            if($info['refuse'] == 0){
                if ($info['status'] == 0) {
                $url = base_url().'resources/app/vc-visiter-manager-info.html?sid=' . $sid;
                $rs = [];
                $rs['pass'] = false;
                $rs['url'] = $url;
                $this->success('成功',$rs);
                return;
                }
                elseif($info['status'] == 1){
                    $rs = [];
                    $rs['pass'] = true;
                    $rs['url'] = '';
                    $this->success('成功',$rs);
                    return;
                }
            }
            else{
                $url = base_url().'resources/app/vc-visiter-manager-zc.html?refuse=1&sid='.$sid;
                $rs = [];
                $rs['pass'] = false;
                $rs['url'] = $url;
                $this->success('成功',$rs);
                return;
            }
        }
        else{
            $url = base_url().'resources/app/vc-visiter-manager-zc.html?sid='.$sid;
            $rs = [];
            $rs['pass'] = false;
            $rs['url'] = $url;
            $this->success('成功',$rs);
            return;
        }
    }
    public function caipiao()
    {
//        $sid = $this->get_input('sid');//用户ID
//        $url=base_url().'resources/wechat/shopzc.html';
//        header("location:" . $url . "?sid=" . $sid);
        $url = base_url().'/resources/app/news/activity.html';
        header("location:" . $url);
    }
    public function checkVersion(){
        $this->load->model('Common_model'); 
        $rs = [];
        $version = $this->getParam('version');
        $type = $this->getParam('type');
        if(empty($version)){
            $this->reply('缺少参数');
            return;
        }
        empty($type) ? $type = 1 : '';
        $this->Common_model->setTable('tbl_app_version');
        $order = array('create_date'=>'desc');
        $filters = array('type'=>$type);
        $info = $this->Common_model->fetchOne(array('type'=>$type),$order);
        $rs['version'] = $info['version'];
        if($info['compel'] == 1){
            $rs['compel'] = true;
        }
        else{
            $rs['compel'] = false;
        }
        $this->success('成功',$rs);
    }
}
