<?php

// user table model
class Receipt_model extends Base_Model {

    // constructor
    public function __construct()
    {
        parent::__construct();


        $this->tbl = "tbl_receipt_order";
    }



    public function getOrderInfoById($Id)
    {
        $where = array('id' => $Id);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    public function updateDeductCredits($uid,$credits){
        if(!isset($uid)||!isset($credits))
            return false;
        $where = array(
            'id' => $uid
        );
        try{
            $this->db->set('point',"point-$credits",false);
            $this->db->where($where);
            $this->db->update($this->tbl);
        }catch (Exception $e){
            return false;
        }
        return true;
    }
    public function is_exist_tradeno($trade_no) {
        if ($trade_no == null || $trade_no == '') return 0;
        $where = array(
            'partner_trade_no' => $trade_no
        );
        $query = $this->db->get_where($this->tbl, $where);
        $order = $query->result_array();
        if ($order == null)
            return 0;
        return $order->id;
    }

    // get user info by nickname
    public function getInfoByNickname($nickname)
    {
        $where = array('nickname' => $nickname);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    // get user info by weixin
    public function getInfoByWeixin($weixin)
    {
        $where = array('weixin' => $weixin);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    // add new user - signup
    public function order_insert($data)
    {
        return $this->_insert($data);
    }



    // delete user
    public function deleteByName($username)
    {
        $where = array('username' => $username);
        return $this->db->delete($this->tbl, $where);
    }


    // statistics function
    public function getCountInfo($filter)
    {
        $filter['device_type'] = DEVICE_TYPE_IPHONE;
        $info['iphone'] = $this->getCount($filter);

        $filter['device_type'] = DEVICE_TYPE_ANDROID;
        $info['android'] = $this->getCount($filter);

        unset($filter['device_type']);
        $filter['weixin !='] = null;
        $info['weixin'] = $this->getCount($filter);

        $info['total'] = $info['iphone'] + $info['android'];

        return $info;
    }
    public function get_order_update($data,$where){

        foreach ($where as $key=>$value){
            $this->db->where($key, $value);
        }

        $this->db->update($this->tbl, $data);
        return $this->db->affected_rows();


    }
    public function deleteData($where){
        try
        {
            $this->db->delete($this->tbl, $where);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function fetchAll($where = array()){
        try
        {
            if(!empty($where)){
                $query = $this->db->get_where($this->tbl,$where);
            }
            else{
                $query = $this->db->get($this->tbl);
            }
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    public function get_today_sum_money($userId = '',$start_day = '',$end_day = ''){
        if($userId == '' || $start_day == '' || $end_day == '')
            return null;
        $sql = "select  ifnull(sum(amount),0) as summoney from tbl_receipt_order where user_id = $userId and add_time >= {$start_day} and add_time <= {$end_day} and status = 1";
        $rs = $this->queryAll($sql);

        if(empty($rs)){
            return 0;
        }else{
            return $rs[0]['summoney'];
        }
    }
    public function updateData($data, $where)
    {
        try
        {
            $this->db->where($where)->update($this->tbl, $data);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function queryAll($sql)
    {
        try
        {
            $query = $this->db->query($sql);
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    public function execSql($sql)
    {
        try
        {
            $query = $this->db->query($sql);
            return $query;
        }
        catch (Exception $e)
        {
            return null;
        }
    }
}
?>