<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
class Area_manager extends Base_AdminController
{
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
        $this->view_path = 'admin/area_manager/';
        $this->load->model('Area_manager_model');
        $this->load->model('club_model');
        $this->load->model('area_model');
        $this->filepath = get_instance()->config->config['log_path_file'];
    }

    public function lists()
    {
        $this->load->model('user_model');
        $this->load->model('audit_model');
        $areaId = $this->get_input('area_id');
        $searchKey = $this->get_input('search_key');
        $startDate = $this->get_input('start_date');
        $endDate = $this->get_input('end_date');
        $type = $this->get_input('type');

        $selectprovince = $this->get_input('selectprovince');
        $selectcity = $this->get_input('selectcity');
        $selectcityname = $this->get_input('selectcityname');
        $selectcounty = $this->get_input('selectcounty');
        $selectstreet = $this->get_input('selectstreet');
        $filters = array();
        $orders = array();
        $filters['refuse'] = 0;
        if (!empty($areaId)) {
            $filters['area_id'] = $areaId;
        }
        if (!empty($searchKey)) {
            $filters['name%,phone%,id_number%'] = $searchKey;
        }
        if(!empty($startDate) && !empty($endDate)){
            $filters['create_date>='] = $startDate.' 00:00:00';
            $filters['create_date<='] = $endDate.' 23:59:59';
        }

        $area_param = [];
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
        if(!empty($type)){
            if($type == 2){
                $filters['status'] = 0;
            }
            elseif($type == 3){
                $filters['status'] = 1;
            }
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Area_manager_model->getCountTwo($filters,$area_param);
        $rsltList = $this->Area_manager_model->getListTwo($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
        foreach ($rsltList as $key => $value) {
            $avatar = $this->user_model->getInfoById($value['user_id']);
            $rsltList[$key]['avatar_url'] = $avatar['avatar_url'];
        }
        //获取城市列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        foreach ($rsltList as $key => $item) {
            $province_new = $item['area_id'] == null?'未知':$provinces_temp[$item['area_id']];
            $rsltList[$key]['address'] =  $province_new.$item['city'].$item['address'];
            $avatar = $this->user_model->getInfoById($item['user_id']);
            $rsltList[$key]['avatar'] = $avatar['avatar_url'];
        }

        $this->data['itemList'] = $rsltList;
        $this->data['cities'] = $this->area_model->getCityList();
        $this->data['areaId'] = $areaId;
        $this->data['searchKey'] = $searchKey;

        $this->data['selectprovince'] = $selectprovince;
        $this->data['selectcity'] = $selectcity;
        $this->data['provinces'] = $provinces_temp;
        $this->data['selectcounty'] = $selectcounty;
        $this->data['selectstreet'] = $selectstreet;
        $this->data['isEditable'] = $this->auth_role('member/add');
        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;
        $this->data['type'] = $type;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('list');
    }

    public function check()
    {
        $area_manager_id = '';
        $ids = $this->post_input('ids');
        $status = $this->post_input('status');

        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }

        $this->load->model('manager_model');
        foreach ($ids as $id) {
            $this->Area_manager_model->update($id, ['status' => $status]);
        }

        if($status == 1){
            $this->add_log('区域经理审核信息通过', $ids);
        }
        elseif($status == 0){
            $this->add_log('区域经理审核信息不通过', $ids);
        }
        $area_info = $this->Area_manager_model->get_bazaar_user_info($ids[0]);


        $area_user_id = $area_info['area_user_id'];
        $bazaar_user_id = $area_info['bazaar_user_id'];
        $this->load->model('user_model');
        //取市场经理下的客户经理和客户经理下的店铺订单
        $id_number = $area_info['phone'];
        /*设置分省
        */
        $this->load->model("Common_model");
        $this->Common_model->setTable('tbl_area_manager');
        $tempInfo = $this->Common_model->fetchOne(array('id'=>$ids[0]));
        $province_id = $tempInfo['area_id'];
        !empty($province_id) ? $province = $province_id : $province = 7;
        $tick_order = 'tbl_ticket_order_'.$province;
        $this->Area_manager_model->set_ticket_order($tick_order);
        $order_num = 'tbl_order_num_'.$province;
        $this->Area_manager_model->set_order_num($order_num);
        /*设置分省
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
            $this->load->model("user_credits_model");
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
        $data = parent::success_message();
        echo json_capsule($data);
    }

    public function delete()
    {
        $ids = $this->post_input('ids');
        if (empty($ids)) {
            $data = parent::error_message('输入参数错误');
            die(json_capsule($data));
        }

        foreach ($ids as $id) {
            $this->Area_manager_model->deleteData('tbl_area_manager',array('id'=>$id));
        }

        $this->add_log('删除市场经理', $ids);

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
        $this->load->model('session_model');

        $id = $this->get_input('id');
        $info = $this->Area_manager_model->fetchOne(array('id'=>$id));
        $user_id = $info['user_id'];
        $avatar_url = $this->user_model->getInfoById($user_id);
        $this->data['isNew'] = true;
        $this->data['itemInfo'] = $info;
        $this->data['avatar_url'] = $avatar_url['avatar_url'];
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
    public function do_edit() {
        $id = $this->post_input('id');

        $auditItemId = $this->post_input('audit_item_id');
        $data['name'] = $this->post_input('name');
        $data['phone'] = $this->post_input('phone');
        $data['area_manager_id'] = $this->post_input('phone');
        $data['id_number'] = $this->post_input('id_number');
        $data['intro'] = $this->post_input('intro');
        $data['bazaar_name'] = $this->post_input('manager_name');
        $data['bazaar_phone'] = $this->post_input('manager_id');

        $data['area_id'] = $this->post_input('selectprovince');
        $data['city'] = $this->post_input('selectcityname');
        $data['address'] = $this->post_input('address');
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
        if(!empty($data['area_manager_id'])){
            $info = $this->Area_manager_model->fetchOne(array('phone'=>$data['phone']));
            if(!empty($info) && $info['id'] != $id){
                echo "<script>alert('此手机号码已经使用')</script>";
                echo "<script>history.back();</script>";
                die;
            }
        }
        $info = $this->Area_manager_model->fetchOne(array('id_number'=>$data['id_number']));
        if(!empty($info) && $info['id'] != $id){
            echo "<script>alert('此身份证号已经存在')</script>";
            echo "<script>history.back();</script>";
            die;
        }
        foreach ($data as $key => $value) {
            if(empty($value)){
                unset($data[$key]);
            }
        }
        $this->Area_manager_model->updateData($data,array('id'=>$id));
        $this->add_log('编辑区域经理', $id);
        $this->success_redirect('./area_manager/lists');
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
    public function ajaxSend($mobile) {
        // 인증코드생성 및 전화번호에 발송
        $this->load->model("verifyphone_model");

        $authCode = gen_rand_num(6);
        $response = $this->zwsendsms($mobile, array($authCode,'5'), "223986");
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
    //访销审核拒绝
    public function refuse(){
        $this->load->model("Common_model");
        $rs = [];
        $id = $this->post_input('id');
        $content = $this->post_input('content');
        //取用户手机号
        $info = $this->Area_manager_model->fetchOne(array('id'=>$id));
        $phone = $info['phone'];
        // $response = $this->ajaxSend($phone);
        $response = [];
        $response['success'] = true;
        //更删除店铺
        // $this->Area_manager_model->deleteData(array('id'=>$id));
        $flag2 = $this->Area_manager_model->updateData(array('refuse'=>1),array('id'=>$id));

        $user_id = $info['user_id'];
        //记录拒绝信息
        $this->Common_model->setTable('tbl_appmsg');
        $insertData = [];
        $insertData['msg'] = $content;
        $insertData['from'] = -1;
        $insertData['to'] = $user_id;
        $insertData['msgtype'] = 'area_manager no pass';
        $insertData['data'] = json_encode($info);
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $flag = $this->Common_model->insertData($insertData);
        // $this->refuseBazaarManager($openid,$content);
        $response = $this->zwsendsms($phone, array('1','5'), "235981");
        $response = [];
        $response['success'] = true;

        $this->add_log('区域经理审核拒绝', $id);
        echo json_capsule($response);
    }
    public function tongzhi(){
        $this->load->model('sendmsg_model2');
        $sql = "SELECT * FROM tbl_user as a,tbl_club as b WHERE b.`status`=0 AND b.user_id=a.id ";
        $userList = $this->Area_manager_model->queryAll($sql);
        // var_dump($userList);
        $openid = [];
        // foreach ($userList as $key => $value) {
        //     $openid[] = $value['weixin'];
        // }
        $openid[] = "oyAK9w-kXDem_W5bgu-NCogtriuc";
        $openid[] = "oyAK9wzag0ccx03XFk67o0_N5NWA";
        // $openid[] = "oyAK9w2Ld1ShsVQTt4Vtkli8Apho";
        // var_dump($openid);die();

        foreach ($openid as $key => $value) {
            $re = $this->sendmsg_model2->tongzhi($value);
        }
        // $re = $this->sendmsg_model2->tongzhi($openid);
    }
    public function setunionid(){
        set_time_limit(600);
        ignore_user_abort(true);
        $this->load->model('user_model');
        $this->load->model('token_model');

        //取得所有用户的openid
        $userList = $this->user_model->fetchAll();
        // var_dump($userOpendiList);
        $userOpendiList = [];
        foreach ($userList as $key => $value) {
            // if(empty($value['weixin']) && !empty($value['unionid'])){
            //     continue;
            // }
            if(empty($value['unionid'])){
                $userOpendiList[] = $value['weixin'];
            }
        }
        $access_token = $this->token_model->getAccessToken();
        echo "<pre>";
        foreach ($userOpendiList as $key => $value) {
            $openid = $value;
            $urll = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid;
            $userinfo = $this->token_model->post($urll);
            $userinfo = json_decode($userinfo,true);
            if(empty($userinfo['subscribe']) || empty($userinfo['unionid']) || empty($userinfo['openid'])){
                var_dump(false,$openid,json_encode($userinfo));
                continue;
            }
            $unionid = $userinfo['unionid'];
            //更新tbl_user 表中 union
            $where = array('weixin'=>$openid);
            $updateData = array('unionid'=>$unionid);
            $flag = $this->user_model->updateData($updateData,$where);
            var_dump($flag,$openid,json_encode($userinfo));
        }
    }
    public function passAreaManager($openid){
        $this->load->model('sendmsg_model');
        $re = $this->sendmsg_model->passAreaManager($openid);
    }
    public function refuseAreaManager($openid,$content){
        $this->load->model('sendmsg_model');
        $re = $this->sendmsg_model->refuseAreaManager($openid,$content);
    }
    //导出市场经理
    public function exportAreaManager(){
        set_time_limit(0);
        $this->load->model('excel_model');
        $this->load->model('porder_model');
        
        $startDate= $this->post_input('startDate');
        $endDate= $this->post_input('endDate');
        $search_key= $this->post_input('search_key');
        $type= $this->post_input('type');

        $selectprovince = $this->post_input('selectprovince');
        $selectcity = $this->post_input('selectcity');
        $selectcityname = $this->post_input('selectcityname');
        $selectcounty = $this->post_input('selectcounty');
        $selectstreet = $this->post_input('selectstreet');
        if(empty($startDate) || empty($endDate)){
            echo "<script>alert('请输入起止时间')</script>";
            echo "<script>history.back()</script>";
            return;
        }

        $area_param = [];
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
        $filters['create_date>='] = $startDate.' 00:00:00'; 
        $filters['create_date<='] = $endDate.' 23:59:59';
        if(!empty($search_key)){
            $filters['name%, phone%,id_number%'] = $search_key;
        }
        if(!empty($type) && $type != 1){
            if($type == 2){
                $filters['status'] = 0;
            }   
            elseif($type == 3){
                $filters['status'] = 1;
            } 
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $totalCount = $this->Area_manager_model->getCountTwo($filters,$area_param);
        $this->pager['pageNumber'] = 1;
        $this->pager['pageSize'] = $totalCount;
        $info = $this->Area_manager_model->getListTwo($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],$area_param);
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
            $temp[$key][0] = $value['id'];
            $temp[$key][1] = $value['name'];
            $temp[$key][2] = $value['phone'].'`';
            $temp[$key][3] = $value['id_number'].'`';

            $temp[$key][4] = $value['area_id'] == null?'未知':$area_temp[$value['area_id']];
            $temp[$key][5] = $value['city'];
            if(!empty($value['area_code'])){
                $temp[$key][6] = $area_temp[substr($value['area_code'],0,6)];
            }else{
                $temp[$key][6] = '未知';
            }
            $temp[$key][7] = $value['address'];
            $temp[$key][8] = $value['bazaar_name'];
            $temp[$key][9] = $value['bazaar_phone'].'`';
            $temp[$key][10] = $value['create_date'];
        }
        $Lists = $temp;
        $date = date("Y_m_d", time());
        $fileName = "市场经理信息_{$date}.xls";

        
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15,'H'=>60);
        $data['headArr'] = array(
            'A1'=>'市场经理ID',
            'B1'=>'姓名',
            'C1'=>'手机号码',
            'D1'=>'身份证号',
            'E1'=>'省份',
            'F1'=>'城市',
            'G1'=>'区域',
            'H1'=>'详细地址',
            'J1'=>'区域经理',
            'K1'=>'区域经理手机',
            'L1'=>'注册时间'
        );
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }
}
