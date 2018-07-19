<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wechatpay extends Base_MobileController
{
    protected $filepath ='';
    protected $view = '';
    protected $data = [];
    function __construct()
    {
        parent::__construct();

        $this->load->model('ticket_order_model');
        $this->filepath = get_instance()->config->config['log_path_file'];
        $this->view = 'mobile/';
    }
    public function callWxpay(){
        $data = $this->decrypt($_REQUEST['sid'], '2026star');
        $sid = preg_replace('/[^[:print:]]/', '', $data);
        $sign = $this->get_input('sign');
        $area_id = $this->get_input('area_id');
        if($sid == '' || $sign == '' || $area_id == ''){

            $this->data['title'] = "非法请求";
            $this->load->view($this->view . 'error', $this->data);
            return;
        }

        $key = "DFefawev4&Kl8445*D";
        $check_sign = md5($_REQUEST['sid'].$key);
        if($sign != $check_sign){
            $this->data['title'] = "非法请求";
            $this->load->view($this->view . 'error', $this->data);
            return;

        }
        $this->data['area_id'] = $area_id;
        $order_info = $this->ticket_order_model->get_order_info_by_id($sid,$area_id);

        $fp = $this->filepath.'order_'.date('Y-m-d').'.log';
        if($order_info != null){
            $this->data['total_money'] = $order_info['total_money'];
            $order_info_detail = $this->ticket_order_model->getInfoByTradeno($order_info['trade_no'],$area_id);
            $title = '';
            foreach ($order_info_detail as $value){
                $title.= $value->title."*".$value->ticket_num.",&nbsp";
            }
            $this->data['title'] = $title;
            if(empty($order_info['relmoney'])){
                $relmoney =  (string) number_format($order_info['total_money'] * (1 - 0.07),2,'.','');
            }
            else{
                $relmoney = (string)number_format($order_info['relmoney'],2,'.','') ;
            }
            $rebate =  (string)number_format(($order_info['total_money'] - $relmoney),2,'.','');
            $this->data['pay_money'] = $relmoney;
            $this->data['rebata'] = $rebate;



        }else{
            $this->data['total_money'] = 0;
            $this->data['title'] = "错误订单";
            $this->data['pay_money'] = 0;
            $this->data['rebata'] = 0;
            $this->data['canPay'] = false;
            $this->data['remainTime'] = "00:00";
            $this->data['ordernum'] = '';
            $this->data['pay_status'] = 0;
            $this->load->view($this->view . 'pay', $this->data);
            return;
        }

        $this->load->model('payrequest_model');
        $pay_request = $this->payrequest_model->getPayrequestInfo($order_info['trade_no'],$area_id);
        if(empty($pay_request)){

            $pay_rs = $this->payrequest_model->insert($order_info['trade_no'],$relmoney*100,'weixin',$area_id);
            if(!$pay_rs){
                $contents = '{"ordernum"'.$order_info['trade_no'].',"money:"'.$relmoney.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;

                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                $this->data['total_money'] = 0;
                $this->data['title'] = "错误订单";
                $this->data['pay_money'] = 0;
                $this->data['rebata'] = 0;
                $this->data['canPay'] = false;
                $this->data['remainTime'] = "00:00";
                $this->data['ordernum'] = '';
                $this->data['pay_status'] = 0;
                $this->load->view($this->view . 'pay', $this->data);
                return;
            }
            $this->data['pay_status'] = 0;
            $this->data['canPay'] = true;
            $this->data['remainTime'] = "30:00";
            $this->data['ordernum'] = $order_info['trade_no'];
        }else{
            $contents = '{"ordernum"'.$order_info['trade_no'].',"money:"'.$relmoney.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;

            file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
            if($pay_request['p_status'] == 1){
                $this->data['title'] = "已支付过";
                $this->data['pay_status'] = $pay_request['p_status'];
                $this->data['canPay'] = false;
                $this->data['remainTime'] = "00:00";
                $this->data['ordernum'] = $order_info['trade_no'];
            }else{
                $this->data['canPay'] = true;
                $this->data['remainTime'] = "30:00";
                $this->data['pay_status'] = $pay_request['status'];
                $this->data['ordernum'] = $order_info['trade_no'];
            }
        }



        $this->load->view($this->view . 'pay', $this->data);


    }
    public function ajaxCanPay(){
        $ordernum = $this->post_input('ordernum');
        $area_id = $this->post_input('area_id');
        $this->load->model('payrequest_model');
        $pay_request = $this->payrequest_model->getPayrequestInfo($ordernum,$area_id);
        if(isset($pay_request['trade_no']) && $pay_request['trade_no'] != null ){
            $data['success'] = false;
            $data['message'] = '已经支付过';
        }else{
            $data['success'] = true;
        }
        parent::output($data);
    }

    public function wxPay(){
        $ordernum = $this->get_input('ordernum');
        $area_id = $this->get_input('area_id');

        $order_info = $this->ticket_order_model->get_order_info_by_tradeno($ordernum,$area_id);
        if(empty($order_info)){
            echo "异常订单！";
            exit();
        }
        if(empty($order_info['relmoney'])){
            $relmoney =  (string) number_format($order_info['total_money'] * (1 - 0.07),2,'.','');
        }
        else{
            $relmoney = (string)number_format($order_info['relmoney'],2,'.','') ;
        }
        $price = $relmoney*100;
        //$price = 1;

        $surl = base_url() . 'mobile/wechatpay/paysuccess?ordernum='.$ordernum.'&weixn='.$area_id;

        $furl = base_url() . 'mobile/wechatpay/repay?ordernum='.$ordernum.'&area_id='.$area_id;
        $nurl = base_url() . "www/wxpay/notify.php";
        $body = urlencode('北京中维商品');
        $url = base_url().'www/wxpay/jsapi.php?body='.$body.'&ordernum='.$ordernum.'&price='.$price.'&surl='.urlencode($surl).'&furl='.urlencode($furl).'&nurl='.urlencode($nurl);
        gopage($url);
    }
    public function repay(){
        $ordernum = $this->get_input('ordernum');
        $area_id = $this->get_input('area_id');

        $order_info = $this->ticket_order_model->get_order_info_by_tradeno($ordernum,$area_id);
        $fp = $this->filepath.'order_'.date('Y-m-d').'.log';
        $this->data['area_id'] = $area_id;
        if($order_info != null){
            $this->data['total_money'] = $order_info['total_money'];
            $order_info_detail = $this->ticket_order_model->getInfoByTradeno($order_info['trade_no'],$area_id);
            $title = '';
            foreach ($order_info_detail as $value){
                $title.= $value->title."*".$value->ticket_num.",&nbsp";
            }
            $this->data['title'] = "支付失败请，重新支付<br/>".$title;
            if(empty($order_info['relmoney'])){
                $relmoney =  (string) number_format($order_info['total_money'] * (1 - 0.07),2,'.','');
            }
            else{
                $relmoney = (string)number_format($order_info['relmoney'],2,'.','') ;
            }
            $rebate =  (string)number_format(($order_info['total_money'] - $relmoney),2,'.','');
            $this->data['pay_money'] = $relmoney;
            $this->data['rebata'] = $rebate;



        }else{
            $this->data['total_money'] = 0;
            $this->data['title'] = "错误订单";
            $this->data['pay_money'] = 0;
            $this->data['rebata'] = 0;
            $this->data['canPay'] = false;
            $this->data['remainTime'] = "00:00";
            $this->data['ordernum'] = '';
            $this->data['pay_status'] = 0;
            $this->load->view($this->view . 'pay', $this->data);
            return;
        }

        $this->load->model('payrequest_model');
        $pay_request = $this->payrequest_model->getPayrequestInfo($order_info['trade_no'],$area_id);
        if(empty($pay_request)){
            $pay_rs = $this->payrequest_model->insert($order_info['trade_no'],$relmoney*100,'weixin',$area_id);
            if(!$pay_rs){
                $contents = '{"ordernum"'.$order_info['trade_no'].',"money:"'.$relmoney.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;

                file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                $this->data['total_money'] = 0;
                $this->data['title'] = "错误订单";
                $this->data['pay_money'] = 0;
                $this->data['rebata'] = 0;
                $this->data['canPay'] = false;
                $this->data['remainTime'] = "00:00";
                $this->data['ordernum'] = '';
                $this->data['pay_status'] = 0;
                $this->load->view($this->view . 'pay', $this->data);
                return;
            }

        }else{

            $contents = '{"ordernum"'.$order_info['trade_no'].',"money:"'.$relmoney.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;

            file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
            if($pay_request['p_status'] == 1){
                $this->data['title'] = "已支付过";
                $this->data['pay_status'] = $pay_request['status'];
                $this->data['canPay'] = false;
                $this->data['remainTime'] = "00:00";
                $this->data['ordernum'] = $order_info['trade_no'];
            }else{
                $this->data['canPay'] = true;
                $this->data['pay_status'] = $pay_request['status'];
                $this->data['remainTime'] = "30:00";
                $this->data['ordernum'] = $order_info['trade_no'];
            }
        }




        $this->load->view($this->view . 'pay', $this->data);
    }
    public function paySuccess(){
        $ordernum = $this->get_input('ordernum');
        $area_id = $this->get_input('weixn');
        $this->load->model('payrequest_model');
        $fp = $this->filepath.'order_'.date('Y-m-d').'.log';
        $where = array(
            'ordernum'=>$ordernum
        );
        $data = array(
            'p_status'=>1
        );
        $order_rs = $this->payrequest_model->order_update($data,$where,$area_id);
        if($order_rs == 1 ){
            $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"同步回调更新成功"}'.PHP_EOL;
        }else{
            $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"同步回调更新失败"}'.PHP_EOL;

        }
        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);

        $p_where = array(
          'trade_no' => $ordernum
        );
        $p_data = array(
            'pay_status'=>1
        );
        $p_order_rs = $this->ticket_order_model->get_order_update($p_data,$p_where,$area_id);

        if($p_order_rs == 1 ){
            $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"同步回调更新成功","p_order":1}'.PHP_EOL;
        }else{
            $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"同步回调更新失败","p_order":0}'.PHP_EOL;

        }
        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
        $v_data['title'] = '你已经支付成功！';
        $this->load->view($this->view . 'paysuccess', $v_data);
        return;

    }
    public function wxNotify(){
        $ordernum = $this->get_input('ordernum');
        $trade_no = $this->get_input('trade_no');
        $amount = $this->get_input('amount');

        $area_num = substr($ordernum,-2);
        if($area_num < 10){
            $area_num = substr($area_num,-1);
        }
        $this->load->model('payrequest_model');
        $pay_request = $this->payrequest_model->getPayrequestInfo($ordernum,$area_num);
        $fp = $this->filepath.'order_'.date('Y-m-d').'.log';
        if (empty($pay_request)) {

            $this->payrequest_model->add_payrequest($ordernum, $trade_no, 'weixin',$amount,$area_num);
            $where = array(
                'ordernum'=>$ordernum
            );
            $data = array(
                'status'=>1
            );
            $order_rs = $this->payrequest_model->order_update($data,$where,$area_num);

            if($order_rs != 1){
                $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更新失败"}'.PHP_EOL;

            }else{
                $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更成功"}'.PHP_EOL;
            }
            file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
            //hanli
            $p_where = array(
                'trade_no' => $ordernum
            );
            $p_data = array(
                'pay_status'=>1
            );
            $p_order_rs = $this->ticket_order_model->get_order_update($p_data,$p_where,$area_num);

            if($p_order_rs == 1 ){
                $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更新成功","p_order":1}'.PHP_EOL;
            }else{
                $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更新失败","p_order":0}'.PHP_EOL;

            }
            file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);

            //

        }else {
            $where = array(
                'ordernum'=>$ordernum

            );
            $data = array(
                'trade_no'=>$trade_no,
                'status'=>1
            );
            $order_rs = $this->payrequest_model->order_update($data,$where,$area_num);
            if($order_rs != 1){
                $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更新失败"}'.PHP_EOL;
            }else{
                $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更成功"}'.PHP_EOL;
            }
        }
        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
        //hanli
        $p_where = array(
            'trade_no' => $ordernum
        );
        $p_data = array(
            'pay_status'=>1
        );
        $p_order_rs = $this->ticket_order_model->get_order_update($p_data,$p_where,$area_num);

        if($p_order_rs == 1 ){
            $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更新成功","p_order":1}'.PHP_EOL;
        }else{
            $contents = '{"ordernum"'.$ordernum.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":"异步回调更新失败","p_order":0}'.PHP_EOL;

        }
        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);

        //

    }


}
