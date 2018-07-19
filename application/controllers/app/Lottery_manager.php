<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
class Lottery_manager extends Base_AppController
{
    private $logPath = '';
    function __construct()
    {
        parent::__construct();
        $this->logPath = get_instance()->config->config['log_path_file'];
    }
    public function lotteryBind(){
        $this->load->model("Common_model");
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        $name = $this->getParam('name');
        $phone = $this->getParam('phone');
        $lottery_papers = $this->getParam('lottery_papers');
        $lottery_papers_image = $this->getParam('lottery_papers_image');
        if(empty($sid) || empty($name) || empty($phone) || empty($lottery_papers) || empty($lottery_papers_image)){
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
        $this->Common_model->setTable('tbl_club');
        $clubInfo = $this->Common_model->fetchOne(array('phone'=>$phone));
        if(empty($clubInfo)){
            $this->reply('找不到店铺，请核对信息');
            return;
        }
        if($clubInfo['status'] == 0){
            $this->reply('该店铺未通过客户经理审核');
            return;
        }
        if($clubInfo['name'] != $name){
            $this->reply('店主姓名不匹配，请核对信息');
            return;
        }
        $club_id = $clubInfo['id'];
        // if($clubInfo['status'] == 1){
        //     $this->reply('该店铺已通过审核');
        //     return;
        // }
        //取代销证信息
        $this->Common_model->setTable('tbl_lottery_papers');
        $lottery_papersInfo = $this->Common_model->fetchOne(array('club_id'=>$club_id));
        if(!empty($lottery_papersInfo) && $lottery_papersInfo['valid'] == 1){
            $this->reply('该店铺已经绑定过代销证');
            return;
        }
        $club_id = $clubInfo['id'];
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['club_id'] = $club_id;
        $insertData['lottery_papers'] = $lottery_papers;
        $insertData['lottery_papers_image'] = $lottery_papers_image;
        $insertData['name'] = $name;
        $insertData['phone'] = $phone;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_lottery_papers');
        $flag = $this->Common_model->insertData($insertData);
        if(!$flag){
            $this->reply('绑定信息失败，请联系管理人员。code:1');
            file_put_contents($this->logPath.'insert_lottery.log', date("Y-m-d H:i:s").PHP_EOL.var_export($insertData).PHP_EOL,FILE_APPEND|LOCK_EX);
            return;
        }
        //改变店铺状态
        $this->Common_model->setTable('tbl_redeemConfig');
        $user_id = $clubInfo['user_id'];
        $area_id = $clubInfo['area_id'];
        $redeemInfo = $this->Common_model->fetchOne(array('province_id'=>$area_id));
        if(!empty($redeemInfo)){
            $stationId = $redeemInfo['stationId'];
        }
        $updateData = [];
        $updateData['status'] = 1;
        $updateData['refuse'] = 0;
        $updateData['lottery_license'] = $lottery_papers;
        $updateData['audit_time_lottery'] = date("Y-m-d H:i:s");
        $this->Common_model->setTable('tbl_club');
        $flag3 = $this->Common_model->updateData($updateData,array('id'=>$clubInfo['id']));
        //给用户分配站点
        $updateData = [];
        $updateData['stationId'] = $stationId;
        $this->Common_model->setTable('tbl_user');
        $flag3 = $this->Common_model->updateData($updateData,array('id'=>$user_id));
        $flag = $this->ajaxSend($clubInfo['phone'],'236001');
        $this->success('成功');
    }
    public function modifyLotteryBind(){
        $this->load->model("Common_model");
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        $name = $this->getParam('name');
        $phone = $this->getParam('phone');
        $lottery_papers = $this->getParam('lottery_papers');
        $lottery_papers_image = $this->getParam('lottery_papers_image');
        $id = $this->getParam('id');
        if(empty($sid) || empty($name) || empty($phone) || empty($lottery_papers) || empty($lottery_papers_image)){
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
        $this->Common_model->setTable('tbl_club');
        $clubInfo = $this->Common_model->fetchOne(array('phone'=>$phone));
        if(empty($clubInfo)){
            $this->reply('找不到店铺，请核对信息');
            return;
        }
        if($clubInfo['status'] == 0){
            $this->reply('该店铺未通过客户经理审核');
            return;
        }
        if($clubInfo['name'] != $name){
            $this->reply('店主姓名不匹配，请核对信息');
            return;
        }
        // if($clubInfo['status'] == 1){
        //     $this->reply('该店铺已通过审核');
        //     return;
        // }
        $club_id = $clubInfo['id'];
        $updateData = [];
        $updateData['club_id'] = $club_id;
        $updateData['lottery_papers'] = $lottery_papers;
        $updateData['lottery_papers_image'] = $lottery_papers_image;
        $updateData['name'] = $name;
        $updateData['phone'] = $phone;
        $this->Common_model->setTable('tbl_lottery_papers');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if(!empty($info) && $info['id'] != $id){
            $this->reply('您没有权限编辑此条');
            return;
        }
        $info = $this->Common_model->fetchOne(array('lottery_papers'=>$lottery_papers));
        if(!empty($info) && $info['id'] != $id){
            $this->reply('该证号已经分配');
            return;
        }
        $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
        if(!$flag){
            $this->reply('绑定信息失败，请联系管理人员。code:1');
            file_put_contents($this->logPath.'update_lottery.log', date("Y-m-d H:i:s").PHP_EOL.var_export($updateData).PHP_EOL,FILE_APPEND|LOCK_EX);
            return;
        }
        //改变店铺状态
        $this->Common_model->setTable('tbl_redeemConfig');
        $user_id = $clubInfo['user_id'];
        $area_id = $clubInfo['area_id'];
        $redeemInfo = $this->Common_model->fetchOne(array('province_id'=>$area_id));
        if(!empty($redeemInfo)){
            $stationId = $redeemInfo['stationId'];
        }
        $updateData = [];
        $updateData['status'] = 1;
        $updateData['refuse'] = 0;
        $updateData['lottery_license'] = $lottery_papers;
        $updateData['audit_time_lottery'] = date("Y-m-d H:i:s");
        $this->Common_model->setTable('tbl_club');
        $flag3 = $this->Common_model->updateData($updateData,array('id'=>$clubInfo['id']));
        //给用户分配站点
        $updateData = [];
        $updateData['stationId'] = $stationId;
        $this->Common_model->setTable('tbl_user');
        $flag3 = $this->Common_model->updateData($updateData,array('id'=>$user_id));
        // $flag = $this->ajaxSend($clubInfo['phone'],'236001');
        $this->success('成功');
    }
    public function getLotteryDetail(){
        $this->load->model("Common_model");
        $this->load->model('session_model');
        $this->load->model('club_model');
        $this->load->model('area_model');
        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        if(empty($sid) || empty($id)){
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
        $filters = [];
        $filters['id'] = $id;
        $filters['valid'] = 1;
        $this->Common_model->setTable('tbl_lottery_papers');
        $info = $this->Common_model->fetchOne($filters);
        if(empty($info)){
            $this->reply('未知错误，code:1');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('id'=>$info['club_id']));
        if(empty($clubInfo)){
            $this->reply('未知错误，code:2');
            return;
        }
        $province = $this->area_model->fetchAll(array('id'=>$clubInfo['area_id']));
        if(empty($province)){
            $this->reply('未知错误，code:3');
            return;
        }
        $province = $province[0]['name'];
        $info['address'] = $province.$clubInfo['city'].$clubInfo['address'];
        $this->success('成功',$info);
    }
    public function lotteryList(){
        $this->load->model("Common_model");
        $this->load->model('session_model');
        $this->load->model('club_model');
        $this->load->model('area_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
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
        if(empty($pageIndex) || empty($entryNum)){
            $pageIndex = 1;
            $entryNum = 10;
        }
        $filters = [];
        $orders = [];
        $filters['user_id'] = $user_id;
        $filters['valid'] = 1;
        if(empty($orders)){
            $orders['create_date'] = 'desc';
        }
        $this->Common_model->setTable('tbl_lottery_papers');
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $pageIndex, $entryNum);
        $provinces = $this->area_model->fetchAll(array('type'=>1));
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        $ids = [];
        foreach ($rsltList as $key => $value) {
            $ids[] = $value['club_id'];
        }
        $ids = implode(',', $ids);
        $filters = [];
        if(!empty($ids)){
            $sql = "select * from tbl_club where id in ({$ids})";
            $clubList = $this->Common_model->queryAll($sql);
        }
        else{
            $clubList = [];
        }
        $clubTemp = [];
        foreach ($clubList as $key => $value) {
            $clubTemp[$value['id']] = $value;
        }
        foreach ($rsltList as $key => $value) {
            $rsltList[$key]['address'] = @($provinces_temp[$clubTemp[$value['club_id']]['area_id']].$clubTemp[$value['club_id']]['city'].$clubTemp[$value['club_id']]['address']);
        }
        $rs['length'] = $totalCount;
        $rs['list'] = $rsltList;
        $this->success('成功',$rs);
    }
    public function delLottery(){
        $this->load->model('Common_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        if(empty($sid) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_papers');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if(empty($info)){
            $this->reply('未知错误，code:1');
            return;
        }
        if($info['user_id'] != $user_id){
            $this->reply('您没有权限删除');
            return;
        }
        $updateData = [];
        $updateData['valid'] = 0;
        $filters = [];
        $filters['id'] = $id;
        $flag = $this->Common_model->updateData($updateData,$filters);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
            return;
        }
    }
    public function addReceipt(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $receipt_image = $this->getParam('receipt_image');
        $notes = $this->getParam('notes');
        if(empty($receipt_image)){
            $this->reply('缺少参数');
            return;
        }
        // $receipt_image = json_encode($receipt_image);
        $insertData = [];
        $insertData['receipt_image'] = $receipt_image;
        $insertData['notes'] = $notes;
        $insertData['user_id'] = $user_id;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_lottery_receipt');
        $flag = $this->Common_model->insertData($insertData);
        $this->success('成功');
    }
    public function modifyReceipt(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $receipt_image = $this->getParam('receipt_image');
        $notes = $this->getParam('notes');
        $id = $this->getParam('id');
        if(empty($receipt_image) || empty($notes) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        // $receipt_image = json_encode($receipt_image);
        $updateData = [];
        $updateData['receipt_image'] = $receipt_image;
        $updateData['notes'] = $notes;
        $this->Common_model->setTable('tbl_lottery_receipt');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if($info['user_id'] != $user_id){
            $this->reply('您没有权限编辑此条');
            return;
        }
        $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
        $this->success('成功');
    }
    public function getReceiptDetail(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $id = $this->getParam('id');
        if(empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $filters = [];
        $filters['id'] = $id;
        $filters['valid'] = 1;
        $this->Common_model->setTable('tbl_lottery_receipt');
        $info = $this->Common_model->fetchOne($filters);
        $info['receipt_image'] = json_decode($info['receipt_image'],true);
        $this->success('成功',$info);
    }
    public function receiptList(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        if(empty($pageIndex) || empty($entryNum)){
            $pageIndex = 1;
            $entryNum = 10;
        }
        $filters = [];
        $orders = [];
        $filters['user_id'] = $user_id;
        $filters['valid'] = 1;
        if(empty($orders)){
            $orders['create_date'] = 'desc';
        }
        $user_id = $user_id;
        $this->Common_model->setTable('tbl_lottery_receipt');
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $pageIndex, $entryNum);
        foreach ($rsltList as $key => $value) {
            $rsltList[$key]['receipt_image'] = json_decode($value['receipt_image'],true);
        }
        $rs['length'] = $totalCount;
        $rs['list'] = $rsltList;
        $this->success('成功',$rs);
    }
    public function delReceipt(){
        $this->load->model('Common_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        if(empty($sid) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_receipt');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if(empty($info)){
            $this->reply('未知错误，code:1');
            return;
        }
        if($info['user_id'] != $user_id){
            $this->reply('您没有权限删除');
            return;
        }
        $updateData = [];
        $updateData['valid'] = 0;
        $filters = [];
        $filters['id'] = $id;
        $flag = $this->Common_model->updateData($updateData,$filters);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
            return;
        }
    }
    public function addLog(){
        $this->load->model('Common_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $log = $this->getParam('log');
        if(empty($sid) || empty($log)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_log');
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['log'] = $log;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $flag = $this->Common_model->insertData($insertData);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
        }
    }
    public function logList(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        if(empty($pageIndex) || empty($entryNum)){
            $pageIndex = 1;
            $entryNum = 10;
        }
        $filters = [];
        $orders = [];
        $filters['user_id'] = $user_id;
        $filters['valid'] = 1;
        if(empty($orders)){
            $orders['create_date'] = 'desc';
        }
        $user_id = $user_id;
        $this->Common_model->setTable('tbl_lottery_log');
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $pageIndex, $entryNum);
        $rs['length'] = $totalCount;
        $rs['list'] = $rsltList;
        $this->success('成功',$rs);
    }
    public function getLogDetail(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $id = $this->getParam('id');
        if(empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $filters = [];
        $filters['id'] = $id;
        $filters['valid'] = 1;
        $this->Common_model->setTable('tbl_lottery_log');
        $info = $this->Common_model->fetchOne($filters);
        $this->success('成功',$info);
    }
    public function delLog(){
        $this->load->model('Common_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        if(empty($sid) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_log');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if(empty($info)){
            $this->reply('未知错误，code:1');
            return;
        }
        if($info['user_id'] != $user_id){
            $this->reply('您没有权限删除');
            return;
        }
        $updateData = [];
        $updateData['valid'] = 0;
        $filters = [];
        $filters['id'] = $id;
        $flag = $this->Common_model->updateData($updateData,$filters);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
            return;
        }
    }
    public function modifyLog(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $log = $this->getParam('log');
        $id = $this->getParam('id');
        if(empty($log) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        // $receipt_image = json_encode($receipt_image);
        $updateData = [];
        $updateData['log'] = $log;
        $this->Common_model->setTable('tbl_lottery_log');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if($info['user_id'] != $user_id || $info['valid'] == 0){
            $this->reply('您没有权限编辑此条');
            return;
        }
        $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
        $this->success('成功');
    }

    public function addInterviewLog(){
        $this->load->model('Common_model');
        $rs = [];
        $club_id = $this->getParam('club_id');
        $images = $this->getParam('images');
        $question = $this->getParam('question');
        if(empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['club_id'] = $club_id;
        !empty($images) ? $insertData['images'] = $images : '';
        !empty($question) ? $insertData['question'] = $question : '';
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $flag = $this->Common_model->insertData($insertData);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
        }
    }
    public function modifyInterviewLog(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $id = $this->getParam('id');
        $images = $this->getParam('images');
        $question = $this->getParam('question');
        if(empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $updateData = [];
        $updateData['question'] = $question;
        $updateData['update_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if($info['user_id'] != $user_id || $info['valid'] == 0){
            $this->reply('您没有权限编辑此条');
            return;
        }
        $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
        $this->success('成功');
    }
    public function interviewLogList(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        $club_id = $this->getParam('club_id');
        if(empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        if(empty($pageIndex) || empty($entryNum)){
            $pageIndex = 1;
            $entryNum = 10;
        }
        $filters = [];
        $orders = [];
        $filters['user_id'] = $user_id;
        $filters['club_id'] = $club_id;
        $filters['valid'] = 1;
        if(empty($orders)){
            $orders['create_date'] = 'desc';
        }
        $user_id = $user_id;
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $pageIndex, $entryNum);
        foreach ($rsltList as $key => $value) {
            $rsltList[$key]['create_date'] = strtotime($value['create_date']).'000';
        }
        $rs['length'] = $totalCount;
        $rs['list'] = $rsltList;
        $this->success('成功',$rs);
    }
    public function getInterviewLogDetail(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $id = $this->getParam('id');
        if(empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $filters = [];
        $filters['id'] = $id;
        $filters['valid'] = 1;
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $info = $this->Common_model->fetchOne($filters);
        $info['create_date'] = strtotime($info['create_date']).'000';
        $this->success('成功',$info);
    }
    public function delInterviewLog(){
        $this->load->model('Common_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        if(empty($sid) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if(empty($info)){
            $this->reply('未知错误，code:1');
            return;
        }
        if($info['user_id'] != $user_id){
            $this->reply('您没有权限删除');
            return;
        }
        $updateData = [];
        $updateData['valid'] = 0;
        $updateData['delete_date'] = date('Y-m-d H:i:s');
        $filters = [];
        $filters['id'] = $id;
        $flag = $this->Common_model->updateData($updateData,$filters);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
            return;
        }
    }

    public function addInterviewImages(){
        $this->load->model('Common_model');
        $rs = [];
        $club_id = $this->getParam('club_id');
        $images = $this->getParam('images');
        if(empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_interview_images');
        if(!empty($images)){
            $images = json_decode($images,true);
            foreach ($images as $key => $value) {
                $temp = [];
                $insertData = [];
                $insertData['user_id'] = $user_id;
                $insertData['club_id'] = $club_id;
                $temp[] = $value;
                $insertData['images'] = json_encode($temp);
                $insertData['create_date'] = date('Y-m-d H:i:s');
                $flag = $this->Common_model->insertData($insertData);
            }
        }
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
        }
    }
    public function modifyInterviewImages(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $id = $this->getParam('id');
        $images = $this->getParam('images');
        if(empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $updateData = [];
        $updateData['images'] = $images;
        $updateData['update_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_lottery_interview_images');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if($info['user_id'] != $user_id || $info['valid'] == 0){
            $this->reply('您没有权限编辑此条');
            return;
        }
        $flag = $this->Common_model->updateData($updateData,array('id'=>$id));
        $this->success('成功');
    }
    public function interviewImagesList(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        $club_id = $this->getParam('club_id');
        if(empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        if(empty($pageIndex) || empty($entryNum)){
            $pageIndex = 1;
            $entryNum = 10;
        }
        $filters = [];
        $orders = [];
        $filters['user_id'] = $user_id;
        $filters['club_id'] = $club_id;
        $filters['valid'] = 1;
        if(empty($orders)){
            $orders['create_date'] = 'desc';
        }
        $user_id = $user_id;
        $this->Common_model->setTable('tbl_lottery_interview_images');
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $pageIndex, $entryNum);
        $rs['length'] = $totalCount;
        $rs['list'] = $rsltList;
        $this->success('成功',$rs);
    }
    public function getInterviewImagesDetail(){
        $this->load->model("Common_model");
        $user_id = $this->checkSid();
        $id = $this->getParam('id');
        if(empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $filters = [];
        $filters['id'] = $id;
        $filters['valid'] = 1;
        $this->Common_model->setTable('tbl_lottery_interview_images');
        $info = $this->Common_model->fetchOne($filters);
        $this->success('成功',$info);
    }
    public function delInterviewImages(){
        $this->load->model('Common_model');
        $rs = [];
        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        if(empty($sid) || empty($id)){
            $this->reply('缺少参数');
            return;
        }
        $user_id = $this->checkSid();
        $this->Common_model->setTable('tbl_lottery_interview_images');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        if(empty($info)){
            $this->reply('未知错误，code:1');
            return;
        }
        if($info['user_id'] != $user_id){
            $this->reply('您没有权限删除');
            return;
        }
        $updateData = [];
        $updateData['valid'] = 0;
        $updateData['delete_date'] = date('Y-m-d H:i:s');
        $filters = [];
        $filters['id'] = $id;
        $flag = $this->Common_model->updateData($updateData,$filters);
        if($flag){
            $this->success('成功');
        }
        else{
            $this->reply('失败');
            return;
        }
    }
}