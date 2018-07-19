<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
class Common extends Base_AppController
{
    protected $filepath ='';
    protected $wx_base_url = '';
    function __construct()
    {
        parent::__construct();
    }
    // public function getBankChilds(){
    //     $this->load->model("Common_model");
    //     $rs = [];
    //     $city_id = $this->getParam('city_id');
    //     $sid = $this->getparam('sid');
    //     if(empty($city_id)){
    //         $this->reply('缺少参数');
    //         return;
    //     }
    //     $this->Common_model->setTable('tbl_banks');
    //     $filters = [];
    //     $filters['city_id'] = $city_id;
    //     $res = $this->Common_model->fetchAll($filters);
    //     $parent = [];
    //     $bankids = array(1026,1002,1003,1005,1020);
    //     foreach ($res as $key => $value) {
    //         if(!in_array($value['bank_id'], $bankids)){
    //             unset($res[$key]);
    //         }
    //     }
    //     $bankIdName = [];
    //     foreach ($res as $key => $value) {
    //         if(!in_array($value['bank_id'], array_keys($bankIdName))){
    //             $bankIdName[$value['bank_id']]['id'] = $value['bank_id'];
    //             $bankIdName[$value['bank_id']]['value'] = $value['bank_name'];
    //         }
    //     }
    //     $i = 0;
    //     foreach ($res as $key => $value) {
    //         $bankIdName[$value['bank_id']]['childs'][$i]['id'] = $value['sub_branch_id'];
    //         $bankIdName[$value['bank_id']]['childs'][$i]['value'] = $value['sub_branch_name'];
    //         $i++;
    //     }
    //     foreach ($bankIdName as $key => $value) {
    //         $rs[] = $value;
    //     }
    //     $this->success('成功',$bankIdName);
    // }

    // public function getBankChilds(){
    //     $rs =[];
    //     $city_id = $this->getParam('city_id');
    //     $sid = $this->getparam('sid');
    //     if(empty($city_id)){
    //         $this->reply('缺少参数');
    //         return;
    //     }
    //     $res = file_get_contents('/var/www/yan.eeseetech.cn/application/third_party/bank/bank-codes.json');
        
    //     $res = json_decode($res);
    //     $res = $res->$city_id;
    //     $bankids = array(1026,1002,1003,1005,1020);
    //     foreach ($res as $key => $value) {
    //         if(in_array($value->id, $bankids)){
    //             $rs[$key]['id'] = $value->id;
    //             $rs[$key]['value'] = $value->value;
    //             $rs[$key]['childs'] = $value->childs;
    //         }
    //     }
    //     $this->success('成功',$rs);
    // }
    public function getBankList(){
        $this->load->model("Common_model");
        $sid = $this->getParam('sid');
        // $user_id = $this->checkSid();

        $this->Common_model->setTable('tbl_banks');
        $bankids = array(1026,1002,1003,1005,1020,1066,1023);
        $res = array(
            array('id'=>'1026','value'=>'中国银行'),
            array('id'=>'1002','value'=>'中国工商银行'),
            array('id'=>'1003','value'=>'中国建设银行'),
            array('id'=>'1005','value'=>'中国农业银行'),
            array('id'=>'1020','value'=>'交通银行'),
            array('id'=>'1066','value'=>'中国邮政储蓄银行'),
            array('id'=>'1023','value'=>'农村合作信用社'),
            );
        $this->success('成功',$res);
    }
    public function getBankChilds(){
        $this->load->model("Common_model");
        $rs = [];
        $city_id = $this->getParam('city_id');
        $bank_id = $this->getParam('bank_id');
        $sid = $this->getparam('sid');
        if(empty($city_id) || empty($bank_id)){
            $this->reply('缺少参数');
            return;
        }
        $this->Common_model->setTable('tbl_banks');
        $filters = [];
        $filters['city_id'] = $city_id;
        $filters['bank_id'] = $bank_id;
        $res = $this->Common_model->fetchAll($filters);
        foreach ($res as $key => $value) {
            $rs[$key]['id'] = $value['sub_branch_id'];
            $rs[$key]['value'] = $value['sub_branch_name'];
        }
        $this->success('成功',$rs);
    }
}
