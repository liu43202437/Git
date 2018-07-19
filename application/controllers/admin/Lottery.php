<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Lottery
 * 访销经理管理
 */
class Lottery extends Base_AdminController {
    public function __construct()
    {
        parent::__construct();
        $this->view_path='admin/lottery/';
        $this->load->model('club_model');
        $this->load->model('lottery_model');
    }

    public function lists(){
        $this->load->model('area_model');
        $this->load->model('audit_model');
        $this->load->model('auditconfig_model');
        $this->load->model('user_model');
        $searchKey = $this->get_input('search_key');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');
        $selectprovince = $this->get_input('selectprovince');
        $selectcity = $this->get_input('selectcity');
        $selectcityname = $this->get_input('selectcityname');
        $selectprovincename = $this->get_input('selectprovincename');
        $selectcounty = $this->get_input('selectcounty');
        $selectstreet = $this->get_input('selectstreet');
        $type == NULL ? $type = 2 : '';
        $filters = array();
        $area_param = array();
        $orders = array();
        $filters['refuse'] = 0;
        if (!empty($searchKey)) {
            $filters['name%, phone%, id_number%'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        $clubStatus = array(0,1);
        if(in_array($type, $clubStatus)){
            $filters['status'] = $type;
        }
        elseif($type == 3){
            $filters['refuse'] = 1;
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
            $area_param['area_id'] = $selectprovince;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->lottery_model->getCountTwo($filters,$area_param);
        $rsltList = $this->lottery_model->getListTwo($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);


        $this->data['searchKey'] = $searchKey;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->data['selectprovince'] = $selectprovince;
        $this->data['selectprovincename'] = $selectprovincename;
        $this->data['selectcity'] = $selectcity;
        $this->data['cities'] = $this->area_model->getCityList();
        $this->data['selectcounty'] = $selectcounty;
        $this->data['selectstreet'] = $selectstreet;
        $this->assign_pager($totalCount);
        $this->assign_message();
        return $this->load_view('lists');
    }

    public function exportClub(){
        set_time_limit(0);
        $this->load->model('area_model');
        $this->load->model('excel_model');
        $searchKey = $this->post_input('search_key');
        $startDate = $this->post_input('startDate');
        $endDate = $this->post_input('endDate');
        $type = $this->post_input('type');
        $selectprovince = $this->post_input('selectprovince');
        $selectcity = $this->post_input('selectcity');
        $selectcityname = $this->post_input('selectcityname');
        $selectprovincename = $this->post_input('selectprovincename');
        $selectcounty = $this->post_input('selectcounty');
        $selectstreet = $this->post_input('selectstreet');
        $type == NULL ? $type = 2 : '';
        $filters = array();
        $area_param = array();
        $orders = array();
        $filters['refuse'] = 0;
        if (!empty($searchKey)) {
            $filters['name%, phone%, id_number%'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        $clubStatus = array(0,1);
        if(in_array($type, $clubStatus)){
            $filters['status'] = $type;
        }
        elseif($type == 3){
            $filters['refuse'] = 1;
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
            $area_param['area_id'] = $selectprovince;
        }

        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->lottery_model->getCountTwo($filters,$area_param);
        $this->pager['pageNumber'] = 1;
        $this->pager['pageSize'] = $totalCount;
        $info = $this->lottery_model->getListTwo($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
        if(count($info) > 5000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        $area_code_name = $this->area_model->get_area_code_name($selectprovince,$selectcity,$selectcounty);
        $area_temp = [];
        foreach ($area_code_name as $val){
            $area_temp[$val['area_id']] = $val['name'];
        }
        $temp = [];
        foreach ($info as $key => $value) {
            $temp[$key][0] = $value['name'];
            $temp[$key][1] = $value['phone'];
            $temp[$key][2] = $value['id_number'];
            $temp[$key][3] = $value['company'];
            $temp[$key][4] = $value['create_date'];
            $temp[$key][5] = $value['area_id'];
            $temp[$key][6] = $value['city'];
            $temp[$key][7] = $value['address'];
        }
        $Lists = $temp;
        $date = date("Y_m_d", time());
        $fileName = "访销经理信息_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15,'I'=>40);
        $data['headArr'] = array(
            'A1'=>'姓名',
            'B1'=>'电话',
            'C1'=>'身份证号',
            'D1'=>'所属公司',
            'E1'=>'申请时间',
            'F1'=>'省份',
            'G1'=>'城市',
            'H1'=>'区域'
        );

        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }

    /**
     * 审核通过
     */
    public function check(){
        $this->load->model('user_model');
        $id_num = '';
        $ids = $this->post_input('ids');
        $status = $this->post_input('status');
        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }
        $info = $this->lottery_model->fetchOne(array('id'=>$ids[0]));
        if(!empty($info) && $info['status'] == 1){
            $data = parent::success_message();
            echo json_capsule($data);
            return;
        }
        foreach ($ids as $id) {
            $this->lottery_model->update($id, ['status' => $status]);
        }
        if($status == 1){
            $this->add_log('访销经理审核通过', $ids);
        }
        elseif($status == 0){
            $this->add_log('访销经理审核不通过', $ids);
        }
        $flag = $this->ajaxSend($info['phone'],'238665');
        //取店铺信息
        // $info = $this->club_model->fetchOne(array('id'=>$ids[0]));
        // $name = $info['name'];
        // $phone = $info['phone'];
        // $user_id = $info['user_id'];
        // $info = $this->user_model->getInfoById($user_id);
        // $openid = $info['weixin'];

        // $this->passShop($openid,$name,$phone);
        if($flag['success']){
            $data = parent::success_message();
            echo json_capsule($data);
        }
        else{
            $data = array(
                'type' => 'success',
                'content' => '通过成功，但发送短信通知失败'
            );
            echo json_capsule($data);
        }
    }


    /**
     * 审核拒绝
     */
    public function refuse(){
        $this->load->model("Common_model");
        $rs = [];
        $id = $this->post_input('id');
        $content = $this->post_input('content');

        //取店铺信息
        $info = $this->lottery_model->fetchOne(array('id'=>$id));
        $user_id = $info['user_id'];
        $phone = $info['phone'];
        //删除用户店铺信息
        // $flag = $this->club_model->deleteData(array('id'=>$id));
        $flag2 = $this->lottery_model->updateData(array('refuse'=>1),array('id'=>$id));
        //记录拒绝信息
        $this->Common_model->setTable('tbl_appmsg');
        $insertData = [];
        $insertData['msg'] = $content;
        $insertData['from'] = -1;
        $insertData['to'] = $user_id;
        $insertData['msgtype'] = 'lottery no pass';
        $insertData['data'] = json_encode($info);
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $flag = $this->Common_model->insertData($insertData);

        // $this->test_tmp($openid,$content);
        $flag = $this->ajaxSend($phone,'238666');
        if($flag['success']){
            $response = [];
            $response['success'] = true;
        }
        else{
            $response = [];
            $response['success'] = false;
        }
        $this->add_log('访销审核不通过', ['id'=>$id]);
        echo json_capsule($response);
    }


    /**
     * @param $mobile
     * @param string $modelId
     * @return array
     * 发送短信
     */
    public function ajaxSend($mobile,$modelId = '223984') {
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


    public function edit(){
        $this->load->model('area_model');
        $this->load->model('user_model');
        $id = $this->get_input('id');
        if (empty($id)) {
            $this->data['isNew'] = true;
            $itemInfo = $this->club_model->getEmptyRow();

        } else {
            $this->data['isNew'] = false;
            $itemInfo = $this->lottery_model->get($id);
            if (!empty($itemInfo['area_id'])) {
                $city = $this->area_model->get($itemInfo['area_id']);
                $this->data['cities'] = $this->area_model->getCityList($city['id']);
                $itemInfo['province_id'] = $city['id'];
            }
        }
        $this->data['provinces'] = $this->area_model->getProvinceList();
        $cities = $this->area_model->fetchAll();

        foreach ($cities as $key => $value) {
            if($value['name'] == $itemInfo['city']){
                $itemInfo['city_id'] = $value['id'];
            }
        }
        $this->data['itemInfo'] = $itemInfo;
        $this->assign_message();
        $this->load_view('edit');
    }

    public function saves(){
        $this->load->model('user_model');
        $id = $this->post_input('id');
        // var_dump($this->input->post());die;

        $data['name'] = $this->post_input('name');
        $data['area_id'] = $this->post_input('selectprovince');
        $data['city'] = $this->post_input('selectcityname');
        $data['address'] = $this->post_input('address');
        $data['phone'] = $this->post_input('phone');
        $data['id_number'] = $this->post_input('id_number');
        $data['company'] = $this->post_input('company');
        $selectprovince = $this->post_input('selectprovince');
        $selectcity = $this->post_input('selectcity');
        $selectcounty = $this->post_input('selectcounty');
        $selectstreet = $this->post_input('selectstreet');
        $selectcityname = $this->post_input('selectcityname');
        if(!empty($selectprovince)){
            $data['area_code'] = $selectprovince;
        }
        if(!empty($selectcity)){
            $data['area_code'] = $selectcity;
        }
        if(!empty($selectcounty)){
            $data['area_code'] = $selectcounty;
        }
        if(!empty($selectstreet)){
            $data['area_code'] = $selectstreet;
        }
        if(!empty($data['phone'])){
            $info = $this->lottery_model->fetchOne(array('phone'=>$data['phone']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('手机号码已经存在')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        if(!empty($data['id_number'])){
            $info = $this->lottery_model->fetchOne(array('id_number'=>$data['id_number']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('身份证号码已经存在')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        $this->lottery_model->update($id, $data);
        $this->success_redirect('lottery/lists');
    }
    public function more(){  
        $kind = $this->get_input('kind');
        empty($kind) ? $kind = 1 : '';
        switch ($kind) {
            case 1:
                $this->lottery_papers_list();
                break;
            case 2:
                $this->receipt_list();
                break;
            case 3:
                $this->interview_list();
                break;
            case 4:
                $this->interview_log_list();
                break;
            case 5:
                $this->interview_images_list();
                break;
            
            
            default:
                # code...
                break;
        }
    }
    public function lottery_papers_list(){
        $id = $this->get_input('id');
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_papers');
        $user_id = $this->get_input('user_id');
        $kind = $this->get_input('kind');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');
        $lottery_papers = $this->get_input('lottery_papers');
        $name = $this->get_input('name');
        $phone = $this->get_input('phone');
        $filters = array();
        $orders = array();
        !empty($user_id) ? $filters['user_id'] = $user_id : '';
        !empty($lottery_papers) ? $filters['lottery_papers%'] = $lottery_papers : '';
        !empty($name) ? $filters['name%'] = $name : '';
        !empty($phone) ? $filters['phone%'] = $phone : '';
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '1':
                $filters['valid'] = 1;
                break;
            case '2':
                $filters['valid'] = 0;
                break;
            
            default:
                # code...
                break;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($rsltList as $key => $value) {
            $info = $this->club_model->fetchOne(array('id'=>$value['club_id']));
            if(!empty($info)){
                $rsltList[$key] += $info;
            }
            else{
                $rsltList[$key]['yan_code'] = NULL;
                $rsltList[$key]['city'] = NULL;
                $rsltList[$key]['address'] = NULL;
            }
        }
        $this->data['user_id'] = $user_id;
        $this->data['kind'] = $kind;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->data['lottery_papers'] = $lottery_papers;
        $this->data['name'] = $name;
        $this->data['phone'] = $phone;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('more/lottery_papers/lists');
    }
    public function lottery_papers_edit(){
        $this->load->model('Common_model');
        $this->Common_model->setTable('tbl_lottery_papers');
        $id = $this->get_input('id');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        $this->data['itemInfo'] = $info;
        $this->assign_message();
        $this->load_view('more/lottery_papers/edit');
    }
    public function receipt_list(){
        $id = $this->get_input('id');
        $this->load->model('Common_model');
        $this->Common_model->setTable('tbl_lottery_receipt');
        $user_id = $this->get_input('user_id');
        $kind = $this->get_input('kind');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');
        $notes = $this->get_input('notes');
        $filters = array();
        $orders = array();
        !empty($user_id) ? $filters['user_id'] = $user_id : '';
        !empty($notes) ? $filters['notes%'] = $notes : '';
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '1':
                $filters['valid'] = 1;
                break;
            case '2':
                $filters['valid'] = 0;
                break;
            
            default:
                # code...
                break;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($rsltList as $key => $value) {
            $rsltList[$key]['receipt_image'] = json_decode($value['receipt_image'],true);
        }
        $this->data['user_id'] = $user_id;
        $this->data['kind'] = $kind;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->data['notes'] = $notes;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('more/receipt/lists');
    }
    public function receipt_edit(){
        $this->load->model('Common_model');
        $this->Common_model->setTable('tbl_lottery_receipt');
        $id = $this->get_input('id');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        $info['receipt_image'] = json_decode($info['receipt_image'],true);
        $this->data['itemInfo'] = $info;
        $this->assign_message();
        $this->load_view('more/receipt/edit');
    }
    public function interview_list(){
        $id = $this->get_input('id');
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_interview');
        $user_id = $this->get_input('user_id');
        $kind = $this->get_input('kind');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');
        $filters = array();
        $orders = array();
        !empty($user_id) ? $filters['user_id'] = $user_id : '';
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '1':
                $filters['valid'] = 1;
                break;
            case '2':
                $filters['valid'] = 0;
                break;
            
            default:
                # code...
                break;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($rsltList as $key => $value) {
            $info = $this->club_model->fetchOne(array('id'=>$value['club_id']));
            if(!empty($info)){
                $rsltList[$key] += $info;
            }
            else{
                $rsltList[$key]['name'] = NULL;
                $rsltList[$key]['phone'] = NULL;
                $rsltList[$key]['city'] = NULL;
                $rsltList[$key]['address'] = NULL;
            }
            if(!empty($value['end_time'])){
                $interval = strtotime($value['end_time']) - strtotime($value['begin_time']);
                $hour =  floor($interval/3600);
                $minute = floor(($interval - $hour*3600)/60);
                $second = floor(($interval - $hour*3600 - 60*$minute)%60);
                $interval = "{$hour}小时{$minute}分钟{$second}秒";
                $rsltList[$key]['interval'] = $interval;
            }
            else{
                $rsltList[$key]['interval'] = NULL;
            }

        }
        $this->data['user_id'] = $user_id;
        $this->data['kind'] = $kind;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('more/interview/lists');
    }
    public function interview_edit(){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_interview');
        $id = $this->get_input('id');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        $clubInfo = $this->club_model->fetchOne(array('id'=>$info['club_id']));
        $info['name'] = $clubInfo['name'];
        $info['phone'] = $clubInfo['phone'];
        $info['address'] = $clubInfo['city'].$clubInfo['address'];
        if(!empty($info['end_time'])){
            $interval = strtotime($info['end_time']) - strtotime($info['begin_time']);
            $hour =  floor($interval/3600);
            $minute = floor(($interval - $hour*3600)/60);
            $second = floor(($interval - $hour*3600 - 60*$minute)%60);
            $interval = "{$hour}小时{$minute}分钟{$second}秒";
        }
        else{
            $interval = NULL;
        }
        $info['interval'] = $interval;
        switch ($info['status']) {
            case '1':
                $info['status'] = '打卡中';
                break;
            case '2':
                $info['status'] = '结束';
                break;
            default:
                # code...
                break;
        }
        $this->data['itemInfo'] = $info;
        $this->assign_message();
        $this->load_view('more/interview/edit');
    }
    public function interview_log_list(){
        $id = $this->get_input('id');
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $user_id = $this->get_input('user_id');
        $kind = $this->get_input('kind');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');
        $filters = array();
        $orders = array();
        !empty($user_id) ? $filters['user_id'] = $user_id : '';
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '1':
                $filters['valid'] = 1;
                break;
            case '2':
                $filters['valid'] = 0;
                break;
            
            default:
                # code...
                break;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($rsltList as $key => $value) {
            $info = $this->club_model->fetchOne(array('id'=>$value['club_id']));
            if(!empty($info)){
                $rsltList[$key] += $info;
            }
            else{
                $rsltList[$key]['name'] = NULL;
                $rsltList[$key]['phone'] = NULL;
                $rsltList[$key]['city'] = NULL;
                $rsltList[$key]['address'] = NULL;
            }
        }
        $this->data['user_id'] = $user_id;
        $this->data['kind'] = $kind;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('more/interview_log/lists');
    }
    public function interview_log_edit(){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_interview_log');
        $id = $this->get_input('id');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        $clubInfo = $this->club_model->fetchOne(array('id'=>$info['club_id']));
        $info['name'] = $clubInfo['name'];
        $info['phone'] = $clubInfo['phone'];
        $info['address'] = $clubInfo['city'].$clubInfo['address'];
        $this->data['itemInfo'] = $info;
        $this->assign_message();
        $this->load_view('more/interview_log/edit');
    }
    public function interview_images_list(){
        $id = $this->get_input('id');
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_interview_images');
        $user_id = $this->get_input('user_id');
        $kind = $this->get_input('kind');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');
        $filters = array();
        $orders = array();
        !empty($user_id) ? $filters['user_id'] = $user_id : '';
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '1':
                $filters['valid'] = 1;
                break;
            case '2':
                $filters['valid'] = 0;
                break;
            
            default:
                # code...
                break;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($rsltList as $key => $value) {
            $info = $this->club_model->fetchOne(array('id'=>$value['club_id']));
            if(!empty($info)){
                $rsltList[$key] += $info;
            }
            else{
                $rsltList[$key]['name'] = NULL;
                $rsltList[$key]['phone'] = NULL;
                $rsltList[$key]['city'] = NULL;
                $rsltList[$key]['address'] = NULL;
            }
            $rsltList[$key]['images'] = json_decode($value['images'],true);
        }
        // var_dump($rsltList);
        $this->data['user_id'] = $user_id;
        $this->data['kind'] = $kind;
        $this->data['itemList'] = $rsltList;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('more/interview_images/lists');
    }
    public function interview_images_edit(){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_lottery_interview_images');
        $id = $this->get_input('id');
        $info = $this->Common_model->fetchOne(array('id'=>$id));
        $clubInfo = $this->club_model->fetchOne(array('id'=>$info['club_id']));
        $info['name'] = $clubInfo['name'];
        $info['phone'] = $clubInfo['phone'];
        $info['address'] = $clubInfo['city'].$clubInfo['address'];
        $info['images'] = json_decode($info['images'],true);
        $this->data['itemInfo'] = $info;
        $this->assign_message();
        $this->load_view('more/interview_images/edit');
    }
}