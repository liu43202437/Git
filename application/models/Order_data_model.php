<?php

// order_data table model
class Order_data_model extends Base_Model
{

    // constructor
    public function __construct()
    {
        parent::__construct();


        $this->tbl = 'tbl_ticket_order_7';
    }
    public function getCountInfo($filter)
    {

        $info['order_num'] = $this->getCount($filter);
        return $info;
    }
    public function getOrderCounInfo($filter)
    {

        $info['add_order'] = $this->getCount($filter);
        $filter['order_status >'] = 0;
        $info['dump_order'] = $this->getCount($filter);
        $info['undump_order'] = $info['add_order'] - $info['dump_order'];
        $start_time = $filter['create_date >='] ;
        unset($filter['create_date >=']);
        $end_time = $filter['create_date <='] ;
        unset($filter['create_date <=']);
        $filter['update_date >='] = $start_time;
        $filter['update_date <='] = $end_time;
        $filter['pay_status'] = 1;
        $info['pay_order'] = $this->getCount($filter);
        return $info;
    }
}