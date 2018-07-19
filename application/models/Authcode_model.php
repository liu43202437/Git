<?php

// auth code table model
class Authcode_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['auth_code'];
	}
	
	// get info by id
	public function getInfoByTarget($target)
	{
		$where = array('target' => $target);
		$this->db->order_by('create_date', 'desc');
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}

	// insert
	public function insert($target, $code, $expireMinutes, $deviceId) 
	{
		$expireDate = t2dt(time() + $expireMinutes * 60);
		$data = array(
			'target' => $target,
			'code' => $code,
			'expire_date' => $expireDate,
			'device_udid' => $deviceId,
			'create_date' => now()
		);
		return $this->_insert($data);
	}

	// delete by target
	public function deleteByTarget($target)
	{
		$where = array(
			'target' => $target
		);
		return $this->db->delete($this->tbl, $where);
	}
}
?>
