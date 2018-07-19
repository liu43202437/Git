<?php

/**
 * @Author: liuzudong
 * @Date:   2018-03-30 19:35:07
 * @Last Modified by:   liuzudong
 */
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/config/main_config.php";
class Socket extends Base_WechatPay
{
    private $issueTicketUrl = '';
    private $memcache = '';
    private $mem_ip = '';
    private $mem_port = '';
    function __construct()
    {
        parent::__construct();
        $this->issueTicketUrl = get_instance()->config->config['issueTicketUrl'];
        $this->mem_ip = get_instance()->config->config['memcache_ip'];
        $this->mem_port = get_instance()->config->config['memcache_part'];
        ob_clean();
    }
    //下发出票指令
    public function issueTicket(){
        $this->load->model('Common_model');
        $this->load->model('ticket_model');
        $oid = $this->getParam('oid');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $machine_id = $this->getParam('machine_id');
        $order_id = $this->getParam('order_id');
        $openid = $this->getParam('openid');
        if(empty($oid)){
           $oid = '20180330211124033378500000000001';
        }
        //订单查询
        $this->Common_model->setTable('tbl_hunan_order');
        $orderInfo = $this->Common_model->fetchOne(array('oid'=>$oid));
        if($orderInfo['status'] == 1 && $orderInfo['ticket_status'] == 1){
            $url = base_url()."lottery-vending/index.html#/paySuccess"."?openid={$openid}&machine_id={$machine_id}&order_id={$order_id}";
            header("location:" . $url);
            die;
        }
        $ticketInfo = $this->ticket_model->fetchOne(array('id'=>$ticket_id));
        if(empty($ticketInfo)){
            $this->jump('5');
            return;
        }
        $arr = $this->sOrder($oid);
        if(!$this->chekSign($arr)){
            $this->logs('notify_error.log', '签名错误 out_trade_no='.$arr['out_trade_no']);
            $this->jump('16');
            return;
        }
        if(empty($orderInfo)){

            $this->logs('notify_error.log', '没有找到订单 out_trade_no='.$arr['out_trade_no']);
            $this->jump('17');
            return;
        }
        if($orderInfo['total_fee'] != $arr['total_fee']/100){
            // $this->logs('notify_error.log', '订单金额错误 out_trade_no='.$arr['out_trade_no']);
            // $this->jump('18');
            // return;
        }
        $url = base_url()."lottery-vending/index.html#/paySuccess"."?openid={$openid}&machine_id={$machine_id}&order_id={$order_id}";
        header("location:" . $url);
    }
    //回调
    public function notify(){
        $this->load->model('Common_model');
        $this->load->model('ticket_model');
        $this->memcache = new Memcache;
        $this->memcache->connect($this->mem_ip, $this->mem_port);
        $xmlData = $this->getPost();
        $arr = $this->XmlToArr($xmlData);
        if(!$this->chekSign($arr)){
            $this->logs('notify_error.log', '签名错误');
        }
        if($arr['return_code'] != 'SUCCESS' || $arr['result_code'] != 'SUCCESS' ){
            $this->logs('notify_error.log', '支付失败');
        }
        //验证订单和金额
        $oid = $arr['out_trade_no'];
        $res = [
            'out_trade_no' => $arr['out_trade_no'],
            'total_fee' => $arr['total_fee']
        ];
        $returnParams = [
            'return_code' => 'SUCCESS',
            'return_msg'  => 'OK'
        ];
        echo $this->ArrToXml($returnParams);
        $this->logs('wechatpayResponse.log', json_encode($arr));
        //中间件高速缓存
        $memcacheInfo = $this->memcache->get($oid);
        if(!empty($memcacheInfo)){
            die;
        }
        $this->memcache->set($oid,'true',0,3600);
        //查询订单
        $where = array('oid'=>$arr['out_trade_no']);
        $this->Common_model->setTable('tbl_hunan_order');
        $orderInfo = $this->Common_model->fetchOne($where);
        if($orderInfo['status'] == 1 && $orderInfo['ticket_status'] == 1){
            // $this->success('出票成功');
            $this->memcache->delete($oid);
            die;
        }
        //已经收到微信反馈则结束
        if(!empty($orderInfo['trade_no']) || $orderInfo['notify_status'] == 1){
            $this->memcache->delete($oid);
            die;
        }
        $ticketInfo = $this->ticket_model->fetchOne(array('id'=>$orderInfo['ticket_id']));
        if(empty($ticketInfo)){
            // $this->jump('没有找到票种');
            $this->memcache->delete($oid);
            return;
        }
        if($orderInfo['total_fee'] != $arr['total_fee']/100){
            // $this->logs('notify_error.log', '订单金额错误 out_trade_no='.$arr['out_trade_no']);
            // $this->jump('订单金额错误，请联系管理员');
            $this->memcache->delete($oid);
            // return;
        }
        //获取机器信息
        $this->Common_model->setTable('tbl_hunan_machine');
        $filter = [];
        $filter['id'] = $orderInfo['machine_id'];
        $machineInfo = $this->Common_model->fetchOne($filter);
        $machine_id = $machineInfo['machine_id'];
        //更新订单状态
        $updateData = [];
        $updateData['pay_status'] = 1;
        $updateData['pay_date'] = date('Y-m-d H:i:s');
        $updateData['trade_no'] = $arr['transaction_id'];
        $updateData['notify_status']  = 1;
        $where = array('id'=>$orderInfo['id']);
        $this->Common_model->setTable('tbl_hunan_order');
        $this->Common_model->updateData($updateData,$where);
        //下发出票指令
        $price = explode('.', $ticketInfo['price']);
        $params = [];
        $params['uid'] = $machine_id;
        $params['seq'] =  str_pad($orderInfo['id'],16,'F',STR_PAD_LEFT );
        $params['nose'] = 0;
        $params['price'] = !empty($ticketInfo['size']) ? $ticketInfo['size'] : 6 ;
        $params['num'] = $orderInfo['ticket_num'];
        $params['date'] = substr(date('YmdHis'), 2);
        $params = json_encode($params);
        $t = time();
        $sign = $this->getSignTwo($params,$t);
        $url = $this->issueTicketUrl."?t={$t}&sign={$sign}";
        $this->logs('socketParams.log', $params);   #记录 下发命令日志
        $response = $this->postData($url,$params);
        $response = json_decode($response,true);
        $this->logs('socketRequest.log', json_encode($response)); # 记录反馈日志
        if($response['code'] != 0){
            //更新订单状态
            $updateData = [];
            $updateData['ticket_response'] = $response['code'];
            $updateData['ticket_status'] = 2;
            $where = array('id'=>$orderInfo['id']);
            $this->Common_model->setTable('tbl_hunan_order');
            $this->Common_model->updateData($updateData,$where);
            //更新机器状态
            $updateData = [];
            $updateData['abnormity'] = $response['code'];
            $updateData['abnormity_time'] = date('Y-m-d H:i:s');
            $where = array('id'=>$orderInfo['machine_id']);
            $this->Common_model->setTable('tbl_hunan_machine');
            $this->Common_model->updateData($updateData,$where);
        }
        else{
            $this->logs('notify_success.log', json_encode($res));
        }
        //清除缓存
        $this->memcache->delete($oid);
    }


