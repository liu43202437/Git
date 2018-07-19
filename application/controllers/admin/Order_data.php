<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_data extends Base_AdminController
{

    function __construct()
    {
        parent::__construct();

        $this->view_path = '';
        $this->load->model('Order_data_model');
    }

    public function order_add_info()
    {
        $startDate = $this->post_input('start_date', day_before(30));
        $endDate = $this->post_input('end_date', nowdate());



        $reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
            $filters['create_date >='] = d2bt($day);
            $filters['create_date <='] = d2et($day);
            $item = $this->Order_data_model->getOrderCounInfo($filters);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;
        $filters['create_date >='] = d2bt(day_before(1));
        $filters['create_date <='] = d2et(day_before(1));
        $data['yesterday'] = $this->Order_data_model->getOrderCounInfo($filters);

        $filters['create_date >='] = stime_type('week');
        $filters['create_date <='] = date("Y-m-d H:i:s",time());
        $data['week'] = $this->Order_data_model->getOrderCounInfo($filters);

        $filters['create_date >='] = stime_type('month');
        $filters['create_date <='] = date("Y-m-d H:i:s",time());
        $data['month'] = $this->Order_data_model->getOrderCounInfo($filters);

        $data['labels'] = array('增加订单','导出订单','未导出订单','支付订单');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
}