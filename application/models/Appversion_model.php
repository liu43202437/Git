<?php

// app_version table model
class Appversion_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['app_version'];
	}
	
	// insert
	public function insert($filename, $filesize, $version, $url, $description, $adminId, $adminName) 
	{
		$data = array(
			'filename' => $filename,
			'filesize' => $filesize,
			'version' => $version,
			'url' => $url,
			'description' => $description,
			'admin_id' => $adminId,
			'admin_name' => $adminName,
			'create_date' => now()
		);
		
		return $this->_insert($data);
	}
}
?>
