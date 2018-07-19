<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
class Sendmsg_task extends CI_Controller
{
    protected $file_path = '';

    function __construct()
    {
        parent::__construct();


        $this->file_path = get_instance()->config->config['log_path_file'];

    }

    public function consumer_sendmsg_task()
    {
        $command = $this->input->get("command");
        if($command != 'consumer'){
            return;
        }
        $this->load->model('consumer_model');
        $all_consumers = $this->consumer_model->listAll(array('status=>1'));
        if(!empty($all_consumers)){
            $this->load->model("club_model");
            try{
                foreach ($all_consumers as $one){
                    $noaudit_num = $this->club_model->get_club_noaudit_num($one['phone']);
                    if($noaudit_num['num'] != 0){
                        $re_code = $this->zwsendsms($one['phone'],array($noaudit_num['num']),'236011');
                        $fp =  $this->file_path."sendmsgtip_".date("Y-m",time()).".log";
                        if($re_code['code'] == 0){
                            $contents = '{"phone:"'.$one['phone'].',"code:"'.$re_code['code'].',"date_time":"'.date("Y-m-d H:i:s",time()).'}'.PHP_EOL;
                        }else{
                            $contents = '{"phone:"'.$one['phone'].',"code:"'.$re_code['code'].',"date_time":"'.date("Y-m-d H:i:s",time()).'}'.PHP_EOL;
                        }
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }
                }
            }catch (Exception $e){
                $fp =  $this->file_path."sendmsgtip_error".date("Y-m",time()).".log";
                $contents = 'consumer'.$e->getMessage();
                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
            }

        }
    }
    public function area_bazaar_sendmsg_task()
    {
        $command = $this->input->get("command");
        if($command != 'area_bazaar'){
            return;
        }
        $this->load->model('area_manager_model');
        $this->load->model('bazaar_manager_model');
        //发送市场经理
        $all_area_managers = $this->area_manager_model->fetchAll('tbl_area_manager',array('status=>1'));
        if(!empty($all_area_managers)){
            $this->load->model("consumer_model");
            try{
                foreach ($all_area_managers as $one){
                    $noaudit_num = $this->consumer_model->get_consumer_noaudit_num($one['phone']);
                    if($noaudit_num['num'] != 0){
                        $re_code = $this->zwsendsms($one['phone'],array($noaudit_num['num']),'236013');
                        $fp =  $this->file_path."sendmsgtip_".date("Y-m",time()).".log";
                        if($re_code['code'] == 0){
                            $contents = '{"phone:"'.$one['phone'].',"code:"'.$re_code['code'].',"date_time":"'.date("Y-m-d H:i:s",time()).'}'.PHP_EOL;
                        }else{
                            $contents = '{"phone:"'.$one['phone'].',"code:"'.$re_code['code'].',"date_time":"'.date("Y-m-d H:i:s",time()).'}'.PHP_EOL;
                        }
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }
                }
            }catch (Exception $e){
                $fp =  $this->file_path."sendmsgtip_error".date("Y-m",time()).".log";
                $contents = 'area_manager'.$e->getMessage();
                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
            }
        }
        //发送区域经理
        $all_bazaar_managers = $this->bazaar_manager_model->fetchAll('tbl_bazaar_manager',array('status=>1'));
        if(!empty($all_bazaar_managers)){
            try{
                foreach ($all_area_managers as $ones){
                    $noaudit_num = $this->area_manager_model->get_area_noaudit_num($ones['phone']);
                    if($noaudit_num['num'] != 0){
                        $re_code = $this->zwsendsms($ones['phone'],array($noaudit_num['num']),'236014');
                        $fp =  $this->file_path."sendmsgtip_".date("Y-m",time()).".log";
                        if($re_code['code'] == 0){
                            $contents = '{"phone:"'.$ones['phone'].',"code:"'.$re_code['code'].',"date_time":"'.date("Y-m-d H:i:s",time()).'}'.PHP_EOL;
                        }else{
                            $contents = '{"phone:"'.$ones['phone'].',"code:"'.$re_code['code'].',"date_time":"'.date("Y-m-d H:i:s",time()).'}'.PHP_EOL;
                        }
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }

                }
            }catch (Exception $e){
                $fp =  $this->file_path."sendmsgtip_error".date("Y-m",time()).".log";
                $contents = 'bazaar_manager'.$e->getMessage();
                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
            }
        }

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
}