<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Redeem extends Base_AdminController
{
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
        $this->view_path = 'admin/redeem/';
        $this->load->model('Redeem_model');
        $this->load->model('Receipt_model');
    }

    public function redeemLists()
    {
        $this->load->model('area_model');
        $this->load->model('club_model');
        $this->load->model("UserRedeem_model");

        $searchKey = $this->post_input('search_key');
        $startDate = $this->post_input('start_date');
        $endDate = $this->post_input('end_date');
        $type = $this->post_input('type');
        $selectprovince = $this->post_input('selectprovince');
        $selectcity = $this->post_input('selectcity');
        $filters = array();
        $orders = array();
        //获取省份列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        if (!empty($searchKey)) {
            $filters['user_id,stationId,code, ticketNo,gameName,price'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['time<='] = $endDate.' 23:59:59';
            $filters['time>='] = $startDate.' 00:00:00';
        }
        if(!empty($type)){
            if($type == 1){
                $filters['ret'] = 1301;
            }
            else{
                $filters['ret!='] = 1301;
            }
        }
        else{
            $filters['ret'] = 1301;
        }

        if(!empty($selectprovince) && $selectprovince != '中国'){
            $filters['province'] = $provinces_temp[$selectprovince];
        }
        if(!empty($selectcity) && $selectcity != '中国' ){
            $filters['city'] = $selectcity;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['time'] = 'DESC';
        }
        $totalCount = $this->Redeem_model->getCount($filters);
        $rsltList = $this->Redeem_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        //获取城市列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        foreach ($rsltList as $key => $item) {
            $rsltList[$key]['address'] = $item['province'].$item['city'];
            $clubInfo = $this->club_model->fetchOne(array('user_id'=>$item['user_id']));
            if(!empty($clubInfo)){
                $rsltList[$key]['name'] = $clubInfo['name'];
            }
            else{
                $claninfo = $this->UserRedeem_model->fetchOne(array('user_id'=>$item['user_id']));
                $rsltList[$key]['name'] = $claninfo['name'];
            }
            
        }
        $this->data['searchKey'] = $searchKey;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->data['selectprovince'] = $selectprovince;
        $this->data['selectcity'] = $selectcity;     
        $this->data['provinces'] = $provinces_temp;
        $this->data['cities'] = $this->area_model->getCityList();
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('list');
    }
    public function receiptLists()
    {
        $this->load->model('area_model');
        $this->load->model('club_model');

        $searchKey = $this->post_input('search_key');
        $startDate = $this->post_input('start_date');
        $endDate = $this->post_input('end_date');
        $type = $this->post_input('type');
        // var_dump($this->input->post());
        $filters = array();
        $orders = array();
        if (!empty($searchKey)) {
            $filters['user_id,transfer_name'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['add_time<='] = strtotime($endDate.' 23:59:59');
            $filters['add_time>='] = strtotime($startDate.' 00:00:00');
        }
        if(!empty($type)){
            if($type == 1){
                $filters['status'] = 1;
            }
            else{
                $filters['status'] = 0;
            }
        }

        if(!empty($selectprovince) && $selectprovince != '中国'){
            $filters['province'] = $provinces_temp[$selectprovince];
        }
        if(!empty($selectcity) && $selectcity != '中国' ){
            $filters['city'] = $selectcity;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['add_time'] = 'DESC';
        }
        $totalCount = $this->Receipt_model->getCount($filters);
        $rsltList = $this->Receipt_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        // echo $this->db->last_query();
        //获取城市列表
        $this->data['searchKey'] = $searchKey;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('receiptlist');
    }
    public function checking(){
        //取总兑奖金额
        $sql = "SELECT SUM(prize) as total FROM `tbl_redeem` WHERE ret=1301";
        $totalRedeem = $this->Redeem_model->queryAll($sql);
        $totalRedeem = (int)$totalRedeem[0]['total'];
        //取总提现金额
        $sql = "SELECT SUM(amount)/100 as total FROM tbl_receipt_order WHERE `status`=1";
        $totalReceipt = $this->Redeem_model->queryAll($sql);
        $totalReceipt = (int)$totalReceipt[0]['total'];
        //取用户总剩余金额
        $sql = "SELECT SUM(IFNULL(prize,0)) as total FROM tbl_user  ";
        $remaining = $this->Redeem_model->queryAll($sql);
        $remaining = (int)$remaining[0]['total'];
        //计算差值
        $difference = $totalRedeem - $totalReceipt;
        $other = $remaining  - $difference;
        $this->data['totalRedeem'] = $totalRedeem;
        $this->data['totalReceipt'] = $totalReceipt;
        $this->data['remaining'] = $remaining;
        $this->data['difference'] = $difference;
        $this->data['other'] = $other;
        $this->load_view('checking');
    }
    //导出兑奖记录
    public function exportRedeem(){
        set_time_limit(0);
        $this->load->model('area_model');
        $this->load->model('club_model');
        $this->load->model('excel_model');
        $this->load->model('porder_model');

        $searchKey = $this->post_input('search_key');
        $startDate = $this->post_input('startDate');
        $endDate = $this->post_input('endDate');
        $type = $this->post_input('type');
        $selectprovince = $this->post_input('selectprovince');
        $selectcity = $this->post_input('selectcity');
        $filters = array();
        $orders = array();
        // var_dump($this->input->post());die;
        //获取省份列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        if (!empty($searchKey)) {
            $filters['user_id,stationId,code,ticketNo,gameName,price'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['time<='] = $endDate.' 23:59:59';
            $filters['time>='] = $startDate.' 00:00:00';
        }
        if(!empty($type)){
            if($type == 1){
                $filters['ret'] = 1301;
            }
            else{
                $filters['ret!='] = 1301;
            }
        }
        else{
            $filters['ret'] = 1301;
        }
        if(!empty($selectprovince) && $selectprovince != '中国'){
            $filters['province'] = $provinces_temp[$selectprovince];
        }
        if(!empty($selectcity) && $selectcity != '中国' ){
            $filters['city'] = $selectcity;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['time'] = 'DESC';
        }
        $totalCount = $this->Redeem_model->getCount($filters);
        $rsltList = $this->porder_model->lists($filters, $orders,'tbl_redeem'); 
        //获取城市列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        foreach ($rsltList as $key => $item) {
            $rsltList[$key]['address'] = $item['province'].$item['city'];
            $clubInfo = $this->club_model->fetchOne(array('user_id'=>$item['user_id']));
            if(!empty($clubInfo)){
                $rsltList[$key]['name'] = $clubInfo['name'];
            }
            else{
                $claninfo = $this->UserRedeem_model->fetchOne(array('user_id'=>$item['user_id']));
                $rsltList[$key]['name'] = $claninfo['name'];
            }
        }
        $info = $rsltList;
        if(count($info) > 5000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        $temp = [];
        foreach ($info as $key => $value) {
            $temp[$key][0] = $value['id'];
            $temp[$key][1] = $value['user_id'];
            $temp[$key][2] = $value['name'];
            $temp[$key][3] = $value['province'];
            $temp[$key][4] = $value['city'];
            $temp[$key][5] = $value['code'];
            $temp[$key][6] = $value['ticketNo'];
            $temp[$key][7] = $value['gameName'];
            $temp[$key][8] = $value['prize'];
            $temp[$key][9] = $value['time'];
        }
        $Lists = $temp;
        $date = date("Y_m_d", time());
        $fileName = "兑奖记录_{$date}.xls";
  
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15);
        $data['headArr'] = array(
            'A1'=>'记录ID',
            'B1'=>'用户ID',
            'C1'=>'用户姓名',
            'D1'=>'省份',
            'E1'=>'城市',
            'F1'=>'保安码',
            'H1'=>'票号',
            'I1'=>'票种',
            'J1'=>'中奖金额',
            'K1'=>'兑奖时间',
        );
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }
    //导出提现记录
    public function exportReceipt(){
        // var_dump(1);die;
        set_time_limit(0);
        $this->load->model('area_model');
        $this->load->model('club_model');
        $this->load->model('excel_model');
        $this->load->model('porder_model');

        $searchKey = $this->post_input('search_key');
        $startDate = $this->post_input('startDate');
        $endDate = $this->post_input('endDate');
        $type = $this->post_input('type');
        $filters = array();
        $orders = array();
        if (!empty($searchKey)) {
            $filters['user_id,transfer_name'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['add_time<='] = strtotime($endDate.' 23:59:59');
            $filters['add_time>='] = strtotime($startDate.' 00:00:00');
        }
        if(!empty($type)){
            if($type == 1){
                $filters['status'] = 1;
            }
            else{
                $filters['status'] = 0;
            }
        }

        if(!empty($selectprovince) && $selectprovince != '中国'){
            $filters['province'] = $provinces_temp[$selectprovince];
        }
        if(!empty($selectcity) && $selectcity != '中国' ){
            $filters['city'] = $selectcity;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['add_time'] = 'DESC';
        }
        $rsltList = $this->porder_model->lists($filters, $orders,'tbl_receipt_order'); 
        $info = $rsltList;
        if(count($info) > 5000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        $temp = [];
        foreach ($info as $key => $value) {
            $temp[$key][0] = $value['id'];
            $temp[$key][1] = $value['user_id'];
            $temp[$key][2] = $value['transfer_name'];
            $temp[$key][3] = $value['amount']/100;
            $temp[$key][4] = $value['detail_id'];
            $temp[$key][5] = $value['status'] == 1 ? '成功' : '失败' ;
            $temp[$key][6] = $value['reason'];
            $temp[$key][7] = date('Y-m-d H:i:s',$value['add_time']);
        }
        $Lists = $temp;
        $date = date("Y_m_d", time());
        $fileName = "提现记录_{$date}.xls";
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15);
        $data['headArr'] = array(
            'A1'=>'记录ID',
            'B1'=>'用户ID',
            'C1'=>'提现人',
            'D1'=>'提现金额',
            'E1'=>'提现订单号',
            'F1'=>'提现状态',
            'H1'=>'提现备注',
            'I1'=>'提现时间',
        );
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }
}
