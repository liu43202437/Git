<?php

// baby table model
class Baby_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['baby'];
	}
	
	// insert
	public function insert($title, $babyDate, $image, $hits) 
	{
		$data = array(
			'title' => $title,
			'baby_date' => $babyDate,
			'image' => $image,
			'hits' => $hits,
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
	
	// increase hits count
	public function increaseHits($id)
	{
		$data = array('hits$' => 'hits + 1');
		$this->update($id, $data);
	}
}
?>
