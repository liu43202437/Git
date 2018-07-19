<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

    function __construct()
    {
        parent::__construct();
         
        $this->view_path = 'admin/order/';
        $this->load->model('porder_model');
    }
    public function ajax_dump(){
        $command = $this->input->get("command");
        if($command != true){
           return;
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
        $filters['order_status'] = 0;

        $orders['user_id'] = 'DESC';
        $orders['create_date'] = 'DESC';

        // $itemList = $this->porder_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize'],'tbl_ticket_order');
        $itemList = $this->porder_model->fetchAll($filters, $orders);
        $Lists = [];



        foreach ($itemList as $key=>$item) {
            $Lists[$key][0] = $item['trade_no'];
            $Lists[$key][1] = $item['phone'];
            $this->load->model('user_model');
            $Lists[$key][2] = $item['name'];
            $Lists[$key][3] = $item['total_money'];
            $order_num = $this->porder_model->getInfoByTradeno($item['trade_no']);
            $description = '';
            foreach ($order_num as $value){
                $description.= $value->title."| 数量".$value->ticket_num."包 | 单价 ".$value->count_price."元\n";
            }
            $Lists[$key][4] = $description;
            if($item['order_status'] == 0){
                $Lists[$key][5] = '已下单';
            }elseif($item['order_status'] == 1){
                $Lists[$key][5] = '已配送';
            }elseif($item['order_status'] == 2){
                $Lists[$key][5] = '已支付';
            }else{
                $Lists[$key][5] = '已取消';
            }

            $Lists[$key][6] = $item['area'].$item['city'].$item['address'];
            $Lists[$key][7] = $item['create_date'];

            $Lists[$key][8] = date("Y-m-d H:i:s",time());
            $datas['dump_status'] = 1;
            $datas['order_status'] = 1;
            $datas['dump_time'] =date("Y-m-d H:i:s",time());
            $this->porder_model->update($item['id'],$datas);

        }


        date_default_timezone_set('Asia/Shanghai');
        //对数据进行检验
        if (empty($Lists) || !is_array($Lists)) {
            

        }
        $date = date("Y_m_d", time());
        $fileName = "ticket_{$date}.xls";

        $this->load->model('excel_model');
        $data = [];
        $data['fileName'] = $fileName;
        $data['lineWidth'] = array('A'=>30,'E'=>40,'G'=>40);
        $data['headArr'] = array(
            'A1'=>'订单号',
            'B1'=>'手机号码',
            'C1'=>'姓名',
            'D1'=>'总金额',
            'E1'=>'订单详情描述',
            'F1'=>'状态',
            'G1'=>'寄件地址',
            'H1'=>'下单时间',
            'I1'=>'导出时间',
        );

        $data['bodyArr'] =  $Lists;
        $this->excel_model->dump_excels($data);

        $mail_arr = ['hanli@eeseetech.com','zhengchuchu@bjzwhz.cn','liumeng@eeseetech.com','jianglin@eeseetech.com','caoliu@eeseetech.com','madehuan@eeseetech.com','xieziyu@eeseetech.com'];

        $this->excel_model->mailto('中维公益卷票订单明细表'.date("Y-m-d"),'/mnt/nas/www/log/xls/'.$fileName,$mail_arr);

      

    }
}
