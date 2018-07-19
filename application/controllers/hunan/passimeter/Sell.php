<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/third_party/Qcode/phpqrcode.php";
require_once "application/config/main_config.php";
class Sell extends Base_WechatPay
{
    private $logPath = '';
    private $qrcodePath = '';
    // private $qrcodeUrl = '';
    private $logo = '';
    // const APPID = MainConfig::APPID;
    // const SECRET = MainConfig::APPSECRET ;
    // const MCHID = MainConfig::MCHID;
    // const KEY = MainConfig::KEY;
    const CODEURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const OPENIDURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    
    const UNURL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    function __construct()
    {
        parent::__construct();
        $this->logPath = get_instance()->config->config['log_path_file'];
        // $this->qrcodePath = '/var/www/download/zhongwei/qrcode/hunan/passimeter/';     【上线后调整】
        $this->qrcodePath = 'D:/image/fulei/machine/';
        // $this->qrcodeUrl = get_instance()->config->config['base_hunan_qrcode_url'];
    }
    public function obtionOpenid(){
        $this->getOpenId2();
    }
    //获取openid
    public function getOpenId2(){
        unset($_SESSION);   //清除内存中变量
        session_destroy();  //清除文件
        $this->logs('openid2.log', 1);
        if(!empty($this->session->openid)){
            $this->logs('openid2.log', 4);
            return $this->session->openid;
        }else{
            //1.用户访问一个地址 先获取到code
            if(!isset($_GET['code'])){
                //print_r($_SERVER);
                $redurl = $this->curPageURL();
                $this->logs('openid2.log', 2);
                $this->logs('openid2.log', $_GET['code']);
                $url = self::CODEURL . "appid=" .self::APPID ."&redirect_uri={$redurl}&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
                //构建跳转地址 跳转
                header("location:{$url}");
                die;
            }else{
                //2.根据code获取到openid
                //调用接口获取openid
                $this->logs('openid2.log', 3);
                $this->logs('openid2.log', $_GET['code']);
                $openidurl = self::OPENIDURL . "appid=" . self::APPID . "&secret=".self::SECRET . "&code=" . $_GET['code'] . "&grant_type=authorization_code";
                $data = file_get_contents($openidurl);
                $arr = json_decode($data,true);
                $this->session->set_userdata(array('openid'=>$arr['openid']));
                return $arr['openid'];             
            }
        }
    }
    public function test(){
        $this->batchQrcode();
    }
    public function batchQrcode(){
        ignore_user_abort(true);
        set_time_limit(0);
        header('HTTP/1.1 200 OK');
        header('Content-Length:0');
        header('Connection:Close');
        flush();
        // $basestr = 'Z610M01CS00';
        $basestr = 'Z610M01CZ';
        $arr = range(1, 1540);
        // var_dump($arr);
        foreach ($arr as $key => $value) {
            if(strlen($value)<4){
                // continue;
            }
            // if(strlen($value) == 1){
                $arr[$key] = str_pad($value,4,'0',STR_PAD_LEFT );
            // }
                // var_dump($arr[$key]);
                // die;
            // else{
            //     $arr[$key] = (string)$value;
            // }
        }
        // var_dump($arr);
        // die;
        foreach ($arr as $key => $value) {
            $arr[$key] = $basestr.$value;
        }
        foreach ($arr as $key => $value) {
            $machine_id = $value;
            $uid = $this->stringToASCII($value);
            $fileName = "{$value}_{$uid}";
            $this->createQrcode($machine_id,$fileName);
            // die;
        }
        // var_dump($arr);
    }
    public function createQrcode($machine_id='',$fileName = '',$filePath = ''){
        ob_clean();     
        // $url = base_url().'hunan/passimeter/Sell/order';
        // $url = "http://test.yan.eeseetech.cn/hunan/passimeter/Consumer/submitOrder?machine_id=4512478&ticket_id=41&ticket_num=1&openid=oGpqZ0ZwYIWohX2mcBOcMGQWum-0&total_fee=2";
        // $url = "http://test.yan.eeseetech.cn/hunan/passimeter/Machine/login?machine_id={$machine_id}";
        // $url = "http://test.yan.eeseetech.cn/hunan/passimeter/Consumer/login?machine_id={$machine_id}";

        $url = "https://yan.bjzwhz.cn/hunan/passimeter/Machine/login?machine_id={$machine_id}";
        // $url = "https://yan.bjzwhz.cn/hunan/passimeter/Consumer/login?machine_id={$machine_id}";
        // if(empty($fileName)){
        //     $fileName = '1.png';
        // }
        // else{
        //     $fileName = $area_id.'_'.$club_id.'.png';
        // } 
        if(!$this->createDir($this->qrcodePath)){
            $this->reply('创建存放二维码目录失败');
            return;
        };
        $savePath = $this->qrcodePath.$fileName.'.jpg';
        // QRcode::png($url,$savePath,'H',4,2,false);
        $logo = 'D:\www\branchyan\logo\1.jpg';
        QRcode::png($url,$savePath,'H',3.2,2,false,$logo);
        die;
    }
    public function productQrcode($fileName = '',$club_id = '',$area_id = ''){
        ob_clean();     
        // $url = base_url().'hunan/passimeter/Sell/order';
        // $url = "http://test.yan.eeseetech.cn/hunan/passimeter/Consumer/submitOrder?machine_id=4512478&ticket_id=41&ticket_num=1&openid=oGpqZ0ZwYIWohX2mcBOcMGQWum-0&total_fee=2";
        // $url = "http://test.yan.eeseetech.cn/hunan/passimeter/Machine/login?machine_id=0000000000015";
        // $url = "http://test.yan.eeseetech.cn/hunan/passimeter/Consumer/login?machine_id=Z610M01CS0002";

        $url = "https://yan.bjzwhz.cn/hunan/passimeter/Consumer/login?machine_id=0000000000015";
        if(empty($fileName)){
            $fileName = '1.png';
        }
        else{
            $fileName = $area_id.'_'.$club_id.'.png';
        } 
        if(!$this->createDir($this->qrcodePath)){
            $this->reply('创建存放二维码目录失败');
            return;
        };
        $savePath = $this->qrcodePath.$fileName;
        // QRcode::png($url,$savePath,'H',4,2,false);
        $logo = 'D:\www\branchyan\logo\31323299763674547.jpg';
        QRcode::png($url,false,'H',3.2,2,false);
    }
    public function dispatch(){
        $this->load->model("Common_model");
        $this->load->model("club_model");
        $club_id = $this->getParam('club_id');
        if(empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('id'=>$club_id));
        if(empty($clubInfo)){
            $this->reply('没有找到店铺，请联系管理人员');
            return;
        }
        if($clubInfo['status'] != 1){
            $this->reply('店铺没有通过审核，请联系管理人员');
            return;
        }
        $url = base_url().'hunan/passimeter/Sell/productQrcode';  //跳转到售票h5页面
        header("location:" . $url);
    }
    public function getTicketType(){
        $this->load->model("Common_model");
        $this->load->model("club_model");
        $club_id = $this->getParam('club_id');
        if(empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('id'=>$club_id));
        if(empty($clubInfo)){
            $this->reply('没有找到店铺，请联系管理人员');
            return;
        }
        if($clubInfo['status'] != 1){
            $this->reply('店铺没有通过审核，请联系管理人员');
            return;
        }
        $this->Common_model->setTable('tbl_ticket_club');
        $ticketInfo = $this->Common_model->fetchOne(array('club_id'=>$club_id));
        if(empty($ticketInfo)){
            $this->reply('店铺没有票券');
            return;
        }
    }
    public function submitOrder(){

    }

