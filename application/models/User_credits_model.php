<?php


class User_credits_model extends Base_Model
{

    function __construct()
    {
        $this->tbl = 'tbl_user_credits';
        parent::__construct();
    }

    public function insertData($data = '')
    {
        try {
            $this->db->insert($this->tbl, $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getInfoByBazaarManagerUserId($userId)
    {
        $where = array('user_id' => $userId);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
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
    public function updateData($data, $where)
    {
        try
        {
            $this->db->where($where)->update($this->tbl,$data);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function get_credits_lists($user_id,$status = 1,$page = null,$size = 20,$order = null){
        $sql = "select*from {$this->tbl}";
        $where = " where user_id = {$user_id} and status = {$status} ";
        if($page == null){
            $limit = " limit 0,{$size}";
        }else{
            $limit = " limit ".(($page-1)*$size).",{$size}";
        }

        if($order == null){
            $orderby = " order by add_time desc";
        }else{
            $orderby = " order by add_time asc";
        }
        $sql = $sql.$where.$orderby.$limit;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return null;
        }
        return $result;
    }
    public function get_credits_count($user_id,$status = 1){
        $sql = "select*from {$this->tbl}";
        $where = " where user_id = {$user_id} and status = {$status} ";

        $sql=$sql.$where;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return 0;
        }
        return count($result);
    }
    public function get_today_credits($user_id,$start,$end,$status = 1){
        $sql = "select sum(credits) as sum_credits from {$this->tbl} where user_id = {$user_id} and add_time >= {$start} and add_time <= {$end} and status = {$status}";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        if(empty($result)){
            return 0;
        }
        return $result['sum_credits'];
    }
    public function fetchAll($tbl,$where = array()){
        try
        {
            if(!empty($where)){
                $query = $this->db->get_where($tbl,$where);
            }
            else{
                $query = $this->db->get($tbl);
            }
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    public function fetchOne($where = array()){
        try
        {
            if(!empty($where)){
                $query = $this->db->get_where($this->tbl,$where);
            }
            else{
                $query = $this->db->get($this->tbl);
            }
            return $query->row_array();
        }
        catch (Exception $e)
        {
            return null;
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
    public function get_all_task_orders($start,$end,$type){
        $sql = "select*from {$this->tbl} where add_time >= {$start} and add_time <= {$end} and type = {$type}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return array();
        }
        return $result;
    }

}