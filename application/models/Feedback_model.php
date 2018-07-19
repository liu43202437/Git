<?php

// feedback table model
class Feedback_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['feedback'];
	}
	
	// insert
	public function insert($userId, $title, $content, $contact) 
	{
		$data = array(
			'user_id' => $userId,
			'title' => $title,
			'content' => $content,
			'contact' => $contact,
			'status' => FEEDBACK_STATUS_REQUESTED,
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
	
	// set mark/unmark
	public function setMark($id, $isMarked = true)
	{
		$data = array('is_marked', $isMarked ? 1 : 0);
		$this->update($id, $data);
	}
}
?>
