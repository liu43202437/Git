<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

/**
 * Class Lottery
 *
 */
class Lottery extends Base_AppController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("session_model");
        $this->load->model("lottery_manager");
    }
    /**
     * 点击后检测状态
     */
    public function check_lottery(){
        $sid=$this->getParam('sid');
        $sessionid=$this->session_model->getInfoBySId($sid);
        if (empty($sessionid)){
            parent::wechatAlert('请登录！');
        }
        $userid=$sessionid['user_id'];
        $info=$this->lottery_manager->fetchOne(array('user_id'=>$userid));
        if(!empty($info)){
            if ($info['status'] == 0) {
                $url = base_url().'resources/app/vc-visiter-manager-info.html?sid=' . $sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
            }
            elseif($info['status'] == 1){
                $url = base_url().'resources/app/vc-visiter-manager-info.html?sid=' . $sid;
                header("location:" . $url);
            }
        }
        else{
            $url = base_url().'resources/app/vc-visiter-manager-zc.html';
            header("location:" . $url . "?sid=" . $sid  ."&refuse=". $info['refuse']);
        }
    }

    public function check_lottery_wechat(){
        $sid=$this->getParam('sid');
        $sessionid=$this->session_model->getInfoBySId($sid);
        if (empty($sessionid)){
            parent::wechatAlert('请登录！');
        }
        $userid=$sessionid['user_id'];
        $info=$this->lottery_manager->fetchOne(array('user_id'=>$userid));
        if(!empty($info)){
            if ($info['status'] == 0) {
                $url = base_url().'resources/proxy-sell/index.html#/info?sid='.$sid;
                parent::wechatAlert('您的身份正在审核中，请耐心等待',$url);
            }
            elseif($info['status'] == 1){
                $url = base_url().'/resources/proxy-sell/index.html#/card-list?sid='.$sid;
                header("location:" . $url);
            }
        }
        else{
            $url = base_url().'resources/proxy-sell/index.html#/register';
            header("location:" . $url . "?sid=" . $sid  ."&refuse=". $info['refuse']);
        }
    }

    /**
     * 注册
     */
    public function enroll_lottery(){
        $sid=$this->getParam('sid');
        $name=$this->getParam('name');
        $componey=$this->getParam('componey');
        $phone=$this->getParam('phone');
        $id_number=$this->getParam('id_number');
        $area_id=$this->getParam('area_id');
        $city=$this->getParam('city');
        $address=$this->getParam('address');
        $area_code=$this->getParam('area_code');
        $code=$this->getParam('code');

        if (empty($sid) || empty($componey) || empty($name) || empty($id_number) || empty($phone) ||empty($code) || empty($area_id) || empty($city) || empty($address) || empty($area_code) ) {
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
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::output(102);
        }
        $user_id = $sessionInfo['user_id'];
        //验证手机号码身份证是否被使用
        $info = $this->lottery_manager->fetchOne(array('user_id'=>$user_id));
        if(!empty($info)){
            parent::output(19);
        }
        $info = $this->lottery_manager->fetchOne(array('phone'=>$phone));
        if(!empty($info)){
            parent::output(3);
        }
        $info = $this->lottery_manager->fetchOne(array('id_number'=>$id_number));
        if(!empty($info)){
            parent::output(13);
        }
        $user_id = $user_id;
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['id_number'] = $id_number;
        $data['company'] = $componey;
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $user_id;
        $data['area_id'] = $area_id;
        $data['city'] = $city;
        $data['address'] = $address;
        $data['area_code'] = $area_code;
        if (!$this->lottery_manager->insertData($data)) {
            parent::output(99);
        }
        $out = array();
        parent::output($out);
    }
    public function send_lotteryinfo(){
        $res = [];
        $sid=$this->getParam('sid');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            parent::output(102);
        }
        $user_id=$sessionInfo['user_id'];
        $sql = "select * from tbl_lottery_manager where user_id='{$user_id}'";
        $info = $this->lottery_manager->queryAll($sql);
        if(!empty($info)){
            $res=$info[0];
            $data['name']=$res['name'];
            $data['id_number']=$res['id_number'];
            $data['address']=$res['address'];
            $data['componey']=$res['company'];
            $data['phone']=$res['phone'];
        }
        parent::output($res);
    }


    /**
     * 返回访销经理的店铺
     */
    public function sendclub(){
        $sid=$this->getParam('sid');
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        $seach=$this->getParam('search');
        $arr=array('sid','pageIndex','entryNum','search');
        foreach ($_REQUEST as $key => $item) {
            if (!in_array($key,$arr)){
                $this->reply('参数不正确');
                return;
            }
        }
        if (empty($entryNum)){
            $entryNum = 10 ;
        }
        $this->load->model('session_model');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = 2;
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $userId = $sessionInfo['user_id'];
        $this->load->model('lottery_manager');
        $count=$this->lottery_manager->countclub($seach,$userId);
        $count=$count[0]['number'];
        $res=$this->lottery_manager->club($seach,$userId,$pageIndex,$entryNum);
        $item=array();
        if (!empty($res)) {
            foreach ($res as $key => $re) {
                $item[$key]['name'] = $re['name'];
                $item[$key]['club_id'] = $re['club_id'];
                $item[$key]['club_name'] = $re['view_name'];
                $item[$key]['phone'] = $re['phone'];
                $item[$key]['address'] = $re['address'];
            }
        }else{
            $item=[];
        }
        $this->countsuccess('成功',$item,$count);
    }


    /**
     * 店铺详情
     */
    public function club_detail(){
        $club_id=$this->getParam('club_id');
        $sid=$this->getParam('sid');
        $arr=array('club_id','sid');
        foreach ($_REQUEST as $key => $item) {
            if (!in_array($key,$arr)){
                $this->reply('参数不正确');
                return;
            }
        }
        $this->load->model('session_model');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = 2;
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $this->load->model('lottery_manager');
        $info=$this->lottery_manager->club_detail($club_id);
        if (empty($info)){
            $this->reply('无此店铺');return;
        }
        $items=array();
        foreach ($info as $key => $item) {
            $items[$key]['name']=$item['name'];
            $items[$key]['club_id']=$item['id'];
            $items[$key]['club_name']=$item['view_name'];
            $items[$key]['phone']=$item['phone'];
            $items[$key]['address']=$item['address'];
        }
        $this->success('成功',$items[0]);
    }

    /**
     * 打卡(开始，结束)
     */
    public function sign(){
        $sid=$this->getParam('sid');
        $long=$this->getParam('long');
        $lat=$this->getParam('lat');
        $club=$this->getParam('club_id');
        $status=$this->getParam('status');
        $this->load->model('session_model');
        $arr=array('sid','long','lat','club_id','status');
        foreach ($_REQUEST as $key => $item) {
            if (!in_array($key,$arr)){
                $this->reply('参数不正确');
                return;
            }
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = 2;
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $userId = $sessionInfo['user_id'];
        $this->load->model('lottery_manager');
        $info=$this->lottery_manager->checkclub($userId,$club);
        if ($info == 1){
            $this->reply("无此店铺管理权限");exit();
        }
        if (empty($sid) || empty($club) ||  empty($long) || empty($lat)){
            $this->reply("参数不全");exit();
        }
        if ($status != 1 && $status != 2 && $status !=3){
            $this->reply("参数不正确");exit();
        }
        $res=$this->lottery_manager->sign($userId,$club,$long,$lat,$status);
        if ($res){
            $this->success("成功",'打卡成功');
        }
    }

    /**
     * 是否打卡中判断
     */
    public function on_sign(){
        $sid=$this->getParam('sid');
        $arr=array('sid');
        foreach ($_REQUEST as $key => $item) {
            if (!in_array($key,$arr)){
                $this->reply('参数不正确');
                return;
            }
        }
        $this->load->model('session_model');
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = 2;
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $userId = $sessionInfo['user_id'];
        $this->load->model('lottery_manager');
        $info=$this->lottery_manager->on_sign($userId);
        $rs =[];
        $rs['code'] = 0;
        $rs['msg'] = '成功';
        if (empty($info)){
            $rs['data']['on_sign'] = false;
        }else{
            $rs['data']['on_sign'] = true;
            $rs['data']['club_id'] = $info[0]['club_id'];
            $rs['data']['begin_time'] = strtotime($info[0]['begin_time'])*1000;
        }
        echo json_encode($rs);
    }


    /**
     * 走访记录
     */
    public function lottery_record(){
        $sid=$this->getParam('sid');
        $search=$this->getParam('search');
        $this->load->model('session_model');
        $this->load->model('lottery_manager');
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        $arr=array('sid','pageIndex','entryNum','search');
        foreach ($_REQUEST as $key => $item) {
            if (!in_array($key,$arr)){
                $this->reply('参数不正确');
                return;
            }
        }
        if (empty($entryNum)){
            $entryNum = 10 ;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = 2;
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $userId = $sessionInfo['user_id'];
        $res=$this->lottery_manager->getrecord($userId,$search,$pageIndex,$entryNum);
        $count=$this->lottery_manager->countgetrecord($userId,$search);
        $item=array();
        if (!empty($res)) {
            foreach ($res as $key => $re) {
                $item[$key]['name'] = $re['name'];
                $item[$key]['club_id'] = $re['club_id'];
                $item[$key]['club_name'] = $re['view_name'];
                $item[$key]['begin_time'] = $re['begin_time'];
                $item[$key]['end_time'] = $re['end_time'];
                $item[$key]['begin_timestamp'] = strtotime($re['begin_time'])*1000;;
                $item[$key]['end_timestamp'] = strtotime($re['end_time'])*1000;;
            }
        }else{
            $item=[];
        }
        $this->countsuccess('成功',$item,$count[0]['number']);
    }
}