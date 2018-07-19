<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Base_AdminController {

    function __construct()
    {
        parent::__construct();

        $this->view_path = 'admin/order/';
        $this->load->model('porder_model');
    }

    public function lists()
    {
        $this->load->model('area_model');
        //获取省份列表
        //获取城市列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }

        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $selectType = $this->get_input('selectType');
        $selectKeyword = $this->get_input('selectKeyword');
        $selectprovince=$this->get_input('selectprovince');
        $selectprovincename=$this->get_input('selectprovincename');
        $selectcity    =$this->get_input('selectcity');
        $selectstreet  =$this->get_input('selectstreet');
        $selectcounty  =$this->get_input('selectcounty');
        $exportStatus = $this->get_input('exportStatus');
        $reload = $this->get_input('reload');

        $filters = array();
        $orders = array();
        $area_param=array();
        $allinfo = $this->input->get();

        if (!empty($startDate)) {
            $filters['create_date >='] = d2bt($startDate);
        }
        if (!empty($endDate)) {
            $filters['create_date <='] = d2et($endDate);
        }
        if(!empty($selectKeyword)){
            $column = $selectType.'%';
            $filters[$column] = $selectKeyword;
        }
        if(!empty($selectstreet)){
            $filters['area_code'] = $selectstreet;
        }
        elseif(!empty($selectcounty)){
            $filters['left(area_code,6)'] = $selectcounty;
        }
        elseif(!empty($selectcity)){
            $filters['left(area_code,4)'] = $selectcity;
        }
        elseif(!empty($selectprovince)){
            $filters['area'] = $selectprovincename;
        }

        if ($exportStatus == '' || $exportStatus == '5'){

        }

        if ($exportStatus == 1){
            $filters['pay_status']= 0 ;
            $filters['order_status'] = 1;
        }

        if ($exportStatus == 2){
            $filters['pay_status']= 1 ;
            $filters['order_status'] = 1;
        }
        if ($exportStatus == 3){
            $filters['pay_status']= 1;
            $filters['order_status'] = 2;
        }

        if ($exportStatus == 4){
            $filters['pay_status']= 0 ;
            $filters['order_status'] = 2;
        }


        if($reload == 1){
            $this->pager['pageNumber'] = 1;
        }

        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->porder_model->countorder($filters);
        $itemList = $this->porder_model->getorder($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

        $Lists = [];
        foreach ($itemList as $key=>$item) {

            $Lists[$key]['id'] = $item['id'];
            $Lists[$key]['trade_no'] = $item['trade_no'];
            $Lists[$key]['money'] = $item['total_money'];
            if($item['order_status'] == 0){
                $Lists[$key]['status'] = '已下单';
            }elseif($item['order_status'] == 1 && $item['pay_status'] == 1){
                $Lists[$key]['status'] = '已支付';
            }elseif($item['order_status'] == 1 && $item['pay_status'] == 0){
                $Lists[$key]['status'] = '已配送';
            }elseif ($item['order_status'] == 2 && $item['pay_status'] == 1){
                $Lists[$key]['status'] = '已完成';
            } else{
                $Lists[$key]['status'] = '已取消';
            }
            $Lists[$key]['nickname'] = $item['name'];
            if($item['dump_status'] == 0){
                $Lists[$key]['dump_status'] = '未配送';
            }else{
                $Lists[$key]['dump_status'] = '已配送';
            }
            $Lists[$key]['address'] = $item['area'].$item['city'].$item['address'];
            $Lists[$key]['create_date'] = $item['create_date'];
            $Lists[$key]['dump_time'] = $item['dump_time'];
            $Lists[$key]['phone'] = $item['phone'];
            $order_num = $this->porder_model->getInfoByTradeno($item['trade_no']);
            $description = '';
            if(!empty($order_num)){
                foreach ($order_num as $value){
                    $description.= $value->title.'| 数量'.$value->ticket_num.'包 | 单价 '.$value->count_price.'元</br>';
                }
            }
            $Lists[$key]['description'] = $description;
        }

        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['selectType'] = $selectType;
        $this->data['selectKeyword'] = $selectKeyword;
        $this->data['selectprovince']=$selectprovince;
        $this->data['selectprovincename']=$selectprovincename;
        $this->data['selectcounty']=$selectcounty;
        $this->data['selectstreet']=$selectstreet;
        $this->data['selectcity']=$selectcity;
        $this->data['exportStatus'] = $exportStatus;
        $this->data['itemList'] = $Lists;
        $this->data['provinces'] = $provinces_temp;
        $this->data['cities'] = $this->area_model->getCityList();
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('list');
    }

    public function ajax_dump(){
        set_time_limit(0);
        $this->load->model('excel_model');
        $filters = array();
        $orders = array();
        $H = date("H",time());
        if($H <= 8){
            $stime = date("Y-m-d",strtotime('-1 day'));
            $stime = $stime." 16:00:00";
            $etime = date("Y-m-d",time());
            $etime = $etime." 08:00:00";
        }else{
            $stime = date("Y-m-d",time());
            $stime = $stime." 08:00:00";
            $etime = date("Y-m-d",time());
            $etime = $etime." 23:00:00";
        }

        // $filters['create_date >='] = $stime;
        // $filters['create_date <'] = $etime;
        $filters['dump_status'] = 0;
        $filters['order_status'] = 0;





        $orders['name'] = 'asc';
        $orders['create_date'] = 'asc';

        // $itemList = $this->porder_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],'tbl_ticket_order');
        $itemList = $this->porder_model->fetchAll($filters, $orders);
        $Lists = [];

        foreach ($itemList as $key=>$item) {
            $Lists[$key][0] = $item['trade_no'];
            $Lists[$key][1] = $item['phone'];
            $this->load->model('user_model');
            $Lists[$key][2] = $item['name'];
            $Lists[$key][3] = $item['total_money'];
            $order_num = $this->porder_model->getInfoByTradeno($item['trade_no']);
            $description = '';
            foreach ($order_num as $value){
                $description.= $value->title."| 数量".$value->ticket_num."包 | 单价 ".$value->count_price."元\n";
            }
            $Lists[$key][4] = $description;
            if($item['order_status'] == 0){
                $Lists[$key][5] = '已下单';
            }elseif($item['order_status'] == 2 or $item['pay_status'] == 1){
                $Lists[$key][5] = '已支付';
            }elseif($item['order_status'] == 1){
                $Lists[$key][5] = '已配送';
            }else{
                $Lists[$key][5] = '已取消';
            }

            $Lists[$key][6] = $item['area'].$item['city'].$item['address'];
            $Lists[$key][7] = $item['create_date'];

            $Lists[$key][8] = date("Y-m-d H:i:s",time());
            $datas['dump_status'] = 1;
            $datas['order_status'] = 1;
            $datas['dump_time'] =date("Y-m-d H:i:s",time());
            //$this->porder_model->update($item['id'],$datas);

        }


        date_default_timezone_set('Asia/Shanghai');
        //对数据进行检验
        if (empty($Lists) || !is_array($Lists)) {
            $this->lists();

        }
        $date = date("Y_m_d", time());
        $fileName = "订单列表_{$date}.xls";

        $this->load->model('excel_model');
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>30,'E'=>40,'G'=>40);
        $data['headArr'] = array(
            'A1'=>'订单号',
            'B1'=>'手机号码',
            'C1'=>'用户昵称',
            'D1'=>'总金额',
            'E1'=>'订单详情描述',
            'F1'=>'状态',
            'G1'=>'用户地址',
            'H1'=>'下单时间',
            'I1'=>'配送时间',
        );

        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel($data);

    }
    public function credits_order_lists(){
        $id = $this->get_input('id');
        $date_type = $this->get_input('date_type');
        $trade_no =$this->get_post_input('trade_no');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $phone = $this->get_input('phone');
        $status = $this->get_input('status');

        var_dump($this->pager);
        $filters = array();
        $orders = array();

        if (!empty($trade_no)) {
            $filters['trade_no'] = $trade_no;
        }
        if (!empty($date_type)) {
            $filters['date_type'] = $date_type;
        }
        if (!empty($phone)) {
            $filters['phone'] = $phone;
        }
        if (!empty($startDate)) {
            $filters['startDate'] = d2bt($startDate);
        }
        if (!empty($endDate)) {
            $filters['endDate'] = d2et($endDate);
        }

        if (!empty($status)) {
            $filters['status'] = $status;
        }

        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else{
            $orders['create_date'] = 'DESC';
        }


        $this->data['trade_no'] = $trade_no;
        $this->data['phone'] = $phone;
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['id'] = $id;
        $this->data['status'] = $status;
        $this->data['start'] = 0;
        $this->data['end'] = 1;

        $this->data['itemList'] = [];
        $this->assign_pager(10);
        $this->assign_message();
        $this->load_view('credits_order_list');
    }

    public function delete()
    {
        $ids = $this->post_input('ids');
        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }

        $this->load->model('feedback_model');
        $this->load->model('session_model');

        foreach ($ids as $id) {
            $userInfo = $this->user_model->get($id);

            $this->session_model->deleteByUser($id);
            $this->feedback_model->deleteByUser($id);
            $this->user_model->delete($id);
        }

        $this->add_log('删除用户', $ids);

        echo json_capsule(parent::success_message());
    }

    public function toggle_enable()
    {
        $id = $this->post_input('id');
        $ids = $this->post_input('ids');
        if (empty($id) && empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }

        if (empty($ids)) {	// one item operation
            $data['is_enabled$'] = '1-is_enabled';
            $this->user_model->update($id, $data);

        } else {			// batch item operation
            $data['is_enabled'] = $this->post_input('is_enabled', 0);
            foreach ($ids as $id) {
                $this->user_model->update($id, $data);
            }
        }

        echo json_capsule(parent::success_message());
    }

    public function edit()
    {
        $id = $this->get_input('id');
        $userInfo = $this->user_model->get($id);
        if (empty($userInfo)) {
            $this->error_redirect('user/lists', '信息不正确！');
        }

        $this->data['itemInfo'] = $userInfo;
        $this->assign_message();
        $this->load_view('edit');
    }

    public function order_list()
    {
        $userId = $this->get_input('id');
        if (empty($userId)) {
            $this->error_redirect('user/lists');
        }

        $orderStatus = $this->get_input('order_status', '');
        $timeType = $this->get_input('time_type', 'create');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');

        $filters = array();
        $orders = array();

        $filters['user_id'] = $userId;
        $filters['kind !='] = ORDER_KIND_MANUALPOINT;
        if ($orderStatus !== '') {
            $orderStatus = intval($orderStatus);
            $filters['order_status'] = $orderStatus;
        }
        if ($timeType == 'create') {
            $field = 'create_date';
        } else {
            $field = 'proceed_date';
        }
        if (!empty($startDate)) {
            $filters[$field . ' >'] = d2bt($startDate);
        }
        if (!empty($endDate)) {
            $filters[$field . ' <'] = d2et($endDate);
        }

        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }

        $this->load->model('order_model');

        $totalCount = $this->order_model->getCount($filters);
        $itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

        foreach ($itemList as $key=>$item) {

        }

        $this->data['userId'] = $userId;
        $this->data['orderStatus'] = $orderStatus;
        $this->data['timeType'] = $timeType;
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;

        $this->data['itemList'] = $itemList;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('order_list');
    }

    public function consume_history()
    {
        $userId = $this->get_input('id');
        if (empty($userId)) {
            $this->error_redirect('user/lists');
        }

        $filters = array();
        $orders = array();

        $filters['user_id'] = $userId;
        $filters['kind'] = array(ORDER_KIND_YUNJIFEN, ORDER_KIND_TICKET, ORDER_KIND_BUYPOINT);
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        }

        $this->load->model('order_model');

        $totalCount = $this->order_model->getCount($filters);
        $itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

        foreach ($itemList as $key=>$item) {

        }

        $this->data['userId'] = $userId;
        $this->data['itemList'] = $itemList;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('consume_history');
    }

    public function point_history()
    {
        $userId = $this->get_input('id');
        if (empty($userId)) {
            $this->error_redirect('user/lists');
        }

        $filters = array();
        $orders = array();

        $filters['user_id'] = $userId;
        $filters['kind'] = array(ORDER_KIND_YUNJIFEN, ORDER_KIND_GIFT, ORDER_KIND_BUYPOINT, ORDER_KIND_MANUALPOINT);
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        }

        $this->load->model('order_model');

        $totalCount = $this->order_model->getCount($filters);
        $itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

        foreach ($itemList as $key=>$item) {

        }

        $this->data['userId'] = $userId;
        $this->data['itemList'] = $itemList;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('point_history');
    }

    public function ranks()
    {
        $orders = array();
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        }

        $this->load->model('userrank_model');

        $itemList = $this->userrank_model->getAll(null, $orders);
        $count = count($itemList);

        $this->data['itemList'] = $itemList;
        $this->assign_pager($count, $count);
        $this->assign_message();
        $this->load_view('ranks');
    }

    public function edit_rank()
    {
        $id = $this->post_input('id');
        $data['name_male'] = $this->post_input('name_male');
        $data['name_female'] = $this->post_input('name_female');
        $data['rank'] = $this->post_input('rank');
        $data['min_exp'] = $this->post_input('min_exp');

        $this->load->model('userrank_model');
        $orgRank = $this->userrank_model->getByRank($data['rank']);
        if (!empty($orgRank) && $orgRank['id'] != $id) {
            $this->error_redirect('user/ranks', '等级不能重复使用！');
        }

        if (empty($id)) {
            $this->userrank_model->insert($data['name_male'], $data['name_female'], $data['rank'], $data['min_exp']);
        } else {
            $this->userrank_model->update($id, $data);
        }
        $this->success_redirect('user/ranks');
    }
    public function exportOrders(){
        set_time_limit(0);
        $this->load->model('excel_model');
        $this->load->model('area_model');
        $rs = [];
        $startDate = $this->post_input('startDate');
        $endDate = $this->post_input('endDate');
        $selectType = $this->post_input('selectType');
        $selectKeyword = $this->post_input('selectKeyword');
        $selectprovince=$this->post_input('selectprovince');
        $selectprovincename=$this->post_input('selectprovincename');
        $selectcity    =$this->post_input('selectcity');
        $selectstreet  =$this->post_input('selectstreet');
        $selectcounty  =$this->post_input('selectcounty');
        $exportStatus = $this->post_input('exportStatus');

        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        $filters = array();
        $orders = array();
        $area_param=array();
        if(empty($startDate) || empty($endDate)){
            echo "<script>alert('请输入起止时间')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        $filters['create_date >='] = $startDate.' 00:00:00';
        $filters['create_date <'] = $endDate.' 23:59:59';
        if(!empty($selectType) && !empty($selectKeyword)){
            $filters[$selectType.'%'] = $selectKeyword;
        }
        if(!empty($selectstreet)){
            $area_param['area_code'] = $selectstreet;
        }
        elseif(!empty($selectcounty)){
            $area_param['left(area_code,6)'] = $selectcounty;
        }
        elseif(!empty($selectcity)){
            $area_param['left(area_code,4)'] = $selectcity;
        }
        elseif(!empty($selectprovince)){
            $area_param['area'] = $selectprovince;
        }
        if ($exportStatus == '' || $exportStatus == '5'){

        }

        if ($exportStatus == 1){
            $filters['pay_status']= 0 ;
            $filters['order_status'] = 1;
        }

        if ($exportStatus == 2){
            $filters['pay_status']= 1 ;
            $filters['order_status'] = 1;
        }
        if ($exportStatus == 3){
            $filters['pay_status']= 1;
            $filters['order_status'] = 2;
        }

        if ($exportStatus == 4){
            $filters['pay_status']= 0 ;
            $filters['order_status'] = 2;
        }
        $orders['create_date'] = 'asc';
        $itemList = $this->porder_model->lists($filters, $orders);
        if(count($itemList) > 10000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        $Lists = [];
        foreach ($itemList as $key=>$item) {
            $Lists[$key][0] = $item['trade_no'];
            $Lists[$key][1] = $item['phone'];
            $Lists[$key][2] = $item['name'];
            $Lists[$key][3] = $item['total_money'];
            $order_num = $this->porder_model->getInfoByTradeno($item['trade_no']);
            $description = '';
            foreach ($order_num as $value){
                $description.= $value->title."| 数量".$value->ticket_num."包 | 单价 ".$value->count_price."元\n";
            }
            $Lists[$key][4] = $description;
            if($item['order_status'] == 0){
                $Lists[$key][5] = '已下单';
            }elseif($item['order_status'] == 2 or $item['pay_status'] == 1){
                $Lists[$key][5] = '已支付';
            }elseif($item['order_status'] == 1){
                $Lists[$key][5] = '已配送';
            }else{
                $Lists[$key][5] = '已取消';
            }

            $Lists[$key][6] = $item['area'].$item['city'].$item['address'];
            $Lists[$key][7] = $item['create_date'];

            $Lists[$key][8] = date("Y-m-d H:i:s",time());
        }
        $date = date("Y_m_d", time());
        $fileName = "订单列表_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>30,'E'=>40,'G'=>40);
        $data['headArr'] = array(
            'A1'=>'订单号',
            'B1'=>'手机号码',
            'C1'=>'用户昵称',
            'D1'=>'总金额',
            'E1'=>'订单详情描述',
            'F1'=>'订单状态',
            'G1'=>'用户地址',
            'H1'=>'下单时间',
            'I1'=>'导出时间',
        );

        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel($data);
    }
    //订单统计
    public function statistics()
    {

        $this->assign_message();
        $this->load_view('statistics');
    }

    //区域内报表导出
    public function area(){
        $startDate=$this->get_input('start_date');
        $endDate=$this->get_input('end_date');
        $selectprovince=$this->get_input('selectprovince');
        $selectprovincename=$this->get_input('selectprovincename');
        $selectcity    =$this->get_input('selectcity');
        $selectstreet  =$this->get_input('selectstreet');
        $selectcounty  =$this->get_input('selectcounty');
        $exportStatus  =$this->get_input('exportStatus');

//        var_dump($_REQUEST);exit();
        $filters = array();
        $area_param = array();
        $orders = array();



        if(!empty($startDate)){
            $filters['create_date>='] = $startDate;
        }
        if(!empty($endDate)){
            $filters['create_date<='] = $endDate;
        }

        if($exportStatus == 2 || $exportStatus == ''){
        }elseif($exportStatus == 0){
            $filters['pay_status'] = 1;
            $filters['order_status'] = 1;
        }elseif($exportStatus == 1){
            $filters['pay_status'] = 1;
            $filters['order_status'] = 2;
        }else{
            $filters['pay_status'] = 0;
            $filters['order_status'] = 2;
        }
        if(!empty($selectstreet)){
            $area_param['area_code'] = $selectstreet;
        }elseif(!empty($selectcounty)){
            $area_param['left(area_code,6)'] = $selectcounty;
        }elseif(!empty($selectcity)){
            $area_param['left(area_code,4)'] = $selectcity;
        }elseif(!empty($selectprovince)){
            $area_param['area'] = $selectprovince;
        }



        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }

        $this->load->model('porder_model');
        $totalCount = $this->porder_model->countarea($filters,$area_param);
        $rsltList = $this->porder_model->area($filters, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);

        $this->data['selectprovince']=$selectprovince;
        $this->data['selectprovincename']=$selectprovincename;
        $this->data['selectcounty']=$selectcounty;
        $this->data['selectstreet']=$selectstreet;
        $this->data['selectcity']=$selectcity;
        $this->data['startDate']=$startDate;
        $this->data['endDate']=$endDate;
        $this->data['itemList']=$rsltList;
        $this->data['exportStatus']=$exportStatus;
        $this->assign_pager($totalCount[0]['number']);
        $this->assign_message();
        $this->load_view('area');
    }


    /*
     * 区域内订单表导出
     */
    public function exportarea(){
//        var_dump($_REQUEST);exit();
        $startDate=$this->post_input('start_date');
        $endDate=$this->post_input('end_date');
        $selectprovince=$this->post_input('selectprovince');
        $selectprovincename=$this->post_input('selectprovincename');
        $selectcity    =$this->post_input('selectcity');
        $selectstreet  =$this->post_input('selectstreet');
        $selectcounty  =$this->post_input('selectcounty');
        $exportStatus  =$this->post_input('exportStatus');

        $filters = array();
        $area_param = array();
        $orders = array();
        $search='';


        if($exportStatus == 2 || $exportStatus == ''){

        }elseif($exportStatus == 0){
            $filters['pay_status'] = 1;
            $filters['order_status'] = 1;
            $search.='已付款未完成 . ';
        }elseif($exportStatus == 1){
            $filters['pay_status'] = 1;
            $filters['order_status'] = 2;
            $search.='已完成 . ';
        }else{
            $filters['pay_status'] = 0;
            $filters['order_status'] = 2;
            $search.='已取消 . ';
        }
        if(!empty($startDate)){
            $filters['create_date>='] = $startDate;
            $search.="开始时间：{$startDate} . ";
        }
        if(!empty($endDate)){
            $filters['create_date<='] = $endDate;
            $search.="结束时间：{$endDate} . ";
        }
        if(!empty($selectstreet)){
            $area_param['area_code'] = $selectstreet;
        }
        elseif(!empty($selectcounty)){
            $area_param['left(area_code,6)'] = $selectcounty;
        }
        elseif(!empty($selectcity)){
            $area_param['left(area_code,4)'] = $selectcity;
        }
        elseif(!empty($selectprovince)){
            $area_param['area'] = $selectprovince;
        }


        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $this->load->model('porder_model');
        $totalCount = $this->porder_model->countarea($filters,$area_param);
        $this->pager['pageNumber'] = 1;
        $this->pager['pageSize'] = $totalCount[0]['number'];
        $rsltList = $this->porder_model->area($filters, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
        if(count($rsltList) > 5000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        foreach ($rsltList as $key => $item) {
            $Lists[$key][0]=$item['area'];
            $Lists[$key][1]=$item['city'];
            $Lists[$key][2]=$item['names'];
            $Lists[$key][3]=$item['number'];
            $Lists[$key][4]=$item['total_money'];
            $Lists[$key][5]=$search;
        }

        $date = date("Y_m_d", time());
        $fileName = "区域订单信息_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15,'I'=>40);
        $data['headArr'] = array(
            'A1'=>'省',
            'B1'=>'市',
            'C1'=>'区域',
            'D1'=>'订单数量',
            'E1'=>'总金额',
            'F1'=>'导出条件'
        );

        $this->load->model('excel_model');
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }



    /**
     * 彩票统计
     */
    public function lottery(){
        $this->load->model('porder_model');
        $startDate=$this->get_input('start_date');
        $endDate=$this->get_input('end_date');
        $selectKeyword=$this->get_input('selectKeyword');
        $selectprovince=$this->get_input('selectprovince');
        $selectprovincename=$this->get_input('selectprovincename');
        $selectcity    =$this->get_input('selectcity');
        $selectstreet  =$this->get_input('selectstreet');
        $selectcounty  =$this->get_input('selectcounty');

        $filters = array();
        $area_param = array();
        $orders = array();
        $group_by = array();



        if(!empty($startDate)){
            $filters['shijian>='] = $startDate;
        }
        if(!empty($endDate)){
            $filters['shijian<='] = $endDate;
        }
        if (!empty($selectKeyword)){
            $filters['title%'] = $selectKeyword;
        }
        if(!empty($selectstreet)){
            $area_param['a.area_code'] = $selectstreet;
        }
        elseif(!empty($selectcounty)){
            $area_param['left(a.area_code,6)'] = $selectcounty;
        }
        elseif(!empty($selectcity)){
            $area_param['left(a.area_code,4)'] = $selectcity;
        }
        elseif(!empty($selectprovince)){
            $this->load->model('area_model');
            $province = $this->area_model->getProvince($selectprovince);
            $area_param['a.area'] = "'".$province."'";
        }




        $info=$this->porder_model->lottery($filters, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
        $count=$this->porder_model->countlottery($filters, $area_param);

//        var_dump($area_param);
        $this->data['itemList']=$info;
        $this->data['selectprovince']=$selectprovince;
        $this->data['selectprovincename']=$selectprovincename;
        $this->data['selectcounty']=$selectcounty;
        $this->data['selectstreet']=$selectstreet;
        $this->data['selectcity']=$selectcity;
        $this->data['startDate']=$startDate;
        $this->data['endDate']=$endDate;
        $this->data['selectKeyword']=$selectKeyword;
        $this->assign_pager($count[0]['number']);
        $this->load_view('lottery');
    }

    /**
     * 彩票导出
     */

    public function exportlottery(){
        $selectprovince=$this->post_input('selectprovince');
        $selectprovincename=$this->post_input('selectprovincename');
        $selectcity    =$this->post_input('selectcity');
        $selectstreet  =$this->post_input('selectstreet');
        $selectcounty  =$this->post_input('selectcounty');
        $startDate=$this->post_input('start_date');
        $endDate=$this->post_input('end_date');
        $selectKeyword=$this->post_input('selectKeyword');

        $filters = array();
        $area_param = array();
        $orders = array();

        if(!empty($startDate)){
            $filters['shijian>='] = $startDate;
        }
        if(!empty($endDate)){
            $filters['shijian<='] = $endDate;
        }
        if (!empty($selectKeyword)){
            $filters['title%'] = $selectKeyword;
        }

        if(!empty($selectstreet)){
            $area_param['area_code'] = $selectstreet;
        }
        elseif(!empty($selectcounty)){
            $area_param['left(area_code,6)'] = $selectcounty;
        }
        elseif(!empty($selectcity)){
            $area_param['left(area_code,4)'] = $selectcity;
        }
        elseif(!empty($selectprovince)){
            $area_param['area'] = $selectprovince;
        }


        $this->load->model('porder_model');
        $count=$this->porder_model->countlottery($filters, $area_param);
        $this->pager['pageNumber'] = 1;
        $this->pager['pageSize'] = $count[0]['number'];
        $rsltList=$this->porder_model->lottery($filters, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
        if(count($rsltList) > 5000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        foreach ($rsltList as $key => $item) {
            $Lists[$key][0]=$item['title'];
            $Lists[$key][1]=$item['count_price'];
            $Lists[$key][2]=$item['ticket_num'];
            $Lists[$key][3]=$item['ticke_money'];
//            $Lists[$key][4]=$item['name'];
//            $Lists[$key][5]=$item['area'];
//            $Lists[$key][6]=$item['city'];
//            $Lists[$key][7]=$item['address'];
        }

        $date = date("Y_m_d", time());
        $fileName = "彩票出售_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15,'I'=>40);
        $data['headArr'] = array(
            'A1'=>'票名',
            'B1'=>'单价',
            'C1'=>'数量',
            'D1'=>'总价',
//            'E1'=>'购买人',
//            'F1'=>'省',
//            'G1'=>'市',
//            'H1'=>'区域'
        );

        $this->load->model('excel_model');
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }


    public function test(){
        $this->load->model('porder_model');
        $res=$this->porder_model->test_order();
        var_dump($res);
    }
}