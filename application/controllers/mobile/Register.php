<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Access-Control-Max-Age: ' . 3600 * 24);
require_once "application/third_party/Upload/fileupload.class.php";
class Register extends Base_MobileController
{
    private $logPath = '';
    private $file_dir_base = '';
    private $url_base = '';
    private $file_id_number = '';
    private $url_id_number = '';
    private $file_yan_code = '';
    private $url_yan_code = '';
    private $file_lottery_papers = '';
    private $url_lottery_papers = '';
    private $file_receipt = '';
    private $url_receipt = '';
    private $file_interview = '';
    private $url_interview = '';
    function __construct()
    {
        parent::__construct();
        $this->logPath = get_instance()->config->config['log_path_file'];
        $this->file_dir_base = '/var/www/download/zhongwei/image/club/';
        $this->url_base = get_instance()->config->config['base_download_url'].'zhongwei/image/club/';
        $this->file_id_number = '/var/www/download/zhongwei/image/club/id_number/';
        $this->url_id_number = get_instance()->config->config['base_download_url'].'zhongwei/image/club/id_number/';
        $this->file_yan_code = '/var/www/download/zhongwei/image/club/yan_code/';
        $this->url_yan_code = get_instance()->config->config['base_download_url'].'zhongwei/image/club/yan_code/';
        $this->file_lottery_papers = '/var/www/download/zhongwei/image/lottery_manager/lottery_papers/';
        $this->url_lottery_papers = get_instance()->config->config['base_download_url'].'zhongwei/image/lottery_manager/lottery_papers/';
        $this->file_receipt = '/var/www/download/zhongwei/image/lottery_manager/receipt/';
        $this->url_receipt = get_instance()->config->config['base_download_url'].'zhongwei/image/lottery_manager/receipt/';
        $this->file_interview = '/var/www/download/zhongwei/image/lottery_manager/interview/';
        $this->url_interview = get_instance()->config->config['base_download_url'].'zhongwei/image/lottery_manager/interview/';
    }
    //所有的test都可以删除，但需要向作者本人确认！！！
    public function test(){
        set_time_limit(0);
        $this->load->model("Common_model");
        $this->Common_model->setTable('tbl_new_area');
        $area = file_get_contents('C:\Users\liuzudong\Desktop\temp\temp.js');
        // var_dump($area);
        $area = strstr($area, '[{"id":"2",');
        // var_dump($area);
        $area = json_decode($area);
        echo "<pre>";
        $area_arr = [];
        $insertData = [];
        foreach ($area as $key => $value) {
            $insertData['area_id'] = $value->id;
            $insertData['name'] = $value->value;
            $insertData['parent_id'] = 1;
            $insertData['type'] = 1;
            // var_dump($insertData);die;
            // $this->Common_model->insertData($insertData);
            if(!empty($value->childs) && is_array($value->childs)){
                foreach ($value->childs as $key2 => $value2) {
                    $insertData['area_id'] = $value2->id;
                    $insertData['name'] = $value2->value;
                    $insertData['parent_id'] = $value->id;
                    $insertData['type'] = 2;
                    // $this->Common_model->insertData($insertData);
                    if(!empty($value2->childs) && is_array($value2->childs)){
                        foreach ($value2->childs as $key3 => $value3) {
                            $insertData['area_id'] = $value3->id;
                            $insertData['name'] = $value3->value;
                            $insertData['parent_id'] = $value2->id;
                            $insertData['type'] = 3;
                            // $this->Common_model->insertData($insertData);
                            if(!empty($value3->childs) && is_array($value3->childs)){
                                foreach ($value3->childs as $key4 => $value4) {
                                    $insertData['area_id'] = $value4->id;
                                    $insertData['name'] = $value4->value;
                                    $insertData['parent_id'] = $value3->id;
                                    $insertData['type'] = 4;
                                    // $this->Common_model->insertData($insertData);
                                }
                            }
                        }
                    }
                }
            }
        }
        var_dump($insertData);
    }
    //补充appuser数据
    public function supplementAppUser(){
        set_time_limit(600);

        $this->load->model('AppUser_model');
        $this->load->model('club_model');
        $this->load->model('manager_model');
        $this->load->model("Area_manager_model");
        $this->load->model('Bazaar_manager_model');

        $clubInfo = $this->club_model->fetchAll();
        $managerInfo = $this->manager_model->fetchAll('tbl_consumer');
        $AreaManagerInfo = $this->Area_manager_model->fetchAll('tbl_area_manager');
        $BazaarManagerInfo = $this->Bazaar_manager_model->fetchAll('tbl_bazaar_manager');
        $insertData = [];
        $rs= [];
        foreach ($clubInfo as $key => $value) {
            $insertData[$key]['phone'] = $value['phone'];
            $insertData[$key]['pwd'] = md5('123456');
            $insertData[$key]['create_date'] = date('Y-m-d H:i:s');
            $insertData[$key]['user_id'] = $value['user_id'];
            $insertData[$key]['name'] = $value['name'];
            $insertData[$key]['type'] = 'club';
        }
        foreach ($managerInfo as $key => $value) {
            $key = $key  + count($clubInfo);
            $insertData[$key]['phone'] = $value['phone'];
            $insertData[$key]['pwd'] = md5('123456');
            $insertData[$key]['create_date'] = date('Y-m-d H:i:s');
            $insertData[$key]['user_id'] = $value['consumer_userid'];
            $insertData[$key]['name'] = $value['name'];
            $insertData[$key]['type'] = 'manager';
        }
        foreach ($AreaManagerInfo as $key => $value) {
            $key = $key  + count($clubInfo) + count($managerInfo);
            $insertData[$key]['phone'] = $value['phone'];
            $insertData[$key]['pwd'] = md5('123456');
            $insertData[$key]['create_date'] = date('Y-m-d H:i:s');
            $insertData[$key]['user_id'] = $value['user_id'];
            $insertData[$key]['name'] = $value['name'];
            $insertData[$key]['type'] = 'area';
        }
        foreach ($BazaarManagerInfo as $key => $value) {
            $key = $key  + count($clubInfo) + count($managerInfo) + count($AreaManagerInfo);
            $insertData[$key]['phone'] = $value['phone'];
            $insertData[$key]['pwd'] = md5('123456');
            $insertData[$key]['create_date'] = date('Y-m-d H:i:s');
            $insertData[$key]['user_id'] = $value['user_id'];
            $insertData[$key]['name'] = $value['name'];
            $insertData[$key]['type'] = 'bazaar';
        }
        //去重
        $distinct = [];
        $chongfu = [];
        $chnogfuid = [];
        foreach ($insertData as $key => $value) {
            if(!in_array($value['phone'], $distinct)){
                $distinct[] = $value['phone'];
            }
            else{
                $chongfu[$key]['phone'] = $value['phone'];
                $chongfu[$key]['name'] = $value['name'];
                $chongfu[$key]['type'] = $value['type'];
                unset($insertData[$key]);
            }
            if(!in_array($value['user_id'], $distinct)){
                $distinct[] = $value['user_id'];
            }
            else{
                $chnogfuid[$key]['user_id'] = $value['user_id'];
                $chnogfuid[$key]['name'] = $value['name'];
                $chnogfuid[$key]['type'] = $value['type'];
                unset($insertData[$key]);
            }
            unset($insertData[$key]['name']);
            unset($insertData[$key]['type']);
        }
        $sql = 'TRUNCATE tbl_appuser';
        $flag = $this->AppUser_model->execSql($sql);
        $flag = $this->AppUser_model->insertBatch($insertData);
    }
    public function clubClanRegister(){
        $this->load->model("verifyphone_model");
        $this->load->model("user_model");
        $this->load->model("club_model");
        $this->load->model("session_model");
        $this->load->model("UserRedeem_model");
        $this->load->model("ThirdOpenid_model");

        $sid = $this->getParam('sid');
        $user_name = $this->getParam('user_name');
        $user_phone = $this->getParam('user_phone');
        $code = $this->getParam('code');
        $shop_name = $this->getParam('shop_name');
        $shop_phone = $this->getParam('shop_phone');
        if(empty($sid) || empty($user_name) || empty($user_phone) || empty($code) || empty($shop_name) || empty($shop_phone) ){
            $this->reply('缺少参数');
            return;
        }
        if (!$this->verifyphone_model->is_exist_code($user_phone, $code)) {
            $this->reply('验证码错误');
            return;
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($user_phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            $this->reply('验证码过期');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if (empty($sessionInfo)) {
            $this->reply('找不到用户session信息');
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $old_user_id = $user_id;
        $recordId = $this->wechatBindApp($user_id,$user_phone);
        if(gettype($recordId) == 'string'){
            $info = $this->UserRedeem_model->fetchOne(array('user_id'=>$recordId));
            if(!empty($info)){
                $this->reply('您已经在APP注册过，请关闭此页面重新打开');
                return;
            }
            $user_id = $recordId;
            //更新用户session表，更新user_id
            //更新用户sid
            $updateData = [];
            $updateData['user_id'] = $user_id;
            $this->session_model->updateBySId($sid,$updateData);
            $sessionInfo = $this->session_model->getByUser($user_id);
        }
        $userInfo = $this->user_model->getInfoById($user_id);
        if (empty($userInfo)) {
            $this->reply('找不到用户信息');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('phone'=>$shop_phone));
        if(empty($clubInfo)){
            $this->reply('找不到店铺信息，请核对信息');
            return;
        }
        // if($clubInfo['name'] != $shop_name){
        //     $this->reply('店主姓名填写错误，请核对信息');
        //     return;
        // }
        if($clubInfo['status'] == 0){
            $this->reply('该店铺暂未通过审核');
            return;
        }
        $claninfo = $this->UserRedeem_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($claninfo)){
            $this->reply('该用户已经绑定过店铺，绑定店主'.$claninfo['clubOwner']);
            return;
        }
        $claninfo = $this->UserRedeem_model->fetchOne(array('phone'=>$user_phone));
        if(!empty($claninfo)){
            $this->reply('该手机已经绑定过店铺，绑定店主'.$claninfo['clubOwner']);
            return;
        }
        $clanList = $this->UserRedeem_model->fetchAll(array('club_id'=>$clubInfo['id']));
        if(count($clanList) > 9){
            $this->reply('店铺绑定亲友数已超上限');
            return;
        }
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['name'] = $user_name;
        $insertData['phone'] = $user_phone;
        $insertData['clubOwner'] = $shop_name;
        $insertData['clubOwnerPhone'] = $shop_phone;
        $insertData['club_id'] = $clubInfo['id'];
        $insertData['status'] = 1;
        $insertData['time'] = date('Y-m-d H:i:s');
        $flag = $this->UserRedeem_model->insertData($insertData);
        //更新用户站点
        $updateFlag = $this->user_model->updateData(array('stationId'=>'45898888'),array('id'=>$user_id));
        if($flag && $updateFlag !== false){
            $this->success('true',array('sid'=>$sessionInfo['session_id']));
            return;
        }
        else{
            $this->success('false');
            return;
        }
    }
    public function appUserRegister(){
        $this->load->model("verifyphone_model");
        $this->load->model('session_model');
        $this->load->model('user_model');
        $this->load->model('club_model');
        $this->load->model('manager_model');
        $this->load->model("Area_manager_model");
        $this->load->model('Bazaar_manager_model');

        $phone = $this->getParam('phone');
        $pwd = $this->getParam('password');
        $pwd2 = $this->getParam('password2');
        $code = $this->getParam('code');
        if(empty($phone) || empty($pwd) || empty($pwd2) || empty($code) ){
            $this->reply('缺少参数');
            return;
        }
        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            $this->reply('验证码填写错误');
            return;
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            $this->reply('验证码过期');
            return;
        }
        if($pwd != $pwd2){
            $this->reply('两次密码填写不一致');
            return;
        }
        $userInfo = $this->user_model->fetchOne(array('phone'=>$phone));
        if(!empty($userInfo)){
            $this->reply('手机号码已经注册');
            return;
        }
        //调整tbl_user
        //从所有的身份中查询用户手机号码
        $clubInfo = $this->club_model->fetchOne(array('phone'=>$phone));
        if(empty($clubInfo)){
            $managerInfo = $this->manager_model->fetchOne(array('phone'=>$phone));
            if(empty($managerInfo)){
                $AreaManagerInfo = $this->Area_manager_model->fetchOne(array('phone'=>$phone));
                if(empty($AreaManagerInfo)){
                    $BazaarManagerInfo = $this->Bazaar_manager_model->fetchOne(array('phone'=>$phone));
                    if(empty($BazaarManagerInfo)){

                    }
                    else{
                        $user_id = $BazaarManagerInfo['user_id'];
                    }
                }
                else{
                    $user_id = $AreaManagerInfo['user_id'];
                }
            }
            else{
                $user_id = $managerInfo['consumer_userid'];
            }
        }   
        else{
            $user_id = $clubInfo['user_id'];
        }
        if(!empty($user_id)){
            $userInfo = $this->user_model->fetchOne(array('id'=>$user_id));
            if(empty($userInfo['phone'])){
                $updateData = [];
                $updateData['phone'] = $phone;
                $updateData['username'] = $phone;
                $updateData['pwd'] = md5($pwd);
                $flag = $this->user_model->updateData($updateData,array('id'=>$user_id));
                if($flag === false){
                    $this->reply('注册失败,code:1');
                    return;
                }
            }
            else{
                $insertData = [];
                $insertData['username'] = $phone;
                $insertData['exp'] = 0;
                $insertData['money'] = 0;
                $insertData['phone'] = $phone;
                $insertData['pwd'] = md5($pwd);
                $insertData['create_date'] = date('Y-m-d H:i:s');
                $flag = $this->user_model->insertData($insertData);
                $user_id = $flag;
                if(!$flag){
                    $this->reply('注册失败,code:2');
                    return;
                }
                //处理sid
                if (!$this->session_model->insert($user_id)) {
                    $this->reply('注册失败,code:3');
                    return;
                }
            }
            
        }
        else{
            $insertData = [];
            $insertData['username'] = $phone;
            $insertData['exp'] = 0;
            $insertData['money'] = 0;
            $insertData['phone'] = $phone;
            $insertData['pwd'] = md5($pwd);
            $insertData['create_date'] = date('Y-m-d H:i:s');
            $flag = $this->user_model->insertData($insertData);
            $user_id = $flag;
            if(!$flag){
                $this->reply('注册失败,code:2');
                return;
            }
            //处理sid
            if (!$this->session_model->insert($user_id)) {
                $this->reply('注册失败,code:3');
                return;
            }
        }
        $sessionInfo = $this->session_model->getByUser($user_id);
        //更新sid
        $updateData = [];
        $updateData['expire_date'] = date('Y-m-d H:i:s',time()+3600*24*7);
        $this->session_model->updateBySId($sessionInfo['session_id'],$updateData);
        $this->success('成功',array('sid'=>$sessionInfo['session_id']));
    }
    public function upload(){
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        $type = $this->getParam('type');
        if(empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('无此用户');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $rs = [];
        if(empty($_FILES)){
            $this->reply('缺少上传文件');
            return;
        }
        $key = array_keys($_FILES)[0];
        if ("OPTIONS" == $_SERVER['REQUEST_METHOD'])
            return $rs;
        if(!empty($_FILES[$key]['name'])){
            //图片上传
            switch ($type) {
                case 'id_number':
                    $savePath = $this->file_id_number;
                    $netPath = $this->url_id_number;
                    break;
                case 'yan_code':
                    $savePath = $this->file_yan_code;
                    $netPath = $this->url_yan_code;
                    break;
                case 'lottery_papers':
                    $savePath = $this->file_lottery_papers;
                    $netPath = $this->url_lottery_papers;
                    break;
                case 'receipt':
                    $savePath = $this->file_receipt;
                    $netPath = $this->url_receipt;
                    break;
                case 'interview':
                    $savePath = $this->file_interview;
                    $netPath = $this->url_interview;
                    break;
                
                default:
                    $savePath = $this->file_dir_base;
                    $netPath = $this->url_base;
                    break;
            }
            $savePath .= date('Y_m').'/';
            $netPath .= date('Y_m').'/';
            $Upload = new FileUpload;
            //设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
            $Upload -> set("path", $savePath);
            $Upload -> set("maxsize", 10000000);
            $Upload -> set("allowtype", array("gif", "png", "jpg","jpeg"));
            $Upload -> set("israndname", true);
            if($Upload -> upload($key)) {
                $saveName = $Upload->getFileName();
            } 
            else {
                $getErrorMsg = $Upload->getErrorMsg();
                if(gettype($getErrorMsg) == 'string'){
                    $getErrorMsg = preg_replace('/\<\w+\s\w+.{7}/', ' ', $getErrorMsg);
                    $getErrorMsg = preg_replace('/\<\/.{5}/', ' ', $getErrorMsg);
                    $getErrorMsg = preg_replace('/\<\w{2}\>/', '', $getErrorMsg);
                    $this->reply('上传失败,'.$getErrorMsg);
                }
                elseif(gettype($getErrorMsg) == 'array'){
                    foreach ($getErrorMsg as $key => $value) {
                        $value = preg_replace('/\<\w+\s\w+.{7}/', ' ', $value);
                        $value = preg_replace('/\<\/.{5}/', ' ', $value);
                        $value = preg_replace('/\<\w{2}\>/', '', $value);
                        $getErrorMsg[$key] = $value;
                    }
                    $rs = [];
                    foreach ($getErrorMsg as $key => $value) {
                        $temp = explode(' ', $value);
                        $rs[$key]['filename'] = $temp[1];
                        $rs[$key]['error'] = $value;
                    }
                    $this->reply('上传失败',$getErrorMsg);
                }
                return;
            }
            if(is_array($saveName)){
                foreach ($saveName as $key => $value) {
                    $rs[] = $netPath.$value;
                }
            }
            else{
               isset($saveName) && $rs = $netPath.$saveName; 
            }
        }
        else{
            $this->reply('缺少上传文件');
            return;
        }
        $this->success('成功',$rs);
        return;
    }
    public function uploadClubImage(){
        $this->load->model('Common_model');
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        $front = $this->getParam('front');
        $back = $this->getParam('back');
        $yan_image = $this->getParam('yan_code');
        if(empty($yan_code)){
            $yan_code = '';
        }
        if(empty($sid) || empty($front) || empty($back) || empty($yan_image)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['data'] = json_encode(array('front'=>$front,'back'=>$back,'yan_image'=>$yan_image));
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $insertData['type'] = 'id_number';
        $this->Common_model->setTable('tbl_image');
        $info = $this->Common_model->fetchOne(array('user_id'=>$user_id,'type'=>'id_number'));
        if(empty($info)){
            $flag = $this->Common_model->insertData($insertData);
        }
        elseif($info['type'] == 'id_number'){
            $flag = $this->Common_model->updateData($insertData,array('id'=>$info['id']));
            $imageData = json_decode($info['data'],true);
            foreach ($imageData as $key => $value) {
                $Arr = parse_url($value);
                // @unlink('/var/www/download'.$Arr['path']);
            }
        }
        elseif($info['type'] == 'yan_code'){
            $flag = $this->Common_model->updateData($insertData,array('id'=>$info['id']));
        }
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
        }
    }
    public function getClubImage(){
        $this->load->model('Common_model');
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        if(empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $this->Common_model->setTable('tbl_image');
        $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
        $this->success('成功',json_decode($info['data']));
    }
    public function modifyInfo(){
        $this->load->model('Common_model');
        $this->load->model('session_model');
        $this->load->model("verifyphone_model");
        $data = [];
        $temp = $this->input->post();
        foreach ($temp as $key => $value) {
            $data[$key] = $value;
        }
        $temp = $this->input->get();
        foreach ($temp as $key => $value) {
            $data[$key] = $value;
        }
        $sid = $data['sid'];
        $type = $data['type'];
        $id = $data['id'];
        $phone = $data['phone'];
        $code = $data['code'];
        $updateData = [];
        $updateData['refuse'] = 0;
        if(empty($sid) || empty($type) || empty($id)){
            $this->error('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->error('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            parent::output(701);
        }
        $send_time = $this->verifyphone_model->get_RegisterTimeByPhoneAndCode($phone, $code);
        if(time() - strtotime($send_time) > 5 * 60) {
            parent::output(801);
        }
        switch ($type) {
            case 'club':
                $this->Common_model->setTable('tbl_club');
                $info = $this->Common_model->fetchOne(array('id'=>$id));
                if(empty($info)){
                    $this->error('找不到相关信息');
                    return;
                }
                if($sessionInfo['user_id'] != $info['user_id']){
                    $this->error('该身份不属于该ID');
                    return;
                }
                if($info['refuse'] == 0){
                    $this->error('无编辑权限');
                    return;
                }
                if(!empty($updateData['phone'])){
                    $info = $this->Common_model->fetchOne(array('phone'=>$updateData['phone']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('手机号码已使用');
                        return;
                    }
                }
                if(!empty($updateData['id_number'])){
                    $info = $this->Common_model->fetchOne(array('id_number'=>$updateData['id_number']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('身份证已使用');
                        return;
                    }
                }
                //重取身份证
                $this->Common_model->setTable('tbl_image');
                $image = $this->Common_model->fetchAll(array('user_id'=>$info['user_id']));
                if(!empty($image)){
                    foreach ($image as $key => $value) {
                        if($value['type'] == 'id_number'){
                            $updateData['id_number_image'] = $value['data'];
                        }
                    }
                }
                $this->Common_model->setTable('tbl_club');
                $updateData['name'] = $data['name'];
                $updateData['view_name'] = $data['view_name'];
                $updateData['phone'] = $data['phone'];
                $updateData['id_number'] = $data['id_number'];
                $updateData['area_id'] = $data['area_id'];
                $updateData['city'] = $data['city'];
                $updateData['address'] = $data['address'];
                $updateData['area_code'] = $data['area_code'];
                $updateData['yan_code'] = $data['yan_code'];
                $updateData['manager_name'] = $data['manager_name'];
                $updateData['manager_id'] = $data['manager_id_number'];
                $updateData['status'] = 0;
                $updateData['question'] = 0;
                unset($updateData['refuse']);
                $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
                if(!$flag){
                    $this->error('编辑失败');
                    return;
                }
                break;
            case 'manager':
                $this->Common_model->setTable('tbl_consumer');
                $info = $this->Common_model->fetchOne(array('id'=>$id));
                if(empty($info)){
                    $this->error('找不到相关信息,code:1');
                    return;
                }
                if($sessionInfo['user_id'] != $info['consumer_userid']){
                    $this->error('该身份不属于该ID,code:2');
                    return;
                }
                if($info['refuse'] == 0){
                    $this->error('无编辑权限,code:3');
                    return;
                }
                if(!empty($updateData['phone'])){
                    $info = $this->Common_model->fetchOne(array('phone'=>$updateData['phone']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('手机号码已使用,code:4');
                        return;
                    }
                }
                if(!empty($updateData['id_number'])){
                    $info = $this->Common_model->fetchOne(array('id_number'=>$updateData['id_number']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('身份证已使用,code:5');
                        return;
                    }
                }
                $updateData['phone'] = $data['phone'];
                $updateData['manager_id'] = $data['phone'];
                $updateData['id_number'] = $data['id_number'];
                $updateData['name'] = $data['manager_name'];
                // $updateData['area_managerid'] = $data['area_managerid'];
                // $updateData['area_managername'] = $data['area_managername'];
                $updateData['area_id'] = $data['area_id'];
                $updateData['city'] = $data['city'];
                $updateData['address'] = $data['address'];
                $updateData['area_code'] = $data['area_code'];
                $updateData['status'] = 0;
                $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
                if(!$flag){
                    $this->error('编辑失败,code:6');
                    return;
                }
                break;
            case 'area_manager':
                $this->Common_model->setTable('tbl_area_manager');
                $info = $this->Common_model->fetchOne(array('id'=>$id));
                if(empty($info)){
                    $this->error('找不到相关信息');
                    return;
                }
                if($sessionInfo['user_id'] != $info['user_id']){
                    $this->error('该身份不属于该ID');
                    return;
                }
                if($info['refuse'] == 0){
                    $this->error('无编辑权限');
                    return;
                }
                if(!empty($updateData['phone'])){
                    $info = $this->Common_model->fetchOne(array('phone'=>$updateData['phone']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('手机号码已使用');
                        return;
                    }
                }
                if(!empty($updateData['id_number'])){
                    $info = $this->Common_model->fetchOne(array('id_number'=>$updateData['id_number']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('身份证已使用');
                        return;
                    }
                }
                $updateData['phone'] = $data['phone'];
                $updateData['id_number'] = $data['id_number'];
                $updateData['name'] = $data['area_manager_name'];
                $updateData['bazaar_phone'] = $data['bazaar_phone'];
                $updateData['bazaar_name'] = $data['bazaar_name'];
                $updateData['area_id'] = $data['area_id'];
                $updateData['city'] = $data['city'];
                $updateData['address'] = $data['address'];
                $updateData['area_code'] = $data['area_code'];
                $updateData['status'] = 0;
                $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
                if(!$flag){
                    $this->error('编辑失败');
                    return;
                }
                break;
            case 'bazaar_manager':
                $this->Common_model->setTable('tbl_bazaar_manager');
                $info = $this->Common_model->fetchOne(array('id'=>$id));
                if(empty($info)){
                    $this->error('找不到相关信息');
                    return;
                }
                if($sessionInfo['user_id'] != $info['user_id']){
                    $this->error('该身份不属于该ID');
                    return;
                }
                if($info['refuse'] == 0){
                    $this->error('无编辑权限');
                    return;
                }
                if(!empty($updateData['phone'])){
                    $info = $this->Common_model->fetchOne(array('phone'=>$updateData['phone']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('手机号码已使用');
                        return;
                    }
                }
                if(!empty($updateData['id_number'])){
                    $info = $this->Common_model->fetchOne(array('id_number'=>$updateData['id_number']));
                    if(!empty($info) && $info['id'] != $id){
                        $this->error('身份证已使用');
                        return;
                    }
                }
                $updateData['phone'] = $data['phone'];
                $updateData['id_number'] = $data['id_number'];
                $updateData['name'] = $data['area_manager_name'];
                $updateData['area_id'] = $data['area_id'];
                $updateData['city'] = $data['city'];
                $updateData['address'] = $data['address'];
                $updateData['area_code'] = $data['area_code'];
                $updateData['status'] = 0;
                $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
                if(!$flag){
                    $this->error('编辑失败');
                    return;
                }
                break;
            default:
                # code...
                break;
        }
        $this->successForModify('成功');
    }
    public function modifyQuestions(){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->load->model('session_model');
        $this->load->model('audit_model');
        $this->load->model('auditconfig_model');

        $data = [];
        $temp = $this->input->post();
        foreach ($temp as $key => $value) {
            $data[$key] = $value;
        }
        $temp = $this->input->get();
        foreach ($temp as $key => $value) {
            $data[$key] = $value;
        }
        $answer = $this->post_input('answer');
        $sid = $data['sid'];
        unset($data);
        if(empty($sid)){
            $this->error('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->error('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $auditInfo = $this->audit_model->getByUserid($user_id);
        if (!empty($auditInfo)) {
            $flag = $this->audit_model->deleteData(array('user_id'=>$user_id));
        }
        if (empty($answer) || !is_array($answer)) {
            $this->error('缺少参数');
            return;
        }
        $data['kind'] = 1;
        $data['status'] = 0;
        $data['is_marked'] = 0;
        $data['user_id'] = $user_id;
        $num = 1;
        foreach ($answer as $n => $v) {
            $config = $this->auditconfig_model->getById($n);
            $data['attribute' . ($num)] = $v . "|" . $config['attr_label'];
            $num++;
        }
        $this->audit_model->wechat_insert($data);
        //添加之后将店铺问卷状态置为1
        $flag = $this->club_model->updateData(array('question'=>1,'refuse'=>0),array('user_id'=>$user_id));
        $url = base_url().'resources/app/shopinfo.html?sid=' . $sid;
        parent::wechatAlert('注册成功，店铺审核中！' ,$url);
    }
    public function getDetail(){
        $this->load->model('session_model');
        $this->load->model("Common_model");
        $sid = $this->getParam('sid');
        $type = $this->getParam('type');
        if(empty($sid) || empty($type)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';

            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        switch ($type) {
            case 'club':
                $this->Common_model->setTable('tbl_club');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                if(!empty($info)){
                    if(!empty($info['id_number_image'])){
                        $info['id_number_image'] = json_decode($info['id_number_image'],true);
                    }
                }
                break;
            case 'manager':
                $this->Common_model->setTable('tbl_consumer');
                $info = $this->Common_model->fetchOne(array('consumer_userid'=>$user_id));
                break;
            case 'area_manager':
                $this->Common_model->setTable('tbl_area_manager');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                break;
            case 'bazaar_manager':
                $this->Common_model->setTable('tbl_bazaar_manager');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                break;
            default:
                # code...
                break;
        }
        if(!empty($info)){
            if(!empty($info)){
                $this->Common_model->setTable('tbl_new_area');
                $province = $this->Common_model->fetchOne(array('area_id'=>$info['area_id']));
                $info['province'] = $province['name'];
            }
            else{
                $info['province'] = '';
            }
        }
        $this->success('成功',$info);
    }
    public function getQuestion(){
        $rs = [];
        $this->load->model('Common_model');
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        if(empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $this->Common_model->setTable('tbl_audit');
        $info = $this->Common_model->fetchAll(array('user_id'=>$user_id));
        if(!empty($info)){
            if(count($info) > 1){
                $info = array_pop($info);
            }
            else{
                $info = $info[0];
            }
            foreach ($info as $key => $value) {
                if(strpos($key, 'attribute') !== false && !empty($value)){
                    $key = substr($key, -1);
                    $rs[$key] = substr($value, 0,strpos($value, '|'));
                }
            }
        }
        else{
            $rs = [];
        }
        $this->success('成功',$rs);
    }
    public function uploadBase64(){
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        $type = $this->getParam('type');
        $base64= $this->getParam('base64');
        if(empty($sid) || empty($type) || empty($base64)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('无此用户');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $rs = [];
        $format = false;
        $format === false && strpos($base64, 'data:image/jpg;base64,') !== false ? ( $format = 'jpg') && $base64 = str_replace('data:image/jpg;base64,', '', $base64) : '';
        $format === false && strpos($base64, 'data:image/png;base64,') !== false ? ( $format = 'png') && $base64 = str_replace('data:image/png;base64,', '', $base64) : '';
        $format === false && strpos($base64, 'data:image/jpeg;base64,') !== false ? ( $format = 'jpeg') && $base64 = str_replace('data:image/jpeg;base64,', '', $base64) : '';
        $format === false && strpos($base64, 'data:image/gif;base64,') !== false ? ( $format = 'gif') && $base64 = str_replace('data:image/gif;base64,', '', $base64) : '';
        if($format === false){
            $this->reply('不支持的格式');
            return;
        }
        switch ($type) {
            case 'id_number':
                $savePath = $this->file_id_number;
                $netPath = $this->url_id_number;
                break;
            case 'yan_code':
                $savePath = $this->file_yan_code;
                $netPath = $this->url_yan_code;
                break;
            case 'lottery_papers':
                $savePath = $this->file_lottery_papers;
                $netPath = $this->url_lottery_papers;
                break;
            case 'receipt':
                $savePath = $this->file_receipt;
                $netPath = $this->url_receipt;
                break;
            
            default:
                $savePath = $this->file_dir_base;
                $netPath = $this->url_base;
                break;
        }
        $filename = date('YmdHis').gen_rand_num(6).".{$format}";
        $savePath .= date('Y_m').'/';
        is_dir($savePath) || mkdir($savePath,0777,true);
        $savePath .= $filename;
        $netPath .= date('Y_m').'/'.$filename;
        $flag = @file_put_contents($savePath, base64_decode($base64));
        if($flag){
            $this->success('成功',$netPath);
        }
        else{
            $this->reply('上传失败');
        }
    }
    public function successForModify($msg = '成功'){
        $status = array(
               'status' => array(
                   'succeed' => 1,
                   'error_code' =>0,
                   'error_desc' => $msg
               )
           );
           echo json_capsule($status);
           exit();
    }
    public function error($msg){
        $status = array(
               'status' => array(
                   'succeed' => 0,
                   'error_code' =>0,
                   'error_desc' => $msg
               )
           );
           echo json_capsule($status);
           exit();
    }
    public function wechatBindApp($user_id,$phone){
        // return true;
        $this->load->model('user_model');
        $this->load->model('Common_model');
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
                //判断用户是否存在身份
                $this->Common_model->setTable('tbl_club');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                if(!empty($info)){
                    return true;
                }
                $this->Common_model->setTable('tbl_consumer');
                $info = $this->Common_model->fetchOne(array('consumer_userid'=>$user_id));
                if(!empty($info)){
                    return true;
                }
                $this->Common_model->setTable('tbl_area_manager');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                if(!empty($info)){
                    return true;
                }
                $this->Common_model->setTable('tbl_bazaar_manager');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                if(!empty($info)){
                    return true;
                }
                $this->Common_model->setTable('tbl_lottery_manager');
                $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
                if(!empty($info)){
                    return true;
                }
                $flag =  $this->user_model->deleteData(array('id'=>$user_id));
                file_put_contents($this->logPath.'deleteUser.log', date("Y-m-d H:i:s").PHP_EOL.var_export($userInfo,true).PHP_EOL,FILE_APPEND|LOCK_EX);
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
    public function bindBankCard(){
        $this->load->model('session_model');
        $this->load->model('club_model');
        $sid = $this->getParam('sid');
        $name = $this->getParam('name');
        $bank_name= $this->getParam('bank_name');
        $bank_card_id= $this->getParam('bank_card_id');
        if(empty($sid) || empty($name) || empty($bank_name) || empty($bank_card_id)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('未知错误，code：2');
            return;
        }

        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $clubInfo = $this->club_model->fetchOne(array('user_id'=>$user_id));
        if(empty($clubInfo)){
            $this->reply('未知错误，code：1');
            return;
        }
        if($clubInfo['name'] != $name){
            $this->reply('姓名与注册店铺时不一致');
            return;
        }
        $updateData = [];
        $updateData['bank_name'] = $bank_name;
        $updateData['bank_card_id'] = $bank_card_id;
        $updateData['step'] = 3;
        $flag = $this->club_model->updateData($updateData,array('user_id'=>$user_id));
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
        }
    }
}