    public function getSocketRes(){
        $this->load->model('Common_model');
        $data = $this->getParam('data');
        // $data = '{"uid":"30303030303030303030303135","seq":"0000000000000100","nose":0,"num":1,"date":{"_data":[{"_v":18},{"_v":4},{"_v":1},{"_v":15},{"_v":44},{"_v":12}],"_length":6},"price":6}';
        // $this->logs('socketRes.log', $data);
        $data = json_decode($data,true);
        $order_id =  substr($data['seq'], strripos($data['seq'], 'F') + 1);
        //更新订单状态
        $this->Common_model->setTable('tbl_hunan_order');
        $filter = [];
        $filter['id'] = $order_id;
        $orderInfo = $this->Common_model->fetchOne(array('id'=>$order_id));
        $data['order_num'] = $orderInfo['ticket_num'];
        $this->logs('socketRes.log', json_encode($data));
        if(empty($orderInfo)){
            $this->reply('找不到订单信息');
            return;
        }
        if($orderInfo['status'] == 1 || $orderInfo['ticket_response'] != 0){
            echo json_encode($data);
            return;
        }
        $where = [];
        $where['id'] = $order_id;
        $updateData = [];
        $updateData['real_ticket_num'] = $data['num'];
        $updateData['ticket_date'] = date('Y-m-d H:i:s');
        if($orderInfo['ticket_num'] == $data['num']){
            //更新订单状态
            $updateData = [];
            $updateData['ticket_status'] = 1;
            $updateData['ticket_response'] = 1;
            $updateData['real_ticket_num'] = $data['num'];
            $updateData['ticket_date'] = date('Y-m-d H:i:s');
            $updateData['status'] = 1;
            $updateData['response_time'] = date('Y-m-d H:i:s');
            $flag = $this->Common_model->updateData($updateData,$where);
            //累计机器售票数
            $sql = "update tbl_hunan_machine set total_ticket_num=IFNULL(total_ticket_num,0)+{$data['num']},total_ticket_amount=IFNULL(total_ticket_amount,0)+{$orderInfo['total_fee']},locked=0,ticket_num = IFNULL(ticket_num,0)-{$data['num']}  where machine_id='{$data['uid']}'";
        }
        elseif($orderInfo['ticket_num'] != $data['num']){            
            $updateData['ticket_status'] = 2;
            $updateData['ticket_response'] = 2;
            $updateData['real_ticket_num'] = $data['num'];
            $updateData['ticket_date'] = date('Y-m-d H:i:s');
            $updateData['response_time'] = date('Y-m-d H:i:s');
            $flag = $this->Common_model->updateData($updateData,$where);
            $orderInfo['total_fee'] = $orderInfo['total_fee'] * $data['num'] / $orderInfo['ticket_num'];
            $sql = "update tbl_hunan_machine set total_ticket_num=IFNULL(total_ticket_num,0)+{$data['num']},total_ticket_amount=IFNULL(total_ticket_amount,0)+{$orderInfo['total_fee']},ticket_num = IFNULL(ticket_num,0)-{$data['num']} where machine_id='{$data['uid']}'";
        }        
        $flag2 = $this->Common_model->sqlUpdate($sql);
        echo json_encode($data);
    }


