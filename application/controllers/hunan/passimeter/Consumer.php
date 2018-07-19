<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/config/main_config.php";
//require_once "application/core/Base_Inventory.php";
require_once '/var/www/api/Extend/PHPMailer/PHPMailerAutoload.php';
//require_once 'application/helpers/phpmailer/phpmailer/src/PHPMailer.php';
require_once "application/third_party/CCPRestSmsSDK.php";
class Consumer extends Base_WechatPay
{
    private $memcache = '';
    private $mem_ip = '';
    private $mem_port = '';
    private $dispatchUrl = 'http://yan.eeseetech.cn';
    function __construct()
    {
        parent::__construct();
        $this->mem_ip = get_instance()->config->config['memcache_ip'];
        $this->mem_port = get_instance()->config->config['memcache_part'];
        ob_clean();
    }
    public function login(){
        $this->dispatch();
        die;


        $this->load->model('Common_model');
        $machine_id = $this->getParam('machine_id');
        $machine_id = $this->stringToASCII($machine_id);
        if(empty($machine_id) || strlen($machine_id) != 26){
            $this->jump('1');
            return;
        }
        //获取openid
        $openid = $this->getOpenId();
        if(empty($openid) || gettype($openid) != 'string' || strlen($openid) != 28){
            $this->jump('2');
            return;
        }
        $openid = 'Consumer';
        if($openid){
            #跳转到售票
            $url = base_url()."lottery-vending/index.html?1523411266#/orders"."?openid={$openid}&machine_id={$machine_id}&timestamp=".time();
            header("location:" . $url);
        }
        else{
            echo "<script>alert('获取用户信息失败，请稍后再试')</script>";
            return;
        }
    }
    //获取之前彩票种类
    public function getLastTicketId(){
        $this->load->model('Common_model');
        $sid = $this->getParam('sid');
        $machine_id = $this->getParam('machine_id');
        if(empty($sid) || empty($machine_id)){
            $this->jump('3');
            return;
        }
        $sessionInfo = $this->getSessionInfo();
        $this->Common_model->setTable('tbl_hunan_machine');
        $filter = [];
        $filter['machine_id'] = $machine_id;
        $machineInfo = $this->Common_model->fetchOne($filter);
        if(empty($machineInfo)){
            $this->jump('4');
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
            $this->jump('3');
            return;
        }
        $this->Common_model->setTable('tbl_ticket');
        $info = $this->Common_model->fetchOne(array('id'=>$ticket_id));
        if(!empty($info)){
            $this->success('成功',$info);
        }
        else{
            $this->jump('5');
        }
    }
    //用户提交订单
    public function submitOrder(){
        $redurl = $this->curPageURL();
        $this->logs('redurl.log', $redurl);
        $this->load->model('Common_model');
        $openid = $this->getParam('openid');
        $machine_id = $this->getParam('machine_id');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $total_fee = $this->getParam('total_fee');
        $t = $this->getParam('t');
        $state = $this->getParam('state');
        if(empty($machine_id) || empty($ticket_id) || empty($ticket_num) || empty($total_fee) || empty($t)){
            $this->jump('3');
            return;
        }
        $openid = $this->getOpenId();
        if(empty($openid)){
            echo "<script>alert('获取用户openid失败')</script>";
            echo "<script>history.back()</script>";
            return;
        }
        //高速缓存羧基
        $this->memcache = new Memcache;
        $this->memcache->connect($this->mem_ip, $this->mem_port);
        if(empty($state)){  
            $mem_data =  $this->memcache->get('order_'.$machine_id);
            if(!empty($mem_data)){
                $this->jump('12');
                return;
            }
            $this->memcache->set('order_'.$machine_id,'true',0,300);
        }
        //查询订单状态
        $this->Common_model->setTable('tbl_hunan_order');
        $filter = [];
        $filter['openid'] = $openid;
        $filter['t'] = $t;
        if($openid == 'openid'){
            unset($filter['openid']);
        }
        $orderInfo = $this->Common_model->fetchOne($filter);
        if(!empty($orderInfo)){
            #跳转到售票
            $this->memcache->delete('order_'.$machine_id);
            $url = base_url()."lottery-vending/index.html#/orders"."?openid={$openid}&machine_id={$machine_id}";
            header("location:" . $url);
            die;
        }
        $this->Common_model->setTable('tbl_hunan_machine');
        $machineInfo = $this->Common_model->fetchOne(array('machine_id'=>$machine_id));
        if(empty($machineInfo)){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('4');
            return;
        }
        //查询票价
        $this->Common_model->setTable('tbl_ticket');
        $ticketInfo =  $this->Common_model->fetchOne(array('id'=>$ticket_id));
        $total = $ticketInfo['price'] * $ticket_num;
        if($total != $total_fee){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('6');
            return;
        }
        if($ticket_id != $machineInfo['ticket_id']){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('8');
            return;
        }
        if($ticket_num > 10){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('9');
            return;
        }
        if($ticket_num > $machineInfo['ticket_num']){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('15');
            return;
        }
        //查询机器状态
        if(time() - $machineInfo['update_date'] > 60*2){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('22');
            return;
        }
        if($machineInfo['net_status'] == 2){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('19');
            return;
        }
        if($machineInfo['status'] != 0){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('20');
            return;
        }
        if($machineInfo['header_status'] == 2){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('24');
            return;
        }
        if($machineInfo['header_status'] != 0){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('21');
            return;
        }
        if($machineInfo['abnormity'] == 1){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('26');
            return;
        }
        if($machineInfo['abnormity'] == 2){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('25');
            return;
        }
        if($machineInfo['abnormity'] == 3){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('27');
            return;
        }
        list($t1, $t2) = explode(' ', microtime());
        $milisec = (int)sprintf('%.0f', (floatval($t1)) * 1000);
        $milisec = sprintf("%03d", $milisec);
        $oid = nownum() . $milisec . gen_rand_num(4).str_pad($machineInfo['id'],11,'0',STR_PAD_LEFT );
        //判断机器状态
        if(!empty($machineInfo['locked_time']) && $machineInfo['locked'] == 1 && $machineInfo['openid'] != $openid){
            //判断是否存在过期订单
            $this->Common_model->setTable('tbl_hunan_order');
            $filter = [];
            $filter['machine_id'] = $machine_id;
            $filter['status'] = 0;
            $orderInfo = $this->Common_model->fetchAll($filter);
            if(time() - strtotime($machineInfo['locked_time']) >= 5*60){
                //查询是否有过期未完成订单
                 //更新订单状态
                if(!empty($orderInfo)){
                    $updateData = [];
                    $updateData['status'] = 2;
                    foreach ($orderInfo as $key => $value) {
                        $where = [];
                        $where['id'] = $value['id'];
                        $flag = $this->Common_model->updateData($updateData,$where);
                    }
                }
                //更新机器状态
                $updateData = [];
                $updateData['locked'] = 0;
                $where = ['machine_id'=>$machine_id];
                $this->Common_model->setTable('tbl_hunan_machine');
                $flag = $this->Common_model->updateData($updateData,$where);
            }
            //订单支付中
            if(time() - strtotime($machineInfo['locked_time']) < 60*5){
                if($orderInfo['pay_status'] == 0 || $orderInfo['ticket_status'] == 0){
                    $this->memcache->delete('order_'.$machine_id);
                    $this->jump('12');
                    return;
                }
            }
        }
        //当同一机器同一人时判断是否 此人存在付款未出票订单
        if($machineInfo['openid'] == $openid){
            $filter = [];
            $filter['machine_id'] = $machineInfo['id'];
            $filter['openid'] = $openid;
            $orders = [];
            $orders['id'] = 'desc';
            $this->Common_model->setTable('tbl_hunan_order');
            $orderInfo = $this->Common_model->fetchOne($filter,$orders);
            if(!empty($orderInfo) && $orderInfo['pay_status'] == 1 && $orderInfo['ticket_status'] == 0 && time() - strtotime($orderInfo['pay_date']) < 60*2 ){
                $this->memcache->delete('order_'.$machine_id);
                $this->jump('12');
                return;
            }
        }
        $insertData = [];
        $insertData['oid'] = $oid;
        $insertData['openid'] = $openid;
        $insertData['machine_id'] = $machineInfo['id'];
        $insertData['ticket_id'] = $ticket_id;
        $insertData['ticket_num'] = $ticket_num;
        $insertData['total_fee'] = $total;
        $insertData['status'] = 0;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $insertData['t'] = $t;
        $this->Common_model->setTable('tbl_hunan_order');
        $order_id = $this->Common_model->insertData($insertData);
        if(!$order_id){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('13');
            return;
        }
        //锁定机器状态
        $updateData = [];
        $updateData['locked'] = 1;
        $updateData['locked_time'] = date('Y-m-d H:i:s');
        $updateData['openid'] = $openid;
        $this->Common_model->setTable('tbl_hunan_machine');
        $flag = $this->Common_model->updateData($updateData,array('machine_id'=>$machine_id));
        if($flag === false){
            $this->memcache->delete('order_'.$machine_id);
            $this->jump('14');
            return;
        }
        #提交微信订单
        $flag = $this->memcache->delete('order_'.$machine_id);
        // var_dump($flag);die;
        $total_fee = $total_fee;
        $this->submitWechatOrder($oid,$total_fee,$openid,$order_id,$machine_id);
    }
   //统一下单
    public function submitWechatOrder($oid,$total_fee,$openid,$order_id,$machine_id){
        $machine_id = $this->getParam('machine_id');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $total_fee *= 100;
        $notify_url = base_url()."hunan/passimeter/Consumer/notify";
        $prepay_id = $this->getPrepayId($oid,$total_fee,$notify_url,$openid);
        $json = $this->getJsParams($prepay_id);
        $okUrl = base_url()."hunan/passimeter/Socket/issueTicket?oid={$oid}&ticket_id={$ticket_id}&ticket_num={$ticket_num}&machine_id={$machine_id}&order_id={$order_id}&openid={$openid}&machine_id={$machine_id}";
        $cancelUrl = base_url()."hunan/passimeter/Socket/cancel?oid={$oid}&ticket_id={$ticket_id}&ticket_num={$ticket_num}&machine_id={$machine_id}&order_id={$order_id}&openid={$openid}&machine_id={$machine_id}";
        $failUrl = base_url()."hunan/passimeter/Socket/failed?oid={$oid}&ticket_id={$ticket_id}&ticket_num={$ticket_num}&machine_id={$machine_id}&order_id={$order_id}&openid={$openid}&machine_id={$machine_id}";
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
               // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。 
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
    //获取订单状态
    public function getOrderDetail(){
        $this->load->model('Common_model');
        $order_id = $this->getParam('order_id');
        if(empty($order_id)){
            $this->reply('缺少参数');
            return;
        }
        $this->Common_model->setTable('tbl_hunan_order');
        $orderInfo = $this->Common_model->fetchOne(array('id'=>$order_id));
        if($orderInfo['ticket_response'] == 0 && time() - strtotime($orderInfo['pay_date']) > 60*2 ){
            $orderInfo['timeout'] = true;
            $this->success('成功',$orderInfo);
        }
        else{
            $orderInfo['timeout'] = false;
            $this->success('成功',$orderInfo);
        }
    }
    //获取设备信息
    public function getmachineInfo(){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->load->model('ticket_model');
        $openid = $this->getParam('openid');
        $machine_id = $this->getParam('machine_id');
        if(empty($machine_id)){
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

        @($this->check_inventory($machineInfo,$ticketInfo,$clubInfo)); //余票不足的提醒

        $rs =[];
        $rs += $machineInfo;
        $rs['ticket_name'] = $ticketInfo['title'];
        $rs['price'] = $ticketInfo['price'];
        $rs['image'] = base_url().$ticketInfo['image'];
        $rs['viewname'] = $clubInfo['view_name'];
        $rs['address'] = $clubInfo['address'];
        $this->success('成功',$rs);
    }


    /**
     * 余票检测，不足30%发送短信和邮件通知
     */

    public function check_inventory($machineInfo,$ticketInfo,$clubInfo)
    {
        $this->load->model("machine_model");

        $last_add_time = $this->machine_model->get_last_add_time($machineInfo["machine_id"]);
        $last_filter_time = $this->machine_model->get_last_filter_time($machineInfo["machine_id"]);

        if ($last_add_time >= $last_filter_time) {

            $inventory = $ticketInfo['count_price'] / $ticketInfo['price'];
            $aiis = str_split($machineInfo['machine_id'], 2);
            $machine_id = '';
            foreach ($aiis as $k => $aii) {
                $machine_id .= hex2bin($aii);
            }
            if (0.3 * $inventory >= $machineInfo['ticket_num']) {
                $userinfo = $this->machine_model->get_email($clubInfo['user_id']);
                $email = $userinfo['email'];
                $phone = $clubInfo['phone'];
                if ($email == null) {
                    $status1='2';
                } else {
                    $toemail = $email;
                    $name = '中维';
                    $subject = '余票不足提醒';
                    $content = "你好，" . $machine_id . "号机器余票不足，请及时加票";
                    $email_res = $this->send_mail($toemail, $name, $subject, $content);
                    $status1='1';
                }
                if ($phone == null) {
                    $status2='2';
                } else {
                    $phone_res = $this->zwsendsms($phone, array($clubInfo['view_name'], $clubInfo['address'], $machine_id, $machineInfo['ticket_num']), "243628");
                    $status2='1';
                }
                if ($status1 == '1' && $status2='1'){
                    $status="1";
                    $contents="发送成功";
                }elseif($status1 == '2' && $status2 == '1'){
                    $status="1";
                    $contents="邮件发送失败";
                }elseif($status1 == '1' && $status2 == '2'){
                    $status="1";
                    $contents="短信发送失败";
                }else{
                    $status="2";
                    $contents="邮件、短信发送失败";
                }
                $this->machine_model->add_filter_log($machineInfo["machine_id"],$status,$contents);
            }
        }
    }

    public function test(){
        $res=$this->zwsendsms("18715103271", array("名称","合肥","555","10"), "243628");
        var_dump($res);
    }

    public function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {
        $mail = new PHPMailer();           //实例化PHPMailer对象
        $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();                    // 设定使用SMTP服务
        $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
        $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';          // 使用安全协议
        $mail->Host = "smtp.mxhichina.com"; // SMTP 服务器
        $mail->Port = 465;                  // SMTP服务器的端口号
        $mail->Username = "eesee@eeseetech.com";    // SMTP服务器用户名
        //  $mail->Password = "suajukwgkqytbbac";     // SMTP服务器密码
        $mail->Password = "A82i#39szy";     // SMTP服务器密码
        $mail->SetFrom('eesee@eeseetech.com', '中维');
        $replyEmail = '';                   //留空则为发件人EMAIL
        $replyName = '';                    //回复名称（留空则为发件人名称）
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($tomail, $name);
        if (is_array($attachment)) { // 添加附件
            foreach ($attachment as $file) {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        return $mail->Send() ? true : $mail->ErrorInfo;
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


    public function payPserson(){
        $oid = '20180402114023699342900000000001';
        $openid = 'oGpqZ0ZwYIWohX2mcBOcMGQWum-0';
        $amount = 100;
        $res = $this->payToPerson($oid,$openid,$amount);
        var_dump($res);
    }
    public function resJson(){
        $rs = array(
              1=> "机器编码错误",
              2=> "获取openid失败，请联系管理人员",
              3=> "缺少参数",
              4=> "机器不存在",
              5=> "没有票种信息",
              6=> "订单金额有误",
              7=> "票种不符",
              8=> "票种不符",
              9=> "一次最多下10张票",
              10=> "机器异常，暂时无法下单,code:1",
              11=> "机器异常，暂时无法下单,code:2",
              12=> "设备被使用中, 请稍后重试！",
              13=> "提交订单失败，请换台机器重试，code:1",
              14=> "提交订单失败，请换台机器重试，code:2",
              15=> "余票不足",
              16=> "微信支付返回签名错误，请联系管理员",
              17=> "没有找到订单，请联系管理员",
              18=> "订单金额错误，请联系管理员",
              19=> "机器网络状态错误",
              20=> "机器运行状态错误",
              21=> "机器机头状态错误",
              22=> "机器心跳超时",
              23=> "机器使用中",
              24=> "机头无票",
              25=> "设备未连接",
              26=> "系统请求错误",
              27=> "设备离线",
            );
        $this->success('成功',$rs);
    }
    //扫码跳转到 yan.eeseetech.cn
    public function dispatch(){
        $url = $this->dispatchUrl.$_SERVER["REQUEST_URI"];
         header("location:" . $url);
    }

//     array(7) {
//   ["return_code"]=>
//   string(7) "SUCCESS"
//   ["return_msg"]=>
//   string(12) "支付失败"
//   ["mch_appid"]=>
//   string(18) "wx103329a9f81a1866"
//   ["mchid"]=>
//   string(10) "1493356772"
//   ["result_code"]=>
//   string(4) "FAIL"
//   ["err_code"]=>
//   string(9) "NOTENOUGH"
//   ["err_code_des"]=>
//   string(12) "余额不足"
// }
}
?>
