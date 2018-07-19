<?php

class Porder_model extends Base_Model {


    protected $ticket_order;
    protected $ticket_num;
    // constructor
    public function __construct()
    {
        parent::__construct();

        $this->tbl = 'ticket';
        $this->ticket_order = 'ticket_order';
        $this->order_num = 'order_num';

    }



    // add administrator
    public function insert_num($data)
    {

        $id = $this->_insert($data, $this->order_num);
        return $id;
    }
    public function getInfoByTradeno($trade_no)
    {
        $sql = "select a.ticket_id,a.ticket_num,b.title,b.count_price from tbl_order_num as a left join tbl_ticket as b
               on a.ticket_id = b.id  where a.trade_no = {$trade_no}";
        $result = $this->execSQL($sql);
        if(empty($result)){
            return [];
        }
        return $result;
    }
    public function insert_order($data)
    {

        $id = $this->_insert($data, $this->ticket_order);
        return $id;
    }

    // update info
    public function update($id, $data, $table = null)
    {

        return parent::update($id, $data,$this->ticket_order);
    }

    public function get_order_update($data,$where){

        foreach ($where as $key=>$value){
            $this->db->where($key, $value);
        }

        $this->db->update($this->ticket_order, $data);
        return $this->db->affected_rows();


    }
    public function getManagerCredits($manager_id){
        $sql = "select sum(manager_credits) as credits from tbl_ticket_order where manager_id = '{$manager_id}' and order_status = 2";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return null;
        }
        return $result;
    }
    public function getAreaManagerCredits($managerlist){
        $sql = "select sum(area_manager_credits) as credits from tbl_ticket_order where manager_id in {$managerlist} and order_status = 2";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return null;
        }
        return $result;
    }
    // delete by name
    public function deleteByName($username)
    {
        $where = array(
            'username' => $username
        );
        return $this->db->delete($this->tbl, $where);
    }

    public function get_order_info($id){
        $sql = "select a.get_credits,a.manager_credits,a.user_id,b.consumer_userid,a.area_manager_credits,c.user_id as area_userid from tbl_ticket_order as a left join tbl_consumer as b
               on a.manager_id = b.manager_id left join tbl_area_manager as c on a.area_manager_id = c.area_manager_id  where a.id ={$id}";
        $result = $this->execSQL($sql);
        if(empty($result)){
            return null;
        }
        return $result;
    }
    public function get_credits_list($useid,$managerid,$area_manager_id,$bazaar_id,$page,$order = null){
        $sql = "select trade_no,user_id,update_date,get_credits,manager_credits,manager_name,area_manager_id,manager_id,area_manager_credits,name,city,address,area,bazaar_id,bazaar_credits,bazaar_name from tbl_ticket_order ";
        $where = " where order_status = 2 and (user_id = {$useid}";
        if($managerid != null){
            $where.= " or manager_id = '{$managerid}'";
        }
        if($area_manager_id != null){
            $where.= " or area_manager_id = '{$area_manager_id}'";
        }
        if($bazaar_id != null){
            $where.= " or bazaar_id = {$bazaar_id}";
        }
        $where.= ")";
        if($page == null){
            $limit = " limit 0,20";
        }else{
            $limit = " limit ".(($page-1)*20).",20";
        }

        if($order == null){
            $orderby = " order by update_date desc";
        }else{
            $orderby = " order by update_date asc";
        }
        $sql=$sql.$where.$orderby.$limit;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return null;
        }
        return $result;

    }
    public function get_credits_count($useid,$managerid,$area_manager_id,$bazaar_id){
        $sql = "select trade_no,user_id,update_date,get_credits,manager_credits,area_manager_id,manager_id,name,city,address,area from tbl_ticket_order ";
        $where = " where order_status = 2 and (user_id = {$useid}";
        if($managerid != null){
            $where.= " or manager_id = '{$managerid}'";
        }
        if($area_manager_id != null){
            $where.= " or area_manager_id = '{$area_manager_id}'";
        }
        if($bazaar_id != null){
            $where.= " or bazaar_id = {$bazaar_id}";
        }
        $where.= ")";

        $sql=$sql.$where;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return 0;
        }
        return count($result);
    }

    public function get_order_list($where,$data,$status) {

        if($status == 'wait' || $status == 'payed'){
            $query = $this->db->limit($data['entryNum'],($data['pageIndex']-1)*$data['entryNum'])->order_by('create_date desc')->get_where($this->ticket_order, $where);
        }
        else{
            $query = $this->db->limit($data['entryNum'],($data['pageIndex']-1)*$data['entryNum'])->order_by('update_date desc')->get_where($this->ticket_order, $where);
        }
        $order = $query->result_array();
        if ($order == null)
            return 0;
        return $order;
    }
    public function get_order_list_num($where) {
        $this->db->where($where);
        $num = $this->db->count_all_results('tbl_ticket_order');
        return $num;
    }

    public function is_exist_tradeno($trade_no) {
        if ($trade_no == null || $trade_no == '') return 0;
        global $TABLE;
        $where = array(
            'trade_no' => $trade_no
        );
        $query = $this->db->get_where($this->ticket_order, $where);
        $order = $query->result_array();
        if ($order == null)
            return 0;
        return $order->id;
    }
    public function getTicketInfo($province_id = ''){
        if(empty($province_id)){
            $sql = "select a.ticket_id,b.price,b.count_price,b.title,b.description,b.id,sum(a.ticket_num) as num,b.inventory from tbl_order_num as a left join tbl_ticket as b
               on a.ticket_id = b.id where b.status=0  group by a.ticket_id order by num desc ";
        }
        else{
            $sql = "select a.ticket_id,b.price,b.count_price,b.title,b.description,b.id,sum(a.ticket_num) as num,b.inventory from tbl_order_num as a inner join tbl_ticket as b
               on a.ticket_id = b.id where b.province_id={$province_id} and b.status=0  group by a.ticket_id order by num desc";
        }
        $result = $this->execSQL($sql);
        if(empty($result)){
            return null;
        }
        return $result;

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
    public function fetchAll($where = array(),$order){
        try
        {
            if(!empty($where)){
                if(!empty($order)){
                    foreach ($order as $key => $value) {
                        $this->db->order_by($key, $value);
                    }
                }
                $query = $this->db->get_where('tbl_ticket_order',$where);
            }
            else{
                $query = $this->db->get('tbl_ticket_order');
            }
            return $query->result_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }

    public function get_order_info_by_id($id) {
        if ($id == null || $id == '') return 0;
        $where = array(
            'id' => $id
        );
        $query = $this->db->get_where($this->ticket_order, $where);
        $order = $query->row_array();
        return $order;
    }
    public function get_order_info_by_tradeno($ordernum) {
        $where = array(
            'trade_no' => $ordernum
        );
        $query = $this->db->get_where($this->ticket_order, $where);
        $order = $query->row_array();
        return $order;
    }
    public function get_order_info_by_userid($user_id) {

        $sql = "select id from tbl_ticket_order where user_id = {$user_id}";
        $result = $this->execSQL($sql);
        if(empty($result)){
            return array();
        }
        return $result;
    }
    public function is_exists_order($user_id,$create_date){
        $where = array(
            'user_id' => $user_id,
            'create_date <' => $create_date
        );
        $query = $this->db->get_where($this->ticket_order, $where);
        $order = $query->row_array();
        return $order;
    }
    public function update_by_ids( $order_ids,$dump_time){
        $sql = "update tbl_ticket_order set order_status = 1,dump_status = 1,dump_time = '{$dump_time}' WHERE id in {$order_ids}";
        $result = $this->db->query($sql);
        return $result;
    }
    public function insertBatch($data = '')
    {
        try
        {
            $this->db->insert_batch($this->order_num,$data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function insertData($data = '')
    {
        try
        {
            $this->db->insert($this->ticket_order,$data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function lists($filters = null, $orders = null,$table = 'ticket_order')
    {
        try {
            if (!empty($filters)) {
                foreach ($filters as $key=>$value) {
                    if (strpos($key, ",") === false) {
                        $this->setWhere($key, $value);
                    } else {
                        $keys = preg_split("/[\s,]+/", $key, 0, PREG_SPLIT_NO_EMPTY);
                        $this->db->group_start();
                        foreach ($keys as $k) {
                            $this->setWhere($k, $value, true);
                        }
                        $this->db->group_end();
                    }
                }
            }
            if (!empty($orders)) {
                foreach ($orders as $key=>$value) {
                    $this->db->order_by($key, $value);
                }
            }
            $query = $this->db->get($table);
            return $query->result_array();
        } catch (Exception $e) {
            return null;
        }
    }

    public function get_all_no_determine_order($order_status = 1,$pay_status = 1){
        $sql = "select id,trade_no,user_id,area,city,address,create_date from tbl_ticket_order  where order_status = {$order_status} and pay_status = {$pay_status}";

        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return null;
        }
        return $result;
    }
    public function get_all_notify_dump_order($order_status = 0,$dump_status = 0, $user_id = '(201143,201039)'){
        $sql = "select*from tbl_ticket_order where order_status = 0 and dump_status = 0 and user_id  in (202120,201112,201115,201149,201188,201195,201205,201203,201280,201277,201305,201322,201310,201328,201325,201336,201340,201344,201355,201444,201420,201402,201400,201452,201446,201423,201458,201353,201494,201496,201473,201497,201517,201519,201525,201528,201530,201534,201440,201544,201545,201549,201557,201520,201564,201579,201591,201587,201504,201598,201451,201612,201615,201619,201536,201347,201655,201266,201627,201648,201660,201629,201580,201511,201383,201419,201721,201449,201719,201567,201718,201703,201724,201732,201726,201740,201748,201800,201806,201808,201802,201846,201850,201854,201856,201609,201866,201920,201922,201925,201289,201460,201932,201934,201652,201333,201278) order by user_id,create_date desc";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return array();
        }
        return $result;
    }
    public function get_allorder_info_by_userids($user_ids) {

        $sql = "select trade_no,update_date,create_date,name,get_credits,area,city,address from tbl_ticket_order where order_status = 2 and pay_status = 1 and user_id in {$user_ids}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return array();
        }
        return $result;
    }

    public function get_first_order($userid){
        $sql = "select order_status from tbl_ticket_order where user_id = {$userid} order by create_date asc limit 1";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        return $result;
    }

    public function get_all_need_dump_order($pay_status = 0,$order_status = 0,$dump_status = 0, $user_id = '(201143,201039)'){
        $sql = "select*from tbl_ticket_order where pay_status = {$pay_status} and order_status = {$order_status} and dump_status = {$dump_status} and user_id  not in {$user_id} order by user_id,create_date desc";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(empty($result)){
            return array();
        }
        return $result;
    }

}
?>
