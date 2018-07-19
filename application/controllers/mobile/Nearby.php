<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nearby extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
	
	public function club_list()
	{
		$longitude = $this->post_input('longitude', 0.0);
		$latitude = $this->post_input('latitude', 0.0);

		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('club_model');

		$filters['is_show'] = 1;
        $orders['distance'] = 'ASC';

        $q = $this->post_input('q');      
        $filters['view_name%'] = $q;    

		// get list count
		$totalCount = $this->club_model->getCount($filters);
		
		// get paged list
		$this->club_model->select("*, POW(longitude-$longitude, 2)+POW(latitude-$latitude, 2) AS distance");
		$data = $this->club_model->getList($filters, $orders, $page, $size);

		if ($page == 1) {
			$top_row = $this->club_model->get(16);
		
			array_unshift($data, $top_row);
		}
		
		$clubList = array();
		foreach ($data as $key=>$item) {
			$club['id'] = $item['id'];
			$club['thumb'] = getFullUrl($item['thumb']);
			$club['view_name'] = $item['view_name'];
			$club['city'] = $item['city'];
			
			// get distance
			$distance = getDistance($longitude, $latitude, $item['longitude'], $item['latitude']);
			if ($distance < 100) {
				$club['distance'] = '<100米';
			} else if ($distance < 1000) {
				$club['distance'] = round($distance, 2) . '米';
			} else {
				$club['distance'] = round($distance / 1000, 2) . '公里';
			}
			$clubList[] = $club;			
		}

		// out data
		parent::output(
			array(
				'items' => $clubList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function club_info()
	{
		$longitude = $this->post_input('longitude', 0.0);
		$latitude = $this->post_input('latitude', 0.0);
		
		$clubId = $this->post_input('club_id');
		if (empty($clubId)) {
			parent::output(100);
		}
		
		$this->load->model('club_model');
		$this->load->model('area_model');
		
		// get item
		$club = $this->club_model->get($clubId);
		if (empty($club)) {
			parent::output(199);
		}
		
		$club['thumb'] = getFullUrl($club['thumb']);
		$club['logo'] = getFullUrl($club['logo']);
		if (!empty($club['images'])) {
			foreach ($club['images'] as &$image) {
				$image['image'] = getFullUrl($image['image']);
			}
		}
		
		$area = $this->area_model->getAreaInfo($club['area_id']);
		if (empty($area)) {
			$club['full_address'] = $club['address'];
		} else {
			$club['full_address'] = $area['fullname'] . $club['address'];
		}
		
		// get recommend club list
		$filters['is_show'] = 1;
		$filters['id !='] = $clubId;
		$orders['relation'] = 'ASC';
		$this->club_model->select("*, ABS(area_id-".$club['area_id'].") AS relation");
		$data = $this->club_model->getList($filters, $orders, 1, 4);
		$clubList = array();
		foreach ($data as $key=>$item) {
			$rItem['id'] = $item['id'];
			$rItem['thumb'] = getFullUrl($item['thumb']);
			$rItem['view_name'] = $item['view_name'];
			$rItem['city'] = $item['city'];
			// get distance
			$distance = getDistance($longitude, $latitude, $item['longitude'], $item['latitude']);
			if ($distance < 100) {
				$rItem['distance'] = '<100米';
			} else if ($distance < 1000) {
				$rItem['distance'] = round($distance, 2) . '米';
			} else {
				$rItem['distance'] = round($distance / 1000, 2) . '公里';
			}
			$clubList[] = $rItem;			
		}
		
		// out data
		parent::output(
			array(
				'club' => $club,
				'club_list' => $clubList
			)
		);
	}
	
	public function user_list()
	{
		$longitude = $this->post_input('longitude', 0.0);
		$latitude = $this->post_input('latitude', 0.0);
		$gender = $this->post_input('gender');

		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('user_model');
		$this->load->model('chat_model');
		
		$longitude = floatval($longitude);
		$latitude = floatval($latitude);
		//log_info("lng:$longitude, lat:$latitude");
		
		$filters['is_enabled'] = 1;
		if ($gender == GENDER_MALE || $gender == GENDER_FEMALE) {
			$filters['gender'] = $gender;
		}
		if (!empty($this->user)) {
			$filters['id !='] = $this->user['id'];
			if ($longitude == 0) {
				$longitude = floatval($this->user['longitude']);
			}
			if ($latitude == 0) {
				$latitude = floatval($this->user['latitude']);
			}
		}
		$orders['distance'] = 'ASC';
		$orders['city'] = 'ASC';


        $q = $this->post_input('q');      
        $filters['nickname%'] = $q;   

		// get list count
		$totalCount = $this->user_model->getCount($filters);
		
		// get paged list
		$this->user_model->select("*, POW(longitude-$longitude, 2)+POW(latitude-$latitude, 2) AS distance");
		$data = $this->user_model->getList($filters, $orders, $page, $size);

		if ($page == 1) {
			$top_row = $this->user_model->get('200025');
		
			array_unshift($data, $top_row);
		}
		
		$userList = array();
		foreach ($data as $key=>$item) {
			$user = array();
			$user['id'] = $item['id'];
			$user['nickname'] = $item['nickname'];
			$user['gender'] = $item['gender'];
			$user['avatar_url'] = $item['avatar_url'];
			$user['city'] = $item['city'];
			$user['chat_user'] = $this->chat_model->getUsername($item['id']);
			
			// get distance
			$distance = getDistance($item['longitude'], $item['latitude'], $longitude, $latitude);
			if ($user['id'] == '200025') {
				$user['distance'] = '中维合众总部';
			} else if ($distance < 100) {
				$user['distance'] = '<100米';
			} else if ($distance < 1000) {
				$user['distance'] = round($distance, 2) . '米';
			} else {
				$user['distance'] = round($distance / 1000, 2) . '公里';
			}
			//$user['distance'] .= "(" . $item['distance'] . ")";
			$userList[] = $user;
		}

		// out data
		parent::output(
			array(
				'items' => $userList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function chat_user_avatar()
	{
		$chatUserId = $this->post_input('chat_user');
		if (empty($chatUserId)) {
			parent::output(99);
		}
		
		$userId = intval(str_replace(CHAT_USERNAME_PREFIX, "", $chatUserId));
		
		$this->load->model('user_model');
		$user = $this->user_model->get($userId);
		if (empty($userId)) {
			parent::output(199);
		}
		
		parent::output(
			array(
				'nickname' => $user['nickname'],
				'avatar' => $user['avatar_url']
			)
		);
	}
}
