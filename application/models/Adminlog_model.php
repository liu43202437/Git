<?php

// admin_log table model
class Adminlog_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['admin_log'];
	}
	
	// add administrator
	public function insert($adminId, $username, $operation, $content) 
	{
		$data = array(
			'admin_id' => $adminId,
			'username' => $username,
			'ip' => getIPAddress(),
			'operation' => $operation,
			'content' => $content,
			'create_date' => now()
		);		
		return $this->_insert($data);
	}
}
?>
