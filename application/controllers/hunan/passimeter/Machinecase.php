<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
class Machinecase extends Base_MobileController
{
    private $memcache = '';
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
        $this->filepath = get_instance()->config->config['log_path_file'];
        $mem_ip = get_instance()->config->config['memcache_ip'];
        $mem_port = get_instance()->config->config['memcache_part'];
        $this->memcache = new Memcache;
        $this->memcache->connect($mem_ip, $mem_port);

    }
    public static function machice_put($code = 0, $msg = NULL,$data = [])
    {
            $re_data = array(
                'code'=>$code,
                'msg'=>$msg,
                'data'=>$data
            );
        die(json_capsule($re_data));
    }
    public function machine_ping()
    {
        $fp =  $this->filepath."machine_ping_".date("Y-m-d",time()).".log";

        $data = [];
        $this->load->model('machinecase_model');
        $data = (array)json_decode($this->post_input('data'));
        if(empty($data)){
            $data = (array)json_decode($this->get_input('data'));
        }
       /* $contents = '{"data":'.json_encode($data).'}'.PHP_EOL;
        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);*/
        $data['update_date'] = time();

        if(!isset($data['machine_id']) || $data['machine_id'] == ''){
            $this::machice_put(1,'缺少machine_id信息',array());
        }else{
            $machine_info = $this->machinecase_model->get_info_by_machine_id($data['machine_id']);
            if(empty($machine_info)){
                $this::machice_put(4,'机器编号不正确',array());
            }
        }
        $machine_id = $data['machine_id'];
        $mem_data =  $this->memcache->get($machine_id);
        if(empty($mem_data)){
            $this->memcache->set($machine_id,$data);
        }else{
            $limit_time = time()-$data['update_date'];
            if($mem_data['status'] !== 0 || $data['header_status'] !== 0 || $data['net_status'] !== 0 || $limit_time >= 60*5){
                $this->machinecase_model->log_insert($data);
            }
        }


        $must_need = ['status','header_status','net_status'];
        foreach ($must_need as $value){
            if(!isset($data[$value]) || $data[$value] === ''){
               $this::machice_put(1,'缺少'.$value.'信息',array());
            }
        }
        $this->memcache->set($machine_id,$data);
        unset($data['machine_id']);

        //$this->memcache->set("hanli",$data);
        if($data['status'] === 0 && $data['header_status'] === 0 && $data['net_status'] === 0 && $machine_info['header_status'] == 2){
            $data['locked'] = 0;
            $data['abnormity'] =0;
            //如果 机器机器不处于出票状态
            $this->load->model('Common_model');
            $this->Common_model->setTable('tbl_hunan_order');
            $filter = [];
            $filter['machine_id'] = $machine_info['id'];
            $orders = [];
            $orders['id'] = 'desc';
            $orderInfo = $this->Common_model->fetchOne($filter,$orders);
            if(!empty($orderInfo) && $orderInfo['pay_status'] == 1 && $orderInfo['ticket_status'] == 0){
                unset($data['locked']);
            }
        }
        //如果机器 下单失败
        if($data['status'] === 0 && $data['header_status'] === 0 && $data['net_status'] === 0 && $machine_info['abnormity'] != 0){
            $data['abnormity'] =0;
        }
        $this->load->model('machinecase_model');
        $rs = $this->machinecase_model->updateData($data, array('machine_id'=>$machine_id));
        if($rs){
            $this::machice_put(0,'成功',array());
        }else{
            $this::machice_put(7,'更新数据库失败',array());
        }


    }
}
