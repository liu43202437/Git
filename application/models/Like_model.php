<?php

// like table model
class Like_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['like'];
	}
	
	// get like count of a item
	public function getLikeCount($itemKind, $itemId)
	{
		$filters['item_kind'] = $itemKind;
		$filters['item_id'] = $itemId;
		return $this->getCount($filters);
	}
	
	// is user's like item?
	public function isLikeItem($userId, $itemKind, $itemId)
	{
		$filters['user_id'] = $userId;
		$filters['item_kind'] = $itemKind;
		$filters['item_id'] = $itemId;
		
		$rslt = $this->getCount($filters);
		return empty($rslt) ? 0 : 1;
	}
	
	// insert
	public function insert($userId, $itemKind, $itemId) 
	{
		$data = array(
			'user_id' => $userId,
			'item_id' => $itemId,
			'item_kind' => $itemKind
		);
		return $this->_insert($data);
	}

	// delete by user
	public function deleteByUser($userId)
	{
		$where = array('user_id' => $userId);
		return $this->db->delete($this->tbl, $where);
	}
	
	// delete by item
	public function deleteByItem($itemKind, $itemId)
	{
		$where = array('item_kind' => $itemKind, 'item_id' => $itemId);
		return $this->db->delete($this->tbl, $where);
	}
	
	// delete like
	public function deleteLike($userId, $itemKind, $itemId)
	{
		$where = array('user_id' => $userId, 'item_kind' => $itemKind, 'item_id' => $itemId);
		return $this->db->delete($this->tbl, $where);
	}
}
?>
