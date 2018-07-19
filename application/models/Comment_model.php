<?php

// comment table model
class Comment_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['comment'];
	}
	
	// get comment count
	public function getCommentCount($targetKind, $targetId)
	{
		$filters['target_kind'] = $targetKind;
		$filters['target_id'] = $targetId;
		$filters['status'] = COMMENT_STATUS_PASSED;
		return $this->getCount($filters);
	}
	
	// insert
	public function insert($userId, $targetKind, $targetId, $title, $content) 
	{
		$data = array(
			'user_id' => $userId,
			'target_kind' => $targetKind,
			'target_id' => $targetId,
			'title' => $title,
			'content' => $content,
			'status' => COMMENT_STATUS_PASSED,// COMMENT_STATUS_REQUESTED,
			'is_marked' => 0,
			'create_date' => now()
		);
		return $this->_insert($data);
	}

	// delete by user
	public function deleteByUser($userId)
	{
		$where = array(
			'user_id' => $userId
		);
		return $this->db->delete($this->tbl, $where);
	}
	
	// set comment status
	public function setStatus($id, $status)
	{
		$data = array('status' => $status);
		$this->update($id, $data);
	}
	
	// set mark/unmark
	public function setMark($id, $isMarked = true)
	{
		$data = array('is_marked' => $isMarked ? 1 : 0);
		$this->update($id, $data);
	}
}
?>
