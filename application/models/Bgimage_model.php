<?php

// splash table model
class Bgimage_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['bgimage'];
	}
	
	// insert
	public function insert($name, $image) 
	{
		$data = array(
			'name' => $name,
			'image' => $image,
			'is_show' => 1,
			'create_date' => now()
		);
		$id = $this->_insert($data);
	}
	
	// custom get 
	public function getAvailableBgimages()
	{
		$this->db->select('id, name, image')
				->where('is_show', 1)
				->order_by('create_date', 'DESC')
				->limit(1);
		$image = $this->db->get($this->tbl)->result_array();
		return $image;
	}
}
?>
