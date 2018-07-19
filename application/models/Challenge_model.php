<?php

// challenge table model
class Challenge_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['challenge'];
	}
	
	// insert
	public function insert($title, $image, $introduction) 
	{
		$data = array(
			'title' => $title,
			'image' => $image,
			'introduction' => $introduction,
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
