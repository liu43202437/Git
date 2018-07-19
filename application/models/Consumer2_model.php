<?php

// user table model
class Consumer2_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		$this->tbl = 'tbl_consumer2';
	}
    // get user info by username
    public function getInfoByPhone($phone)
    {
        $where = array('phone' => $phone);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
	// get user info by username
	public function getInfoByIdNum($id_number)
	{
		$where = array('id_number' => $id_number);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}

    public function getInfoById($userId)
    {
        $where = array('id' => $userId);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
	
	// get user info by nickname
	public function getInfoByNickname($nickname)
	{
		$where = array('nickname' => $nickname);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}
	
	// get user info by weixin
	public function getInfoByWeixin($weixin)
	{
		$where = array('weixin' => $weixin);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}
	
	// add new user - signup
	public function insert($data) 
	{
		return $this->_insert($data);
	}

    public function wechatInsert($openid, $nickname,$avatar_url )
    {
    	$data = array(
            'username' => $openid,
            'nickname' => $nickname,
            'avatar_url' =>  $avatar_url,
            'is_enabled' => 1,
            'rank' => 1,
            'exp' => 0,
            'money' => 0,
            'point' => 0,
            'weixin' => $openid,
            'longitude' => 0,
            'latitude' => 0,
            'create_date' => now()
        );
        return $this->_insert($data);
    }

	// delete user
	public function deleteByName($username)
	{
		$where = array('username' => $username);
		return $this->db->delete($this->tbl, $where);
	}
	
	
	// statistics function
	public function getCountInfo($filter)
	{
		$filter['device_type'] = DEVICE_TYPE_IPHONE;
		$info['iphone'] = $this->getCount($filter);
		
		$filter['device_type'] = DEVICE_TYPE_ANDROID;
		$info['android'] = $this->getCount($filter);

		unset($filter['device_type']);
		$filter['weixin !='] = null;
		$info['weixin'] = $this->getCount($filter);

		$info['total'] = $info['iphone'] + $info['android'];

		return $info;
	}
	public function listAll($where = '1')
	{
		// $query = $this->db->query('select * from tbl_consumer where type="manager"');
		$query = $this->db->get_where($this->tbl, $where);
		return $query->result_array();
	}
	public function queryAll($sql)
	{
		try
		{
			$query = $this->db->query($sql);
			return $query->result_array();
		}
		catch (Exception $e)
		{
			return null;
		}
	}
}
?>
