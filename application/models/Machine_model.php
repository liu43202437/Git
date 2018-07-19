<?php

class Machine_model extends Base_Model{
    public function __construct()
    {
        parent::__construct();
        $this->tbl="";
    }


    public function get_machine($filters,$page,$size){
        $start=($page-1)*$size;
        $where="1=1";
        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $where .= " and tbl_hunan_machine.{$key}" . "'{$filter}'";
            }
        }
        $sql=<<<SQL
SELECT *,tbl_hunan_machine.id AS mid,tbl_hunan_machine.status AS status FROM tbl_hunan_machine LEFT JOIN tbl_club ON tbl_hunan_machine.club_id=tbl_club.id LEFT JOIN tbl_hunan_staff ON tbl_hunan_machine.staff_id=tbl_hunan_staff.id LEFT JOIN tbl_ticket ON tbl_hunan_machine.ticket_id=tbl_ticket.id WHERE {$where} LIMIT {$start},{$size}
SQL;
        $res=$this->db->query($sql);
        return $res->result_array();
    }

    public function getcount_machine($filters){
        $where="1=1";
        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $where .= " and tbl_hunan_machine.{$key}" . "'{$filter}'";
            }
        }
        $sql=<<<SQL
SELECT count(tbl_hunan_machine.id)AS number FROM tbl_hunan_machine LEFT JOIN tbl_club ON tbl_hunan_machine.club_id=tbl_club.id LEFT JOIN tbl_hunan_staff ON tbl_hunan_machine.staff_id=tbl_hunan_staff.id LEFT JOIN tbl_ticket ON tbl_hunan_machine.ticket_id=tbl_ticket.id WHERE {$where}
SQL;
        $res=$this->db->query($sql);
        return $res->row_array();
    }

    public function get_machine_detail($id){
        $sql=<<<SQL
select * from tbl_hunan_machine LEFT JOIN tbl_club ON tbl_hunan_machine.club_id=tbl_club.id WHERE tbl_hunan_machine.id='{$id}';
SQL;
        $res=$this->db->query($sql);
        $res=$res->row_array();
        return $res;
    }
    
    public function getorder($filters,$page,$size){
        $start=($page-1)*$size;
        $where="1=1";
        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $where .= " and tbl_hunan_order.{$key}" . "'{$filter}'";
            }
        }
        $sql=<<<SQL
select *,tbl_hunan_order.create_date,tbl_hunan_machine.machine_id,tbl_hunan_order.ticket_num AS ticket_num from tbl_hunan_order LEFT JOIN tbl_ticket ON tbl_hunan_order.ticket_id=tbl_ticket.id LEFT JOIN tbl_hunan_machine ON tbl_hunan_order.machine_id=tbl_hunan_machine.id WHERE {$where} ORDER BY tbl_hunan_order.id DESC LIMIT {$start},{$size};
SQL;
        $res=$this->db->query($sql);
        return $res->result_array();

    }


    public function count_order($filters){
        $where="1=1";
        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $where .= " and tbl_hunan_order.{$key}" . "'{$filter}'";
            }
        }
        $sql=<<<SQL
