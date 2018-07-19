<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include('www/include/qrcode_create.php');
class Qcode extends CI_Controller {
    protected $file_path = '';
    function __construct()
    {
        parent::__construct();
        $this->view_path = 'admin/order/';
        $this->file_path = get_instance()->config->config['log_path_file'];
        $this->load->model('ticket_order_model');
        $this->load->model('email_model');
    }
    public static function encrypt($encrypt, $key)
    {
        $iv        = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt(MCRYPT_DES, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        //$encode = base64_encode($passcrypt);
        $encode = str_replace(array( '+', '/' ), array( '-', '_' ), base64_encode($passcrypt));

        return $encode;
    }
    public function distribute_task(){
        $command = $this->input->get("command");
        $province = $this->input->get("province");
        $this->load->model('area_model');
        $province_name = $this->area_model->getProvince($province);
        if(empty($province_name)){
            return false;
        }
        $str = 'ajax_dump'.$province;
        $this->$str($command,$province);


    }
    public function ajax_dump7($command,$province){

        if($command != 'go'){
            return;
        }
        $mail_info =  $this->email_model->get_need_email_by_province($province);

        if(!$mail_info){
           die('缺少收件人');
        }
        $mail_arr = [];
        foreach ($mail_info as $mail_value){
            $mail_arr[] = $mail_value['email'];
        }

        $this->load->model('excel_model');
        $filters = array();
        $orders = array();
        $H = date("H",time());
        if($H <= 8){
            $stime = date("Y-m-d",strtotime('-1 day'));
            $stime = $stime." 16:00:00";
            $etime = date("Y-m-d",time());
            $etime = $etime." 08:00:00";
        }else{
            $stime = date("Y-m-d",time());
            $stime = $stime." 08:00:00";
            $etime = date("Y-m-d",time());
            $etime = $etime." 23:00:00";
        }

        // $filters['create_date >='] = $stime;
        // $filters['create_date <'] = $etime;
        $filters['dump_status'] = 0;
        $filters['order_status '] = 0;

        $orders['user_id'] = 'DESC';
        $orders['create_date'] = 'DESC';

        $itemList = $this->ticket_order_model->get_all_notify_dump_order_by_province($province);

        $Lists = [];
        if(!empty($itemList)){
            $order_ids = '(';
            foreach ($itemList as $key=>$item) {
                $Lists[$key][0] = $item['trade_no'];
                $user_orders = $this->ticket_order_model->is_exists_order($item['user_id'],$item['create_date'],$province);
                if(!empty($user_orders)){
                    $mkkey = "DFefawev4&Kl8445*D";
                    $sid = $item['id'];
                    $sid = $this->encrypt($sid, '2026star');
                    $sign = md5($sid.$mkkey);

                    $url = base_url()."mobile/wechatpay/callwxpay?sign=".$sign."&sid=".$sid."&area_id=".$province;
                    $img_path = qr_code_create($url);
                    $Lists[$key][1] = $img_path;
                }else{
                    $Lists[$key][1] = '';
                }

                
               
                $Lists[$key][2] = $item['phone'];
                $this->load->model('user_model');
                $Lists[$key][3] = $item['name'];
                $Lists[$key][4] = $item['total_money'];
                $order_num = $this->ticket_order_model->getInfoByTradeno($item['trade_no'],$province);
                $description = '';
                foreach ($order_num as $value){
                    $description.= $value->title."| 数量".$value->ticket_num."包 | 单价 ".$value->count_price."元\n";
                }
                $Lists[$key][5] = $description;
                if($item['order_status'] == 0){
                    $Lists[$key][6] = '已下单';
                }elseif($item['order_status'] == 1){
                    $Lists[$key][6] = '已配送';
                }elseif($item['order_status'] == 2){
                    $Lists[$key][6] = '已支付';
                }else{
                    $Lists[$key][6] = '已取消';
                }

                $Lists[$key][7] = $item['area'].$item['city'].$item['address'];


                $Lists[$key][8] = $item['create_date'];
                $Lists[$key][9] = date("Y-m-d H:i:s",time());
               
                $order_ids.=$item['id'].',';
            }
            $order_ids = substr($order_ids,0,-1);
            $order_ids.=")";

            $dump_time = date("Y-m-d H:i:s",time());
            $res = $this->ticket_order_model->update_by_ids( $order_ids,$dump_time,$province);
        }



         
           
        date_default_timezone_set('Asia/Shanghai');
        //对数据进行检验
        if (empty($Lists) || !is_array($Lists)) {


        }
        $date = date("YmdHis", time());
        $fileName = "ticket_{$date}.xls";

        $this->load->model('qrcode_model');
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>40,'F'=>40,'H'=>40);
        $data['headArr'] = array(
            'A1'=>'订单号',
            'B1'=>'支付二维码',
            'C1'=>'手机号码',
            'D1'=>'姓名',
            'E1'=>'总金额',
            'F1'=>'订单详情描述',
            'G1'=>'状态',
            'H1'=>'寄件地址',
            'I1'=>'下单时间',
            'J1'=>'导出时间',
        );

        $data['bodyArr'] =  $Lists;
        $this->qrcode_model->dump_excels($data);



        $this->excel_model->mailto('中维公益卷票订单明细表_广西省'.date("Y-m-d H:i:s"),$this->file_path.$fileName,$mail_arr);




    }
    public function ajax_dump14($command,$province){
        if($command != 'go'){
            return;
        }
        $mail_info =  $this->email_model->get_need_email_by_province($province);

        if(!$mail_info){
            die('缺少收件人');
        }
        $mail_arr = [];
        foreach ($mail_info as $mail_value){
            $mail_arr[] = $mail_value['email'];
        }

        $this->load->model('excel_model');
        $filters = array();
        $orders = array();
        $H = date("H",time());
        if($H <= 8){
            $stime = date("Y-m-d",strtotime('-1 day'));
            $stime = $stime." 16:00:00";
            $etime = date("Y-m-d",time());
            $etime = $etime." 08:00:00";
        }else{
            $stime = date("Y-m-d",time());
            $stime = $stime." 08:00:00";
            $etime = date("Y-m-d",time());
            $etime = $etime." 23:00:00";
        }

        // $filters['create_date >='] = $stime;
        // $filters['create_date <'] = $etime;
        $filters['dump_status'] = 0;
        $filters['order_status '] = 0;

        $orders['user_id'] = 'DESC';
        $orders['create_date'] = 'DESC';

        $itemList = $this->ticket_order_model->get_all_notify_dump_order_by_province($province);

        $Lists = [];
        if(!empty($itemList)){
            $order_ids = '(';
            foreach ($itemList as $key=>$item) {
                $Lists[$key][0] = $item['trade_no'];
                $user_orders = $this->ticket_order_model->is_exists_order($item['user_id'],$item['create_date'],$province);
                if(!empty($user_orders)){
                    $mkkey = "DFefawev4&Kl8445*D";
                    $sid = $item['id'];
                    $sid = $this->encrypt($sid, '2026star');
                    $sign = md5($sid.$mkkey);

                    $url = base_url()."mobile/wechatpay/callwxpay?sign=".$sign."&sid=".$sid."&area_id=".$province;
                    $img_path = qr_code_create($url);
                    $Lists[$key][1] = $img_path;
                }else{
                    $Lists[$key][1] = '';
                }



                $Lists[$key][2] = $item['phone'];
                $this->load->model('user_model');
                $Lists[$key][3] = $item['name'];
                $Lists[$key][4] = $item['total_money'];
                $order_num = $this->ticket_order_model->getInfoByTradeno($item['trade_no'],$province);
                $description = '';
                foreach ($order_num as $value){
                    $description.= $value->title."| 数量".$value->ticket_num."包 | 单价 ".$value->count_price."元\n";
                }
                $Lists[$key][5] = $description;
                if($item['order_status'] == 0){
                    $Lists[$key][6] = '已下单';
                }elseif($item['order_status'] == 1){
                    $Lists[$key][6] = '已配送';
                }elseif($item['order_status'] == 2){
                    $Lists[$key][6] = '已支付';
                }else{
                    $Lists[$key][6] = '已取消';
                }

                $Lists[$key][7] = $item['area'].$item['city'].$item['address'];


                $Lists[$key][8] = $item['create_date'];
                $Lists[$key][9] = date("Y-m-d H:i:s",time());

                $order_ids.=$item['id'].',';
            }
            $order_ids = substr($order_ids,0,-1);
            $order_ids.=")";

            $dump_time = date("Y-m-d H:i:s",time());
            $res = $this->ticket_order_model->update_by_ids( $order_ids,$dump_time,$province);
        }





        date_default_timezone_set('Asia/Shanghai');
        //对数据进行检验
        if (empty($Lists) || !is_array($Lists)) {


        }
        $date = date("YmdHis", time());
        $fileName = "ticket_{$date}.xls";

        $this->load->model('qrcode_model');
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>40,'F'=>40,'H'=>40);
        $data['headArr'] = array(
            'A1'=>'订单号',
            'B1'=>'支付二维码',
            'C1'=>'手机号码',
            'D1'=>'姓名',
            'E1'=>'总金额',
            'F1'=>'订单详情描述',
            'G1'=>'状态',
            'H1'=>'寄件地址',
            'I1'=>'下单时间',
            'J1'=>'导出时间',
        );
        $data['bodyArr'] =  $Lists;
        $this->qrcode_model->dump_excels($data);

        $this->excel_model->mailto('中维公益卷票订单明细表_湖南省'.date("Y-m-d H:i:s"),$this->file_path.$fileName,$mail_arr);




    }
}
