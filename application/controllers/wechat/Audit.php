<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/third_party/CCPRestSmsSDK.php";
class Audit extends Base_MobileController
{
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
        $this->filepath = get_instance()->config->config['log_path_file'];
    }
    public function audit_(){
        $this->load->model('user_model');
        $this->load->model('session_model');
        $this->load->model('club_model');
        $this->load->model('manager_model');
        $this->load->model("Area_manager_model");
        $this->load->model('Bazaar_manager_model');
        $this->load->model("Common_model");

        $rs = [];
        $sid = $this->getParam('sid');
        $identity = $this->getParam('identity');
        $type = $this->getParam('type');
        $pageIndex = $this->getParam('pageIndex');
        $entryNum = $this->getParam('entryNum');
        if(empty($sid) || empty($identity) || empty($type)){
            $this->reply('缺少参数');
            return;
        }
        if(empty($pageIndex) || empty($entryNum)){
            $pageIndex = 1;
            $entryNum = 10;
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
            case 'undone':
                $orders['id'] = 'DESC';
                break;
            case 'done':
                $orders['audit_time'] = 'DESC';
                break;
            case 'refuse':
                $orders['create_date'] = 'DESC';
                break;
            default:
                # code...
                break;
        }
        
        $start = $pageIndex;
        $quantity = (int)$entryNum;
        switch ($identity) {
            case 'manager':
                $info = $this->manager_model->fetchOne(array('consumer_userid'=>$user_id));
                if(empty($info)){
                    $this->reply('身份错误,code:1');
                    return;
                }
                if($info['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $phone = $info['phone'];
                $filters = array('manager_id'=>$phone);
                $filters['refuse'] = 0;
                if($type == 'done'){
                    $filters['status!='] = 0;
                }
                elseif($type == 'undone'){
                    $filters['status'] = 0;
                    $filters['question'] = 1;
                }
                if($type == 'refuse'){
                    $this->Common_model->setTable('tbl_appmsg');
                    $filters = [];
                    $filters['from'] = $info['consumer_userid'];
                    $filters['msgtype'] = 'club no pass';
                    $filters['valid'] = 1;
                    $length = $this->Common_model->getCount($filters);
                    $lists = $this->Common_model->getList($filters, $orders, $start, $quantity);
                    $clubLists = [];
                    foreach ($lists as $key => $value) {
                        $clubLists[$key] = json_decode($value['data'],true);
                        $clubLists[$key]['recordId'] = $value['id'];
                        $clubLists[$key]['audit_time'] = $value['create_date'];
                        $clubLists[$key]['reason'] = $value['msg'];
                    }
                }
                else{
                    $length = $this->club_model->getCount($filters);
                    $clubLists = $this->club_model->getList($filters, $orders, $start, $quantity); 
                }
                foreach ($clubLists as $key => $value) {
                    $clubLists[$key]['type'] = 'club';
                }
                $rs['length'] = $length;
                $rs['lists'] = $clubLists;
                break;
            case 'area_manager':
                $info = $this->Area_manager_model->fetchOne(array('user_id'=>$user_id));
                if(empty($info)){
                    $this->reply('身份错误,code:2');
                    return;
                }
                if($info['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $phone = $info['phone'];
                $filters = array('area_managerid'=>$phone);
                $filters['refuse'] = 0;
                if($type == 'done'){
                    $filters['status'] = 1;
                }
                elseif($type == 'undone'){
                    $filters['status'] = 0;
                }
                if($type == 'refuse'){
                    $this->Common_model->setTable('tbl_appmsg');
                    $filters = [];
                    $filters['from'] = $info['user_id'];
                    $filters['msgtype'] = 'manager no pass';
                    $filters['valid'] = 1;
                    $length = $this->Common_model->getCount($filters);
                    $lists = $this->Common_model->getList($filters, $orders, $start, $quantity);
                    $managerLists = [];
                    foreach ($lists as $key => $value) {
                        $managerLists[$key] = json_decode($value['data'],true);
                        $managerLists[$key]['recordId'] = $value['id'];
                        $managerLists[$key]['audit_time'] = $value['create_date'];
                        $managerLists[$key]['reason'] = $value['msg'];
                    }
                }
                else{
                    $length = $this->manager_model->getCount($filters);
                    $managerLists = $this->manager_model->getList($filters, $orders, $start, $quantity); 
                }
                foreach ($managerLists as $key => $value) {
                    $clubLists[$key]['type'] = 'manager';
                }
                $rs['length'] = $length;
                $rs['lists'] = $managerLists;
                break;
            case 'bazaar_manager':
                $info = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$user_id));
                if(empty($info)){
                    $this->reply('身份错误,code:3');
                    return;
                }
                if($info['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $phone = $info['phone'];
                $filters = array('bazaar_phone'=>$phone);
                $filters['refuse'] = 0;
                if($type == 'done'){
                    $filters['status'] = 1;
                }
                elseif($type == 'undone'){
                    $filters['status'] = 0;
                }
                if($type == 'refuse'){
                    $this->Common_model->setTable('tbl_appmsg');
                    $filters = [];
                    $filters['from'] = $info['user_id'];
                    $filters['msgtype'] = 'area_manager no pass';
                    $filters['valid'] = 1;
                    $length = $this->Common_model->getCount($filters);
                    $lists = $this->Common_model->getList($filters, $orders, $start, $quantity);
                    $areaManagerLists = [];
                    foreach ($lists as $key => $value) {
                        $areaManagerLists[$key] = json_decode($value['data'],true);
                        $areaManagerLists[$key]['recordId'] = $value['id'];
                        $areaManagerLists[$key]['audit_time'] = $value['create_date'];
                        $areaManagerLists[$key]['reason'] = $value['msg'];
                    }
                }
                else{
                    $length = $this->Area_manager_model->getCount($filters);
                    $areaManagerLists = $this->Area_manager_model->getList($filters, $orders, $start, $quantity);
                }
                foreach ($areaManagerLists as $key => $value) {
                    $clubLists[$key]['type'] = 'area_manager';
                }
                $rs['lists'] = $areaManagerLists;
                $rs['length'] = $length;
                break;
            default:
                # code...
                break;
        }
        if(!empty($rs['lists'])){
            $this->Common_model->setTable('tbl_new_area');
            foreach ($rs['lists'] as $key => $value) {
                if(!empty($value['area_id'])){
                    $province = $this->Common_model->fetchOne(array('area_id'=>$value['area_id']));
                    $rs['lists'][$key]['province'] = $province['name'];
                }
                else{
                    $rs['lists'][$key]['province'] = '';
                }
            }
            $this->success('成功',$rs);
        }
        else{
            $rs['lists'] = [];
            $this->success('成功',$rs);
            return;
        }
    }
    public function doAudit_(){
        // $this->ajaxSend('13739143260','223984');
    }
    public function pass(){
        $this->load->model('user_model');
        $this->load->model('session_model');
        $this->load->model('club_model');
        $this->load->model('manager_model');
        $this->load->model("Area_manager_model");
        $this->load->model('Bazaar_manager_model');
        $this->load->model('Common_model');
        $this->load->model('sendmsg_model');

        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        $type = $this->getParam('type');
        if(empty($sid) || empty($id) || empty($type)){
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
            $rs['msg'] = 'sid过期';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $userInfo = $this->user_model->getInfoById($user_id);
        switch ($type) {
            case 'club':
                //取客户经理信息
                $managerInfo = $this->manager_model->fetchOne(array('consumer_userid'=>$sessionInfo['user_id']));
                if(empty($managerInfo)){
                    $this->reply('身份错误,code:1');
                    return;
                }
                if($managerInfo['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $clubinfo = $this->club_model->fetchOne(array('id'=>$id));
                if(empty($clubinfo)){
                    $this->reply('店铺不存在');
                    return;
                }
                if($clubinfo['status'] == 1){
                    $this->reply('该店铺已通过审核');
                    return;
                }
                if($clubinfo['question'] != 1){
                    $this->reply('店铺未完成注册');
                    return;
                }
                if($managerInfo['phone'] != $clubinfo['manager_id']){
                    $this->reply('该店铺不属于该客户经理');
                    return;
                }
                $lottery_license = $this->getParam('lottery_license');
                $stationId = $this->getParam('stationId');
                empty($lottery_license) ? $lottery_license = '' : '';
                empty($stationId) ? $stationId = '45898888' : '';
                $info = $this->user_model->fetchOne(array('id'=>$user_id));
                $newPoint = $info['point'] + 10;
                $this->club_model->update($id, ['status' =>2,'lottery_license'=>$lottery_license,'audit_time'=>date('Y-m-d H:i:s')]);
                $flag = $this->user_model->updateData(array('point'=>$newPoint),array('id'=>$user_id));
                //加积分记录
                $insertData = [];
                $insertData['user_id'] = $user_id;
                $insertData['trade_no'] = '客户经理审核积分';
                $insertData['name'] = $clubinfo['name'];
                $insertData['add_time'] = time();
                $insertData['create_date'] = date('Y-m-d H:i:s');
                $insertData['address'] = $clubinfo['city'].$clubinfo['address'];
                $insertData['credits'] = 10;
                $insertData['status'] = 1;
                $insertData['type'] = 6;
                $this->Common_model->setTable('tbl_user_credits');
                $flag = $this->Common_model->insertData($insertData);
                $stationId = trim($stationId);  
                $where = array('id'=>$clubinfo['user_id']);
                $flag = $this->user_model->updateData(array('stationId'=>$stationId),$where);
                
                $this->add_log($managerInfo['id'],$managerInfo['name'],'零售店客户经理审核通过', $id);
                //发送短信
                // $modelId = '236406';
                // $mobile = $clubinfo['phone'];
                // $res = $this->ajaxSend($mobile,$modelId);
                //推送微信消息
                $clubOwner = $this->user_model->getInfoById($clubinfo['user_id']);
                $openid = $clubOwner['weixin'];
                $name = $clubinfo['name'];
                $phone = $clubinfo['phone'];
                $re = $this->sendmsg_model->passShop($openid,$name,$phone);

                if($flag){
                    $this->success('成功');
                }
                else{
                    $this->success('成功');
                }
                break;
            case 'manager':
                //取市场经理信息
                $AreaManagerInfo = $this->Area_manager_model->fetchOne(array('user_id'=>$sessionInfo['user_id']));
                if(empty($AreaManagerInfo)){
                    $this->reply('身份错误,code:2');
                    return;
                }
                if($AreaManagerInfo['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $managerInfo = $this->manager_model->fetchOne(array('id'=>$id));
                if(empty($managerInfo)){
                    $this->reply('客户经理不存在');
                    return;
                }
                if($managerInfo['status'] == 1){
                    $this->reply('该客户经理已通过审核');
                    return;
                }
                if($AreaManagerInfo['phone'] != $managerInfo['area_managerid']){
                    $this->reply('该客户经理不属于该市场经理');
                    return;
                }
                $this->manager_model->updateData( 'tbl_consumer',array('status'=>1,'audit_time'=>date('Y-m-d H:i:s')),array('id'=>$id));
                $this->managerCheck($id,1);
                $this->add_log($AreaManagerInfo['id'],$AreaManagerInfo['name'],'客户经理审核通过', $id);
                //发送短信
                $modelId = '236006';
                $mobile = $managerInfo['phone'];
                $res = $this->ajaxSend($mobile,$modelId);
                if($res['success']){
                    $this->success('发送短信通知成功');
                }
                else{
                    $this->success('发送短信通知失败');
                }
                break;
            case 'area_manager':
                //取区域经理信息
                $BazaarManagerInfo = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$sessionInfo['user_id']));
                if(empty($BazaarManagerInfo)){
                    $this->reply('身份错误,code:3');
                    return;
                }
                if($BazaarManagerInfo['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $AreaManagerInfo = $this->Area_manager_model->fetchOne(array('id'=>$id));
                if(empty($AreaManagerInfo)){
                    $this->reply('市场经理不存在');
                    return;
                }
                if($AreaManagerInfo['status'] == 1){
                    $this->reply('该市场经理已通过审核');
                    return;
                }
                if($BazaarManagerInfo['phone'] != $AreaManagerInfo['bazaar_phone']){
                    $this->reply('该客户经理不属于该市场经理');
                    return;
                }
                $this->Area_manager_model->updateData(array('status'=>1,'audit_time'=>date('Y-m-d H:i:s')),array('id'=>$id));
                $this->area_managerCheck($id,1);
                $this->add_log($BazaarManagerInfo['id'],$BazaarManagerInfo['name'],'市场经理审核通过', $id);
                //发送短信
                $modelId = '236008';
                $mobile = $AreaManagerInfo['phone'];
                $res = $this->ajaxSend($mobile,$modelId);
                if($res['success']){
                    $this->success('发送短信通知成功');
                }
                else{
                    $this->success('发送短信通知失败');
                }
                break;
            default:
                # code...
                break;
        }
    }
    public function nopass(){
        $this->load->model('user_model');
        $this->load->model('session_model');
        $this->load->model('club_model');
        $this->load->model('manager_model');
        $this->load->model("Area_manager_model");
        $this->load->model('Bazaar_manager_model');
        $this->load->model("Common_model");
        $this->load->model('sendmsg_model');

        $sid = $this->getParam('sid');
        $id = $this->getParam('id');
        $type = $this->getParam('type');
        $reason = $this->getParam('reason');
        if(empty($sid) || empty($id) || empty($type) || empty($reason)){
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
            $rs['msg'] = 'sid过期';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        
        $user_id = $sessionInfo['user_id'];
        $userInfo = $this->user_model->getInfoById($user_id);
        switch ($type) {
            case 'club':
                //取客户经理信息
                $managerInfo = $this->manager_model->fetchOne(array('consumer_userid'=>$sessionInfo['user_id']));
                if(empty($managerInfo)){
                    $this->reply('身份错误,code:1');
                    return;
                }
                if($managerInfo['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $clubinfo = $this->club_model->fetchOne(array('id'=>$id));
                if(empty($clubinfo)){
                    $this->reply('店铺不存在');
                    return;
                }
                if($clubinfo['question'] != 1){
                    $this->reply('店铺未完成注册');
                    return;
                }
                if($clubinfo['status'] != 0){
                    $this->reply('该店铺不处于待审核状态');
                    return;
                }
                if($managerInfo['phone'] != $clubinfo['manager_id']){
                    $this->reply('该店铺不属于该客户经理');
                    return;
                }
                $data = json_encode($clubinfo);
                $insertData = [];
                $insertData['msg'] = $reason;
                $insertData['from'] = $sessionInfo['user_id'];
                $insertData['to'] = $clubinfo['user_id'];
                $insertData['msgtype'] = 'club no pass';
                $insertData['data'] = json_encode($clubinfo);
                $insertData['create_date'] = date('Y-m-d H:i:s');
                $this->Common_model->setTable('tbl_appmsg');
                $flag = $this->Common_model->insertData($insertData);
                //更新店铺
                // $flag2 = $this->club_model->deleteData(array('id'=>$id));
                $flag2 = $this->club_model->updateData(array('refuse'=>1),array('id'=>$id));
                $this->add_log($managerInfo['id'],$managerInfo['name'],'零售店审核不通过', $id);
                //推送微信消息
                $clubOwner = $this->user_model->getInfoById($clubinfo['user_id']);
                $openid = $clubOwner['weixin'];
                $re = $this->sendmsg_model->refuseshop($openid,$reason);
                //发送短信
                $modelId = '235976';
                $mobile = $clubinfo['phone'];
                $res = $this->ajaxSend($mobile,$modelId);
                if($res['success']){
                    $this->success('发送短信通知成功');
                }
                else{
                    $this->success('发送短信通知失败');
                }
                break;
            case 'manager':
                //取市场经理信息
                $AreaManagerInfo = $this->Area_manager_model->fetchOne(array('user_id'=>$sessionInfo['user_id']));
                if(empty($AreaManagerInfo)){
                    $this->reply('身份错误,code:2');
                    return;
                }
                if($AreaManagerInfo['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $managerInfo = $this->manager_model->fetchOne(array('id'=>$id));
                if(empty($managerInfo)){
                    $this->reply('客户经理不存在');
                    return;
                }
                if($managerInfo['status'] != 0){
                    $this->reply('客户经理不处于待审核状态');
                    return;
                }
                if($AreaManagerInfo['phone'] != $managerInfo['area_managerid']){
                    $this->reply('该客户经理不属于该市场经理');
                    return;
                }
                $data = json_encode($managerInfo);
                $insertData = [];
                $insertData['msg'] = $reason;
                $insertData['from'] = $sessionInfo['user_id'];
                $insertData['to'] = $managerInfo['consumer_userid'];
                $insertData['msgtype'] = 'manager no pass';
                $insertData['data'] = json_encode($managerInfo);
                $insertData['create_date'] = date('Y-m-d H:i:s');
                $this->Common_model->setTable('tbl_appmsg');
                $flag = $this->Common_model->insertData($insertData);
                //删除客户经理
                // $flag2 = $this->manager_model->deleteData(array('id'=>$id));
                $flag2 = $this->manager_model->updateData('tbl_consumer',array('refuse'=>1),array('id'=>$id));
                $this->add_log($AreaManagerInfo['id'],$AreaManagerInfo['name'],'客户经理审核不通过', $id);
                //发送短信
                $modelId = '235979';
                $mobile = $managerInfo['phone'];
                $res = $this->ajaxSend($mobile,$modelId);
                if($res['success']){
                    $this->success('发送短信通知成功');
                }
                else{
                    $this->success('发送短信通知失败');
                }
                break;
            case 'area_manager':
                //取区域经理信息
                $BazaarManagerInfo = $this->Bazaar_manager_model->fetchOne(array('user_id'=>$sessionInfo['user_id']));
                if(empty($BazaarManagerInfo)){
                    $this->reply('身份错误,code:3');
                    return;
                }
                if($BazaarManagerInfo['status'] != 1){
                    $this->reply('身份未审核通过');
                    return;
                }
                $AreaManagerInfo = $this->Area_manager_model->fetchOne(array('id'=>$id));
                if(empty($AreaManagerInfo)){
                    $this->reply('市场经理不存在');
                    return;
                }
                if($AreaManagerInfo['status'] != 0){
                    $this->reply('市场经理不处于待审核状态');
                    return;
                }
                if($BazaarManagerInfo['phone'] != $AreaManagerInfo['bazaar_phone']){
                    $this->reply('该客户经理不属于该市场经理');
                    return;
                }
                $data = json_encode($AreaManagerInfo);
                $insertData = [];
                $insertData['msg'] = $reason;
                $insertData['from'] = $sessionInfo['user_id'];
                $insertData['to'] = $AreaManagerInfo['user_id'];
                $insertData['msgtype'] = 'area_manager no pass';
                $insertData['data'] = json_encode($AreaManagerInfo);
                $insertData['create_date'] = date('Y-m-d H:i:s');
                $this->Common_model->setTable('tbl_appmsg');
                $flag = $this->Common_model->insertData($insertData);
                //删除店铺
                // $flag2 = $this->Area_manager_model->deleteData(array('id'=>$id));
                $flag2 = $this->Area_manager_model->updateData(array('refuse'=>1),array('id'=>$id));
                $this->add_log($BazaarManagerInfo['id'],$BazaarManagerInfo['name'],'市场经理审核不通过', $id);
                //发送短信
                $modelId = '235981';
                $mobile = $AreaManagerInfo['phone'];
                $res = $this->ajaxSend($mobile,$modelId);
                if($res['success']){
                    $this->success('发送短信通知成功');
                }
                else{
                    $this->success('发送短信通知失败');
                }
                break;
            default:
                # code...
                break;
        }
    }
    public function getDetail(){
        $this->load->model('session_model');
        $this->load->model("Common_model");
        $sid = $this->getParam('sid');
        $type = $this->getParam('type');
        $id = $this->getParam('id');
        $refuse = $this->getParam('refuse');
        if(empty($sid) || empty($type) || empty($id)){
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
        if(!empty($refuse)){
            $this->Common_model->setTable('tbl_appmsg');
            $record = $this->Common_model->fetchOne(array('id'=>$id),array('id'=>'desc'));
            $info = json_decode($record['data'],true);
            $info['reason'] = $record['msg'];
            $info['audit_time'] = $record['create_date'];
            if($type == 'club'){
                $info['id_number_image'] = json_decode($info['id_number_image'],true);
            }
        }
        else{
            switch ($type) {
                case 'club':
                    $this->Common_model->setTable('tbl_club');
                    $info = $this->Common_model->fetchOne(array('id'=>$id));
                    if(!empty($info)){
                        if(!empty($info['id_number_image'])){
                            $info['id_number_image'] = json_decode($info['id_number_image'],true);
                        }
                    }
                    break;
                case 'manager':
                    $this->Common_model->setTable('tbl_consumer');
                    $info = $this->Common_model->fetchOne(array('id'=>$id));
                    break;
                case 'area_manager':
                    $this->Common_model->setTable('tbl_area_manager');
                    $info = $this->Common_model->fetchOne(array('id'=>$id));
                    break;
                case 'bazaar_manager':
                    $this->Common_model->setTable('tbl_bazaar_manager');
                    $info = $this->Common_model->fetchOne(array('id'=>$id));
                    break;
                
                default:
                    # code...
                    break;
            }
        }       
        if(!empty($info)){
            if(!empty($info) && !empty($info['area_id'])){
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
    public function param(){
        return array(
            '236014' => '市场经理申请',
            '236013' => '客户经理申请',
            '236011' => '店铺申请',
            '236010' => '区域经理审核通过',
            '236008' => '市场经理审核通过',
            '236006' => '客户经理审核通过',
            '236001' => '店铺申请通过',
            '235983' => '区域经理审核未通过',
            '235981' => '市场经理审核未通过',
            '235979' => '客户经理审核未通过',
            '235976' => '店铺审核不通过',
            '236406' => '店铺通过客户经理审核',
            );
    }
    public function add_log($adminId,$adminName,$operation, $content)
    {
        $this->load->model('adminlog_model');
        if (is_array($content)) {
            $content = json_encode($content);
        }
        $this->adminlog_model->insert($adminId, $adminName, $operation, $content);
    }
    public function ajaxSend($mobile,$modelId) {
        // 인증코드생성 및 전화번호에 발송
        $this->load->model("verifyphone_model");

        $authCode = gen_rand_num(6);
        $response = $this->zwsendsms($mobile, array($authCode,'5'), $modelId);
        $data = [];
        if( $response['code'] == 0) {
            $this->verifyphone_model->add_new_code($mobile, $authCode);
            $data['success'] = true;
            $data['code'] = $authCode;
        } else {
            $data['success'] = false;
            $data['error'] = "短信发送失败";
        }
        return $data;
    }
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
    public function managerCheck($id = '',$status = '')
    {
        $this->load->model("club_model");
        $this->load->model("porder_model");
        $this->load->model("consumer_model");
        $this->load->model("area_manager_model");
        $this->load->model("user_credits_model");
        $this->load->model('user_model');

        if(empty($id)){
            $id = $this->getParam('id');
        }
        if(empty($status)){
            $status = $this->getParam('status');
        }
        if(empty($id) || empty($status)){
            $this->reply('缺少参数，code:4');
        }
        $info = $this->manager_model->fetchOne(array('id'=>$id));
        $manager_id = $info['phone'];
        $managerinfo = $this->consumer_model->getInfoByManagerid($manager_id);
        if(!empty($managerinfo)){
            $manager_user_id = $managerinfo['consumer_userid'];
            $area_manager_user_id = $managerinfo['area_user_id'];
            $bazaar_user_id = $managerinfo['bazaar_user_id'];
            $manager_name = $managerinfo['name'];//客户经理姓名
            $area_name = $managerinfo['area_name'];//市场经理姓名

        }else{
            $manager_user_id = '';
            $area_manager_user_id = '';
            $bazaar_user_id = '';
            $manager_name = '';//客户经理姓名
            $area_name = '';//市场经理姓名
        }
       //所有的店主user_id 用于查询订单
        $all_user_info = $this->club_model->fetchAll(array('manager_id'=>$manager_id));
        if(!empty($all_user_info)){
            $user_ids = '(';
            foreach ($all_user_info as $one_user){
                $user_ids.= $one_user['user_id'].',';
            }
            $user_ids = substr($user_ids,0,-1).')';
            //查询订单
            $all_user_order = $this->porder_model->get_allorder_info_by_userids($user_ids);
            if(!empty($all_user_order)){
                foreach ($all_user_order as $one_order){
                    /*客户经理
                     * */
                    $manager_user_id = $manager_user_id;
                    $manager_credits =  round($one_order['get_credits']/2);
                    $manager_data['trade_no'] = $one_order['trade_no'];
                    $manager_data['user_id'] = $manager_user_id;
                    $manager_data['create_date'] = $one_order['create_date'];
                    $manager_data['credits'] = $manager_credits;
                    $manager_data['type'] = 2;
                    $manager_data['status'] = 1;
                    $manager_data['add_time'] = time();
                    $manager_data['address'] = $one_order['area'].$one_order['city'].$one_order['address'];
                    $manager_data['name'] = $one_order['name'];
                    /*市场经理
                     * */
                    $area_manager_user_id = $area_manager_user_id;
                    $area_manager_credits = round($one_order['get_credits']/3);
                    $area_manager_data['trade_no'] = $one_order['trade_no'];
                    $area_manager_data['user_id'] = $area_manager_user_id;
                    $area_manager_data['create_date'] = $one_order['create_date'];
                    $area_manager_data['credits'] =  $area_manager_credits;
                    $area_manager_data['type'] = 4;
                    $area_manager_data['status'] = 1;
                    $area_manager_data['add_time'] = time();
                    # $area_manager_data['address'] = $address;
                    $area_manager_data['name'] = $manager_name;
                    /*区域经理
                     * */
                    $bazaar_user_id = $bazaar_user_id;
                    $bazaar_credits = round($one_order['get_credits']/3);
                    $bazaar_data['trade_no'] = $one_order['trade_no'];
                    $bazaar_data['user_id'] = $bazaar_user_id;
                    $bazaar_data['create_date'] = $one_order['create_date'];
                    $bazaar_data['credits'] =  $bazaar_credits;
                    $bazaar_data['type'] = 5;
                    $bazaar_data['status'] = 1;
                    $bazaar_data['add_time'] = time();
                    $bazaar_data['name'] = $area_name;
                    $fp =  $this->filepath."add_admin_credits_".date("Y-m-d",time()).".log";         
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
                }
            }
        }
        // $info = $this->user_model->getInfoById($manager_user_id);
        // $openid = $info['weixin'];
        // $this->passManager($openid);
        // $data = parent::success_message();
        // echo json_capsule($data);
    }
    public function area_managerCheck($id = '',$status = '')
    {
        $this->load->model('manager_model');
        $this->load->model('user_model');
        $this->load->model("user_credits_model");
        if(empty($id)){
            $id = $this->getParam('id');
        }
        if(empty($status)){
            $status = $this->getParam('status');
        }
        if(empty($id) || empty($status)){
            $this->reply('缺少参数');
            die;
        }
        $ids = array($id);
        $area_manager_id = '';

        $area_info = $this->Area_manager_model->get_bazaar_user_info($id);
        $area_user_id = $area_info['area_user_id'];
        $bazaar_user_id = $area_info['bazaar_user_id'];
        //取市场经理下的客户经理和客户经理下的店铺订单
        $id_number = $area_info['phone'];
        /*设置订单分省
        */
        $this->load->model("Common_model");
        $this->Common_model->setTable('tbl_bazaar_manager');
        $tempInfo = $this->Common_model->fetchOne(array('id'=>$id));
        $province_id = $tempInfo['area_id'];
        !empty($province_id) ? $province = $province_id : $province = 7;
        $tick_order = 'tbl_ticket_order_'.$province;
        $this->porder_model->set_ticket_order($tick_order);
        $order_num = 'tbl_order_num_'.$province;
        $this->porder_model->set_order_num($order_num);
        /*设置订单分省
        */
        $all_club_user_order = $this->Area_manager_model->get_all_user_order_info($id_number);
        foreach ($all_club_user_order as $one_order){
            /*市场经理
                     * */
            $area_manager_user_id = $area_user_id;
            $area_manager_credits = round($one_order['get_credits']/3);
            $area_manager_data['trade_no'] = $one_order['trade_no'];
            $area_manager_data['user_id'] = $area_manager_user_id;
            $area_manager_data['create_date'] = $one_order['create_date'];
            $area_manager_data['credits'] =  $area_manager_credits;
            $area_manager_data['type'] = 4;
            $area_manager_data['status'] = 1;
            $area_manager_data['add_time'] = time();
            # $area_manager_data['address'] = $address;
            $area_manager_data['name'] = $one_order['manager_name'];

            /*区域经理
             * */
            $bazaar_user_id = $bazaar_user_id;
            $bazaar_credits = round($one_order['get_credits']/3);
            $bazaar_data['trade_no'] = $one_order['trade_no'];
            $bazaar_data['user_id'] = $bazaar_user_id;
            $bazaar_data['create_date'] = $one_order['create_date'];
            $bazaar_data['credits'] =  $bazaar_credits;
            $bazaar_data['type'] = 5;
            $bazaar_data['status'] = 1;
            $bazaar_data['add_time'] = time();
            $bazaar_data['name'] = $area_info['area_name'];

            $fp =  $this->filepath."add_admin_credits_".date("Y-m-d",time()).".log";
            
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
        }
    }
}
