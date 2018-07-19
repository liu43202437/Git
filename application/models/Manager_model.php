<?php

/**
 * @Author: liu43
 * @Date:   2017-09-07 10:03:44
 * @Last Modified by:   liuzudong
 * @Last Modified time: 2018-03-09 18:22:41
 */
/**
* 数据库操作类
*/
class Manager_model extends Base_Model
{
	
	function __construct()
	{
		$this->tbl = 'tbl_consumer';
		parent::__construct();
	}
	public function insertData($tbl,$data = '')
	{
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
	public function deleteData($where){
		try
		{
			$id=$where['id'];
			$sql=<<<SQL
select consumer_userid,status,refuse from {$this->tbl} WHERE id='{$id}';
SQL;
			$res=$this->db->query($sql);
			$res=$res->row_array();

			if ($res['status'] == '0' || $res['refuse'] == '1'){}else {
				$id = $res['consumer_userid'];
				$sql = <<<SQL
UPDATE tbl_user SET point=point-10 WHERE id='{$id}'
SQL;
				$this->db->query($sql);
				$sql = <<<SQL
DELETE FROM tbl_user_credits WHERE user_id='{$id}' AND type='7' ORDER BY id DESC LIMIT 1
SQL;
				$this->db->query($sql);
			}

			$this->db->delete($this->tbl, $where);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function updateData($tbl = 'tbl_consumer', $data, $where)
	{
		try
		{
			$this->db->where($where)->update($tbl, $data);
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
			$sql = "SELECT a.id,a.trade_no,a.user_id,a.`name`,a.get_credits,a.manager_credits,a.update_date,a.manager_name,b.city,b.address FROM tbl_ticket_order as a ,tbl_club as b WHERE a.order_status=2 AND a.manager_id=b.manager_id AND a.manager_id='{$user_id}' order by a.id desc";
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
    public function getAreaCredits($manager_id,$start,$end){
        $sql = "SELECT SUM(area_manager_credits) as credits_today FROM tbl_ticket_order WHERE order_status=2 AND manager_id=$manager_id AND update_date>='$start' AND update_date <= '$end'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function getBazaarCredits($bazaar_id,$start,$end){
        $sql = "SELECT SUM(bazaar_credits) as credits_today FROM tbl_ticket_order WHERE order_status=2 AND bazaar_id = {$bazaar_id} AND update_date>='$start' AND update_date <= '$end'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}