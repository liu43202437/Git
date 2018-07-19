<?php

// user table model
class User_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['user'];
	}
    // get user info by username
    public function getInfoByNumber($idNumber)
    {
        $where = array('id_number' => $idNumber);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
	// get user info by username
	public function getInfoByName($username)
	{
		$where = array('username' => $username);
		$query = $this->db->get_where($this->tbl, $where);
		return $query->row_array();
	}

    public function getInfoById($userId)
    {
        $where = array('id' => $userId);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    public function updateCredits($uid,$credits){
        if(!isset($uid)||!isset($credits))
            return false;
        $where = array(
            'id' => $uid
        );
        try{
            $this->db->set('point',"point+$credits",false);
            $this->db->where($where);
            $this->db->update($this->tbl);
        }catch (Exception $e){
            return false;
        }
        return true;
	}
    public function updateDeductCredits($uid,$credits){
        if(!isset($uid)||!isset($credits))
            return false;
        $where = array(
            'id' => $uid
        );
        try{
            $this->db->set('point',"point-$credits",false);
            $this->db->where($where);
            $this->db->update($this->tbl);
        }catch (Exception $e){
            return false;
        }
        return true;
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
	public function insert($username, $nickname, $deviceType, $deviceUdid, $weixin = null) 
	{
		$data = array(
			'username' => $username,
			'nickname' => $nickname,
			'avatar_url' =>  base_url() . $this->config->item('default_avatar'),
			'is_enabled' => 1,
			'rank' => 1,
			'exp' => 0,
			'money' => 0,
			'point' => 0,
			'weixin' => $weixin,
			'device_type' => $deviceType,
			'device_udid' => $deviceUdid,
			'longitude' => 0,
			'latitude' => 0,
			'create_date' => now()
		);
		return $this->_insert($data);
	}

    public function wechatInsert($openid, $nickname,$avatar_url,$unionid )
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
            'unionid' =>  $unionid,
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
	public function fetchAll($where = array()){
		try
		{
			if(!empty($where)){
				$query = $this->db->get_where($this->tbl,$where);
			}
			else{
				$query = $this->db->get($this->tbl);
			}
			return $query->result_array();
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	public function updateData($data, $where)
	{
		try
		{
			$this->db->where($where)->update($this->tbl, $data);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
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
	public function execSql($sql)
	{
		try
		{
			$query = $this->db->query($sql);
			return $query;
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	public function insertData($data = '')
	{
		try
		{
			$this->db->insert($this->tbl,$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function fetchOne($where = array()){
        try
        {
            if(!empty($where)){
                $query = $this->db->get_where($this->tbl,$where);
            }
            else{
                $query = $this->db->get($this->tbl);
            }
            return $query->row_array();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
}
?>
