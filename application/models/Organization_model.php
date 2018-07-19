<?php

// organization table model
class Organization_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['organization'];
	}
	
	// insert
	public function insert($name, $viewName, $logo, $thumb, $phone, $contact) 
	{
		$data = array(
			'name' => $name,
			'view_name' => $viewName,
			'logo' => $logo,
			'thumb' => $thumb,
			'phone' => $phone,
			'contact' => $contact,
			'is_show' => 1,
			'create_date' => now()
		);
		return $this->_insert($data);
	}

	// set show/hide
	public function setShow($id, $isShow = true)
	{
		$data = array('is_show' => $isShow ? 1 : 0);
		$this->update($id, $data);
	}
}
?>
