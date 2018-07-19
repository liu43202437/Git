<?php

// user_rank table model
class Userrank_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['user_rank'];
	}
	
	// insert new 
	public function insert($nameMale, $nameFemale, $rank, $minExp) 
	{
		$data = array(
			'name_male' => $nameMale,
			'name_female' => $nameFemale,
			'rank' =>  $rank,
			'min_exp' =>  $minExp,
			'create_date' => now()
		);
		return $this->_insert($data);
	}
	
	// get by rank
	public function getByRank($rank)
	{
		$this->db->where('rank', $rank);
		return $this->db->get($this->tbl)->row_array();
	}
	
	// get appropriate rank
	public function getByExp($exp)
	{
		$filter['min_exp >='] = $exp;
		$order['min_exp'] = 'ASC';
		$ranks = $this->getList($filter, $order, 1, 1);
		if (!empty($ranks)) {
			return $ranks[0];
		}
		return null;
	}
}
?>
