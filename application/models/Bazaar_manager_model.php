<?php


class Bazaar_manager_model extends Base_Model
{
    protected $ticket_order;
    protected $ticket_num;
    function __construct()
    {
        $this->tbl = 'tbl_bazaar_manager';
        parent::__construct();
        $this->ticket_order = 'tbl_ticket_order';
        $this->order_num = 'tbl_order_num';
    }
    public function set_ticket_order($ticket_order){
        $this->ticket_order = $ticket_order;
    }
    public function set_order_num($order_num){
        $this->order_num = $order_num;
    }
    public function insertData($data = '')
    {
        try
        {
            $this->db->insert($this->tbl,$data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function getInfoByBazaarManagerUserId($userId)
    {
        $where = array('user_id' => $userId);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
    public function getInfoByBazaarManagerId($areamanagerid)
    {
        $where = array('bazaar_manager_id' => $areamanagerid);
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
    public function getCreditsByManager($user_id)
    {
        try
        {
            $sql = "SELECT a.id,a.trade_no,a.user_id,a.`name`,a.get_credits,a.manager_credits,a.update_date,a.manager_name,b.city,b.address FROM tbl_ticket_order as a ,tbl_club as b WHERE a.order_status=2 AND a.manager_id=b.manager_id AND a.manager_id={$user_id} order by a.id desc";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    public function getCreditsByManager2($user_id)
    {
        try
        {
            $sql = "SELECT a.id,a.trade_no,a.user_id,a.`name`,a.get_credits,a.manager_credits,a.update_date,b.city,b.address,c.`name` as manager_name,c.consumer_userid FROM tbl_ticket_order as a,tbl_club as b,tbl_consumer as c WHERE a.order_status=2 AND a.user_id=b.user_id AND b.manager_id=c.manager_id AND c.consumer_userid={$user_id} order by a.id desc";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    public function getShopByManager($user_id)
    {
        try
        {
            $sql = "SELECT a.`name`,a.area_id,a.city,a.address,a.user_id,a.phone,a.lottery_license FROM tbl_club as a, tbl_consumer as b WHERE a.manager_id=b.manager_id AND b.consumer_userid={$user_id}";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    public function getCredits($manager_id,$start,$end){
        $sql = "SELECT SUM(manager_credits) as credits_today FROM tbl_ticket_order WHERE order_status=2 AND manager_id=$manager_id AND update_date>='$start' AND update_date <= '$end'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function get_all_user_order_info($phone){
        $sql = "select b.name as area_name,d.trade_no,d.update_date,d.create_date,d.get_credits from  tbl_area_manager as a left join tbl_consumer as b 
                on a.phone = b.area_managerid and b.status = 0 left join tbl_club as c on b.phone = c.manager_id  left join ".($this->ticket_order)." as d on c.user_id = d.user_id  
               where d.pay_status = 1 and d.order_status = 2 and a.phone = {$phone} and a.status = 1";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function get_bazaar_manager_list($area_code)
    {
        $sql = "select phone,name from {$this->tbl} where status = 1 and area_code = '{$area_code}'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}