    public function cancel(){
        $this->load->model('Common_model');
        $this->load->model('ticket_model');
        $oid = $this->getParam('oid');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $machine_id = $this->getParam('machine_id');
        $order_id = $this->getParam('order_id');
        //更新订单状态
        $updateData = [];
        $updateData['pay_status'] = 2;
        $where = [];
        $where = array('id'=>$order_id);
        $this->Common_model->setTable('tbl_hunan_order');
        $this->Common_model->updateData($updateData,$where);
        //跟新机器状态
        $updateData = [];
        $updateData['locked'] = 0;
        $where = [];
        $where = array('machine_id'=>$machine_id);
        $this->Common_model->setTable('tbl_hunan_machine');
        $this->Common_model->updateData($updateData,$where);
        //页面跳转
        $url = base_url()."lottery-vending/index.html#/orders"."?openid={$openid}&machine_id={$machine_id}&order_id={$order_id}";
        header("location:" . $url);
    }


    public function failed(){
        $this->load->model('Common_model');
        $this->load->model('ticket_model');
        $oid = $this->getParam('oid');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $machine_id = $this->getParam('machine_id');
        $order_id = $this->getParam('order_id');
        //更新订单状态
        $updateData = [];
        $updateData['pay_status'] = 2;
        $where = [];
        $where = array('id'=>$order_id);
        $this->Common_model->setTable('tbl_hunan_order');
        $this->Common_model->updateData($updateData,$where);
        //跟新机器状态
        $updateData = [];
        $updateData['locked'] = 0;
        $where = [];
        $where = array('machine_id'=>$machine_id);
        $this->Common_model->setTable('tbl_hunan_machine');
        $this->Common_model->updateData($updateData,$where);
        //页面跳转
        $url = base_url()."lottery-vending/index.html#/payFailure"."?openid={$openid}&machine_id={$machine_id}";
        header("location:" . $url);
    }


    public function test(){
        $order_id = '1';
        $order_id = str_pad($order_id,16,'0',STR_PAD_LEFT );
        $params = [];
        $params['uid'] = '30303030303030303030303135';
        $params['seq'] =  $order_id;
        $params['nose'] = 0;
        $params['price'] = 6;
        $params['num'] = 2;
        $params['date'] = substr(date('YmdHis'), 2);
        $params = json_encode($params);
        $t = time();
        $sign = $this->getSignTwo($params,$t);
        $url = $this->issueTicketUrl."?t={$t}&sign={$sign}";
        $response = $this->postData($url,$params);
        var_dump($response);
    }
    

    public function getSignTwo($params,$t){
        $str = 'eeseetech'.$t.'data'.$params;
        $sign = md5($str);
        return $sign;
    }
    // public function setSign($params){
    //  // $params = array_filter($params);
    //  if(isset($params['sign'])){
    //      unset($params['sign']);
    //  }
    //  $sign = $this->getSign($params);
    //  unset($params['t']);
    //  $params['sign'] = $sign;
    //  return $params;
    // }

    public function postData($url,$params){
        $headers[] = 'Content-Type: text/plain';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }


