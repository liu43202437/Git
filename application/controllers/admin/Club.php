<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
class Club extends Base_AdminController
{

    function __construct()
    {
        parent::__construct();

        $this->view_path = 'admin/club/';
        $this->load->model('club_model');
    }

    public function lists()
    {
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
        $selectcounty = $this->get_input('selectcounty');
        $selectstreet = $this->get_input('selectstreet');
        $type == NULL ? $type = 2 : '';
        $filters = array();
        $area_param = array();
        $orders = array();
        if (!empty($searchKey)) {
            $filters['name%, view_name%, phone%, id_number%'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '0':
                $filters['status'] = 0;
                $filters['refuse'] = 0;
                break;
            case '1':
                $filters['status'] = 1;
                break;
            case '2':
                $filters['status'] = 2;
                break;
            case '3':
                $filters['status'] = 1;
                $filters['order_status!='] = 0;
                break;
            case '4':
                $filters['status'] = 1;
                $filters['order_status'] = array(2,3,4);
                break;
            
            default:
                # code...
                break;
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
        $totalCount = $this->club_model->getCountTwo($filters,$area_param);
        $rsltList = $this->club_model->getListTwo($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
        //获取城市列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        foreach ($rsltList as $key => $item) {
            $rsltList[$key]['address'] = $provinces_temp[$item['area_id']].$item['city'].$item['address'];
            $avatar = $this->user_model->getInfoById($item['user_id']);
            $rsltList[$key]['avatar'] = $avatar['avatar_url'];
        }
        
        //获取省份列表
        //获取城市列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
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
        $this->data['selectcounty'] = $selectcounty;
        $this->data['selectstreet'] = $selectstreet;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('list');
    }

    public function check()
    {
        $this->load->model('user_model');
        $this->load->model('sendmsg_model');
        $id_num = '';
        $ids = $this->post_input('ids');
        $status = $this->post_input('status');
        $lottery_license = $this->post_input('lottery_license');
        $stationId = $this->post_input('stationId');
        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }
        $clubinfo = $this->club_model->fetchOne(array('id'=>$ids[0]));
        if($clubinfo['status'] == 1){
            $data = parent::success_message();
            echo json_capsule($data);
            return;
        }
        foreach ($ids as $id) {
            $this->club_model->update($id, ['status' => $status,'lottery_license'=>$lottery_license]);
        }
        if(empty($stationId)){
		$stationId = '45898888';
        }
        if(!empty($stationId)){
            $stationId = trim($stationId);
            $where = array('id'=>$clubinfo['user_id']);
            $flag = $this->user_model->updateData(array('stationId'=>$stationId),$where);
        }
        if($status == 1){
            $this->add_log('零售店审核通过', $ids);
        }
        elseif($status == 0){
            $this->add_log('零售店审核不通过', $ids);
        }
        //发送短信通知
        if(empty($clubinfo)){
            $clubinfo = $this->club_model->fetchOne(array('id'=>$ids[0]));
        }
        $flag = $this->ajaxSend($clubinfo['phone'],'236001');
        //取店铺信息
        $info = $this->club_model->fetchOne(array('id'=>$ids[0]));
        $name = $info['name'];
        $phone = $info['phone'];
        $user_id = $info['user_id'];
        $info = $this->user_model->getInfoById($user_id);
        $openid = $info['weixin'];
        if(!empty($openid)){
            $this->sendmsg_model->passShopByAdmin($openid,$name,$phone);
        }
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

    public function delete()
    {
        $ids = $this->post_input('ids');
        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }

        foreach ($ids as $id) {
            $this->club_model->deleteclubs($id);
        }

        $this->add_log('删除道馆信息', $ids);

        $data = parent::success_message();
        echo json_capsule($data);
    }

    public function toggle_show()
    {
        $id = $this->post_input('id');
        $ids = $this->post_input('ids');
        if (empty($id) && empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }

        if (empty($ids)) {    // one item operation
            $data['is_show$'] = '1-is_show';
            $this->club_model->update($id, $data);

        } else {            // batch item operation
            $data['is_show'] = $this->post_input('is_show', 0);
            foreach ($ids as $id) {
                $this->club_model->update($id, $data);
            }
        }

        echo json_capsule(parent::success_message());
    }

    public function edit()
    {
        $this->load->model('area_model');
        $this->load->model('user_model');
        $id = $this->get_input('id');
        if (empty($id)) {
            $this->data['isNew'] = true;
            $itemInfo = $this->club_model->getEmptyRow();

        } else {
            $this->data['isNew'] = false;
            $itemInfo = $this->club_model->get($id);
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
        //取站点信息
        $user_id = $itemInfo['user_id'];
        $userinfo = $this->user_model->getInfoById($user_id);
        $itemInfo['stationId'] = $userinfo['stationId'];
        $this->data['itemInfo'] = $itemInfo;
        //获取客户经理列表
        $this->load->model('consumer_model');
        $where = array('type'=>'manager');
        $info = $this->consumer_model->listAll($where);
        $this->data['consumers'] = $info;
        $this->assign_message();
        $this->load_view('edit');
    }

    public function edit_from_audit()
    {
        $id = $this->get_input('id');
        if (empty($id)) {
            show_errorpage();
        }

        $this->load->model('audit_model');
        $auditInfo = $this->audit_model->get($id);

        $itemInfo = $this->club_model->getEmptyRow();
        /*$itemInfo['contact'] = $auditInfo['name'];
        $itemInfo['phone'] = $auditInfo['mobile'];*/

        for ($i = 1; $i <= 20; $i++) {
            $value = $auditInfo['attribute' . $i];
            if (empty($value)) {
                break;
            }
            $parts = explode('|', $value);
            if (count($parts) < 2) {
                break;
            }
            $value = $parts[0];
            if (isset($parts[2]) && !empty($parts[2])) {
                $itemInfo[$parts[2]] = $parts[0];
            }
        }

        $this->load->model('area_model');
        $this->data['provinces'] = $this->area_model->getProvinceList();

        $this->data['isNew'] = true;
        $this->data['auditItemId'] = $id;
        $this->data['itemInfo'] = $itemInfo;
        $this->assign_message();
        $this->load_view('edit');
    }

    public function saves()
    {
        $this->load->model('user_model');
        $id = $this->post_input('id');
        // var_dump($this->input->post());die;

        $auditItemId = $this->post_input('audit_item_id');
        $data['name'] = $this->post_input('name');
        $data['area_id'] = $this->post_input('selectprovince');
        $data['city'] = $this->post_input('selectcityname');
        $data['address'] = $this->post_input('address');
        $data['phone'] = $this->post_input('phone');
        $data['id_number'] = $this->post_input('id_number');
        $data['yan_code'] = $this->post_input('yan_code');
        $data['sales_volume'] = $this->post_input('sales_volume');
        $data['manager_name'] = $this->post_input('manager_name');
        $data['manager_id'] = $this->post_input('manager_id');
        $data['service_time'] = $this->post_input('service_time');
        $data['lottery_license'] = $this->post_input('lottery_license');
        $data['introduction'] = $this->post_input('introduction');
        $selectprovince = $this->post_input('selectprovince');
        $selectcity = $this->post_input('selectcity');
        $selectcounty = $this->post_input('selectcounty');
        $selectstreet = $this->post_input('selectstreet');
        $selectcityname = $this->post_input('selectcityname');
        $stationId = $this->post_input('stationId');
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
        $images = $this->post_input('images', array());
        if(!empty($data['phone'])){
            $info = $this->club_model->fetchOne(array('phone'=>$data['phone']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('手机号码已经存在')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        if(!empty($data['id_number'])){
            $info = $this->club_model->fetchOne(array('id_number'=>$data['id_number']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('身份证号码已经存在')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        if(!empty($data['yan_code'])){
            $info = $this->club_model->fetchOne(array('yan_code'=>$data['yan_code']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('烟草证号码已经存在')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        if(!empty($data['lottery_license'])){
            $info = $this->club_model->fetchOne(array('lottery_license'=>$data['lottery_license']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('彩票许可证号码已经存在')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        if ($auditItemId) {
            $this->load->model('audit_model');

            $aData['status'] = AUDIT_STATUS_PASSED;
            $aData['audit_date'] = now();
            $this->audit_model->update($auditItemId, $aData);
        }
        $this->club_model->update($id, $data);
        //更新stationId
        $clubinfo = $this->club_model->fetchOne(array('id'=>$id));
        $flag = $this->user_model->updateData(array('stationId'=>$stationId),array('id'=>$clubinfo['user_id']));
        $this->add_log('编辑零售店', array('id' => $id));
        $this->club_model->deleteImageByClub($id);
        foreach ($images as $image) {
            $this->club_model->insertImage($id, $image);
        }
        $this->success_redirect('club/lists');
    }
    public function layer_audit(){
        $id = $this->get_input('id');
        //获取问卷信息
        $this->load->model('audit_model');
        $user_id = $this->club_model->fetchOne(array('id'=>$id))['user_id'];
        $audit =  $this->audit_model->getByUserid($user_id);
        $temp = [];
        foreach ($audit as $key => $value) {
            if(strpos($key, 'attribute') !== false && !empty($value)){
                $value = explode('|', $value);
                $temp[] = $value;
            }
        }
        $this->data['audits'] = $temp;
        $this->data['id'] = $id;
        $this->data['user_id'] = $user_id;
        $this->load_view('layer_audit');
    }
    //中维短信
    //Demo调用
    //**************************************举例说明***********************************************************************
    //*假设您用测试Demo的APP ID，则需使用默认模板ID 1，发送手机号是13800000000，传入参数为6532和5，则调用方式为           *
    //*result = sendTemplateSMS("13800000000" ,array('6532','5'),"1");                                                                        *
    //*则13800000000手机号收到的短信内容是：【云通讯】您使用的是云通讯短信模板，您的验证码是6532，请于5分钟内正确输入     *
    //*********************************************************************************************************************
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
    //零售店审核拒绝
    public function refuse(){
        $this->load->model("Common_model");
        $rs = [];
        $id = $this->post_input('id');
        $content = $this->post_input('content');

        //取店铺信息
        $info = $this->club_model->fetchOne(array('id'=>$id));
        $user_id = $info['user_id'];
        $phone = $info['phone'];
        //删除用户店铺信息
        // $flag = $this->club_model->deleteData(array('id'=>$id));
        $flag2 = $this->club_model->updateData(array('refuse'=>1),array('id'=>$id));
        //记录拒绝信息
        $this->Common_model->setTable('tbl_appmsg');
        $insertData = [];
        $insertData['msg'] = $content;
        $insertData['from'] = -1;
        $insertData['to'] = $user_id;
        $insertData['msgtype'] = 'club no pass';
        $insertData['data'] = json_encode($info);
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $flag = $this->Common_model->insertData($insertData);

        // $this->test_tmp($openid,$content);
        $flag = $this->ajaxSend($phone,'235976');
        if($flag['success']){
            $response = [];
            $response['success'] = true;
        }
        else{
            $response = [];
            $response['success'] = false;
        }
        $this->add_log('零售店审核不通过', ['id'=>$id]);
        echo json_capsule($response);
    }
    public function test_tmp($openid,$content){
        $this->load->model('sendmsg_model');
        $re = $this->sendmsg_model->refuseshop($openid,$content);
    }
    public function passShop($openid,$name,$phone){
        $this->load->model('sendmsg_model');
        $re = $this->sendmsg_model->passShop($openid,$name,$phone);
    }
    //添加零售店
    public function addClub(){
        $this->load->model('area_model');
        $this->data['provinces'] = $this->area_model->getProvinceList();
        $this->data['isNew'] = 1;
        $this->load_view('add');
    }
    public function doAddclub(){
        $this->load->model('user_model');
        $allInfo = $this->input->post();
        $keyArr = array('lottery_license','introduction','files','stationId');
        foreach ($allInfo as $key => $value) {
            if(in_array($key, $keyArr)){
                continue;
            }
            if(empty($value)){
                echo "<script>alert('请检查是否漏填')</script>";
                echo "<script>history.back()</script>";
                return;
            }
        }
        //查询该user_id是否注册过店铺
        $user_id = $allInfo['user_id'];
        $yan_code = $allInfo['yan_code'];
        $id_number = $allInfo['id_number'];
        $info = $this->club_model->fetchOne(array('user_id'=>$user_id));
        if(!empty($info)){
             echo "<script>alert('该用户已经注册过店铺')</script>";
             echo "<script>history.back()</script>";
             return;
        }
        $info = $this->club_model->fetchOne(array('yan_code'=>$yan_code));
        if(!empty($info)){
             echo "<script>alert('该烟草证号已经注册过店铺')</script>";
             echo "<script>history.back()</script>";
             return;
        }
        $info = $this->club_model->fetchOne(array('id_number'=>$id_number));
        if(!empty($info)){
             echo "<script>alert('该身份证号已经注册过店铺')</script>";
             echo "<script>history.back()</script>";
             return;
        }
        $insertData = [];
        $insertData['name'] = $allInfo['name'];
        $insertData['logo'] = '';
        $insertData['phone'] = $allInfo['phone'];
        $insertData['area_id'] = $allInfo['area_id'];
        $insertData['city'] = $allInfo['city'];
        $insertData['address'] = $allInfo['address'];
        $insertData['is_show'] = 1;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $insertData['user_id'] = $allInfo['user_id'];
        $insertData['id_number'] = $allInfo['id_number'];
        $insertData['yan_code'] = $allInfo['yan_code'];
        $insertData['status'] = 0;
        $insertData['manager_id'] = $allInfo['manager_id'];
        $insertData['manager_name'] = $allInfo['manager_name'];
        $insertData['question'] = 1;
        $insertData['refuse'] = 0;
        $insertData['lottery_license'] = !empty($allInfo['lottery_license']) ? $allInfo['lottery_license'] : '';
        $insertData['introduction'] = !empty($allInfo['introduction']) ? $allInfo['introduction'] : '';
        $clubId = $this->club_model->wxinsert($insertData);
        if(!empty($allInfo['stationId'])){
            $flag = $this->user_model->updateData(array('stationId'=>$allInfo['stationId']),array('id'=>$user_id));
        }
        $this->add_log('添加店铺',$clubId);
        $this->success_redirect('club/lists');
    }
    public function getUserid(){
        $rs = [];
        $this->load->model('mydb_model');

        $nickname = $this->post_input('nickname');
        if(empty($nickname)){
            echo "请输入用户昵称";
            return;
        }
        $sql = "select * from tbl_user where nickname like '%{$nickname}%'";
        $info = $this->mydb_model->queryAll($sql);
        foreach ($info as $key => $value) {
            $rs['result'][$key]['nickname'] = $value['nickname'];
            $rs['result'][$key]['id'] = $value['id'];
            $rs['error'] = 0;
        }
        echo json_encode($rs);
    }
    //导出店铺
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
        $selectcounty = $this->post_input('selectcounty');
        $selectstreet = $this->post_input('selectstreet');
        $type == NULL ? $type = 2 : '';
        $filters = array();
        $area_param = array();
        $orders = array();
        if (!empty($searchKey)) {
            $filters['name%, view_name%, phone%, id_number%'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date<='] = $endDate.' 23:59:59';
            $filters['create_date>='] = $startDate.' 00:00:00';
        }
        switch ($type) {
            case '0':
                $filters['status'] = 0;
                $filters['refuse'] = 0;
                break;
            case '1':
                $filters['status'] = 1;
                break;
            case '2':
                $filters['status'] = 2;
                break;
            case '3':
                $filters['status'] = 1;
                $filters['order_status!='] = 0;
                break;
            case '4':
                $filters['status'] = 1;
                $filters['order_status'] = array(2,3,4);
                break;
            
            default:
                # code...
                break;
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

//        var_dump($filters);exit();

        $totalCount = $this->club_model->getCountTwo($filters,$area_param);
        $this->pager['pageNumber'] = 1;
        $this->pager['pageSize'] = $totalCount;
        $info = $this->club_model->getListTwo($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
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
            $temp[$key][1] = $value['view_name'];
            $temp[$key][2] = $value['phone'];
            $temp[$key][3] = $value['id_number'].'`';
            $temp[$key][4] = $value['yan_code'].'`';
            $area_id = (int)$value['area_id'];
            $temp[$key][5] = $area_temp[$area_id];
            $temp[$key][6] = $value['city'];
            if(!empty($value['area_code'])){
                $temp[$key][7] = $area_temp[substr($value['area_code'],0,6)];
            }else{
                $temp[$key][7] = '未知';
            }
            $temp[$key][8] = $value['address'];
            $temp[$key][9] = $value['create_date'];
            $temp[$key][10] = $value['bank_name']; //开户行
            $temp[$key][11] = $value['bank_card_id'].'`';  //银行卡账号
            $temp[$key][13] = $value['manager_name']; //客户经理姓名
            $temp[$key][14] = $value['manager_id'];  //客户经理手机
            $temp[$key][15] = $value['id'];  //店铺ID
        }
        $Lists = $temp;
        $date = date("Y_m_d", time());
        $fileName = "零售店信息_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15,'I'=>100,'K'=>80,'L'=>40);
        $data['headArr'] = array(
            'A1'=>'本人姓名',
            'B1'=>'店铺名称',
            'C1'=>'手机号码',
            'D1'=>'身份证号',
            'E1'=>'烟草证号',
            'F1'=>'省份',
            'G1'=>'城市',
            'H1'=>'区域',
            'I1'=>'详细地址',
            'J1'=>'申请时间',
            'K1'=>'开户行',
            'L1'=>'银行卡账号',
            'M1'=>'客户经理姓名',
            'N1'=>'客户经理手机',
            'O1'=>'店铺ID',
        );


        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }
    //批量审核店铺
    public function batchCheck(){
        $this->assign_message();

        $this->load_view('batchCheck');

    }
    public function doBatchCheck(){
        set_time_limit(120);
        $postFile =  $_FILES;
        $filename = $postFile['file']['tmp_name'];
        // $filename = "D:/image/2018.xlsx";
        $data = $this->readExcel($filename);
        $header = $data[1];
        $format = $this->judgeFormat($data);
        array_shift($data);
        $info = $data;
        $res = $this->passCheck($info,$format);
        $this->exportRes($res,$header);
    }
    public function exportRes($res,$header){
        set_time_limit(120);
        $this->load->model('excel_model');
        $Lists = $res;
        $date = date("Y_m_d", time());
        $fileName = "批量审核零售店结果_{$date}.xls";

        $rand = range('A','Z');
        foreach ($rand as $key => &$value) {
            $value = $value.'1';
        }
        $temp = [];
        $length = count($header);
        for ($i=0; $i < $length; $i++) { 
            $temp[$rand[$i]] = $header[$i];
        }
        $temp[$rand[$length]] = '备注';
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15);
        $data['headArr'] = $temp;
        unset($temp);
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }
    public function passCheck($info,$format){
        $record = $info;
        $p = count($record[0]);
        $p_name = $format['name'];
        $p_phone = $format['phone'];
        $p_id_number = $format['id_number'];
        foreach ($info as $key => $value) {
            $name = $value[$p_name];
            $phone = $value[$p_phone];
            $id_number = $value[$p_id_number];
            $res = $this->judgeParam($name,$phone,$id_number);
            if($res !== true){
                $record[$key][$p] = $res;
                continue;
            }
            //处理电话号码，身份证
            $data['phone'] = $phone;
            $data['id_number'] = $id_number;
            $temp = $this->doParam($data);
            unset($data);
            $phone = $temp['phone'];
            $id_number = $temp['id_number'];
            unset($temp);
            $clubInfo = $this->club_model->fetchOne(array('phone'=>$phone));
            if($clubInfo['status'] == 1){
                $record[$key][$p] = '已通过审核';
                continue;
            }
            if(empty($clubInfo)){
                $record[$key][$p] = '通过手机号码找不到店铺';
                continue;
            }
            elseif($clubInfo['name'] != $name){
                 $record[$key][$p] = '姓名不匹配';
                 continue;
            }
            elseif($clubInfo['id_number'] != $id_number){
                $record[$key][$p] = '身份证号码不匹配';
                continue;
            }
            $clubId = $clubInfo['id'];
            $postData = [];
            $postData['ids'] = array($clubId);
            $postData['status'] = 1;
            $postData['lottery_license'] = NULL;
            $postData['stationId'] = '45898888';
            $flag = $this->largeCheck($clubId,$postData['status'],$postData['lottery_license'],$postData['stationId']); 
            if($flag){
                $record[$key][$p] = '成功';
            }
            else{
                $record[$key][$p] = '失败';
            }
        }
        return $record;
    }
    public function largeCheck($id,$status,$lottery_license,$stationId)
    {
        $this->load->model('user_model');
        $ids = array($id);
        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }
        foreach ($ids as $id) {
            $this->club_model->update($id, ['status' => $status,'lottery_license'=>$lottery_license]);
        }
        if(empty($stationId)){
        $stationId = '45898888';
        }
        if(!empty($stationId)){
            $stationId = trim($stationId);
            $clubinfo = $this->club_model->fetchOne(array('id'=>$ids[0]));
            $where = array('id'=>$clubinfo['user_id']);
            $flag = $this->user_model->updateData(array('stationId'=>$stationId),$where);
        }
        if($status == 1){
            $this->add_log('零售店审核通过', $ids);
        }
        elseif($status == 0){
            $this->add_log('零售店审核不通过', $ids);
        }

        //取店铺信息
        $info = $this->club_model->fetchOne(array('id'=>$ids[0]));
        $name = $info['name'];
        $phone = $info['phone'];
        $user_id = $info['user_id'];
        $info = $this->user_model->getInfoById($user_id);
        $openid = $info['weixin'];

        $this->passShop($openid,$name,$phone);
        if($flag !== false){
            return true;
        }
        else{
            return false;
        }
    }
    //判断数据格式,姓名，手机，身份证
    public function judgeParam($name,$phone,$id_number){
        $rs = '';
        $nameL = strlen($name);
        $phoneL = strlen($phone);
        $id_numberL = strlen($id_number);
        if($nameL > 12){
            $rs = '姓名格式错误，长度错误';
            return $rs;
        }
        if($phoneL != 12){
            $rs = '手机格式错误，长度错误' ;
            return $rs;
        }
        if($id_numberL != 19){
            $rs = '身份证格式错误，长度错误' ;
            return $rs;
        }
        if(substr($phone, -1) != '`'){
            $rs = '手机格式错误，缺少`' ;
            return $rs;
        }
        return true;
    }
    public function doParam($data){
        $rs = [] ;
        $phone = $data['phone'];
        $id_number = $data['id_number'];
        $rs['phone'] = substr($phone, 0,strlen($phone) - 1);
        $rs['id_number'] = substr($id_number, 0,strlen($id_number) - 1);
        return $rs;
    }
    public function judgeFormat($data){
        if(count($data) <=1){
            $this->reply('空文件');
            die;
        }
        foreach ($data as $key => $value) {
            if($key == 1){
                if(!in_array('店主姓名', $value) || !in_array('店主手机', $value) || !in_array('店主身份证号码', $value) ){
                    $this->reply('数据格式错误');
                    die;
                }
                foreach ($value as $key2 => $value2) {
                    switch ($value2) {
                        case '店主姓名':
                            $name = $key2;
                            break;
                        case '店主手机':
                            $phone = $key2;
                            break;
                        case '店主身份证号码':
                            $id_number = $key2;
                            break;
                    }
                }
            }
        }
        $rs['name'] = $name;
        $rs['phone'] = $phone;
        $rs['id_number'] = $id_number;
        return $rs;
    }
    public function reply($content){
        echo "<script>alert('{$content}')</script>";
        return;
    }
    public function readExcel($filename){
        //引入PHPExcel对象
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $objReader = IOFactory::createReaderForFile($filename);; //准备打开文件  
        $objPHPExcel = $objReader->load($filename);   //载入文件 
        $sheet = $objPHPExcel->getSheet(0); // 读取第一個工作表  
        $highestRow = $sheet->getHighestRow(); // 取得总行数 
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数  
        /** 循环读取每个单元格的数据 */  
        for ($row = 1; $row <= $highestRow; $row++)    //行号从1开始  
        {  
            for ($column = 'A'; $column <= $highestColumm; $column++)  //列数是以A列开始  
            {  
                $dataset[$row][] = (string)$sheet->getCell($column.$row)->getValue();     
            }  
        }
        return $dataset;
    }
}
