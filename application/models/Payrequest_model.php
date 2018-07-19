<?php

class Payrequest_model extends Base_Model {



    // constructor
    public function __construct()
    {
        parent::__construct();

        $this->tbl = 'payrequest';

    }

    // get info by ordernum
    public function getPayrequestInfo($ordernum,$num = 0)
    {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_payrequest_'.$num;
        }
        $where = array('ordernum' => $ordernum);

        $query = $this->db->get_where($tbl, $where);
        return $query->row_array();
    }
    //get info by id
    public function getTicketInfoById($id)
    {
        $where = array('id' => $id);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->result_array();
    }
    // add pay order
    public function insert($ordernum, $money,$pay_type,$num = 0)
    {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_payrequest_'.$num;
        }
        $data = array(
            'ordernum' => $ordernum,
            'pay_amount' => $money,
            'pay_type' => $pay_type,
            'status' => 0,
        );
        return $this->_insert($data,$tbl);
    }
    // add pay order
    public function add_payrequest($ordernum, $trade_no,$pay_type,$amount,$num)
    {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_payrequest_'.$num;
        }
        $data = array(
            'ordernum' => $ordernum,
            'trade_no' =>$trade_no,
            'pay_amount' => $amount,
            'pay_type' => $pay_type,
            'status' => 0,
        );
        return $this->_insert($data,$tbl);
    }
    // update info
    public function update($id, $data, $table = null)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::update($id, $data);
    }
    public function order_update($data,$where,$num = 0){
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_payrequest_'.$num;
        }

        foreach ($where as $key=>$value){
            $this->db->where($key, $value);
        }

        $this->db->update($tbl, $data);
        return $this->db->affected_rows();


    }
    // delete by name
    public function deleteByName($username)
    {
        $where = array(
            'username' => $username
        );
        return $this->db->delete($this->tbl, $where);
    }
    function execSQL($sql)
    {
        try
        {
            $query = $this->db->query($sql);
            return $query->result();
        }
        catch (Exception $e)
        {
            return null;
        }
    }



}
?>