<?php

// audit table model
class Audit_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['audit'];
	}
	
	public function insert($kind, $data)
	{
		$data['kind'] = $kind;
		$data['status'] = AUDIT_STATUS_REQUESTED;
		$data['is_marked'] = 0;
		$data['create_date'] = now();
		return $this->_insert($data);
	}
    public function wechat_insert($data)
    {
        $data['create_date'] = now();
        return $this->_insert($data);
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

	public function getByUserid($userId){
        $where = array(
            'user_id' => $userId
        );
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
	}
	public function deleteData($where){
		try
		{
			$this->db->delete($this->tbl, $where);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}
?>
