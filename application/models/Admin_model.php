<?php

// admin table model
class Admin_model extends Base_Model {

	protected $tblRole = '';
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['admin'];
		$this->tblRole = $TABLE['admin_role'];
	}
	
	// get info by username
	public function getInfoByName($username)
	{
		$where = array('username' => $username);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}
	
	// get info by email
	public function getInfoByEMail($email)
	{
		$where = array('email' => $email);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}
	
	// add administrator
	public function insert($username, $password) 
	{
		$data = array(
			'username' => $username,
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'is_enabled' => 1,
			'create_date' => now()
		);		
		return $this->_insert($data);
	}

	// update info
	public function update($id, $data, $table = null)
	{
		if (isset($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}
		return parent::update($id, $data);
	}
	
	// delete by name
	public function deleteByName($username)
	{
		$where = array(
			'username' => $username
		);
		return $this->db->delete($this->tbl, $where);
	}
	
	
	/* ============== Role operations ================ */
	public function getRoles($adminId) 
	{
		$this->db->select('action');
		$this->db->where('admin_id', $adminId);
		$query = $this->db->get($this->tblRole);
		return $query->result_array();
	}
	
	public function setRole($adminId, $action, $isPermit) 
	{
		$where = array(
			'admin_id' => $adminId, 
			'action' => $action
		);
		if ($isPermit) {
			return $this->db->delete($this->tblRole, $where);
		} else {
			$query = $this->db->get_where($this->tblRole, $where);
			$row = $query->row_array();
			if (empty($row)) {
				return $this->db->insert($this->tblRole, $where);
			}
			return true;
		}
	}
}
?>
