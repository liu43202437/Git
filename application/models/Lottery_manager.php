<?php


class Lottery_manager extends Base_Model{
    public function __construct()
    {
        parent::__construct();
        $this->tbl="tbl_lottery_manager";
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

    
    public function countclub($seach=null,$user_id){
        $where="1=1";
        if (!empty($seach)){
            $where.=" and tbl_club.name like '%{$seach}%' or tbl_club.view_name like '%{$seach}%'or tbl_club.phone like '%{$seach}%'";
        }
        $sql=<<<SQL
select count(*)AS number from tbl_lottery_papers LEFT JOIN tbl_club on tbl_lottery_papers.club_id=tbl_club.id WHERE ({$where}) AND tbl_lottery_papers.user_id={$user_id} AND tbl_lottery_papers.valid='1';
SQL;
        $query=$this->db->query($sql);
        return $query->result_array();
    }
    
    public function club($seach=null,$user_id,$pageIndex,$entryNum){
        if ($pageIndex<=1){
            $pageIndex=1;
        }
        $start=($pageIndex-1)*$entryNum;
        $where="1=1";
        if (!empty($seach)){
            $where.=" and tbl_club.name like '%{$seach}%' or tbl_club.view_name like '%{$seach}%'or tbl_club.phone like '%{$seach}%'";
        }
        $sql=<<<SQL
select * from tbl_lottery_papers LEFT JOIN tbl_club on tbl_lottery_papers.club_id=tbl_club.id WHERE ({$where}) AND tbl_lottery_papers.user_id={$user_id} AND tbl_lottery_papers.valid='1' ORDER BY tbl_lottery_papers.create_date DESC LIMIT {$start} , {$entryNum};
SQL;
        $query=$this->db->query($sql);
        return $query->result_array();
    }


    /**
     * @param $user_id
     * @param $club
     * @param $long
     * @param $lat
     */
    public function sign($user_id,$club,$long,$lat,$status){
        $time=date("Y-m-d H:i:s",time());
        if ($status == 1) {
            $sql = <<<SQL
INSERT INTO tbl_lottery_interview (user_id,club_id,begin_longitude,begin_latitude,begin_time,create_date) VALUES ('{$user_id}','{$club}','{$long}','{$lat}','{$time}','{$time}')
SQL;
        }elseif($status == 2){
            $sql = <<<SQL
UPDATE tbl_lottery_interview SET club_id='{$club}',end_longitude='{$long}',end_latitude='{$lat}',end_time='{$time}',status='{$status}' WHERE user_id='{$user_id}' AND status='1'
SQL;
        }elseif($status == 3){
            $sql = <<<SQL
UPDATE tbl_lottery_interview SET begin_longitude='{$long}',begin_latitude='{$lat}' WHERE user_id='{$user_id}' AND status='1' AND club_id='{$club}'
SQL;
        }
        $query=$this->db->query($sql);
        return $query;
    }
    
    public function checkclub($user_id,$club){
        $sql=<<<SQL
select * from tbl_lottery_papers WHERE user_id='{$user_id}' AND club_id='{$club}' AND valid='1';
SQL;
        $query=$this->db->query($sql);
        $query=$query->result_array();
        if (empty($query)){
            return 1;
        }else{
            return 2;
        }
    }
    
    public function getrecord($user_id,$seach,$pageIndex,$entryNum){
        if ($pageIndex<=1){
            $pageIndex=1;
        }
        $start=($pageIndex-1)*$entryNum;
        $where="1=1";
        if (!empty($seach)){
            $where.=" and tbl_club.name like '%{$seach}%' or tbl_club.phone like '%{$seach}%' or tbl_lottery_interview.begin_time like '%{$seach}%'";
        }
        $sql=<<<SQL
select * from tbl_lottery_interview LEFT JOIN tbl_club ON tbl_lottery_interview.club_id=tbl_club.id WHERE ({$where}) AND tbl_lottery_interview.user_id='{$user_id}' ORDER BY tbl_lottery_interview.create_date DESC LIMIT {$start},{$entryNum};
SQL;

        $query=$this->db->query($sql);
        return $query->result_array();
    }


    public function countgetrecord($user_id,$seach){
        $where="1=1";
        if (!empty($seach)){
            $where.=" and tbl_club.name like '%{$seach}%' or tbl_club.phone like '%{$seach}%'";
        }
        $sql=<<<SQL
select count(*)AS number from tbl_lottery_interview LEFT JOIN tbl_club ON tbl_lottery_interview.club_id=tbl_club.id WHERE ({$where}) AND tbl_lottery_interview.user_id='{$user_id}';
SQL;

        $query=$this->db->query($sql);
        return $query->result_array();
    }
    
    public function club_detail($club_id){
        $sql=<<<SQL
select * from tbl_club WHERE id='{$club_id}';
SQL;
        $res=$this->db->query($sql);
        return $res->result_array();
    }

    public function on_sign($user_id){
        $sql=<<<SQL
select * from tbl_lottery_interview WHERE user_id='{$user_id}' AND status='1';
SQL;
        $res=$this->db->query($sql);
        return $res->result_array();
    }
}