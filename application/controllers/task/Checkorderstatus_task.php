<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
include('www/include/qrcode_create.php');
class Checkorderstatus_task extends CI_Controller
{
    protected $file_path = '';

    function __construct()
    {
        parent::__construct();


        $this->file_path = get_instance()->config->config['log_path_file'];
        $this->load->model('porder_model');

    }
    public static function encrypt($encrypt, $key)
    {
        $iv        = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt(MCRYPT_DES, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        //$encode = base64_encode($passcrypt);
        $encode = str_replace(array( '+', '/' ), array( '-', '_' ), base64_encode($passcrypt));

        return $encode;
    }
    public function dump_nopay_order(){
        $command = $this->input->get("command");
        if($command != 'pay_order'){
            return;
        }


        $this->load->model('excel_model');

        $H = date("H",time());

        $no_need_userid = '(201143,201039)';
        $itemList = $this->porder_model->get_all_need_dump_order(0,1,1,$no_need_userid);
        $Lists = [];
        if(!empty($itemList)){
            foreach ($itemList as $key=>$item) {
                $over_time = time()-strtotime($item['dump_time']);
                if($over_time < 24*3600 ){
                    continue;
                }

                $Lists[$key][0] = $item['trade_no'];
                //加密地址   生成二维码
                $mkkey = "DFefawev4&Kl8445*D";
                $sid = $item['id'];
                $sid = $this->encrypt($sid, '2026star');
                $sign = md5($sid.$mkkey);

                $url = base_url()."mobile/wechatpay/callwxpay?sign=".$sign."&sid=".$sid;
                $img_path = qr_code_create($url);
                $Lists[$key][1] = $img_path;
                $Lists[$key][2] = $item['phone'];
                $this->load->model('user_model');
                $Lists[$key][3] = $item['name'];
                $Lists[$key][4] = $item['total_money'];
                $order_num = $this->porder_model->getInfoByTradeno($item['trade_no']);
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
                $Lists[$key][10] = $this->Sec2Time($over_time);


            }



        }





        date_default_timezone_set('Asia/Shanghai');
        //对数据进行检验
        if (empty($Lists) || !is_array($Lists)) {


        }
        $date = date("YmdHis", time());
        $fileName = "ticket_nopay_{$date}.xls";

        $this->load->model('qrcode_model');
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>40,'F'=>40,'H'=>40,'K'=>40);
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
            'K1'=>'超时时间'
        );

        $data['bodyArr'] =  $Lists;
        $this->qrcode_model->dump_excels($data);

        //$mail_arr = ['hanli@eeseetech.com','liumeng@eeseetech.com','zhengchuchu@bjzwhz.cn','tianshou1230@163.com','168714940@qq.com','linguoliang@sf-express.com','771dk@sfmail.sf-express.com'];
        $mail_arr = ['hanli@eeseetech.com'];

        $this->excel_model->mailto('中维公益卷票订单超时明细表'.date("Y-m-d H:i:s"),$this->file_path.$fileName,$mail_arr);




    }
    function Sec2Time($time){
        if(is_numeric($time)){
            $value = array(
                "years" => 0, "days" => 0, "hours" => 0,
                "minutes" => 0, "seconds" => 0,
            );
            if($time >= 31556926){
                $value["years"] = floor($time/31556926);
                $time = ($time%31556926);
            }
            if($time >= 86400){
                $value["days"] = floor($time/86400);
                $time = ($time%86400);
            }
            if($time >= 3600){
                $value["hours"] = floor($time/3600);
                $time = ($time%3600);
            }
            if($time >= 60){
                $value["minutes"] = floor($time/60);
                $time = ($time%60);
            }
            $value["seconds"] = floor($time);
            //return (array) $value;
            $t=$value["years"] ."年". $value["days"] ."天"." ". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
            Return $t;

        }else{
            return (bool) FALSE;
        }
    }

}