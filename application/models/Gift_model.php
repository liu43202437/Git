<?php

// gift table model
class Gift_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['gift'];
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
	public function insert($name, $price, $exp, $image) 
	{
		$data = array(
			'name' => $name,
			'price' => $price,
			'exp' => $exp,
			'image' => $image,
			'orders' => 0,
			'is_show' => 1,
			'create_date' => now()
		);
		$id = $this->_insert($data);
		return $this->update($id, array('orders'=>$id));
	}

	// set show/hide
	public function setShow($id, $isShow = true)
	{
		$data = array('is_show' => $isShow ? 1 : 0);
		$this->update($id, $data);
	}
}
?>
