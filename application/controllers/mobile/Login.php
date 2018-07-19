<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
class Login extends Base_MobileController
{
    function __construct()
    {
        parent::__construct();
    }
    public function doLogin(){
        $this->load->model('session_model');
        $this->load->model('user_model');

        $phone = $this->getParam('phone');
        $pwd = $this->getParam('password');
        if(empty($phone) || empty($pwd) ){
            $this->reply('缺少参数');
            return;
        }
        $userInfo = $this->user_model->fetchOne(array('phone'=>$phone));
        if(empty($userInfo)){
            $this->reply('用户不存在');
            return;
        }
        if($userInfo['pwd'] != md5($pwd)){
            $this->reply('密码错误');
            return;
        }
        $sessionInfo = $this->session_model->getByUser($userInfo['id']);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        $updateData['login_date'] = date('Y-m-d H:i:s');
        $this->user_model->updateData($updateData,array('id'=>$userInfo['id']));
        //更新sid
        $updateData = [];
        $updateData['expire_date'] = date('Y-m-d H:i:s',time()+3600*24*7);
        $this->session_model->updateBySId($sessionInfo['session_id'],$updateData);
        $this->success('成功',array('sid'=>$sessionInfo['session_id']));
    }
    public function doLoginByCode(){
        $this->load->model('user_model');
        $this->load->model("verifyphone_model");
        $this->load->model('session_model');

        $phone = $this->getParam('phone');
        $code = $this->getParam('code');
        if(empty($phone) || empty($code) ){
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
        $userInfo = $this->user_model->fetchOne(array('phone'=>$phone));
        if(empty($userInfo)){
            $this->reply('用户不存在');
            return;
        }
        $user_id = $userInfo['id'];
        $sessionInfo = $this->session_model->getByUser($user_id);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        $updateData['login_date'] = date('Y-m-d H:i:s');
        $this->user_model->updateData($updateData,array('id'=>$userInfo['id']));
        //更新sid
        $updateData = [];
        $updateData['expire_date'] = date('Y-m-d H:i:s',time()+3600*24*7);
        $this->session_model->updateBySId($sessionInfo['session_id'],$updateData);
        $this->success('成功',array('sid'=>$sessionInfo['session_id']));
    }
    public function modifyPwd(){
        $this->load->model("verifyphone_model");
        $this->load->model('user_model');

        $phone = $this->getParam('phone');
        $pwd = $this->getParam('password');
        $pwd2 = $this->getParam('password2');
        $code = $this->getParam('code');
        if(empty($phone) || empty($pwd) || empty($pwd2) || empty($code) ){
            $this->reply('缺少参数');
            return;
        }
        if (!$this->verifyphone_model->is_exist_code($phone, $code)) {
            $this->reply('验证码错误');
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
        if(empty($userInfo)){
            $this->reply('该手机号码未注册');
            return;
        }
        $updateData = [];
        $updateData['pwd'] = md5($pwd);
        $flag = $this->user_model->updateData($updateData,array('id'=>$userInfo['id']));
        if($flag){
            $this->success('修改成功');
            return;
        }
        else{
            $this->reply('修改失败');
            return;
        }
    }
    public function checkSidExpire(){
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        if(empty($sid)){
            $this->reply('缺少sid');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid信息');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = '过期';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        //更新sid
        $updateData['expire_date'] = date('Y-m-d H:i:s',time()+3600*24*7);
        $this->session_model->updateBySId($sid,$updateData);
        $this->success('成功');
    }
}
