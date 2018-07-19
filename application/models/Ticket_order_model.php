<?php

class Ticket_order_model extends Base_Model {


    protected $ticket_order;
    protected $ticket_num;
    // constructor
    public function __construct()
    {
        parent::__construct();

        $this->tbl = 'ticket';


    }



    // add administrator
    public function insert_num($data)
    {

        $id = $this->_insert($data, $this->order_num);
        return $id;
    }
    public function getInfoByTradeno($trade_no,$num = 0)
    {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_order_num_'.$num;
        }
        $sql = "select a.ticket_id,a.ticket_num,b.title,b.count_price from {$tbl} as a left join tbl_ticket as b
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

    public function get_order_update($data,$where,$num = 0){
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        foreach ($where as $key=>$value){
            $this->db->where($key, $value);
        }

        $this->db->update($tbl, $data);
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

    public function get_order_list($where,$data,$status,$num = 0) {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        if($status == 'wait' || $status == 'payed'){
            $query = $this->db->limit($data['entryNum'],($data['pageIndex']-1)*$data['entryNum'])->order_by('create_date desc')->get_where($tbl, $where);
        }
        else{
            $query = $this->db->limit($data['entryNum'],($data['pageIndex']-1)*$data['entryNum'])->order_by('update_date desc')->get_where($tbl, $where);
        }
        $order = $query->result_array();
        if ($order == null)
            return 0;
        return $order;
    }
    //根据省份id取已下单
    public function  get_all_notify_dump_order_by_province($province = 0,$order_status = 0,$dump_status = 0, $user_id = '(201143,201039)'){
        if($province == 0 || $province == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$province;
        }
    $sql = "select*from {$tbl} where  order_status = {$order_status} and dump_status = {$dump_status} and user_id  not in {$user_id} order by user_id,create_date desc";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    if(empty($result)){
        return array();
    }
    return $result;
}
    public function get_order_list_num($where,$num = 0) {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        $this->db->where($where);
        $num = $this->db->count_all_results($tbl);
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

    public function get_order_info_by_id($id,$num = 0) {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        if ($id == null || $id == '') return 0;
        $where = array(
            'id' => $id
        );
        $query = $this->db->get_where($tbl, $where);
        $order = $query->row_array();
        return $order;
    }
    public function get_order_info_by_tradeno($ordernum,$num = 0) {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        $where = array(
            'trade_no' => $ordernum
        );
        $query = $this->db->get_where($tbl, $where);
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
    public function is_exists_order($user_id,$create_date,$num = 0){
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        $where = array(
            'user_id' => $user_id,
            'create_date <' => $create_date
        );
        $query = $this->db->get_where($tbl, $where);
        $order = $query->row_array();
        return $order;
    }
    public function update_by_ids( $order_ids,$dump_time,$num = 0){
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        $sql = "update {$tbl} set order_status = 1,dump_status = 1,dump_time = '{$dump_time}' WHERE id in {$order_ids}";
        $result = $this->db->query($sql);
        return $result;
    }
    public function insertBatch($data = '',$num = 0)
    {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_order_num_'.$num;
        }
        try
        {
            $this->db->insert_batch($tbl,$data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    public function insertData($data = '',$num = 0)
    {
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }
        try
        {
            $this->db->insert($tbl,$data);
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

    public function get_all_no_determine_order($order_status = 1,$pay_status = 1,$num = 0){
        if($num == 0 || $num == ''){
            return false;
        }else{
            $tbl = 'tbl_ticket_order_'.$num;
        }

        $sql = "select id,trade_no,user_id,area,city,address,create_date,total_money from {$tbl}  where order_status = {$order_status} and pay_status = {$pay_status}";

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



    /**
     * @param null $filters
     * @param int $page
     * @param int $size
     * @param null $area_code
     * @return mixed
     * 彩票订单统计
     */
    public function lottery($filters = null, $page = 1, $size = PAGE_SIZE, $area_code = NULL){
        $where1="1=1";
        if (is_array($area_code)){
            foreach ($area_code as $key => $val) {
                $where1.=" and {$key}={$val}";
            }
        }
        $start=($page-1)*$size;
        $sql=<<<SQL
select * from tbl_ticket INNER JOIN (select tbl_order_num.trade_no,ticket_id,ticket_num,ticke_money,tbl_ticket_order.name,tbl_ticket_order.phone,tbl_ticket_order.area,tbl_ticket_order.address,tbl_ticket_order.city,tbl_ticket_order.area_code from tbl_order_num LEFT JOIN tbl_ticket_order ON tbl_order_num.trade_no=tbl_ticket_order.trade_no WHERE tbl_ticket_order.pay_status='1')AS a ON tbl_ticket.id=a.ticket_id WHERE {$where1} limit {$start},{$size};
SQL;
        $query=$this->db->query($sql);
        return $query->result_array();
    }


    /**
     * @param null $filters
     * @param null $area_code
     * @return mixed
     * 彩票订单统计数量
     */
    public function countlottery($filters = null, $area_code = NULL){
        $where1="1=1";
        if (is_array($area_code)){
            foreach ($area_code as $key => $val) {
                $where1.=" and {$key}={$val}";
            }
        }
        $sql=<<<SQL
select count(*) as number from tbl_ticket INNER JOIN (select tbl_order_num.trade_no,ticket_id,ticket_num,ticke_money,tbl_ticket_order.name,tbl_ticket_order.phone,tbl_ticket_order.area,tbl_ticket_order.address,tbl_ticket_order.city,tbl_ticket_order.area_code from tbl_order_num LEFT JOIN tbl_ticket_order ON tbl_order_num.trade_no=tbl_ticket_order.trade_no WHERE tbl_ticket_order.pay_status='1')AS a ON tbl_ticket.id=a.ticket_id WHERE {$where1};
SQL;
        $query=$this->db->query($sql);
        return $query->result_array();
    }


    public function area($filters = null, $page = 1, $size = PAGE_SIZE, $area_code = NULL){
        $where1="1=1";
        if (is_array($area_code)){
            foreach ($area_code as $key => $val) {
                $where1.=" and {$key}={$val}";
            }
        }
        $start=($page-1)*$size;
        $sql=<<<SQL
select *,COUNT(DISTINCT id)AS number,sum(total_money)AS total_money from tbl_ticket_order WHERE {$where1} GROUP BY area_code LIMIT {$start},{$size};
SQL;
        $query=$this->db->query($sql);
        return $query->result_array();
    }

    public function countarea($area_code=null){
        $where1="1=1";
        if (is_array($area_code)){
            foreach ($area_code as $key => $val) {
                $where1.=" and {$key}={$val}";
            }
        }
        $sql=<<<SQL
select COUNT(*) AS number from (select * from tbl_ticket_order WHERE {$where1} GROUP BY area_code)AS a;
SQL;
        $query=$this->db->query($sql);
        return $query->result_array();
    }

}
?>
