<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
    
	public function send_auth_code()
	{
		$test = false;
		
		$mobile = $this->post_input('mobile');
		$deviceId = $this->post_input('device_udid');
		if (empty($mobile)) {
			parent::output(2);
		}
		if (!preg_match('/^([0-9]{10,12})$/', $mobile)) {
			parent::output(2);
		}
		
		if ($mobile == '13716323282') {
			parent::output(array());
		}
		
		$this->load->model("authcode_model");
		
		// check already send?
		$authInfo = $this->authcode_model->getInfoByTarget($mobile);
		if (!empty($authInfo)) {
			$create = strtotime($authInfo['create_date']);
			if (time() - $create < 60) {
				parent::output(107);
			}
		}
		
		// check for auth code send limit
		$startTime = today_start();
		$endTime = today_end();
		// 1. device limit
		$filters['device_udid'] = $deviceId;
		$filters['create_date >='] = $startTime;
		$filters['create_date <='] = $endTime;
		$count = $this->authcode_model->getCount($filters);
		if (!$test && $count >= 3) {
			parent::output(109);
		}
		
		// 2. target limit
		$filters = array();
		$filters['target'] = $mobile;
		$filters['create_date >='] = $startTime;
		$filters['create_date <='] = $endTime;
		$count = $this->authcode_model->getCount($filters);
		if (!$test && $count >= 4) {
			parent::output(109);
		}

        $epid = $this->config->item('sms_3rdparty_epid');
        $username = $this->config->item('sms_3rdparty_username');
        $password = $this->config->item('sms_3rdparty_password');
        $authCode = gen_rand_num($this->config->item('auth_code_length'));
        
        $minutes = $this->config->item('auth_code_expire');
        //$message = GBK('尊敬的用户，您本次的手机验证码为：'.$authCode.'，'.$minutes.'分钟内有效。');
	$message = urlencode(GBK('【中维合众】尊敬的用户，您本次的手机验证码为：'.$authCode.'，'.$minutes. '分钟内有效。'));
                
        if (!$test) {
	        $url = 'http://114.255.71.158:8061/?epid=' . $epid .
        		'&username=' . $username .
        		'&password=' . $password . 
        		'&message=' . $message . 
        		'&phone=' . $mobile . 
        		'&linkid=&subcode=01';

	        $ch = curl_init();                              //initialize curl handle
	        curl_setopt($ch, CURLOPT_URL, $url);            //set the url
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return as a variable
	        $response = curl_exec($ch);
	        curl_close($ch);                                //close the curl handle
		} else {
			$authCode = '123456';
			$response = '00';
		}
        
        if($response == '00') {
            //$this->authcode_model->deleteByTarget($mobile);
            $id = $this->authcode_model->insert($mobile, $authCode, $minutes, $deviceId);
            if (empty($id)) {
				parent::output(99);
            }
		} else {
			log_info(json_encode($response));
			parent::output(104);
		}
		
		parent::output(array());
	}
	
	public function check_auth_code()
	{
		$mobile = $this->post_input('mobile');
		$authCode = $this->post_input('auth_code');
		
		if (empty($mobile) || empty($authCode)) {
			parent::output(100);
		}
		
		// verify auth code
		$this->load->model("authcode_model");
		$authInfo = $this->authcode_model->getInfoByTarget($mobile);
		if (empty($authInfo)) {
			parent::output(105);
		}
		if ($authInfo['code'] != $authCode) {
			parent::output(105);
		}
		if ($authInfo['expire_date'] != null && strtotime($authInfo['expire_date']) <= now()) {
			parent::output(106);
		}
		
		$this->authcode_model->deleteByTarget($mobile);
		parent::output(array());
	}
	
	public function comment_list()
	{
		$itemKind = $this->post_input('item_kind');
		$itemId = $this->post_input('item_id');
		if (empty($itemKind) || empty($itemId)) {
			parent::output(100);
		}
		
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('user_model');
		$this->load->model('comment_model');
		$this->load->model('like_model');
		
		$filters['target_kind'] = $itemKind;
		$filters['target_id'] = $itemId;
		$filters['status'] = COMMENT_STATUS_PASSED;
		$orders['create_date'] = 'DESC';

		// get list count
		$totalCount = $this->comment_model->getCount($filters);
		
		// get paged list
		$data = $this->comment_model->getList($filters, $orders, $page, $size);
		$now = time();
		foreach ($data as $key=>$item) {
			// change time format
			$data[$key]['create_time'] = timespan_format($item['create_date'], $now);
			$data[$key]['like_count'] = $this->like_model->getLikeCount(LIKE_ITEM_KIND_COMMENT, $item['id']);
			$data[$key]['is_like_item'] = 0;
			if (!empty($this->user)) {
				$data[$key]['is_like_item'] = $this->like_model->isLikeItem($this->user['id'], LIKE_ITEM_KIND_COMMENT, $item['id']);
			}
			// get user info
			$u = $this->user_model->get($item['user_id']);
			$data[$key]['username'] = $u['nickname'] ? $u['nickname'] : $u['username'];
			$data[$key]['user_avatar'] = $u['avatar_url'];
		}

		// out data
		parent::output(
			array(
				'items' => $data
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function add_comment()
	{
		$itemKind = $this->post_input('item_kind');
		$itemId = $this->post_input('item_id');
		$content = $this->post_input('content');
		if (empty($this->user) || empty($itemKind) || empty($itemId) || empty($content)) {
			parent::output(100);
		}
		
		$this->load->model('restrictvocab_model');
		$content = $this->restrictvocab_model->getFilteredContent($content);
		
		$this->load->model('comment_model');
		$id = $this->comment_model->insert($this->user['id'], $itemKind, $itemId, ellipseStr($content, 20), $content);
		if (empty($id)) {
			parent::output(99);
		}
		$comment = $this->comment_model->get($id);
		$comment['create_time'] = timespan_format($comment['create_date'], now());
		$comment['like_count'] = 0;
		$comment['is_like_item'] = 0;
		// out data
		parent::output(
			array(
				'comment' => $comment
			)
		);
	}
	
	public function set_like()
	{
		$itemKind = $this->post_input('item_kind', LIKE_ITEM_KIND_COMMENT);
		$itemId = $this->post_input('item_id');
		if (empty($this->user) || empty($itemId)) {
			parent::output(100);
		}
		
		$this->load->model('like_model');
		if ($this->like_model->isLikeItem($this->user['id'], $itemKind, $itemId)) {
			if ($itemKind == LIKE_ITEM_KIND_COMMENT) {
				$this->like_model->deleteLike($this->user['id'], $itemKind, $itemId);
			}
		} else {
			$this->like_model->insert($this->user['id'], $itemKind, $itemId);
		}
		parent::output(array());
	}
	
	public function region_info()
	{
		header("Content-Type:text/xml");
		//$this->output->set_content_type('text/xml', 'UTF-8');
		
		$this->load->model('area_model');
		$provinceList = $this->area_model->getProvinceList();
		
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<root>";
		foreach ($provinceList as &$province) {
			echo '<province name="' . $province['name'] . '" id="' . $province['id'] . '">';
			$cityList = $this->area_model->getCityList($province['id']);
			foreach ($cityList as &$city) {
				echo '<city name="' . $city['name'] . '" id="' . $city['id'] . '">';
				$districtList = $this->area_model->getDistrictList($city['id']);
				foreach ($districtList as $district) {
					echo '<district name="' . $district['name'] . '" id="' . $district['id'] . '" />';
				}
				$city['districts'] = $districtList;
				echo '</city>';
			}
			$province['cities'] = $cityList;
			echo '</province>';
		}
		echo "</root>";
		//echo json_encode($provinceList);
	}
	
	public function is_open_gift()
	{
		$this->load->model('config_model');
		$isOpenModel = $this->config_model->getValue('is_open_model');
		parent::output(
			array(
				'is_open_gift' => $isOpenModel
			)
		);
	}
	
	public function gift_list()
	{
		$this->load->model('gift_model');
		$filter['is_show'] = 1;
		$itemList = $this->gift_model->getAll($filter);
		foreach ($itemList as &$item) {
			$item['image'] = getFullUrl($item['image']);
			unset($item['create_date']);
			unset($item['is_show']);
			unset($item['orders']);
		}
		parent::output(
			array(
				'items' => $itemList
			)
		);
	}

	public function give_gift()
	{
		if (empty($this->user)) {
			parent::output(101);
		}

		$contentId = $this->post_input('content_id');
		$giftId = $this->post_input('gift_id');
		$count = $this->post_input('count', 1);
		if (empty($giftId)) {
			parent::output(100);
		}
		
		$this->load->model('order_model');
		$this->load->model('user_model');
		$this->load->model('userrank_model');
		$this->load->model('gift_model');
		$this->load->model('chat_model');
		$this->load->model('content_model');
		
		$gift = $this->gift_model->get($giftId);
		if (empty($gift)) {
			parent::output(199);
		}
		
		$contentItem = $this->content_model->get($contentId);
		if (empty($contentItem)) {
			parent::output(199);
		}
		
		$payPoint = intval($gift['price']) * $count;
		if ($payPoint > $this->user['point']) {
			parent::output(121);
		}

		$order = array();
		$order['user_id'] = $this->user['id'];
		$order['kind'] = ORDER_KIND_GIFT;
		$order['item_id'] = $giftId;
		$order['item_count'] = $count;
		$order['item_money'] = 0;
		$order['total_money'] = 0;
		$order['pay_point'] = $payPoint;
		$order['gain_point'] = 0;
		$order['description'] = '礼物 - ' . $gift['name'];
		$order['pay_status'] = PAY_STATUS_PAID;
		$order['shipping_status'] = SHIP_STATUS_SHIPPED;
		$order['order_status'] = ORDER_STATUS_SUCCEED;
		
		$orderSn = $this->order_model->insert($order);
		if ($orderSn == false) {
			parent::output(102);
		}
		
		$data['exp'] = $this->user['exp'] + intval($gift['exp']) * $count;
		$data['point'] = $this->user['point'] - $payPoint;
		$rank = $this->userrank_model->getByExp($data['exp']);
		if (!empty($rank)) {
			if (intval($rank['rank']) > intval($this->user['rank'])) {
				$data['rank'] = $rank['rank'];
			}
		}
		$this->user_model->update($this->user['id'], $data);
		
		$cData['point'] = intval($contentItem['point']) + $payPoint;
		$this->content_model->updateExtra($contentItem['id'], $contentItem['kind'], $cData['point']);
		
		/*$message = $this->user['nickname'] . "送给" . $count . "个" . $gift['name'];
		$this->chat_model->sendGroupMessage($eventId, $message);*/
		
		parent::output(array());
	}
}