select COUNT(tbl_hunan_order.id)AS number from tbl_hunan_order LEFT JOIN tbl_ticket ON tbl_hunan_order.ticket_id=tbl_ticket.id WHERE {$where};
SQL;
        $res=$this->db->query($sql);
        return $res->row_array();

    }


    /**
     * @param $id彩票机表的主键id
     * 检测彩票机状态
     */
    public function check_machine($id){
        $sql=<<<SQL
select * from tbl_hunan_machine WHERE id='{$id}';
SQL;
        $res=$this->db->query($sql);
        $res=$res->row_array();

        $data=array(
            'code' => '0'
        );
        if ($res['locked'] == '1'){
            $data['code']='1';      //机器锁定
            $data['msg']='机器处于锁定中';
            $data['data']=array(
                'locked_time'=> $res['locked_time']
            );
        }

        if ($res['abnormity'] == '1'){
            $data['code']='2';      //机器异常
            $data['msg']='机器处于异常中';
            $data['data']=array(

            );
        }

        if ($res['header_status'] != '0'){
            $data['code']='3';      //机头异常
            $data['msg']='机头处于异常中';
            if ($res['header_status'] == '1'){
                $data['data']="与机头通讯异常";
            }
            if ($res['header_status'] == '2'){
                $data['data']="机头无票";
            }
            if ($res['header_status'] == '3'){
                $data['data']="机头状态错误";
            }
        }
        
        return $data;
    }


    /**
     * @param $oid订单号
     * 检测订单状态
     */
    public function check_order($oid){
        $res=$this->db->get_where("hunan_order",array('oid'=>$oid));
        $res=$res->row_array();
        $data=array(
            'code' => '0'
        );
        if ($res['status'] == '0'){
            $data['code']= '1';
            if ($res['pay_status'] == '0'){
                $data['msg']="订单未支付";
            }elseif($res['pay_status'] == '2'){
                $data['msg']="订单已取消";
            }else{
                if ($res['ticket_status'] == '0'){
                    $data['msg']="订单未出票";
                }elseif($res['ticket_status'] == '1'){
                    $data['msg']="订单完成";
                }else{
                    $data['msg']="出票故障";
                }
            }
        }elseif ($res['status'] == '1'){
            $data['code']= '0';
            $data['msg']="订单成功";
        }else{
            $data['code']= '1';
            $data['msg']="订单过期";
        }
        return $data;
    }
    
    public function order_detail($oid){
        $sql=<<<SQL
select * from tbl_hunan_order LEFT JOIN tbl_ticket ON tbl_hunan_order.ticket_id=tbl_ticket.id WHERE oid='{$oid}';
SQL;
        $res=$this->db->query($sql);
        return $res->row_array();
    }

    public function get_already_refuse_num($oid){
        $sql=<<<SQL
select SUM(refuse_num) as refuse_num from tbl_hunan_refuse WHERE oid='{$oid}' AND refuse_status='1';
SQL;
        $res=$this->db->query($sql);
        return $res->row_array();
    }

    public function get_price($ticket_id){
        $res=$this->db->get_where("ticket",array('id'=>$ticket_id));
        return $res->row_array();
    }

    public function add_refuse($arr){
        try{
            $this->db->insert("hunan_refuse",$arr);
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    public function update_refuse($id,$arr){
        try{
            $this->db->update("hunan_order",$arr,array('oid' => $id));
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    public function del_machine($id){
        $sql=<<<SQL
select machine_id,club_id,staff_id,openid,total_ticket_num,total_ticket_amount,create_date from tbl_hunan_machine WHERE id='{$id}'
SQL;
        $res=$this->db->query($sql);
        $res=$res->row_array();
        $machine_id=$res['machine_id'];
        $content=serialize($res);
        $create_time=date("Y-m-d H:i:s",time());
        $sql1=<<<SQL
insert into tbl_hunan_initialize (machine_id, content, create_time) values ('{$machine_id}','{$content}','{$create_time}');
SQL;
        try{
            $this->db->query($sql1);
            return true;
        }catch (Exception $e){
            return false;
        }
    }
    
    public function get_email($user_id){
        $query = $this->db->get_where("user",array('id'=>$user_id));
        return $query->row_array();
    }

    public function get_last_add_time($machine){
        $sql=<<<SQL
select * from tbl_hunan_add_ticket WHERE machine_id='{$machine}' ORDER BY create_date DESC limit 1;
SQL;
        $query=$this->db->query($sql);
        $query=$query->row_array();
        if (empty($query)){
            return date('Y-m-d H:i:s',0);
        }else{
            return $query['create_date'];
        }
    }

    public function get_last_filter_time($machine){
        $sql=<<<SQL
select * from tbl_hunan_inventory WHERE machine_id='{$machine}' ORDER BY create_time DESC limit 1;
SQL;
        $query=$this->db->query($sql);
        $query=$query->row_array();
        if (empty($query)){
            return date('Y-m-d H:i:s',0);
        }else{
            return $query['create_time'];
        }
    }

    public function add_filter_log($machine_id,$status,$contents){
        $create_time=date('Y-m-d H:i:s',time());
        $sql=<<<SQL
insert into tbl_hunan_inventory (machine_id, content, status, create_time) values ('{$machine_id}','{$contents}','{$status}','{$create_time}');
SQL;
        $this->db->query($sql);
    }

    
    public function getarea($area_id){
        $sql=<<<SQL
select * from tbl_new_area WHERE parent_id='{$area_id}';
SQL;
        $res=$this->db->query($sql);
        return $res->result_array();
    }
    
    public function get_club($key,$filter){
        $sql=<<<SQL
select * from tbl_club WHERE {$key} = '{$filter}';
SQL;
        $res=$this->db->query($sql);
        return $res->result_array();
    }

    public function update_machine_club($id,$new_club_id){
        $sql=<<<SQL
update tbl_hunan_machine set club_id = '{$new_club_id}' where id = '{$id}';
SQL;
        try{
            $this->db->query($sql);
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    public function getMachineByClubId($club_id){

        $sql = "SELECT machine_id FROM tbl_hunan_machine WHERE club_id = $club_id";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

}