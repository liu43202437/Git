<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
class Base_AppController extends CI_Controller {

    protected $gets = array();
    protected $posts = array();
    protected $pagination = array();
    protected $user = array();

    protected static $error = array(
        1   => '手机号码或者密码错误',
        2   => '手机号码格式不正确',
        3  	=> '手机号码已使用',
        4  	=> '登录密码不能少于 6 个字符',
        5  	=> '没有登录权限',
        6	=> '账号锁着',
        7	=> '账号异常',
        8	=> '性别错误',
        9	=> '问题的答不正确',
        10	=> '账号不存在',
        11	=> '昵称已经被使用',
        12	=> '注册或登录失败',
        13  => '身份证已注册',
        14  => '烟草证书已经存在',
        15  => '您已经注册过店铺',
        16  => '您还未注册过店铺',
        17  => '您已经提交申请',
        18  => '该工作证号已经注册',
        19  => '您已经注册过',
        99  => '数据库操作失败',
        100 => '输入参数不正确',
        101 => '您的帐号信息不正确',
        102 => '您的帐号已过期',
        103 => '没注册的手机号码',
        104 => '验证码发送失败',
        105 => '输入的验证码不一致，请再确认一下',
        106 => '验证码已过期',
        107 => '一分钟内不能再发送验证码',
        109 => '您的操作频繁',
        108 => '新密码与旧密码相同',
        120 => '订单创建失败，请您再试一下。',
        121 => '您的烟币不够',
        199 => '不存在的信息',
        201 => '该门票已卖完了',
        300 => '上传文件格式不正确',
        301 => '图片文件太大了，无法上传',
        302 => '发生了上传错误',
        304 => '上传格式不正确',
        305 => '上传失败',
        306 => '上传失败',
        307 => '上传失败',
        501 => '聊天室加入失败',
        601 => '支付接口失败！',
        701 => '验证码错误！',
        801 => '验证码过期！',
        901 => '你的店铺正在审核中',
        902 => '异常网络',
        903 => '数据错误',
        904 => '签名错误',
        905=> '缺少时间戳',
        906=> '积分未为空',
        907=> '数据不完整',
        908=> '商家余额不足',
        909=>'提取金额必须为整元',
        808 => '请重新进入公众号',
        807 => '提现失败',
        806 => '余额不足',
        803 => '单日提现总额不能超过10000元',
        1001 => '库存不足，请重新选择',
        1002 => '更新库存失败',
        1003 => '您已经在APP注册过，请关闭此页面重新打开',
    );

    function __construct()
    {
        parent::__construct();

        // customize requests
        $this->gets = $this->input->get();
        $this->posts = $this->input->post();
        if (!empty($this->posts['json'])) {
            if (get_magic_quotes_gpc()) {
                $this->posts['json'] = stripslashes($this->posts['json']);
            }
            $this->posts = json_decode($this->posts['json'], true);
        }

        $this->auth_session();
    }

    public function get_input($key, $default = '')
    {
        if (empty($this->gets)) {
            return $default;
        }
        if (!isset($this->gets[$key])) {
            return $default;
        }
        return getValueByDefault($this->gets[$key], $default);
    }

    public function post_input($key, $default = '')
    {
        if (empty($this->posts)) {
            return $default;
        }
        if (!isset($this->posts[$key])) {
            return $default;
        }
        return getValueByDefault($this->posts[$key], $default);
    }

    public function auth_session()
    {
        $session = $this->post_input('session');
        if ($session != null) {
            if (!isset($session['uid']) || !isset($session['sid'])) {
                self::output(100);
            }
            $userId = $session['uid'];
            $sessionId = $session['sid'];

            $this->load->model('session_model');
            $session = $this->session_model->getInfoBySId($sessionId);
            if (empty($session)) {
                self::output(101);
            }

            if (strtotime($session['expire_date']) <= time()) {
                self::output(102);
            }

            $this->load->model('user_model');
            $this->user = $this->user_model->get($userId);
            if (empty($this->user)) {
                self::output(101);
            }
            if (!$this->user['is_enabled']) {
                self::output(7);
            }

            if ($session['user_id']!= $this->user['id']) {
                self::output(101);
            }

            unset($this->user['password']);
        }

        $this->pagination = $this->post_input('pagination', array('page' => 1, 'count' => PAGE_SIZE));
    }

    public static function output($data, $pager = NULL)
    {
        if (!is_array($data)) {
            $status = array(
                'status' => array(
                    'succeed' => 0,
                    'error_code' => $data,
                    'error_desc' => self::$error[$data]
                )
            );
            die(json_capsule($status));
        }
        if (isset($data['data'])) {
            $data = $data['data'];
        }
        $data = array_merge(array('data'=>$data), array('status' => array('succeed' => 1)));
        if (!empty($pager)) {
            $data = array_merge($data, array('paginated'=>$pager));
        }
        die(json_capsule($data));
    }
    public static function app_put($data, $pager = NULL)
    {
        if (!is_array($data)) {
            $data = array(
                'code'=>$data,
                'msg'=>self::$error[$data],
                'data'=>''
            );

        }else{
            $data = array(
                'code'=>0,
                'msg'=>'',
                'data'=>$data
            );
        }

        die(json_capsule($data));
    }

    public static function wechatAlert($msg,$url=''){
        if(empty($url)){
            echo "<script> alert('{$msg}'); </script>";
        } else {
            echo "<script> alert('{$msg}');window.location.href='$url'; </script>";
        }
        exit;
    }

    public static function encrypt($encrypt, $key)
    {
        $iv        = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt(MCRYPT_DES, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        //$encode = base64_encode($passcrypt);
        $encode = str_replace(array( '+', '/' ), array( '-', '_' ), base64_encode($passcrypt));

        return $encode;
    }

    public static function decrypt($decrypt, $key)
    {
        //$decoded = base64_decode($decrypt);
        $decoded   = base64_decode(str_replace(array( '-', '_' ), array( '+', '/' ), $decrypt));
        $iv        = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(MCRYPT_DES, $key, $decoded, MCRYPT_MODE_ECB, $iv);

        return $decrypted;
    }
    public function getParam($param){
        $rs = $this->get_input($param);
        if(empty($rs)){
            $rs = $this->post_input($param);
        }
        return $rs;
    }
    public function reply($msg){
        $rs =[];
        $rs['code'] = 1;
        $rs['msg'] = $msg;
        $rs['data'] = '';
        echo json_encode($rs);
        return;
    }
    public function countsuccess($msg,$data = '',$count){
        $rs =[];
        $rs['code'] = 0;
        $rs['msg'] = $msg;
        $rs['data']['length'] = $count;
        $rs['data']['list'] = $data;
        echo json_encode($rs);
        return;
    }

    public function success($msg,$data = ''){
        $rs =[];
        $rs['code'] = 0;
        $rs['msg'] = $msg;
        $rs['data'] = $data;
        echo json_encode($rs);
        return;
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
    public function ajaxSend($mobile,$modelId) {
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
    public function checkSid(){
        $this->load->model('session_model');
        $sid = $this->getParam('sid');
        if(empty($sid)){
            $this->reply('缺少参数sid');
            die;
        }
        $sessionInfo = $this->session_model->getInfoBySId($sid);
        if(empty($sessionInfo)){
            $this->reply('找不到sid');
            die;
        }
        if($sessionInfo['expire_date'] < date('Y-m-d H:i:s')){
            $rs= [];
            $rs['code'] = 2;
            $rs['msg'] = 'sid过期，请重新登录';
            $rs['data'] = '';
            echo json_encode($rs);
            die;
        }
        return $sessionInfo['user_id'];
    }
}
