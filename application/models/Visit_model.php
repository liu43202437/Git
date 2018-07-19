<?php

// visit table model
class Visit_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['visit'];
	}
	
	// insert
	public function insert($deviceUdid) 
	{
		$data = array(
			'device_udid' => $deviceUdid,
			'create_date' => now()
		);
		return $this->_insert($data);
	}
	
	// statistics function
	public function getCountInfo($filter)
	{
		$info['pv'] = $this->getCount($filter);
		
		$this->db->distinct('device_udid');
		$this->db->select('device_udid');
		$info['uv'] = $this->getCount($filter);

		return $info;
	}
}
?>
