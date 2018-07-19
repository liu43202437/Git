<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
class Msgtip extends Base_MobileController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('session_model');
        $this->filepath = get_instance()->config->config['log_path'];
    }


    public function get_audit_msgtip()
    {
        $this->load->model("Common_model");

        $sid = $this->post_input('sid');//用户ID
        $userInfo = $this->session_model->getUserInfoBySId($sid);
        $userId = $userInfo['user_id'];
        if (empty($userInfo)) {
            parent::output(5);
        }

        $phone = $userInfo['phone'];
        $data = [];
        $this->load->model('consumer_model');
        $consumer = $this->consumer_model->getInfoByConsumerUserId($userId);
        $i = 0;
        if(!empty($consumer)){
            $this->load->model('club_model');
            $club_num = $this->club_model->get_club_noaudit_num($consumer['phone']);
            if($club_num['num'] != 0){
                $data[$i]['type'] = 'manager';
                $data[$i]['num'] = $club_num['num'];
                $data[$i]['msg'] = "您有".$club_num['num']."店铺未审核，请尽快处理。";
                $data[$i]['url'] = base_url().'/resources/app/manager-review.html?action=undone&sid='.$sid;
                $data[$i]['time'] = date("H:i",time());
                $i++;
            }

        }
        //做为市场经理
        $this->load->model('area_manager_model');
        $area_manager = $this->area_manager_model->getInfoByAreaManagerUserId($userId);
        if(!empty($area_manager)){
            $consumer_num = $this->consumer_model->get_consumer_noaudit_num($area_manager['phone']);
            if($consumer_num['num'] != 0){
                $data[$i]['type'] = 'area_manager';
                $data[$i]['num'] = $consumer_num['num'];
                $data[$i]['msg'] = "您有".$consumer_num['num']."客户经理未审核，请尽快处理。";
                $data[$i]['url'] = base_url().'/resources/app/market-manager-review.html?action=undone&sid='.$sid;
                $data[$i]['time'] = date("H:i",time());
                $i++;
            }

        }
        //作为区域经理
        $this->load->model('bazaar_manager_model');
        $bazaar_manager = $this->bazaar_manager_model->getInfoByBazaarManagerUserId($userId);
        if(!empty($bazaar_manager)){
            $area_num = $this->area_manager_model->get_area_noaudit_num($bazaar_manager['phone']);
            if($area_num['num'] != 0){
                $data[$i]['type'] = 'bazaar_manager';
                $data[$i]['num'] = $area_num['num'];
                $data[$i]['msg'] = "您有".$area_num['num']."市场经理未审核，请尽快处理。";
                $data[$i]['time'] = date("H:i",time());
                $data[$i]['url'] = base_url().'/resources/app/area-manager-review.html?action=undone&sid='.$sid;
                $i++;
            }
        }
        //取拒绝店铺信息
        $user_id = $userId;
        unset($userId);
        $this->Common_model->setTable('tbl_club');
        $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info) && $info['refuse'] == 1){
            $this->Common_model->setTable('tbl_appmsg');
            $sql = "select * from tbl_appmsg where `to`={$user_id} and msgtype='club no pass' order by create_date desc limit 1";
            $appInfo = $this->Common_model->queryAll($sql);
            if(!empty($appInfo)){
                $data[$i]['type'] = 'clubNoPass';
                $data[$i]['num'] = '1';
                $data[$i]['msg'] = '您的店铺未通过审核';
                $data[$i]['time'] = date("H:i",time());
                $data[$i]['url'] = base_url()."/resources/app/vc-review-info.html?type=club&id=".$appInfo[0]['id']."&action=refuse&sid=".$sid;
                $i++;
            }
        }
        //取拒绝客户经理信息
        $this->Common_model->setTable('tbl_consumer');
        $info = $this->Common_model->fetchOne(array('consumer_userid'=>$user_id));
        if(!empty($info) && $info['refuse'] == 1){
            $this->Common_model->setTable('tbl_appmsg');
            $sql = "select * from tbl_appmsg where `to`={$user_id} and msgtype='manager no pass' order by create_date desc limit 1";
            $appInfo = $this->Common_model->queryAll($sql);
            if(!empty($appInfo)){
                $data[$i]['type'] = 'managerNoPass';
                $data[$i]['num'] = '1';
                $data[$i]['msg'] = '您的客户经理未通过审核';
                $data[$i]['time'] = date("H:i",time());
                $data[$i]['url'] = base_url()."/resources/app/vc-review-info.html?type=manager&id=".$appInfo[0]['id']."&action=refuse&sid=".$sid;
                $i++;
            }
        }
        //取拒绝市场经理信息
        $this->Common_model->setTable('tbl_area_manager');
        $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info) && $info['refuse'] == 1){
            $this->Common_model->setTable('tbl_appmsg');
            $sql = "select * from tbl_appmsg where `to`={$user_id} and msgtype='area_manager no pass' order by create_date desc limit 1";
            $appInfo = $this->Common_model->queryAll($sql);
            if(!empty($appInfo)){
                $data[$i]['type'] = 'area_managerNoPass';
                $data[$i]['num'] = '1';
                $data[$i]['msg'] = '您的市场经理未通过审核';
                $data[$i]['time'] = date("H:i",time());
                $data[$i]['url'] = base_url()."/resources/app/vc-review-info.html?type=area_manager&id=".$appInfo[0]['id']."&action=refuse&sid=".$sid;
                $i++;
            }
        }
        //取拒绝区域经理信息
        $this->Common_model->setTable('tbl_bazaar_manager');
        $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info) && $info['refuse'] == 1){
            $this->Common_model->setTable('tbl_appmsg');
            $sql = "select * from tbl_appmsg where `to`={$user_id} and msgtype='bazaar_manager no pass' order by create_date desc limit 1";
            $appInfo = $this->Common_model->queryAll($sql);
            if(!empty($appInfo)){
                $data[$i]['type'] = 'bazaar_managerNoPass';
                $data[$i]['num'] = '1';
                $data[$i]['msg'] = '您的区域经理未通过审核';
                $data[$i]['time'] = date("H:i",time());
                $data[$i]['url'] = base_url()."/resources/app/vc-review-info.html?type=bazaar_manager&id=".$appInfo[0]['id']."&action=refuse&sid=".$sid;
                $i++;
            }
        }
        //取拒绝访销经理信息
        $this->Common_model->setTable('tbl_lottery_manager');
        $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info) && $info['refuse'] == 1){
            $this->Common_model->setTable('tbl_appmsg');
            $sql = "select * from tbl_appmsg where `to`={$user_id} and msgtype='lottery no pass' order by create_date desc limit 1";
            $appInfo = $this->Common_model->queryAll($sql);
            if(!empty($appInfo)){
                $data[$i]['type'] = 'lottery_managerNoPass';
                $data[$i]['num'] = '1';
                $data[$i]['msg'] = '您的访销经理未通过审核';
                $data[$i]['time'] = date("H:i",time());
                $data[$i]['url'] = base_url()."/resources/app/vc-review-info.html?type=lottery_manager&id=".$appInfo[0]['id']."&action=refuse&sid=".$sid;
                $i++;
            }
        }
        parent::app_put($data);
    }

    /**
     * @return 未审核数
     */
    public function get_noaudit_num()
    {
        $this->load->model('Common_model');
        $sid = $this->post_input('sid');//用户ID
        $type = $this->post_input('type');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        if (empty($type)){
            $type = $this->get_input('type');
        }
        $type = $type==null?1:$type;
        $userInfo = $this->session_model->getUserInfoBySId($sid);

        if (empty($userInfo)) {
            parent::output(5);
        }
        $user_id = $userInfo['user_id'];
        $phone = $userInfo['phone'];
        //客户经理
        $data = [];
        if($type == 'manager'){
            $this->load->model('club_model');
            $this->Common_model->setTable('tbl_consumer');
            $info = $this->Common_model->fetchOne(array('consumer_userid'=>$user_id));
            $phone = $info['phone'];
            $club_num = $this->club_model->get_club_noaudit_num($phone);
            $data['type'] = 'manager';
            $data['num'] = $club_num==null?0:$club_num['num'];
        }
        //市场经理
        if($type == 'area_manager'){
            $this->load->model('consumer_model');
            $this->Common_model->setTable('tbl_area_manager');
            $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
            $phone = $info['phone'];
            $consumer_num = $this->consumer_model->get_consumer_noaudit_num($phone);
            $data['type'] = 'area_manager';
            $data['num'] = $consumer_num==null?0:$consumer_num['num'];
        }
        //区域经理
        if($type == 'bazaar_manager'){
            $this->load->model('area_manager_model');
            $this->Common_model->setTable('bazaar_manager');
            $info = $this->Common_model->fetchOne(array('user_id'=>$user_id));
            $phone = $info['phone'];
            $area_num = $this->area_manager_model->get_area_noaudit_num($phone);
            $data['type'] = 'bazaar_manager';
            $data['num'] = $area_num==null?0:$area_num['num'];
        }
        parent::output($data);
    }
    public function get_manager_list()
    {

        $sid = $this->post_input('sid');//用户ID
        $type = $this->post_input('type');
        $area_code = $this->post_input('area_code');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        if (empty($type)){
            $type = $this->get_input('type');
        }
        if (empty($area_code)){
            $area_code = $this->get_input('area_code');
        }
        $area_code = substr($area_code,0,6);
        $userInfo = $this->session_model->getUserInfoBySId($sid);
        $new_area_code = substr($area_code,0,3);

        if (empty($userInfo)) {
            parent::output(5);
        }
        if($type == 'manager'){
            $this->load->model('consumer_model');
            if($new_area_code == '430'){
                $manager_list = $this->consumer_model->get_consumer_manager_list($area_code,$new_area_code);
            }else{
                $manager_list = $this->consumer_model->get_consumer_manager_list($area_code,0);
            }

            parent::output($manager_list);
        }
        if($type == 'area_manager'){
            $this->load->model('area_manager_model');
            $area_manager_list = $this->area_manager_model->get_area_manager_list($area_code);
            parent::output( $area_manager_list);
        }
        if($type == 'bazaar_manager'){
            $this->load->model('bazaar_manager_model');
            $area_manager_list = $this->bazaar_manager_model->get_bazaar_manager_list($area_code);
            parent::output( $area_manager_list);
        }


    }
}