    public function login(){
        $this->load->model('Common_model');
        $machine_id = $this->getParam('machine_id');
        if(empty($machine_id)){
            $this->reply('缺少machine_id参数');
            return;
        }
        //获取openid
        $openid = $this->getOpenId();
        if(empty($openid) || gettype($openid) != 'string' || strlen($openid) != 28){
            $this->reply('获取openid失败，请联系管理人员');
            return;
        }
        
        if($openid){
            #跳转到售票
            $url = ""."?openid={$openid}&machine_id={$machine_id}";
            header("location:" . $url);
        }
        else{
            echo "<script>alert('获取用户信息失败，请稍后再试')</script>";
            return;
        }
    }


    public function doLogin(){
        $this->load->model('Common_model');
        $pwd = $this->getParam('pwd');
        $machine_id = $this->getParam('machine_id');
        $sid = $this->getParam('sid');
        if(empty($pwd) || empty($machine_id)){
            $this->reply('缺少参数');
            return;
        }
        $nowTime = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_hunan_session');
        $sessionInfo = $this->Common_model->fetchOne(array('session_id'=>$sid));
        if(empty($sessionInfo)){
            $this->reply('用户不存在');
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $sid = $sessionInfo['session_id'];
        $this->Common_model->setTable('tbl_hunan_staff');
        $staffInfo = $this->Common_model->fetchOne(array('id'=>$user_id));
        if($staffInfo['failed_count'] > 3 && substr($staffInfo['failed_time'], 0,10) == date('Y-m-d')){
            $this->reply('请明天再试或联系管理员解除登录异常');
            return;
        }
        if($pwd != 'zhongwei2017'){
            $this->reply('密码错误');
            //记录错误次数
            if(substr($staffInfo['failed_time'], 0,10) == date('Y-m-d')){
                $sql = "update tbl_hunan_staff set failed_count=failed_count+1 ,failed_time='{$nowTime}' where id = '{$user_id}'";
            }
            else{
                $sql = "update tbl_hunan_staff set failed_count=1 ,failed_time='{$nowTime}' where id = '{$user_id}'";
            }
            $this->Common_model->sqlUpdate($sql);
            return;
        }
        else{
            //更新用户状态
            $updateData = [];
            $updateData['failed_count'] = 0;
            $updateData['failed_time'] = NULL;
            $this->Common_model->updateData($updateData,array('id'=>$user_id));
        }
        $this->Common_model->setTable('tbl_hunan_machine');
        $filter = [];
        $filter['machine_id'] = $machine_id;
        $machineInfo = $this->Common_model->fetchOne($filter);
        $this->success('成功',$sid);
        if(empty($machine_id)){
            #跳转到设备激活页面
        }
        else{
            #进入设备管理页面
        }

    }

    //获取之前彩票种类
    public function getLastTicketId(){
        $this->load->model('Common_model');
        $sid = $this->getParam('sid');
        $machine_id = $this->getParam('machine_id');
        if(empty($sid) || empty($machine_id)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->getSessionInfo();
        $this->Common_model->setTable('tbl_hunan_machine');
        $filter = [];
        $filter['machine_id'] = $machine_id;
        $machineInfo = $this->Common_model->fetchOne($filter);
        if(empty($machineInfo)){
            $this->reply('机器不存在');
            return;
        }
        $ticket_id = $machineInfo['ticket_id'];
        $this->success('成功',$ticket_id);
        return;
    }


    public function getTicketInfo(){
        $this->load->model('Common_model');
        $ticket_id = $this->getParam('ticket_id');
        $openid = $this->getParam('openid');
        if(empty($ticket_id) || empty($openid)){
            $this->reply('缺少参数');
            return;
        }
        $this->Common_model->setTable('tbl_ticket');
        $info = $this->Common_model->fetchOne(array('id'=>$ticket_id));
        if(!empty($info)){
            $this->success('成功',$info);
        }
        else{
            $this->reply('没有票种信息');
        }
    }


    //用户提交订单
    public function submitOrder(){
        $this->load->model('Common_model');
        $openid = $this->getParam('openid');
        $machine_id = $this->getParam('machine_id');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $total_fee = $this->getParam('total_fee');
        if(empty($openid) || empty($machine_id) || empty($ticket_id) || empty($ticket_num) || empty($total_fee)){
            $this->reply('缺少参数');
            return;
        }
        //查询机器状态
        $this->Common_model->setTable('tbl_hunan_machine');
        $machineInfo = $this->Common_model->fetchOne(array('machine_id'=>$machine_id));
        if($machineInfo['abnormity'] == 1){
            $this->reply('机器异常，暂时无法下单');
            return;
        }
        //初始化机器锁定状态
        if(!empty($machineInfo['locked_time']) && time() - strtotime($machineInfo['locked_time']) > 60*5){
            $updateData =[];
            $updateData['locked'] = 0;
            $updateData['locked_time'] = NULL;
            $this->Common_model->updateData($updateData,array('machine_id'=>$machine_id));
        }
        elseif($machineInfo['locked'] == 1){
            $this->reply('该机器处于锁定状态，暂时无法下单');
            return;
        }
        //查询票价
        $this->Common_model->setTable('tbl_ticket');
        $ticketInfo =  $this->Common_model->fetchOne(array('id'=>$ticket_id));
        $total = $ticketInfo['price'] * $ticket_num;
        if($total != $total_fee){
            $this->reply('订单金额有误');
            return;
        }
        if($ticket_id != $machineInfo['ticket_id']){
            $this->reply('票种不符');
            return;
        }
        if($ticket_num > 10){
            $this->reply('一次最多下10张票');
            return;
        }
        list($t1, $t2) = explode(' ', microtime());
        $milisec = (int)sprintf('%.0f', (floatval($t1)) * 1000);
        $milisec = sprintf("%03d", $milisec);
        $oid = nownum() . $milisec . gen_rand_num(4).str_pad($machineInfo['id'],11,'0',STR_PAD_LEFT );
        $insertData = [];
        $insertData['oid'] = $oid;
        $insertData['openid'] = $openid;
        $insertData['machine_id'] = $machine_id;
        $insertData['ticket_id'] = $ticket_id;
        $insertData['ticket_num'] = $ticket_num;
        $insertData['total_fee'] = $total;
        $insertData['status'] = 0;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_hunan_order');
        $flag = $this->Common_model->insertData($insertData);
        if(!$flag){
            $this->reply('提交订单失败,请换台机器重试');
            return;
        }
        //锁定机器状态
        $updateData = [];
        $updateData['locked'] = 1;
        $updateData['locked_time'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_hunan_machine');
        $flag = $this->Common_model->updateData($updateData,array('machine_id'=>$machine_id));
        if($flag === false){
            $this->reply('提交订单失败,请换台机器重试');
            return;
        }
        #提交微信订单
        $total_fee = 0.01;
        $this->submitWechatOrder($oid,$total_fee,$openid);
        // $this->success('成功');
    }

   //统一下单
    public function submitWechatOrder($oid,$total_fee,$openid){
        $oid = md5(time());
        $total_fee *= 100;
        $notify_url = base_url()."hunan/passimeter/Consumer/notify";
        $prepay_id = $this->getPrepayId($oid,$total_fee,$notify_url,$openid);
        $json = $this->getJsParams($prepay_id);
        $okUrl = "";
        $cancelUrl = "";
        $failUrl = "";
        echo <<<order
<script>
function onBridgeReady(){
   WeixinJSBridge.invoke(
       'getBrandWCPayRequest', {$json},
       function(res){     
           if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                window.location.href = "{$okUrl}";
           } 
            else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                window.location.href = "{$cancelUrl}";
            }
            else if(res.err_msg == "get_brand_wcpay_request:fail"){
                window.location.href = "{$failUrl}";
            }
               // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。 
       }
   ); 
}
if (typeof WeixinJSBridge == "undefined"){
   if( document.addEventListener ){
       document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
   }else if (document.attachEvent){
       document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
       document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
   }
}else{
   onBridgeReady();
}
</script>
order;
    }

    //获取设备信息
    public function getmachineInfo(){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->load->model('ticket_model');
        $openid = $this->getParam('openid');
        $machine_id = $this->getParam('machine_id');
        if(empty($openid) || empty($machine_id)){
            $this->reply('缺少参数');
            return;
        }
        $this->Common_model->setTable('tbl_hunan_machine');
        $machineInfo = $this->Common_model->fetchOne(array('machine_id'=>$machine_id));
        if(empty($machineInfo)){
            $this->reply('找不到机器');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('id'=>$machineInfo['club_id']));
        $ticketInfo = $this->ticket_model->fetchOne(array('id'=>$machineInfo['ticket_id']));
        if(empty($clubInfo) || empty($ticketInfo)){
            $this->reply('缺少店铺信息或票种信息，请联系管理人员');
            return;
        }
        $rs =[];
        $rs += $machineInfo;
        $rs['ticket_name'] = $ticketInfo['title'];
        $rs['price'] = $ticketInfo['price'];
        $rs['image'] = $ticketInfo['image'];
        $rs['viewname'] = $clubInfo['view_name'];
        $rs['address'] = $clubInfo['address'];
        $this->success('成功',$rs);
    }
}
