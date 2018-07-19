<?php

// ranking table model
class Ranking_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['ranking'];
	}
	
	// override function
	public function getList($filters = null, $orders = null, $page = 1, $size = PAGE_SIZE, $table = null)
	{
		if ($orders == null) {
			$orders['orders'] = 'ASC';
		}
		return parent::getList($filters, $orders, $page, $size, $table);
	} 
	
	// insert
	public function insert($name, $memberIds) 
	{
		$data = array(
			'name' => $name,
			'orders' => 0,
			'create_date' => now()
		);
		$data = array_merge($data, $memberIds);
		$id = $this->_insert($data);
		return $this->update($id, array('orders'=>$id));
	}
}
?>
