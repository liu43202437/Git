<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
require_once "application/config/main_config.php";
class Machine extends Base_WechatPay
{
    private $dispatchUrl = 'http://yan.eeseetech.cn';
    function __construct()
    {
        parent::__construct();
    }
    public function login(){
        $this->dispatch();
        die;
        
        $this->load->model('Common_model');
        $machine_id = $this->getParam('machine_id');
        //机器编码转换
        $machine_id = $this->stringToASCII($machine_id);
        if(empty($machine_id) || strlen($machine_id) != 26){
            $this->reply('机器编码错误');
            return;
        }
        //获取openid
        $openid = $this->getOpenId();
        if(empty($openid) || gettype($openid) != 'string' || strlen($openid) != 28){
            $this->reply('获取openid失败，请联系管理人员');
            return;
        }
        $filter = [];
        $filter['openid'] = $openid;
        $this->Common_model->setTable('tbl_hunan_staff');
        $userInfo = $this->Common_model->fetchOne($filter);
        if(empty($userInfo)){
            $insertData = [];
            $insertData['openid'] = $openid;
            $insertData['login_date'] = date('Y-m-d H:i:s');
            $insertData['create_date'] = date('Y-m-d H:i:s');
            $user_id = $this->Common_model->insertData($insertData);
            //产生sid
            $insertData = [];
            $insertData['session_id'] = md5($openid);
            $insertData['user_id'] = $user_id;
            $insertData['expire_date'] = date('Y-m-d H:i:s',time() + 3600*24*7);
            $insertData['create_date'] = date('Y-m-d H:i:s');
            $this->Common_model->setTable('tbl_hunan_session');
            $id = $this->Common_model->insertData($insertData);
            if($id){
                $sid = $insertData['session_id'];
            }
        }
        else{
            $updateData = [];
            $updateData['login_date'] = date('Y-m-d H:i:s');
            $filter = [];
            $filter['openid'] = $openid;
            $flag = $this->Common_model->updateData($updateData,$filter);
            $user_id = $userInfo['id'];

            $this->Common_model->setTable('tbl_hunan_session');
            $filter = [];
            $filter['user_id'] = $user_id;
            $sessionInfo = $this->Common_model->fetchOne($filter);
            $sid = $sessionInfo['session_id'];
            //更新session时间
            $updateData = [];
            $updateData['expire_date'] = date('Y-m-d H:i:s',time() + 3600*24*7);
            $filter = [];
            $filter['user_id'] = $user_id;
            $flag = $this->Common_model->updateData($updateData,$filter);
        }
        if(isset($sid)){
            #跳转到登录页面
            $url = base_url()."lottery-vending/index.html#/login-verify"."?sid={$sid}&machine_id={$machine_id}";
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
        //校验密码
        $filter  = [];
        $filter['type'] = 1;
        $this->Common_model->setTable('tbl_hunan_config');
        $config = $this->Common_model->fetchOne($filter);
        if(empty($config)){
            $this->reply('获取配置文件失败，请联系管理员');
            return;
        }
        $config['content'] != $pwd ? $flag = false : $flag = true ;
        unset($config);
        $this->Common_model->setTable('tbl_hunan_staff');
        if(!$flag){
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
        $rs = [];
        if(empty($machineInfo)){
            #跳转到设备激活页面
            $rs['status'] = true;
            $this->success('成功',$rs);
        }
        else{
            #进入设备管理页面
            $rs['status'] = false;
            $this->success('成功',$rs);
        }

    }
    //设备绑定
    public function bindMachine(){
        $this->load->model('Common_model');
        $machine_id = $this->getParam('machine_id');
        $sid = $this->getParam('sid');
        $club_id = $this->getParam('club_id');
        if(empty($machine_id) || empty($sid) || empty($club_id)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->getSessionInfo();
        $user_id = $sessionInfo['user_id'];
        $this->Common_model->setTable('tbl_hunan_staff');
        $staffInfo = $this->Common_model->fetchOne(array('id'=>$user_id));
        if(empty($staffInfo)){
            $this->reply('找不到员工信息');
            return;
        }
        //查询设备是否以及注册
        $this->Common_model->setTable('tbl_hunan_machine');
        $machineInfo = $this->Common_model->fetchOne(array('machine_id'=>$machine_id));
        if(!empty($machineInfo)){
            $this->reply('设备已注册');
            return;
        }
        $insertData = [];
        $insertData['machine_id'] = $machine_id;
        $insertData['machine_code'] = $this->ASCIITostring($machine_id);
        $insertData['club_id'] = $club_id;
        $insertData['staff_id'] = $staffInfo['id'];
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $flag = $this->Common_model->insertData($insertData);
        if(!$flag){
            $this->reply('设备绑定失败，code:1');
            return;
        }
        else{
            #跳转到设备管理页面
            $this->success('成功');
        }
    }
    public function clubList(){
        $this->load->model('club_model');
        $area_code = $this->getParam('area_code');
        if(empty($area_code)){
            $this->reply('缺少参数');
            return;
        }
        $filter = [];
        $filter['area_code'] = $area_code;
        $list = $this->club_model->fetchAll($filter);
        $rs = [];
        foreach ($list as $key => $value) {
            $rs[$key]['id'] = $value['id'];
            $rs[$key]['name'] = $value['name'];
            $rs[$key]['phone'] = $value['phone'];
            $rs[$key]['address'] = $value['address'];
            $rs[$key]['area_code'] = $value['area_code'];
            $rs[$key]['view_name'] = $value['view_name'];
        }
        $this->success('成功',$rs);
    }
    //加票
    public function addTicket(){
        $this->load->model('Common_model');
        $ticket_id = $this->getParam('ticket_id');
        $ticket_num = $this->getParam('ticket_num');
        $sid = $this->getParam('sid');
        $machine_id = $this->getParam('machine_id');
        if(empty($ticket_id) || empty($ticket_num) || empty($sid) || empty($machine_id)){
            $this->reply('缺少参数');
            return;
        }
        if(!preg_match("/^[1-9]\d*$/",$ticket_num)){
            $this->reply('添加票种数错误');
            return;
        }
        $sessionInfo = $this->getSessionInfo();
        $user_id = $sessionInfo['user_id'];
        $this->Common_model->setTable('tbl_hunan_machine');
        $machineInfo = $this->Common_model->fetchOne(array('machine_id'=>$machine_id));
        if(empty($machineInfo)){
            $this->reply('没有找到机器');
            return;
        }
        $old_ticket_num = $machineInfo['ticket_num'];
        $old_ticket_id = $machineInfo['ticket_id'];
        if($old_ticket_num + $ticket_num){
            //暂时不对数量做验证
        }
        if($ticket_num < 1){
            $this->reply('加票数量不能小于1');
            return;
        }
        if(empty($old_ticket_id) && empty($old_ticket_num)){
            $updateData = [];
            $updateData['ticket_num'] = $old_ticket_num + $ticket_num;
            $updateData['last_add_staff_id'] = $user_id;
            $updateData['last_add_num'] = $ticket_num;
            $updateData['ticket_id'] = $ticket_id;
        }
        else{
            if($old_ticket_id != $ticket_id && $old_ticket_num != 0){
                //将之前的票清零
                $updateData = [];
                $updateData['ticket_num'] = $ticket_num;
                $updateData['last_add_staff_id'] = $user_id;
                $updateData['last_add_num'] = $ticket_num;
                $updateData['ticket_id'] = $ticket_id;
                // $this->reply('本次添加票种与余票不符');
                // return;
            }
            else{
                $updateData = [];
                $updateData['ticket_num'] = $old_ticket_num + $ticket_num;
                $updateData['last_add_staff_id'] = $user_id;
                $updateData['last_add_num'] = $ticket_num;
            }
        }
        $num = $updateData['ticket_num'];
        $flag = $this->Common_model->updateData($updateData,array('machine_id'=>$machine_id));
        if($flag === false){
            $this->reply('失败');
            return;
        }
        //加票记录
        $insertData = [];
        $insertData['staff_id'] = $user_id;
        $insertData['machine_id'] = $machine_id;
        $insertData['ticket_id'] = $ticket_id;
        $insertData['ticket_num'] = $ticket_num;
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $this->Common_model->setTable('tbl_hunan_add_ticket');
        $flag2 = $this->Common_model->insertData($insertData);
        if($flag && $flag2){
            $this->addCredits($ticket_id,$machineInfo,$ticket_num);
            $this->success('成功',$num);
            #通知单片机加票成功
            return;
        }
        else{
            $this->reply('添加加票记录失败');
            return;
        }
    }
    //加票时给零售店加公益分
    public function addCredits($ticket_id,$machineInfo,$ticket_num){
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $this->Common_model->setTable('tbl_ticket');
        $filter = [];
        $filter['id'] = $ticket_id;
        $ticketInfo = $this->Common_model->fetchOne($filter);
        $credit = $ticketInfo['count_price'] / 100;
        $clubInfo = $this->club_model->fetchOne(array('id'=>$machineInfo['club_id']));
        $user_id = $clubInfo['user_id'];
        $multiplier =  $ticket_num / ($ticketInfo['count_price'] / $ticketInfo['price']);

        //增加积分
        $credits = $credit * $multiplier;
        $insertData = [];
        $insertData['user_id'] = $user_id;
        $insertData['name'] = $clubInfo['name'];
        $insertData['trade_no'] = '加票';
        $insertData['add_time'] = time();
        $insertData['create_date'] = date('Y-m-d H:i:s');
        $insertData['address'] = $clubInfo['address'];
        $insertData['credits'] = $credits;
        $insertData['status'] = 1;
        $insertData['type'] = 8;
        $this->Common_model->setTable('tbl_user_credits');
        $flag = $this->Common_model->insertData($insertData);
        $sql = "update tbl_user set point=point+{$credits} where id={$user_id}";
        $flag2 = $this->Common_model->sqlUpdate($sql);
    }
    //获取之前彩票种类
    public function getLastTicketId(){
        $this->load->model('Common_model');
        $this->load->model('ticket_model');
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
        $ticketInfo = $this->ticket_model->fetchOne(array('id'=>$ticket_id));
        $rs['ticket_id'] = $ticket_id;
        $rs['price'] = explode('.', $ticketInfo['price'])[0];
        $rs['price'] = $ticketInfo['price'];
        $rs['ticket_num'] = $machineInfo['ticket_num'];
        $this->success('成功',$rs);
        return;
    }
    public function getTicketInfo(){
        $this->load->model('Common_model');
        $ticket_id = $this->getParam('ticket_id');
        $sid = $this->getParam('sid');
        if(empty($ticket_id) || empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        $this->Common_model->setTable('tbl_ticket');
        $info = $this->Common_model->fetchOne(array('id'=>$ticket_id));
        if(empty($info['num'])){
            $info['num'] = $info['count_price'] / $info['price'];
        }
        if(!empty($info)){
            $this->success('成功',$info);
        }
        else{
            $this->reply('没有票种信息');
        }
    }
    public function getTicketType(){
        $this->load->model('ticket_model');
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $sid = $this->getParam('sid');
        $machine_id = $this->getParam('machine_id');
        if(empty($sid) || empty($machine_id)){
            $this->reply('缺少参数');
            return;
        }
        $sessionInfo = $this->getSessionInfo();
        $filter = [];
        if($machine_id){
            $filter = [];
            $filter['machine_id'] = $machine_id;
            $this->Common_model->setTable('tbl_hunan_machine');
            $machineInfo = $this->Common_model->fetchOne($filter);
            if(empty($machineInfo)){
                $this->reply('未找到机器');
                return;
            }
            $filter = [];
            $filter['id'] = $machineInfo['club_id'];
            $clubInfo = $this->club_model->fetchOne($filter);
            if(empty($clubInfo)){
                $this->reply('未找到店铺');
                return;
            }
            unset($filter);
            $filter = [];
            $filter['province_id'] = $clubInfo['area_id'];
        }
        $filter['status'] = 0;
        $info = $this->ticket_model->fetchAll($filter);
        $rs  = [];
        foreach ($info as $key => $value) {
            $rs[] = $value['price'];
        }
        $rs = array_unique($rs);
        $temp = [];
        foreach ($rs as $key => $value) {
            $temp[] = floatval($value); 
        }
        $res = [];
        foreach ($temp as $key => $value) {
            switch ($value) {
                case '0.01':
                    $res[] = array('id'=>'0.01','value'=>'一分票');
                    break;
                case '0.10':
                    $res[] = array('id'=>'0.10','value'=>'一角票');
                    break;
                case '0.1':
                    $res[] = array('id'=>'0.1','value'=>'一角票');
                    break;
                case '2':
                    $res[] = array('id'=>'2','value'=>'二元票');
                    break;
                case '5':
                    $res[] = array('id'=>'5','value'=>'五元票');
                    break;
                case '10':
                    $res[] = array('id'=>'10','value'=>'十元票');
                    break;
                case '20':
                    $res[] = array('id'=>'20','value'=>'二十元票');
                    break;
                default:
                    # code...
                    break;
            }
        }
        // var_dump($res);
        $this->success('成功',$res);
    }
    public function getTicketList(){
        $this->load->model('ticket_model');
        $this->load->model('Common_model');
        $this->load->model('club_model');
        $sid = $this->getParam('sid');
        $price = $this->getParam('price');
        $machine_id = $this->getParam('machine_id');
        $sessionInfo = $this->getSessionInfo();
        if(empty($price) || empty($machine_id)){
            $this->reply('缺少参数');
            return;
        }
        $filter = [];        
        if($machine_id){
            $filter = [];
            $filter['machine_id'] = $machine_id;
            $this->Common_model->setTable('tbl_hunan_machine');
            $machineInfo = $this->Common_model->fetchOne($filter);
            if(empty($machineInfo)){
                $this->reply('未找到机器');
                return;
            }
            $filter = [];
            $filter['id'] = $machineInfo['club_id'];
            $clubInfo = $this->club_model->fetchOne($filter);
            if(empty($clubInfo)){
                $this->reply('未找到店铺');
                return;
            }
            unset($filter);
            $filter = [];
            $filter['province_id'] = $clubInfo['area_id'];
        }
        $filter['status'] = 0;
        $filter['price'] = $price;
        if(strpos($price, '.')){
            $filter['price'] =floatval($price);
        }
        $info = $this->ticket_model->fetchAll($filter);
        foreach ($info as $key => $value) {
            if(empty($value['num'])){
                $info[$key]['num'] = $value['count_price'] / $value['price'];
            }
        }
        $this->success('成功',$info);
    }
    // 获取 sessionInfo
    public function getSessionInfo(){
        $this->load->model('Common_model');
        $sid = $this->getParam('sid');
        if(empty($sid)){
            $this->reply('缺少sid');
            die();
        }
        $this->Common_model->setTable('tbl_hunan_session');
        $sessionInfo = $this->Common_model->fetchOne(array('session_id'=>$sid));
        if(empty($sessionInfo)){
            $this->reply('用户不存在');
            die();
        }
        else{
            return $sessionInfo;
        }
    }

    /**
     * 查询店铺下面的 彩票机情况及销售额
     * @URL http://test.yan.eeseetech.cn/hunan/passimeter/machine/getmachineinfo?sid=111;
     * @sid string 店铺主的session id
     * @data json 该店铺主店铺中所有机器的汇总销售数据
     */
    public function getMachineInfo()
    {
        // 获取验证sid
        $sid = $this->getParam('sid');
        $sid = addslashes($sid);
        if (empty($sid)) {
            $this->reply('参数错误！');
            return;
        }
        //加载所需数据库模型
        $this->load->model('session_model');
        $this->load->model('machine_model');
        $this->load->model('Common_model');
        $this->load->model('Order_model');
        //查询店铺信息
        $clubInfo = $this->session_model->getClubBySid($sid);
        if (!$clubInfo['id']) {
            $this->reply('店铺不存在!');
            return;
        }
        $data = [];
        $data['code'] = 0;
        $data['id'] = $clubInfo['id'];
        $data['name'] = $clubInfo['view_name'];

        //查询店铺绑定的机器信息
        $machineInfo = $this->machine_model->getMachineByClubId($clubInfo['id']);
//        echo '<pre>';var_dump($machineInfo);echo '</pre>';die();
        $count = count($machineInfo);
        if ($count < 1) {
            $data['code'] = 1 ;
            $data['msg'] = '此店铺没有绑定机器!';
            echo json_encode($data);
            return;
        }

        $lastMonthStarttime = date('Y-m-01 00:00:00', strtotime('-1 month'));//上个月月初的时间
        $lastMonthEndtime   = date('Y-m-t 23:59:59', strtotime('-1 month'));//上个月月末的时间
        $thisMonthStarttime = date('Y-m-01 00:00:00');//这个月月初的时间
        $thisMonthEndtime   = date('Y-m-d 23:59:59');//这个月至今的时间
        $todayStarttime = date('Y-m-d 00:00:00');//今天起始时间
        $todayEndtime   = date('Y-m-d H:i:s');//当前时间

        //获取此用户店铺下面所有机器的销售数据
        for($i=0 ; $i<$count; $i++){
            $machineId = $machineInfo[$i]['machine_id'];
            $machineCode = $this->ASCIITostring($machineId);
            $data['list'][$i]['uid'] = $machineCode;
            //获取上月销售张数,佣金,总额
            $data['list'][$i]['lastMonthData'] = $this->Order_model->getHunanCountInfo($machineId, $lastMonthStarttime, $lastMonthEndtime);
            //获取本月销售张数,佣金,总额
            $data['list'][$i]['thisMonthData'] = $this->Order_model->getHunanCountInfo($machineId, $thisMonthStarttime, $thisMonthEndtime);
            //获取今天销售张数,佣金,总额
            $data['list'][$i]['todayData'] = $this->Order_model->getHunanCountInfo($machineId, $todayStarttime, $todayEndtime);
        }

        echo json_encode($data);
        return;
    }
    //扫码跳转到 yan.eeseetech.cn
    public function dispatch(){
        $url = $this->dispatchUrl.$_SERVER["REQUEST_URI"];
         header("location:" . $url);
    }

}
?>
