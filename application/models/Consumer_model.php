<?php

// user table model
class Consumer_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		$this->tbl = 'tbl_consumer';
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

    public function getInfoByConsumerUserId($userId)
    {
        $where = array('consumer_userid' => $userId);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
    public function getInfoByManagerid($managerid)
    {
        $sql="select a.*,b.user_id as area_user_id,c.user_id as bazaar_user_id,b.bazaar_phone,b.bazaar_name,b.name as area_name from {$this->tbl} as a left join tbl_area_manager as b on a.area_managerid = b.phone 
             and b.status = 1  left join tbl_bazaar_manager as c on b.bazaar_phone = c.phone and c.status = 1 where a.status = 1 and a.manager_id ={$managerid}";
        $query = $this->db->query($sql);
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
	public function get_consumer_noaudit_num($phone)
	{
        $sql = "select count(*) as num from {$this->tbl} where status = 0 and refuse=0 and area_managerid = '{$phone}'";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        if(empty($result)){
            return null;
        }
        return $result;
	}
	public function get_consumer_manager_list($area_code)
	{
        $new_area_code = substr($area_code,0,4);
		if($new_area_code != '4301'){
            $sql = "select phone,name from {$this->tbl} where status = 1 and refuse=0 and area_code = '{$area_code}'";
		}else{
            $sql = "select phone,name from {$this->tbl} where status = 1 and refuse=0 and (area_code = '{$area_code}'or phone = '18173196695')";
		}
        $query = $this->db->query($sql);
        return $query->result_array();
	}
}
?>