    //统一下单
    public function order(){
        $oid = md5(time());
        $total_fee = 1;
        $prepay_id = $this->getPrepayId($oid,$total_fee);
        $json = $this->getJsParams($prepay_id);
        echo <<<jsabc
<script>
function onBridgeReady(){
   WeixinJSBridge.invoke(
       'getBrandWCPayRequest', {$json},
       function(res){     
           if(res.err_msg == "get_brand_wcpay_request:ok" ) {} 
            else{
                console.log(res);
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
jsabc;
    }
    //回调
    public function notify(){
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
        $this->logs('notify_success.log', json_encode($res));
    }
    public function createDir($dir){
        if(gettype($dir) == 'string'){
            $flag = is_dir($dir) || mkdir($dir,0777,true);
            return $flag;
        }
        else{
            $this->reply('参数错误，code:99');
            die;
        }
    }
    public function lotteryBind(){
        $this->load->model("Common_model");
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        $name = $this->getParam('name');
        $phone = $this->getParam('phone');
        $lottery_papers = $this->getParam('lottery_papers');
        $lottery_papers_image = $this->getParam('lottery_papers_image');
        if(empty($sid) || empty($name) || empty($phone) || empty($lottery_papers) || empty($lottery_papers_image)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            return;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = '2';
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            return;
        }
        $user_id = $sessionInfo['user_id'];
        $this->Common_model->setTable('tbl_club');
        $clubInfo = $this->Common_model->fetchOne(array('phone'=>$phone));
        if(empty($clubInfo)){
            $this->reply('找不到店铺，请核对信息');
            return;
        }
        if($clubInfo['status'] == 0){
            $this->reply('该店铺未通过客户经理审核');
            return;
        }
        if($clubInfo['name'] != $name){
            $this->reply('店主姓名不匹配，请核对信息');
            return;
        }
        $club_id = $clubInfo['id'];
        // if($clubInfo['status'] == 1){
        //     $this->reply('该店铺已通过审核');
        //     return;
        // }
        //取代销证信息
        $this->Common_model->setTable('tbl_lottery_papers');
        $lottery_papersInfo = $this->Common_model->fetchOne(array('club_id'=>$club_id));
        if(!empty($lottery_papersInfo) && $lottery_papersInfo['valid'] == 1){
            $this->reply('该店铺已经绑定过代销证');
            return;
        }
        $club_id = $clubInfo['id'];
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['club_id'] = $club_id;
        $insertData['lottery_papers'] = $lottery_papers;
        $insertData['lottery_papers_image'] = $lottery_papers_image;
        $insertData['name'] = $name;
        $insertData['phone'] = $phone;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_lottery_papers');
        $flag = $this->Common_model->insertData($insertData);
        if(!$flag){
            $this->reply('绑定信息失败，请联系管理人员。code:1');
            file_put_contents($this->logPath.'insert_lottery.log', date("Y-m-d H:i:s").PHP_EOL.var_export($insertData).PHP_EOL,FILE_APPEND|LOCK_EX);
            return;
        }
        //改变店铺状态
        $this->Common_model->setTable('tbl_redeemConfig');
        $user_id = $clubInfo['user_id'];
        $area_id = $clubInfo['area_id'];
        $redeemInfo = $this->Common_model->fetchOne(array('province_id'=>$area_id));
        if(!empty($redeemInfo)){
            $stationId = $redeemInfo['stationId'];
        }
        $updateData = [];
        $updateData['status'] = 1;
        $updateData['refuse'] = 0;
        $updateData['lottery_license'] = $lottery_papers;
        $updateData['audit_time_lottery'] = date("Y-m-d H:i:s");
        $this->Common_model->setTable('tbl_club');
        $flag3 = $this->Common_model->updateData($updateData,array('id'=>$clubInfo['id']));
        //给用户分配站点
        $updateData = [];
        $updateData['stationId'] = $stationId;
        $this->Common_model->setTable('tbl_user');
        $flag3 = $this->Common_model->updateData($updateData,array('id'=>$user_id));
        $flag = $this->ajaxSend($clubInfo['phone'],'236001');
        $this->success('成功');
    }
}
?>
