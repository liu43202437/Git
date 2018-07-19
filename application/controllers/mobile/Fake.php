<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Fake extends Base_MobileController{
    public $result=array(
        'code'=>200,
        'data'=>'',
        'msg'=>'成功'
    );
    public function senddata(){
        if (!isset($_REQUEST['sid'])){
            $this->result['msg']='失败';
            $this->result['data']='未传入数据';
            echo json_encode($this->result);
        }else {
            $data['session_id'] = $_REQUEST['sid'];
            $this->load->model('test_model');
            $res = $this->test_model->getdata($data);
            $where['user_id'] = $res['user_id'];
            $result = $this->test_model->getdataone($where);
            $info = array();
            foreach ($result as $key => $item) {
                for ($i = 1; $i <= 6; $i++) {
                    if (!empty($item['attribute' . $i])) {
                        $list = explode('|', $item['attribute' . $i]);
                        $info[$key][$i] = $list[0];
                    }else{
                        $info[$key][$i]=null;
                    }
                }
            }
            $this->result['data']=$info;
            echo json_encode($this->result);
        }
    }
}