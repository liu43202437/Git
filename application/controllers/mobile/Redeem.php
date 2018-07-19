<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Access-Control-Max-Age: ' . 3600 * 24);
require_once '/var/www/api/Extend/PHPMailer/PHPMailerAutoload.php';
// require_once 'D:\www\eesee\Extend\PHPMailer\PHPMailerAutoload.php';
class Redeem extends Base_MobileController
{
    const APPID = '99e08b6e5bfabcdf8428aa5232df0d33';
    const APPSECRET = 'MjVkMmFjMzU1NGMxNGM5MWE2ZGYzNTMxYTFlODgzDfd';
    private $Host ='https://117.141.10.99:6618/';
    private $station = 'https://117.141.10.99:6618/station/login';
    private $redeem = 'https://117.141.10.99:6618/validate/thirdAward';
    private $ticketStatistics = 'https://117.141.10.99:6618/account/awardSummary';
    private $statisticsDetail = 'https://117.141.10.99:6618/account/awardDetail';
    private $memcache = '';
    private $token = '';
    private $stationId = '45898888';
    private $savePath = '';
    function __construct()
    {
        parent::__construct();
        $this->memcache = memcache_connect('localhost', 11211);
        $this->savePath = get_instance()->config->config['log_path_file'];
        // $this->savePath = 'D:/image/';
    }
    public function doRedeem(){
        $this->load->model('redeem_model');
        $this->load->model('redeemConfig_model'); 
        $this->load->model('club_model');
        $this->load->model('session_model');
        $this->load->model('area_model');
        $this->load->model('user_model');
        $this->load->model('riskControl_model');
        $this->load->model("UserRedeem_model");
        $rs =[];
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        $code = $this->post_input('code');
        if(empty($code)){
            $code = $this->get_input('code');
        }
        if(empty($code) || empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        //用户验证
        $sessioninfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessioninfo)){
            $this->reply('用户session信息不存在');
            return;
        }
        $user_id = $sessioninfo['user_id'];
        $userinfo = $this->user_model->getInfoById($user_id);
        if(empty($userinfo)){
            $this->reply('用户不存在');
            return;
        }
        $clubInfo = $this->club_model->fetchOne(array('user_id'=>$user_id,'status'=>1));
        if(!empty($clubInfo)){
            if($clubInfo['status'] == 0){
                $this->reply('店铺未通过审核');
                return;
            }
        }
        else{
            //判断是否为亲友团
            $claninfo = $this->UserRedeem_model->fetchOne(array('user_id'=>$user_id));
            if(empty($claninfo)){
                $this->reply('该用户无此权限');
                return;
            }
            else{
                $clubInfo = $this->club_model->fetchOne(array('id'=>$claninfo['club_id']));
            }

        }
        if(empty($clubInfo)){
            $this->reply('该用户未注册店铺,或店铺未通过审核');
            return;
        }
        $area_id = $clubInfo['area_id'];
        $areaInfo = $this->area_model->fetchAll(array('id'=>$area_id));
        //判断异常兑奖上限 兑奖金额上限
        $today = date('Y-m-d');
        $riskinfo = $this->riskControl_model->fetchOne(array('sid'=>$sid,'today'=>$today));
        if(!empty($riskinfo)){
            if($riskinfo['failedCount'] >=5000){
                $this->reply('达到异常扫码上限，请次日再试');
                return;
            }
            if($riskinfo['total'] >= 10000){
                $this->reply('达到单日兑奖上限，请次日再试');
                return;
            }
        }
        //取站点api
        $redeemConfig = $this->redeemConfig_model->fetchOne(array('province_id'=>$area_id));
        if(empty($redeemConfig)){
            $this->reply('找不到站点配置信息');
            return;
        }
        if(empty($userinfo['stationId'])){
            $this->reply('功能暂未开放');
            return;
        }
        //设置配置信息
        $this->setConfig(trim($userinfo['stationId']),trim($redeemConfig['station_login']),trim($redeemConfig['redeem_url']));
        list($t1, $t2) = explode(' ', microtime());
        $milisec = (int)sprintf('%.0f', (floatval($t1)) * 1000);
        $milisec = sprintf("%03d", $milisec);
        $transacId = nownum() . $milisec . gen_rand_num().$user_id;
        $response = $this->getRedeemInfo($sid,$transacId,$code);
        if(empty($response)){
            $this->reply('远程兑奖服务器无响应');
            return;
        }
        $temp = json_decode($response);
        if(empty($temp)){
            $this->reply('服务不可用');
            $note = '服务不可用';
            $this->saveError($sid,$user_id,$response,$note);
            return;
        }
        static $i = 0;
        if(json_decode($response)->ret == 27){
            $re = $this->stationLogin();
            if($re == 1013){
                $this->reply('站点不存在或没有激活');
                return;
            }
            if($i > 2) {
                $this->reply('请稍后再试');
                return;
            }
            $i ++;
            $this->doRedeem();
            return;
        }
        //更新用户奖金
        if($temp->ret == 1301){
            $nowPrize = $temp->content->prize;
            $sql = "update tbl_user set prize=IFNULL(prize,0)+{$nowPrize} where id={$user_id}";
            $flag = $this->user_model->execSql($sql);
            if($flag === false){
                $this->reply('累计金额失败，请联系管理人员');
                //记录异常
                $note = '累计金额失败';
                $this->saveError($sid,$user_id,$response,$note);
                return;
            }
            else{
                //记录兑奖上限
                $today = date('Y-m-d');
                if(empty($riskinfo)){
                    $riskinfo = $this->riskControl_model->fetchOne(array('sid'=>$sid));
                }
                if(empty($riskinfo)){
                $insertData = [];
                $insertData['sid'] = $sid;
                $insertData['user_id'] = $user_id;
                $insertData['stationId'] = $this->stationId;
                $insertData['total'] = $nowPrize;
                $insertData['failedCount'] = 0;
                $insertData['today'] = $today;
                $insertData['note'] = '异常扫码';
                $insertData['time'] = date('Y-m-d H:i:s');
                $flag = $this->riskControl_model->insertData($insertData);
                }
                else{
                    if($riskinfo['today'] != $today){
                        $updateData = [];
                        $updateData['today'] = $today;
                        $updateData['total'] = 0;
                        $flag = $this->riskControl_model->updateData($updateData,array('user_id'=>$user_id));
                    }
                    else{
                        if($riskinfo['total'] >= 10000){
                        $this->reply('达到单日扫码上限，请次日再试');
                        return;
                        }
                        else{
                            $sql = "update tbl_riskControl set total=total+{$nowPrize} where user_id={$user_id}";
                            $flag = $this->riskControl_model->execSql($sql);
                        }
                    }
                }
            }
        }
        else{
            //记录异常扫码
            $today = date('Y-m-d');
            if(empty($riskinfo)){
                $riskinfo = $this->riskControl_model->fetchOne(array('sid'=>$sid));
            }
            if(empty($riskinfo)){
                $insertData = [];
                $insertData['sid'] = $sid;
                $insertData['user_id'] = $user_id;
                $insertData['stationId'] = $this->stationId;
                $insertData['total'] = 0;
                $insertData['failedCount'] = 1;
                $insertData['today'] = $today;
                $insertData['note'] = '异常扫码';
                $insertData['time'] = date('Y-m-d H:i:s');
                $flag = $this->riskControl_model->insertData($insertData);
            }
            else{
                if($riskinfo['today'] != $today){
                    $updateData = [];
                    $updateData['today'] = $today;
                    $updateData['failedCount'] = 1;
                    $flag = $this->riskControl_model->updateData($updateData,array('user_id'=>$user_id));
                }
                else{
                    if($riskinfo['failedCount'] >= 5000){
                    $this->reply('达到异常扫码上限，请次日再试');
                    return;
                    }
                    else{
                        $sql = "update tbl_riskControl set failedCount=failedCount+1 where user_id={$user_id}";
                        $flag = $this->riskControl_model->execSql($sql);
                        if($flag === false){
                            $this->saveError($sid,$user_id,$sql,'累计奖金失败');
                        }
                    }
                }
            }
        }
        //记录兑奖记录
        if(!empty($temp)){
            $insertData = [];
            $insertData['sid'] = $sid;
            $insertData['user_id'] = $user_id;
            $insertData['stationId'] = $this->stationId;
            $insertData['province'] = $areaInfo[0]['name'];
            $insertData['city'] = $clubInfo['city'];
            $insertData['code'] = $code;
            $insertData['time'] = date('Y-m-d H:i:s');
            $content = $temp->content;
            !empty($temp->content) ? $insertData['response'] =  json_encode($temp->content) : '';
            !empty($temp->ret) ? $insertData['ret'] = $temp->ret : '';
            !empty($temp->msg) ? $insertData['msg'] = $temp->msg : '';
            !empty($temp->success) && $temp->success == true ? $insertData['success'] = 1 : $insertData['success'] = 0 ;
            $insertData['transacId'] = $transacId;
            !empty($content->ticketNo) ? $insertData['ticketNo'] = $content->ticketNo : '';
            !empty($content->gameName) ? $insertData['gameName'] = $content->gameName : '';
            !empty($content->price) ? $insertData['price'] = $content->price : '';
            !empty($content->level) ? $insertData['level'] = $content->level : '';
            !empty($content->prize) ? $insertData['prize'] = $content->prize : '';
            !empty($content->balance) ? $insertData['balance'] = $content->balance : '';
            !empty($content->awardStation) ? $insertData['awardStation'] = $content->awardStation : '';
            !empty($content->awardTime) ? $insertData['awardTime'] = $content->awardTime : '';
            !empty($content->soldCity) ? $insertData['soldCity'] = $content->soldCity : '';
            !empty($content->amountTicket) ? $insertData['amountTicket'] = $content->amountTicket : '';
            !empty($content->amountPrize) ? $insertData['amountPrize'] = $content->amountPrize : '';
            !empty($content->transacIdOrg) ? $insertData['transacIdOrg'] = $content->transacIdOrg : '';
            $flag = $this->redeem_model->insertData($insertData);
            if(!$flag){
                $this->saveError($sid,$user_id,$response,'插入数据库失败');
            }
        }
        else{
            $note = '兑奖失败，请联系管理员';
            $this->reply($note);
            $this->saveError($sid,$user_id,$response,$note);
            return;
        }
        $rs['code'] = 200;
        $rs['msg'] = '';
        $rs['data'] = $response;
        echo json_encode($rs);
        return;
    }
    public function statisticsCheck(){
        set_time_limit(120);
        ini_set('memory_limit', '256M');  //设置内存大小
        $this->load->model('redeem_model');
        $hour = $this->getParam('hour');
        $sendArr = array('1309895696@qq.com','liumeng@eeseetech.com','hanli@eeseetech.com');
        if(empty($hour)){
            $hour = 6;
        }
        $yesterday = strtotime('-1 day');
        $beginTime = $yesterday - 3600*($hour + 1);
        $endTime = $yesterday + 3600*($hour + 1);
        $mysqlBeginTime = $yesterday - 3600*($hour + 2);
        $mysqlEndTime = $yesterday + 3600*($hour + 2);
        $beginTime = date('Y-m-d H:i:s',$beginTime);
        $endTime = date('Y-m-d H:i:s',$endTime);
        $mysqlBeginTime = date('Y-m-d H:i:s',$mysqlBeginTime);
        $mysqlEndTime = date('Y-m-d H:i:s',$mysqlEndTime);
        //对比数据明细
        $detail = $this->statisticsDetail($beginTime,$endTime);
        $detail = json_decode($detail,true);
        $detail = $detail['content'];
        //取数据库 明细
        $sql ="SELECT transacId from tbl_redeem where ret=1301 and time>= '{$mysqlBeginTime}'  and time <'{$mysqlEndTime}'";
        $redeemInfo = $this->redeem_model->queryAll($sql);
        $redeemArr = [];
        foreach ($redeemInfo as $key => $value) {
            $redeemArr[] = $value['transacId'];
        }
        //对比数据
        $res = [];
        foreach ($detail as $key => $value) {
            if($key > 10){
                continue;
            }
            if(!in_array($value['transacId'],$redeemArr)){
                $res[] = $value;
            }
        }
        $data = [];
        $data['fileName'] = "兑奖明细对账_".date('Y_m_d').'.xlsx';
        $data['savePath'] = $this->savePath;
        $data['lineWidth'] = array('A'=>15);
        $data['headArr'] = array(
        'A1'=>'方案',
        'B1'=>'批次',
        'C1'=>'本号',
        'D1'=>'张号',
        'E1'=>'查询号(transacId)',
        'F1'=>'兑奖时间',
        'G1'=>'中奖等级',
        'H1'=>'中奖金额',
        );
        $data['bodyArr'] =  $res;
        $save_path = $this->export($data);
        $this->mailto("兑奖明细对账_",$save_path,$sendArr);
    }
    public function export($data){
        $save_path = $this->dump_excels($data);
        return $save_path;
    }
    public function ticketStatistics($beginTime = '',$endTime = '',$isEcho = 0){
        $rs =[];
        if(empty($beginTime) && empty($endTime)){
            $beginTime = $this->post_input('beginTime');
            if(empty($beginTime)){
                $beginTime = $this->get_input('beginTime');
            }
            $endTime = $this->post_input('endTime');
            if(empty($endTime)){
                $endTime = $this->get_input('endTime');
            }
            $isEcho = $this->post_input('isEcho');
            if(empty($isEcho)){
                $isEcho = $this->get_input('isEcho');
            }
        }
        $pattern = '/(\d{4}-\d{2}-\d{2}\d{2}:\d{2}:\d{2})|(\d{2}-\d{2}\d{2}:\d{2}:\d{2})|(\d{2}:\d{2}:\d{2})/';
            if(empty($beginTime) || empty($endTime)){
                $this->reply('缺少起止日期');
                return;
            }
        if(!preg_match($pattern, $beginTime) || !preg_match($pattern, $endTime)){
            $this->reply('请输入正确日期格式');
            return;
        }
        if(strtotime($endTime) > time()){
            $this->reply('结束时间需小于当前时间');
            return;
        }
        if(substr($endTime, 5,2) - substr($beginTime, 5,2) > 3){

            $this->reply('起止日期间隔需小于3个月');
            return;
        }
        //设置站点登录配置信息
        if(!empty($userinfo) && !empty($redeemConfig)){
            $this->setConfig(trim($userinfo['stationId']),trim($redeemConfig['station_login']),trim($redeemConfig['redeem_url']));
        }
        $param['app_key'] = self::APPID;
        $param['access_token'] = $this->getRedeemToekn();
        $param['timestamp'] = date('Y-m-d H:i:s');
        $param['format'] = 'json';
        $param['v'] = '1.0';
        $param_json['gameNo'] = '00000';
        $param_json['batchNo'] = '00000';
        $param_json['beginTime'] = $beginTime;
        $param_json['endTime'] = $endTime;
        $param['param_json'] = json_encode($param_json);
        $postData = $this->setSign($param);
        $postData['sign_method'] = 'MD5';
        $postData = urldecode(http_build_query($postData));
        $response = $this->post($this->ticketStatistics,$postData);
        $temp = json_decode($response,true);
        static $i = 0;
        if($temp['ret'] != 10000){
            $i ++;
            if($i >= 2){
                $this->reply('查询失败，请联系管理员');
                return;
            }
            $this->stationLogin();
            $res = $this->ticketStatistics();
            return $res;
        }
        if($isEcho){
            echo $response;
            return;
        }else{
            return $response;
        }
    }
    public function statisticsDetail($beginTime = '',$endTime = '',$isEcho = 0){
        $rs =[];
        if(empty($beginTime) && empty($endTime)){
            $beginTime = $this->post_input('beginTime');
            if(empty($beginTime)){
                $beginTime = $this->get_input('beginTime');
            }
            $endTime = $this->post_input('endTime');
            if(empty($endTime)){
                $endTime = $this->get_input('endTime');
            }
            $isEcho = $this->post_input('isEcho');
            if(empty($isEcho)){
                $isEcho = $this->get_input('isEcho');
            }
        }
        $pattern = '/(\d{4}-\d{2}-\d{2}\d{2}:\d{2}:\d{2})|(\d{2}-\d{2}\d{2}:\d{2}:\d{2})|(\d{2}:\d{2}:\d{2})/';
        if(empty($beginTime) || empty($endTime)){
            $this->reply('缺少起止日期');
            return;
        }
        if(!preg_match($pattern, $beginTime) || !preg_match($pattern, $endTime)){
            $this->reply('请输入正确日期格式');
            return;
        }
        if(strtotime($endTime) > time()){
            $this->reply('结束时间需小于当前时间');
            return;
        }
        if(strtotime($endTime) - strtotime($beginTime) > 3600*24){
            $this->reply('起止日期间隔需小于一天');
            return;
        }
        //设置站点登录配置信息
        if(!empty($userinfo) && !empty($redeemConfig)){
            $this->setConfig(trim($userinfo['stationId']),trim($redeemConfig['station_login']),trim($redeemConfig['redeem_url']));
        }
        $param['app_key'] = self::APPID;
        $param['access_token'] = $this->getRedeemToekn();
        $param['timestamp'] = date('Y-m-d H:i:s');
        $param['format'] = 'json';
        $param['v'] = '1.0';
        $param_json['gameNo'] = '00000';
        $param_json['batchNo'] = '00000';
        $param_json['beginTime'] = $beginTime;
        $param_json['endTime'] = $endTime;
        $param_json['isAllOperator'] = '1';
        $param_json['operatorNo'] = '1';
        $param['param_json'] = json_encode($param_json);
        $postData = $this->setSign($param);
        $postData['sign_method'] = 'MD5';
        $postData = urldecode(http_build_query($postData));
        $response = $this->post($this->statisticsDetail,$postData);
        if(empty($response)){
            $this->reply('站点无响应');
            $this->saveError('','',$response,'站点无响应');
        }
        $temp = json_decode($response,true);
        if(empty($temp)){
            $this->reply('返回数据格式错误');
            $this->saveError('','',$response,'返回数据格式错误');
        }
        static $i = 0;
        if($temp['ret'] != 10000){
            $i ++;
            if($i >= 2){
                $this->reply('查询失败，请联系管理员');
                return;
            }
            $this->stationLogin();
            $res = $this->statisticsDetail();
            return $res;
        }
        if(empty($isEcho)){
            return $response;
        }
        else{
            $sum = 0;
            foreach ($temp["content"]  as $key => $val)
            {
                $sum = $sum + $val['prize'];
            }
            echo sizeof($temp["content"]). " ". $sum. "<br>";
            echo $response;
        }
    }
    public function redeemList(){
        $rs= [];
        $this->load->model('redeem_model');
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        $pageIndex = $this->post_input('pageIndex');
        if(empty($pageIndex)){
            $pageIndex = $this->get_input('pageIndex');
        }
        $entryNum = $this->post_input('entryNum');
        if(empty($entryNum)){
            $entryNum = $this->get_input('entryNum');
        }
        if(empty($sid) || empty($pageIndex) || empty($entryNum)){
            $this->reply('缺少参数');
            return;
        }
        $where = array('sid'=>$sid,'ret'=>'1301');
        $sql = "select * from tbl_redeem where sid='{$sid}' and ret=1301 order by id desc limit ".(($pageIndex-1)*$entryNum).",{$entryNum}";
        $info = $this->redeem_model->queryAll($sql);
        $counSql = "select count(*) from tbl_redeem where sid='{$sid}' and ret=1301";

        $length = (int)$this->redeem_model->queryAll($counSql)[0]['count(*)'];
        $rs['code'] = 200;
        $rs['msg'] = '';
        if(empty($info)){
            $rs['data']['list'] = [];
        }
        else{
            $rs['data']['list'] = $info;
        }
        $rs['data']['length'] = $length;
        echo json_encode($rs);
        return;
    }
    public function moneyRemaining(){
        $rs =[];
        $this->load->model('session_model');
        $this->load->model('user_model');
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        if(empty($sid)){
            $this->reply('缺少参数');
            return;
        }
        $sessioninfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessioninfo)){
            $this->reply('没有找到sid信息');
            return;
        }
        $userinfo = $this->user_model->getInfoById($sessioninfo['user_id']);
        if(empty($userinfo)){
            $this->reply('没有找到用户信息');
            return;
        }
        $rs['code'] = 0;
        $rs['msg'] = '成功';
        empty($userinfo['prize']) ? $rs['moneyRemaining'] = 0 : $rs['moneyRemaining'] = $userinfo['prize'];
        echo json_encode($rs);
        return;
    }
    public function withdraw(){
        $rs = [];
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        $money = $this->post_input('money');
        if(empty($money)){
            $money = $this->get_input('money');
        }
        if(empty($sid) || empty($money) ){
            $this->reply('缺少参数');
            return;
        }
        $rs['code'] = 200;
        $rs['msg'] = '成功';
        $rs['data'] = [];
        echo json_encode($rs);
        return;
    }
    public function withdrawList(){
        $rs = [];
        $this->load->model('session_model');
        $this->load->model("receipt_model");
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        $pageIndex = $this->post_input('pageIndex');
        if(empty($pageIndex)){
            $pageIndex = $this->get_input('pageIndex');
        }
        $entryNum = $this->post_input('entryNum');
        if(empty($entryNum)){
            $entryNum = $this->get_input('entryNum');
        }
        if(empty($sid) || empty($pageIndex) || empty($entryNum)){
            $this->reply('缺少参数');
            return;
        }
        $sessioninfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessioninfo)){
            $this->reply('没有session信息');
            return;
        }
        $user_id = $sessioninfo['user_id'];
        
        $sql = " select * from tbl_receipt_order where user_id={$user_id} and status=1 order by id desc limit ".(($pageIndex-1)*$entryNum).",".$entryNum;
        $list = $this->receipt_model->queryAll($sql);
        if(empty($list)){
            $rs['data']['list'] = [];
            $rs['data']['length'] = 0;
        }
        else{
            $sql = "select count(*) from tbl_receipt_order where user_id={$user_id} and status=1";
            $length = $this->receipt_model->queryAll($sql)[0]['count(*)'];
            foreach ($list as $key => $value) {
                $list[$key]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
            }
            $rs['data']['list'] = $list;
            $rs['data']['length'] = (int)$length;
        }
        $rs['code'] = 200;
        $rs['msg'] = '成功';
        echo json_encode($rs);
        return;
    }
    public function saveError($sid='',$user_id='',$response='',$note=''){
        $this->load->model('redeemError_model');
        $insertData = [];
        $insertData['sid'] = $sid;
        $insertData['user_id'] = $user_id;
        $insertData['response'] = $response;
        $insertData['note'] = $note;
        $insertData['time'] = date('Y-m-d H:i:s');
        $flag = $this->redeemError_model->insertData($insertData);
        return;
    }
    public function setConfig($stationId = '',$station_login = '',$redeem_url = '',$ticketStatistics_url = '',$statisticsDetail_url = ''){
        !empty($stationId) ? $this->stationId = $stationId : '';
        !empty($station_login) ? $this->station = $station_login : '';
        !empty($redeem_url) ? $this->redeem = $redeem_url : '';
        !empty($ticketStatistics_url) ? $this->ticketStatistics = $ticketStatistics_url : '';
        !empty($statisticsDetail_url) ? $this->statisticsDetail = $statisticsDetail_url : '';
    }
    public function getRedeemInfo($sid,$transacId,$code){
        $rs =[];
        $param['app_key'] = self::APPID;
        $param['access_token'] = $this->getRedeemToekn();
        $param['timestamp'] = date('Y-m-d H:i:s');
        $param['format'] = 'json';
        $param['v'] = '1.0';
        $param_json['userId'] = $sid;
        $param_json['userRealIp'] = $_SERVER['REMOTE_ADDR'];
        empty($param_json['userRealIp']) ? $param_json['userRealIp'] = '192.168.168.202' : '';
        $param_json['transacId'] = $transacId;
        $param_json['type'] = 1701;
        $param_json['barCode'] = $code;
        $param['param_json'] = json_encode($param_json);
        $postData = $this->setSign($param);
        $postData['sign_method'] = 'MD5';
        $postData = urldecode(http_build_query($postData));
        $response = $this->post($this->redeem,$postData);
        return $response;
    }
    public function stationLogin(){
        $rs = [];
        $param = [];
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        if(empty($sid)){
            $this->reply('缺少参数');
            die;
        }
        $param['app_key'] = self::APPID;
        $param['timestamp'] = date('Y-m-d H:i:s');
        $param['format'] = 'json';
        $param['v'] = '1.0';
        $param_json['stationId'] = $this->stationId;
        $param_json['clerkId'] = '0';
        $param_json['password'] = '180130';
        $param_json['type'] = '70';
        $param_json['version'] = '1.0';
        $param_json['serialNo'] = 'BFEBFBFF000506E3';
        $param['param_json'] = json_encode($param_json);
        $postData = $this->setSign($param);
        $postData['sign_method'] = 'MD5';
        $postData = urldecode(http_build_query($postData));
        $response = $this->post($this->station,$postData);
        $record = $response;
        $response = json_decode($response);
        if($response->ret == 1101){
            //保存token
            $this->memcache->set('redeemToekn',$response->content->token,0,3600*24*20);
        }
        else{
            $note = '站点登录失败，请联系管理人员';
            $this->reply($note);
            //记录异常
            $this->saveError($sid,'',$record,$note);
            die;
        }
        return $response->content->token;
    }
    public function getRedeemToekn(){
        $rs = [];
        $sid = $this->post_input('sid');
        if(empty($sid)){
            $sid = $this->get_input('sid');
        }
        if(empty($sid)){
            $this->reply('缺少参数');
            die;
        }
        $redeemToekn =  $this->memcache->get('redeemToekn');
        if(empty($redeemToekn)){
            $redeemToekn = $this->stationLogin();
        }
        return $redeemToekn;
    }
    public function getSign($data){
        foreach ($data as $key => $value) {
            if(empty($value) || $key == 'sign'){
                unset($data[$key]);
            }
        }
        ksort($data);
        $data['key'] = self::APPSECRET;
        $re =  urldecode(http_build_query($data));
        $sign = md5($re);
        return $sign;
    }
    public function setSign($data){
        $rs = [];
        $sign = $this->getSign($data);
        $data['sign'] = $sign;
        $rs = $data;
        return $rs;
    }
    // public function post($url,$postData=''){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url); 
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_POST, 1); 
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
    //     $response = curl_exec($ch);
    //     curl_close($ch);
    //     return $response;
    // }
    public function post($url,$postData){
        $transpondUrl = 'http://gx.redeem.bjzwhz.cn/mobile/Dispatch/transpond';
        $post_data['url'] = $url;
        $post_data['postData'] = $postData;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $transpondUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    public function mailto($title, $file,$mail_arr)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();                                      // 设置邮件使用SMTP
        $mail->Host = 'smtp.mxhichina.com';                     // 邮件服务器地址
        $mail->SMTPAuth = true;                               // 启用SMTP身份验证
        $mail->CharSet = "UTF-8";                             // 设置邮件编码
        $mail->setLanguage('zh_cn');                          // 设置错误中文提示
        $mail->Username = 'eesee@eeseetech.com';              // SMTP 用户名，即个人的邮箱地址
        $mail->Password = 'A82i#39szy';                        // SMTP 密码，即个人的邮箱密码
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        //$mail->SMTPSecure = 'tls';                            // 设置启用加密，注意：必须打开 php_openssl 模块
        $mail->Priority = 3;                                  // 设置邮件优先级 1：高, 3：正常（默认）, 5：低
        $mail->From = 'eesee@eeseetech.com';                 // 发件人邮箱地址
        $mail->FromName = '上海意视信息科技有限公司';                     // 发件人名称
        
        
                // 添加接受者
             foreach($mail_arr as $one_mail){
                       $mail->addAddress($one_mail); 
                 }
        
        // $mail->addAddress('wangxinmei@eeseetech.com', 'wangxinmei');     // 添加接受者
        //$mail->addAddress('ellen@example.com');               // 添加多个接受者
        //$mail->addReplyTo('info@example.com', 'Information'); // 添加回复者
        //$mail->addCC('mail2@sina.com');                // 添加抄送人
        //$mail->addCC('mail3@qq.com');                     // 添加多个抄送人
        //$mail->ConfirmReadingTo = 'liruxing@wanzhao.com';     // 添加发送回执邮件地址，即当收件人打开邮件后，会询问是否发生回执
        //$mail->addBCC('mail4@qq.com');                    // 添加密送者，Mail Header不会显示密送者信息
        $mail->WordWrap = 50;                                 // 设置自动换行50个字符
    
        $mail->addAttachment($file,  
                   substr($file, strrpos($file, "/")+1, strlen($file)));      // "application/zip"
        $mail->isHTML(true);                                  // 设置邮件格式为HTML
        $mail->Subject = $title;
        $mail->Body    = $title;
        $mail->AltBody = $title;

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }

        echo 'Message has been sent';
    }
    public function dump_excels(){
        $fun_arr = func_get_args()[0];
        if(empty($fun_arr)) return;

       //引入PHPExcel对象
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        //创建PHPExcel对象
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties();
        $objActSheet = $objPHPExcel->getActiveSheet();
        if(!isset($fun_arr['headArr']) || !isset($fun_arr['bodyArr'])) return;

        if(array_key_exists('headArr',$fun_arr)){
            if(!is_array($fun_arr['headArr']))return;
            $span = ord("A");
            foreach ($fun_arr['headArr'] as  $item){
                $j = chr($span);
                $objActSheet->setCellvalue($j.'1',$item);
                if(isset($fun_arr['lineWidth']) && array_key_exists($j,$fun_arr['lineWidth'])){
                    $objActSheet->getColumnDimension($j)->setWidth($fun_arr['lineWidth'][$j]);
                }else{
                    $objActSheet->getColumnDimension($j)->setWidth('20');
                }
                $span++;
            }
        }
        if(array_key_exists('bodyArr',$fun_arr)){
            if(!is_array($fun_arr['bodyArr']))return;
            $i = 2;
            foreach ($fun_arr['bodyArr'] as $hineValue){
                $span = ord("A");
                // $objActSheet->getRowDimension($i)->setRowHeight(round(strlen($hineValue[4])/2));
                foreach ($hineValue as $lineValue){
                    $j = chr($span);
                    if(!empty($fun_arr['fontColor'])){

                        if(array_key_exists($j,$fun_arr['fontColor']) ){
                            $objRichText2 = new PHPExcel_RichText();
                            $objRichText2->createText("");
                            $objRed = $objRichText2->createTextRun($lineValue);
                            if(array_key_exists($lineValue,$fun_arr['fontColor'][$j])){
                                $objRed->getFont()->setColor( new PHPExcel_Style_Color( 'FF'.$fun_arr['fontColor'][$j][$lineValue]) );

                            }else{
                                $objRed->getFont()->setColor( new PHPExcel_Style_Color( 'FF'.$fun_arr['fontColor'][$j][$lineValue]) );
                            }
                            $objPHPExcel->getActiveSheet()->getCell($j .$i)->setValue($objRichText2);
                            $objPHPExcel->getActiveSheet()->getStyle($j .$i)->getAlignment()->setWrapText(true);
                        }else{
                            $objActSheet->setCellvalue($j.$i,$lineValue);
                        }
                    }else{
                        $objActSheet->setCellvalue($j.$i,$lineValue);
                    }
                    if($j == 'E'){
                        $objActSheet->getStyle('E'.$i)->getAlignment()->setWrapText(true);
                    }
                    $objActSheet->getStyle($j.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objActSheet->getStyle($j.$i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $span++;
                }
                $i++;
            }
        }

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); //清除缓冲区,避免乱码
        // if(array_key_exists('fileName',$fun_arr)){
        //     if(!is_string($fun_arr['fileName']))return;
        //     $fileName = iconv("utf-8", "gb2312", $fun_arr['fileName']);
        // }else{
        //     return;
        // }
         $fileName = $fun_arr['fileName'];
         $savePath = $fun_arr['savePath'];
       // $save_path = '/mnt/nas/www/log/xls/'.$fileName;
        $save_path = $savePath.$fileName;
         // $save_path = iconv("utf-8", "gb2312", $save_path);
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($save_path); 
        return $save_path;
    }
}
