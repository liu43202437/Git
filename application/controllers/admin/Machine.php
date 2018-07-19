<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/config/WxPay.Config.php";

class Machine extends Base_AdminController{
    public function __construct()
    {
        parent::__construct();
        $this->view_path='admin/machine/';
        $this->load->model("machine_model");
    }


    // public function lists(){
        // $selectKeyword = $this->get_input('selectKeyword');
        // if (!empty($selectKeyword)){
        //     $filters['machine_id like']='%'.$selectKeyword.'%';
        // }
        // $filters='';
        // $count=$this->machine_model->getcount_machine($filters);
        // $items=$this->machine_model->get_machine($filters,$this->pager['pageNumber'], $this->pager['pageSize']);
        // foreach ($items as $key => $item) {
        //     $status=$this->machine_model->check_machine($item['mid']);
        //     if ($status['code'] != '0'){
        //         $items[$key]['machine_status']=$status['msg'];
        //     }else{
        //         $items[$key]['machine_status']= "正常";
        //     }

        //     if ($item['update_date'] == null){
        //         $items[$key]['update_date']='无';
        //     }else{
        //         $items[$key]['update_date']=date("Y-m-d H:i:s",$item['update_date']);
        //     }
        // }
        // $this->data['itemList']=$items;
        // $this->data['selectKeyword']=$selectKeyword;
        // $this->assign_message();
        // $this->assign_pager($count['number']);
        // $this->load_view("lists");
    // }
    public function lists()
    {
        $this->load->model('Common_model');
        $this->load->model('ticket_model');
        $this->load->model('club_model');
        $selectKeyword = $this->get_input('selectKeyword');
        $filters = array();
        $orders = array();
        if($selectKeyword){
            $filters['machine_code'] = $selectKeyword;
        }
        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }
        $this->Common_model->setTable('tbl_hunan_machine');
        $totalCount = $this->Common_model->getCount($filters);
        $rsltList = $this->Common_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($rsltList as $key => $value) {
            $clubInfo = $this->club_model->fetchOne(array('id'=>$value['club_id']));
            $ticketInfo = $this->ticket_model->fetchOne(array('id'=>$value['ticket_id']));
            $rsltList[$key]['name'] = $clubInfo['name'];
            $rsltList[$key]['ticket_name'] = $ticketInfo['title'];
            if(empty($value['machine_code'])){
                $rsltList[$key]['machine_code'] = $this->ASCIITostring($value['machine_id']);
            }
            if($value['abnormity'] == 0){
                if($value['locked'] == 0){
                    $rsltList[$key]['status'] = '正常';
                }
                else{
                    $rsltList[$key]['status'] = '锁定';
                }
            }
            else{
                $rsltList[$key]['status'] = '机器异常';
            }
            if(time() - $value['update_date'] > 60*1.5){
                $rsltList[$key]['status'] = '心跳超时';
            }
        }
        $this->data['selectKeyword'] = $selectKeyword;
        $this->data['itemList'] = $rsltList;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('lists');
    }
    //ASCII转 字符串
    public function ASCIITostring($ascii){
        $str = '';
        $arr = str_split($ascii,2);
        foreach ($arr as $key => $value) {
            $temp = chr(hexdec($value));
            $temp = strtoupper($temp);
            $str .= $temp;
        }
        return $str;
    }

    
    
    public function order(){
        $startDate=$this->get_input('start_date');
        $endDate=$this->get_input('end_date');
        $exportStatus = $this->get_input('exportStatus');
        $selectKeyword = $this->get_input('selectKeyword');
        $filters = array();
        if (!empty($startDate)) {
            $filters['create_date >='] = d2bt($startDate);
        }
        if (!empty($endDate)) {
            $filters['create_date <='] = d2et($endDate);
        }
        if (!empty($selectKeyword)){
            $filters['oid like']='%'.$selectKeyword.'%';
        }

        if ($exportStatus == '' || $exportStatus == '0'){

        }

        if ($exportStatus == 1){
            $filters['pay_status =']= 0 ;
        }

        if ($exportStatus == 2){
            $filters['pay_status ='] = 1 ;
            $filters['ticket_status ='] = 0 ;
        }

        if ($exportStatus == 3){
            $filters['pay_status ='] = 1 ;
            $filters['ticket_status ='] = 1 ;
        }

        if ($exportStatus == 3){
            $filters['pay_status ='] = 1 ;
            $filters['ticket_status ='] = 2 ;
        }

        if ($exportStatus == 4){
            $filters['status ='] = 1 ;
        }


        $count=$this->machine_model->count_order($filters);
        $item=$this->machine_model->getorder($filters,$this->pager['pageNumber'], $this->pager['pageSize']);
        foreach ($item as $key => $value) {
            $status=$this->machine_model->check_order($value['oid']);
            $item[$key]['statuss']=$status['msg'];

            $aiis=str_split($value['machine_id'],2);
            $machine_id='';
            foreach ($aiis as $k => $aii) {
                $machine_id .= hex2bin($aii);
            }
            $item[$key]['machine_id']=$machine_id;
        }

        $this->data['itemList']=$item;
        $this->data['startDate']=$startDate;
        $this->data['endDate']=$endDate;
        $this->data['exportStatus']=$exportStatus;
        $this->data['selectKeyword']=$selectKeyword;
        $this->assign_pager($count['number']);
        $this->load_view("order");
    }

    public function exportorder(){
        $startDate=$this->post_input('startDate');
        $endDate=$this->post_input('endDate');
        $exportStatus = $this->post_input('exportStatus');
        $selectKeyword = $this->post_input('selectKeyword');
        $filters = array();

        if (!empty($startDate)) {
            $filters['create_date >='] = d2bt($startDate);
        }
        if (!empty($endDate)) {
            $filters['create_date <='] = d2et($endDate);
        }
        if (empty($selectKeyword)){
            $filters['oid like']='%'.$selectKeyword.'%';
        }


        if ($exportStatus == '' || $exportStatus == '0'){

        }

        if ($exportStatus == 1){
            $filters['status =']= 1 ;
        }

        if ($exportStatus == 2){
            $filters['status ='] = 0 ;
        }
        $count=$this->machine_model->count_order($filters);
        $rsltList=$this->machine_model->getorder($filters,1, $count['number']);
        foreach ($rsltList as $key => $value) {
            $status=$this->machine_model->check_order($value['oid']);
            $rsltList[$key]['statuss']=$status['msg'];
            if ($value['update_date'] == null){
                $rsltList[$key]['update_date']='无';
            }else{
                $rsltList[$key]['update_date']=date("Y-m-d H:i:s",$value['update_date']);
            }
            $aiis=str_split($value['machine_id'],2);
            $machine_id='';
            foreach ($aiis as $k => $aii) {
                $machine_id .= hex2bin($aii);
            }
            $rsltList[$key]['machine_id']=$machine_id;
        }
        if(count($rsltList) > 5000){
            echo "<script>alert('数量过多，请重新选择')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        foreach ($rsltList as $key => $item) {
            $Lists[$key][0]=$item['oid'];
            $Lists[$key][1]=$item['machine_id'];
            $Lists[$key][2]=$item['header_status'];
            $Lists[$key][3]=$item['net_status'];
            $Lists[$key][4]=$item['update_date'];
            $Lists[$key][5]=$item['title'];
            $Lists[$key][6]=$item['ticket_num'];
            $Lists[$key][7]=$item['real_ticket_num'];
            $Lists[$key][8]=$item['total_fee'];
            $Lists[$key][9]=$item['create_date'];
            $Lists[$key][10]=$item['statuss'];
        }

        $date = date("Y_m_d", time());
        $fileName = "彩票出售_{$date}.xls";

        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>15,'I'=>40);
        $data['headArr'] = array(
            'A1'=>'订单号',
            'B1'=>'机器号',
            'C1'=>'机头状态',
            'D1'=>'网络状态',
            'E1'=>'上次反馈时间',
            'F1'=>'票种',
            'G1'=>'票数',
            'H1'=>'实际出票数',
            'I1'=>'总价',
            'J1'=>'下单时间',
            'K1'=>'状态'
        );

        $this->load->model('excel_model');
        $data['bodyArr'] =  $Lists;
        $this->excel_model->dumpExcel2($data);
    }


    /**
     * 退款
     */
    public function refuse(){
        $oid=$this->get_input('id');
        $items=$this->machine_model->order_detail($oid);
        $this->data['itemInfo']=$items;
        $this->assign_message();
        $this->load_view('refuse');
    }

    public function get_price(){
        $ticket_id=$this->post_input('ticket_id');
        $res=$this->machine_model->get_price($ticket_id);
        if (empty($res)){
            echo json_encode("fail");
        }else{
            echo json_encode($res['price']);
        }
    }
    
    public function on_refuse1(){
        $oid=$this->post_input("oid");
        $this->machine_model->update_refuse($oid,array('refund_status' => 1,'refund_date'=>time()));
        $already_refuse_num=$this->machine_model->get_already_refuse_num($oid);
        $ticket_num=$this->post_input("ticket_num");
        $refuse_num=$this->post_input("refuse_num");
        $refuse_fee=$this->post_input("refuse_fee");
        $ticket_id=$this->post_input('ticket_id');
        $refuse_fee=$refuse_fee*100;
        $test_refuse_fee=100;
        $ip=$_SERVER['SERVER_ADDR'];
        $refuse_oid = md5(time());
        $items=$this->machine_model->order_detail($oid);
        if (($refuse_num + $already_refuse_num['refuse_num']) > $ticket_num){
            $this->success_redirect('machine/refuse?id='.$oid,'退款数超标');
            return;
        }
        $appid=WxPayConfig::APPID;
        $appsecret=WxPayConfig::APPSECRET;
        $mchid=WxPayConfig::MCHID;
        $key=WxPayConfig::KEY;

        $yg_openid="oGpqZ0eEt03DTE_QwOIJwJBVCeYk";
        $data=array(
            'mch_appid'=>$appid,
            'mchid'=>$mchid,
            'nonce_str'=>md5(time()),
            'partner_trade_no'=>$refuse_oid,
            'openid'=>$items['openid'],
            'check_name'=>'NO_CHECK',
            'amount'=>$refuse_fee,
            'spbill_create_ip'=>$ip,
            'desc'=>"彩票机退款"
        );
        $sign=$this->makeSign($data);
        $data['sign']=$sign;
        $xml=$this->toxml($data);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $header[] = "Content-type: text/xml";//定义content-type为xml,注意是数组
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 兼容本地没有指定curl.cainfo路径的错误
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $host=$_SERVER['SERVER_NAME'];

        if ($host == "yan.bjzwhz.cn"){
            curl_setopt($ch, CURLOPT_SSLKEY,"application/config/cert_eesee/apiclient_key.pem");
            curl_setopt($ch, CURLOPT_SSLCERT,"application/config/cert_eesee/apiclient_cert.pem");
        }else{
            curl_setopt($ch, CURLOPT_SSLKEY,"application/config/cert_bird/apiclient_key.pem");
            curl_setopt($ch, CURLOPT_SSLCERT,"application/config/cert_bird/apiclient_cert.pem");
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            // 显示报错信息；终止继续执行
            die(curl_error($ch));
        }
        curl_close($ch);
        $result=$this->toArray($response);
        // 显示错误信息
        if ($result['result_code']=='FAIL') {
//            die($result['return_msg']);

            $note=$result['return_msg'];
            $status=0;
            $this->machine_model->update_refuse($oid,array('refund_status' => 3,'refund_date'=>time()));
        }
//        var_dump($result);exit();
        if ($result['result_code'] == 'SUCCESS'){
            $note='';
            $status=1;
            $this->machine_model->update_refuse($oid,array('refund_status' => 2,'refund_date'=>time()));
        }
        $insert_data=array(
            'oid' => $oid,
            'refuse_time' => date('Y-m-d H:i:s',time()),
            'refuse_num'  => $refuse_num,
            'refuse_fee'  => $refuse_fee,
            'ticket_id'  =>  $ticket_id,
            'refuse_status' => $status,
            'note'    =>  $note
        );
        $info=$this->machine_model->add_refuse($insert_data);
        if ($info && $result['result_code'] == 'SUCCESS'){
            $this->success_redirect('machine/refuse?id='.$oid,'退款成功');
        }else{
            $this->success_redirect('machine/refuse?id='.$oid,'退款失败');
        }
    }

    public function on_refuse(){
        $oid=$this->post_input("oid");
        $this->machine_model->update_refuse($oid,array('refund_status' => 1,'refund_date'=>time()));

        $ticket_num=$this->post_input("ticket_num");
        $refuse_num=$this->post_input("refuse_num");
        $refuse_fee=$this->post_input("refuse_fee");
        $ticket_id=$this->post_input('ticket_id');
        $refuse_fee=$refuse_fee*100;
        $test_refuse_fee=100;
        $ip=$_SERVER['SERVER_ADDR'];
        $refuse_oid = md5(time());
        $items=$this->machine_model->order_detail($oid);
        if ($refuse_num > $ticket_num){
            die("退款数超标");
        }
        $appid=WxPayConfig::APPID;
        $appsecret=WxPayConfig::APPSECRET;
        $mchid=WxPayConfig::MCHID;
        $key=WxPayConfig::KEY;

        $yg_openid="oGpqZ0eEt03DTE_QwOIJwJBVCeYk";
        $data=array(
            'appid'=>$appid,
            'mch_id'=>$mchid,
            'nonce_str'=>md5(time()),
            'out_trade_no'=>$items['oid'],
            'out_refund_no' => $refuse_oid,
            'total_fee' => $items['total_fee']*100,
            'refund_fee' => $refuse_fee,
        );
        $sign=$this->makeSign($data);
        $data['sign']=$sign;
        $xml=$this->toxml($data);
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $header[] = "Content-type: text/xml";//定义content-type为xml,注意是数组
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 兼容本地没有指定curl.cainfo路径的错误
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $host=$_SERVER['SERVER_NAME'];

        if ($host == "yan.bjzwhz.cn"){
            curl_setopt($ch, CURLOPT_SSLKEY,"application/config/cert_eesee/apiclient_key.pem");
            curl_setopt($ch, CURLOPT_SSLCERT,"application/config/cert_eesee/apiclient_cert.pem");
        }else{
            curl_setopt($ch, CURLOPT_SSLKEY,"application/config/cert_bird/apiclient_key.pem");
            curl_setopt($ch, CURLOPT_SSLCERT,"application/config/cert_bird/apiclient_cert.pem");
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            // 显示报错信息；终止继续执行
            die(curl_error($ch));
        }
        curl_close($ch);
        $result=$this->toArray($response);
        // 显示错误信息
        if ($result['result_code']=='FAIL') {
//            die($result['return_msg']);

            $note=$result['return_msg'];
            $status=0;
            $this->machine_model->update_refuse($oid,array('refund_status' => 3,'refund_date'=>date('Y-m-d H:i:s')));
        }
//        var_dump($result);exit();
        if ($result['result_code'] == 'SUCCESS'){
            $note='';
            $status=1;
            $this->machine_model->update_refuse($oid,array('refund_status' => 2,'refund_date'=>date('Y-m-d H:i:s')));
        }
        $insert_data=array(
            'oid' => $oid,
            'refuse_time' => date('Y-m-d H:i:s',time()),
            'refuse_num'  => $refuse_num,
            'refuse_fee'  => $refuse_fee,
            'ticket_id'  =>  $ticket_id,
            'refuse_status' => $status,
            'note'    =>  $note
        );
        $info=$this->machine_model->add_refuse($insert_data);
        if ($info && $result['result_code'] == 'SUCCESS'){
            $this->success_redirect('machine/refuse?id='.$oid,'退款成功');
        }else{
            $this->success_redirect('machine/refuse?id='.$oid,'退款失败');
        }
    }
    public function del(){
        $id=$this->get_input("id");
        //初始化机器
        $this->load->model('Common_model');
        $this->Common_model->setTable('tbl_hunan_machine');
        $machineInfo = $this->Common_model->fetchOne(array('id'=>$id));
        if(empty($machineInfo)){
            echo "<script>alert('没找到机器')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        $insertData = $machineInfo;
        $insertData['init_time'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_hunan_initialize');
        $flag = $this->Common_model->insertData($insertData);
        if($flag){
            $this->Common_model->setTable('tbl_hunan_machine');
            $flag2 = $this->Common_model->deleteData(array('id'=>$id));
            if($flag2){
                $this->success_redirect("machine/lists",'初始化成功');
            }
            else{
                $this->success_redirect("machine/lists",'初始化失败，请重试');
            }
        }
        else{
            $this->success_redirect("machine/lists",'初始化失败，请重试');
        }
    }

    /**
     * 更改店铺
     */
    public function change_club(){
        $id=$this->get_input('id');
        $machine_detail=$this->machine_model->get_machine_detail($id);
        $this->data['id']=$id;
        $this->data['itemInfo']=$machine_detail;
        $this->assign_message();
        $this->load_view("change_club");
    }

    public function on_change_club(){
        $id=$this->post_input('id');
        $new_club_id=$this->post_input('club');

        $info=$this->machine_model->update_machine_club($id,$new_club_id);
        if ($info){
            $this->success_redirect('machine/change_club?id='.$id,'修改成功');
        }else{
            $this->success_redirect('machine/change_club?id='.$id,'修改失败');
        }
    }
    
    public function get_club(){
        $key=$this->post_input('key');
        $filter=$this->post_input('filter');
        $res=$this->machine_model->get_club($key,$filter);
        echo json_encode($res);
    }

    public function getarea(){
        $area=$this->post_input('area_id');
        $res=$this->machine_model->getarea($area);
        echo json_encode($res);
    }    
    private function toxml($data){
        if(!is_array($data) || count($data) <= 0){
            throw new WxPayException("数组数据异常！");
        }
        $xml = "<xml>";
        foreach ($data as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    private function makeSign($data){
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        $key=WxPayConfig::KEY;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

    private function toArray($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
}