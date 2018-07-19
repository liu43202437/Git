<?php

// session table model
class Session_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['session'];
	}
	
	// get info by id
	public function getInfoBySId($sessionId)
	{
		$where = array(
			'session_id' => $sessionId
		);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}

	// insert
	public function insert($userId, $expireDate = null) 
	{
		$sessionId = md5(uniqid(mt_rand(), true));
		
		if (empty($expireDate)) {
			$expireDate = time_after(30);	// after 30 days
		}
		$data = array(
			'session_id' => $sessionId,
			'user_id' => $userId,
			'expire_date' => $expireDate,
			'create_date' => now()
		);
		return $this->_insert($data);
	}

	// update info
	public function updateBySId($sessionId, $data)
	{
		try {
			$where = array(
				'session_id' => $sessionId
			);
			$this->db->update($this->tbl, $data, $where);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	// delete info
	public function deleteBySId($sessionId)
	{
		$where = array(
			'session_id' => $sessionId
		);
		return $this->db->delete($this->tbl, $where);
	}
	
	// delete by user
	public function deleteByUser($userId)
	{
		$where = array(
			'user_id' => $userId
		);
		return $this->db->delete($this->tbl, $where);
	}

    public function getByUser($userId)
    {
        $where = array(
            'user_id' => $userId
        );
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
    public function getUserInfoBySId($sid)
	{
		$sql = "select a.*,b.phone from tbl_session as a left join tbl_user as b on a.user_id = b.id where a.session_id = '{$sid}'";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        if(empty($result)){
            return null;
        }
        return $result;
	}

    public function getClubBySid($sid){
        $sql = "SELECT b.id , b.view_name FROM tbl_session AS a LEFT JOIN tbl_club AS b ON a.user_id = b.user_id WHERE a.session_id = '{$sid}'";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        if(empty($result)){
            return null;
        }
        return $result;
    }
}
?>
