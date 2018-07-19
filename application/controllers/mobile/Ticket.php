<?php
header('Access-Control-Allow-Origin:*');
defined('BASEPATH') OR exit('No direct script access allowed');
class Ticket extends Base_AppController
{
    function __construct()
    {
        parent::__construct();
        
    }
    public function getTicketType(){
        $rs = [];
        $this->load->model('club_model');
        $this->load->model('session_model');
        $this->load->model('user_model');
        $this->load->model('ticket_model');
        $this->load->model("Common_model");

        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        if(empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        //用户验证
        $sessioninfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessioninfo)){
            $this->reply('用户session信息不存在');
            return;
        }
        $user_id = $sessioninfo['user_id'];
        $userinfo = $this->user_model->getInfoById($user_id);
        if(empty($userinfo)){
            $this->reply('用户不存在');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('user_id'=>$user_id,'status'=>1));
        if(empty($clubInfo)){
            $this->reply('您未注册店铺,或店铺未通过审核');
            return;
        }
        $provinceId = $clubInfo['area_id'];
        $ticketList = $this->ticket_model->fetchAll(array('status'=>0,'province_id'=>$provinceId));
        foreach ($ticketList as $key => $value) {
            $price = explode('.', $value['price'])[0];
            $rs[] = $price;
        }
        $this->Common_model->setTable('tbl_ticket_config');
        $filters = [];
        $filters['area_id'] = $provinceId;
        $filters['valid'] = 1;
        $ticketConfig = $this->Common_model->fetchOne($filters);
        $total = $ticketConfig['total_money'];
        if($provinceId == 14){
            if($clubInfo['order_status'] == 0){
                $total = $ticketConfig['first_total_money'];
            }
        }
        $rs = array_unique($rs);
        $rs = array_values($rs);
        $res = [];
        $res['type'] = $rs;
        $res['total'] = $total;
        $this->success('成功',$res);
        // echo json_encode($rs);
        // return ;
    }
}
