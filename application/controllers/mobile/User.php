<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
    }
    
    public function addExtraInfo($user)
	{
		$this->load->model('chat_model');
		if (isset($user['id'])) {
			$user['chat_user'] = $this->chat_model->getUsername($user['id']);
			$user['chat_pwd'] = $this->chat_model->getPassword($user['id']);
		}
		return $user;
	}
    
	public function signin()
	{
		$username = $this->post_input('username');
		$authCode = $this->post_input('auth_code');
		$deviceType = $this->post_input('device_type');
		$deviceUdid = $this->post_input('device_udid');
		$longitude = $this->post_input('longitude');
		$latitude = $this->post_input('latitude');
		$appVersion = $this->post_input('app_version');
		
		if (empty($username) || empty($authCode) || empty($deviceType)) {
			parent::output(100);
		}

		$this->load->model("authcode_model");
		$this->load->model('session_model');
		$this->load->model("chat_model");

//		if ($username != '13716323282' || $authCode != '336699') {
//			// auth code check
//			$authInfo = $this->authcode_model->getInfoByTarget($username);
//			if (empty($authInfo)) {
//				parent::output(105);
//			}
//			if ($authInfo['code'] != $authCode) {
//				parent::output(105);
//			}
//			if ($authInfo['expire_date'] != null && strtotime($authInfo['expire_date']) <= now()) {
//				parent::output(106);
//			}
//			$this->authcode_model->deleteByTarget($username);
//		}


        $codeArray = array(8954, 3562, 9438, 2234, 5982);

        if (!in_array($authCode, $codeArray)) {
            parent::output(105);
        }


		// signin check		
		$userInfo = $this->user_model->getInfoByName($username);
		if (empty($userInfo)) {
			// register new user
			
			$nickname = $username;
			if (strlen($nickname) > 9) {
				$nickname = substr($nickname, 0, 3) . '****' . substr($nickname, 7);
			}
			$userId = $this->user_model->insert($username, $nickname, $deviceType, $deviceUdid);
			if (empty($userId)) {
				parent::output(12);
			}
			
			// register for chat user
			$this->chat_model->register($userId, $nickname, base_url() . $this->config->item('default_avatar'));
		} else {
			// login

            $userId = $userInfo['id'];
            
			// user has disabled		
			if (!$userInfo['is_enabled']) {
				parent::output(7);
			}
			
			// check if login phone is the same phone
			if (empty($deviceUdid)) {
				if ($deviceType != $userInfo['device_type'] || $deviceUdid != $userInfo['device_udid']) {
					// log in another phone
					// send push notification to old phone for log out and update device info
					$this->load->model('message_model');
					$id = $this->message_model->insert('换手机登录通知', '用户登录通知', 0, RECEIVER_TYPE_SINGLE, $userInfo['id'], $userInfo['username']);
					$this->message_model->send($id);
					
					$data['device_type'] = $deviceType;
					$data['device_udid'] = $deviceUdid;
				}
			}
			
			$chatUserInfo = $this->chat_model->get($userId);
			if (!$chatUserInfo) {
				$this->chat_model->register($userId, $userInfo['nickname'], $userInfo['avatar_url']);
			} else {
				if ($chatUserInfo->nick != $userInfo['nickname']) {
					$this->chat_model->updateNickname($userId, $userInfo['nickname']);
				}
				if ($chatUserInfo->icon_url != $userInfo['avatar_url']) {
					$this->chat_model->updateAvatar($userId, $userInfo['avatar_url']);
				}
			}
		}

		// update user data info
		$data['login_date'] = now();
		if (!empty($longitude)) {
			$data['longitude'] = $longitude;
		}
		if (!empty($latitude)) {
			$data['latitude'] = $latitude;
		}
		if (!empty($longitude) && !empty($latitude)) {
			try {
				$addrInfo = getAddress($this->config->item('baidu_map_js_appkey'), $longitude, $latitude);
				if (!empty($addrInfo)) {
					$data['city'] = $addrInfo['city'];
				}
			} catch (Exception $e) {}
		}
		if (!empty($appVersion)) {
			$data['app_version'] = $appVersion;
		}
		if (!$this->user_model->update($userId, $data)) {
			parent::output(99);
		}
		$userInfo = $this->user_model->get($userId);
		$userInfo = $this->addExtraInfo($userInfo);
		
		// create new session
		$this->session_model->deleteByUser($userId);
		$id = $this->session_model->insert($userId);
		if (empty($id)) {
			parent::output(12);
		}
		$session = $this->session_model->get($id);
		
		// output result
		$out = array(
			'session' => array(
				'sid' => $session['session_id'],
				'uid' => $userInfo['id']
			),
			'user' => $userInfo
		);
		parent::output($out);
	}
	
	public function signin_weixin()
	{
		$weixin = $this->post_input('unionid');
		$nickname = $this->post_input('nickname');
		$avatar_url = $this->post_input('avatar_url');
		$deviceType = $this->post_input('device_type');
		$deviceUdid = $this->post_input('device_udid');
		$longitude = $this->post_input('longitude');
		$latitude = $this->post_input('latitude');
		$appVersion = $this->post_input('app_version');
		
		if (empty($weixin) || empty($nickname) || empty($deviceType)) {
			parent::output(100);
		}
		if (empty($avatar_url)) {
			$avatar_url = base_url() . $this->config->item('default_avatar');
		}
		
		$this->load->model('session_model');
		$this->load->model('chat_model');
		
		// weixin informations
		$gender = $this->post_input('gender');
		$city = $this->post_input('city');

		// signin check		
		$userInfo = $this->user_model->getInfoByWeixin($weixin);
		if (empty($userInfo)) {
            // register new user

			// check nickname
			$n = $nickname;
			while ($u = $this->user_model->getInfoByNickname($nickname)) {
				$nickname = $n . '_' . nowtime2() . rand(0, 1000);
			}
			
			$username = $weixin;
			$userId = $this->user_model->insert($username, $nickname, $deviceType, $deviceUdid, $weixin);
			if (empty($userId)) {
				parent::output(12);
			}
			
			// register for chat user
			$this->chat_model->register($userId, $nickname, $avatar_url);
			$data['avatar_url'] = $avatar_url;
		} else {
			// login user
			
			$userId = $userInfo['id'];
			
			// user has disabled		
			if (!$userInfo['is_enabled']) {
				parent::output(7);
			}
			
			// check if log in phone is the same phone
			if (!empty($deviceUdid)) {
				if ($deviceType != $userInfo['device_type'] || $deviceUdid != $userInfo['device_udid']) {
					// log in another phone
					// send push notification to old phone for log out and update device info
					$this->load->model('message_model');
					$id = $this->message_model->insert('换手机登录通知', '用户登录通知', 0, RECEIVER_TYPE_SINGLE, $userInfo['id'], $userInfo['username']);
					$this->message_model->send($id);
					
					$data['device_type'] = $deviceType;
					$data['device_udid'] = $deviceUdid;
				}
			}
			
			$chatUserInfo = $this->chat_model->get($userId);
			if (!$chatUserInfo) {
				$this->chat_model->register($userId, $userInfo['nickname'], $userInfo['avatar_url']);
			} else {
				if ($chatUserInfo->nick != $userInfo['nickname']) {
					$this->chat_model->updateNickname($userId, $userInfo['nickname']);
				}
				if ($chatUserInfo->icon_url != $userInfo['avatar_url']) {
					$this->chat_model->updateAvatar($userId, $userInfo['avatar_url']);
				}
			}
		}

		// update user data info
		$data['login_date'] = now();
		if (!empty($longitude)) {
			$data['longitude'] = $longitude;
		}
		if (!empty($latitude)) {
			$data['latitude'] = $latitude;
		}
		if (!empty($appVersion)) {
			$data['app_version'] = $appVersion;
		}
		if (!empty($city)) {
			if (!endsWith($city, '市') && !endsWith($city, '县')) {
				$city .= '市';
			}
			$data['city'] = $city;
		} else {
			if (!empty($longitude) && !empty($latitude)) {
				try {
					$addrInfo = getAddress($this->config->item('baidu_map_js_appkey'), $longitude, $latitude);
					if (!empty($addrInfo)) {
						$data['city'] = $addrInfo['city'];
					}
				} catch (Exception $e) {}
			}
		}
		if (!empty($gender)) {
			$data['gender'] = $gender;
		}
		if (!$this->user_model->update($userId, $data)) {
			parent::output(99);
		}
		$userInfo = $this->user_model->get($userId);
		$userInfo = $this->addExtraInfo($userInfo);
		
		// create new session
		$this->session_model->deleteByUser($userId);
		$id = $this->session_model->insert($userId);
		if (empty($id)) {
			parent::output(12);
		}
		$session = $this->session_model->get($id);
		
		// output result
		$out = array(
			'session' => array(
				'sid' => $session['session_id'],
				'uid' => $userInfo['id']
			),
			'user' => $userInfo
		);
		parent::output($out);
	}
	
	public function get_info()
	{
		$user = $this->addExtraInfo($this->user);
		if (empty($user)) {
			parent::output(101);
		}
		
		$out = array(
			'user' => $user
		);
		parent::output($out);
	}
	
	public function upload_avatar()
	{
        $this->load->model('fileupload_model');

        $rslt = $this->fileupload_model->uploadImage('user_image', 'avatar/'.nowdate2().'/');
        if (is_int($rslt)) {
			parent::output($rslt);
        }

		$data['avatar_url'] = base_url() . $rslt;
		$this->user_model->update($this->user['id'], $data);
		
		$this->load->model('chat_model');
		$this->chat_model->updateAvatar($this->user['id'], $data['avatar_url']);
		
		parent::output($data);
	}
	
	public function change()
	{
		$data = array();
		$data['nickname'] = $this->post_input('nickname', null);
		$data['gender'] = $this->post_input('gender', null);
		$data['email'] = $this->post_input('email', null);
		//$data['longitude'] = $this->post_input('longitude', null);
		//$data['latitude'] = $this->post_input('latitude', null);
		$data['consignee'] = $this->post_input('consignee', null);
		$data['phone'] = $this->post_input('phone', null);
		$data['area'] = $this->post_input('area', null);
		$data['address'] = $this->post_input('address', null);
		$data['p_index'] = $this->post_input('p_index', null);
		$data['c_index'] = $this->post_input('c_index', null);
		$data['d_index'] = $this->post_input('d_index', null);

		if (empty($this->user)) {
			parent::output(101);
		}
		
		if (!empty($data['nickname'])) {
			$u = $this->user_model->getInfoByNickname($data['nickname']);
			if (!empty($u)) {
				parent::output(11);
			}
			
			$this->load->model('chat_model');
			$this->chat_model->updateNickname($this->user['id'], $data['nickname']);
		}
		
		$rData = array();
		foreach ($data as $key=>$value) {
			if ($value != null) {
				$rData[$key] = $value;
			}
		}
		if (empty($rData)) {
			parent::output(100);
		}
		$this->user_model->update($this->user['id'], $rData);
		
		$userInfo = $this->user_model->get($this->user['id']);
		
		$out = array(
			'user' => $userInfo
		);
		
		parent::output($out);
	}
	
	public function feedback()
	{
		$title = $this->post_input('title');
		$content = $this->post_input('content');
		$contact = $this->post_input('contact');
		if (empty($content) || empty($contact)) {
			parent::output(100);
		}
		
		if (empty($title)) {
			$title = substr($content, 0, 30);
			if (strlen($content) > 30) {
				$title .= " ...";
			}
		}
		
		$this->load->model('feedback_model');
		$userId = 0;
		if (!empty($this->user['id'])) {
			$userId = $this->user['id'];
			$userInfo = $this->user_model->get($userId);
			if (empty($contact)) {
				$contact = $userInfo['nickname'];
			}
		}
		$rslt = $this->feedback_model->insert($userId, $title, $content, $contact);
		if (empty($rslt)) {
			parent::output(99);
		}
		parent::output(array());
	}
	
	public function consume_history()
	{
		if (empty($this->user)) {
			parent::output(100);
		
		}
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('order_model');
		
		$filters['user_id'] = $this->user['id'];
		$filters['kind'] = array(ORDER_KIND_GIFT, ORDER_KIND_BUYPOINT, ORDER_KIND_MANUALPOINT, ORDER_KIND_YUNJIFEN);
		$filters['order_status'] = ORDER_STATUS_SUCCEED;
		
		// get list count
		$totalCount = $this->order_model->getCount($filters);
		
		// get paged list
		$data = $this->order_model->getList($filters, null, $page, $size);
		$itemList = array();
		foreach ($data as $item) {
			$rItem['id'] = $item['id'];
			$rItem['description'] = $item['description'];
			$rItem['create_date'] = $item['create_date'];
			$rItem['point'] = 0;
			if ($item['gain_point'] > 0) {
				$rItem['point'] = $item['gain_point'];
			} else {
				$rItem['point'] = "-" . $item['pay_point'];
			}
			
			$itemList[] = $rItem;
		}
		
		// out data
		parent::output(
			array(
				'items' => $itemList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function order_list()
	{
		if (empty($this->user)) {
			parent::output(100);
		}
		
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('order_model');
		$this->load->model('event_model');
		
		$filters['user_id'] = $this->user['id'];
		//$filters['kind'] = array(ORDER_KIND_TICKET, ORDER_KIND_YUNJIFEN);
		$filters['kind'] = ORDER_KIND_TICKET;
		$filters['pay_status !='] = PAY_STATUS_UNPAID;
		$orders['create_date'] = 'DESC';

		// get list count
		$totalCount = $this->order_model->getCount($filters);
		
		// get paged list
		$data = $this->order_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $item) {
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['item_money'] = $item['item_money'];
			$rItem['item_count'] = $item['item_count'];
			$rItem['total_money'] = $item['total_money'];
			$rItem['pay_point'] = $item['pay_point'];
			$rItem['gain_point'] = $item['gain_point'];
			$rItem['shipping_type'] = $item['shipping_type'];
			
			if ($item['kind'] == ORDER_KIND_TICKET) {
				$event = $this->event_model->get($item['item_id']);
				$rItem['event_title'] = $event['title'];
				$rItem['event_date'] = $event['event_date'];
				$rItem['event_location'] = $event['location'];
				$rItem['ticket_image'] = getFullUrl($event['ticket_image']);
				$rItem['ticket_note'] = $event['ticket_note'];
				$rItem['ticket_take_desc'] = $event['ticket_take_desc'];
			}
			
			$rItem['status'] = 0;
			if ($item['order_status'] == ORDER_STATUS_SUCCEED) {
				$rItem['status'] = 1;
			} else if ($item['order_status'] == ORDER_STATUS_FAILED) {
				$rItem['status'] = 2;
			} else if ($item['pay_status'] == PAY_STATUS_UNPAID) {
				$rItem['status'] = 3;
			} else if ($item['shipping_status'] == SHIP_STATUS_UNSHIPPED) {
				$rItem['status'] = 4;
			} else if ($item['shipping_status'] == SHIP_STATUS_SHIPPED) {
				$rItem['status'] = 5;
			}
			
			$itemList[] = $rItem;
		}
		
		// out data
		parent::output(
			array(
				'items' => $itemList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function order_info()
	{
		if (empty($this->user)) {
			parent::output(101);
		}
		
		$orderId = $this->post_input('order_id');
		if (empty($orderId)) {
			parent::output(100);
		}
		
		$this->load->model('order_model');
		$this->load->model('event_model');
		
		$order = $this->order_model->get($orderId);
		
		if ($order['kind'] == ORDER_KIND_TICKET) {
			$event = $this->event_model->get($order['item_id']);
			$order['event_title'] = $event['title'];
			$order['event_date'] = $event['event_date'];
			$order['event_location'] = $event['location'];
			$order['ticket_image'] = getFullUrl($event['ticket_image']);
			$order['ticket_note'] = $event['ticket_note'];
			$order['ticket_take_desc'] = $event['ticket_take_desc'];
		}
		
		$order['status'] = 0;
		if ($order['order_status'] == ORDER_STATUS_SUCCEED) {
			$order['status'] = 1;
		} else if ($order['order_status'] == ORDER_STATUS_FAILED) {
			$order['status'] = 2;
		} else if ($order['pay_status'] == PAY_STATUS_UNPAID) {
			$order['status'] = 3;
		} else if ($order['shipping_status'] == SHIP_STATUS_UNSHIPPED) {
			$order['status'] = 4;
		} else if ($order['shipping_status'] == SHIP_STATUS_SHIPPED) {
			$order['status'] = 5;
		}
		
		// out data
		parent::output(
			array(
				'order' => $order
			)
		);
	}
	
	public function confirm_receive()
	{
		if (empty($this->user)) {
			parent::output(101);
		}
		
		$orderId = $this->post_input('order_id');
		if (empty($orderId)) {
			parent::output(100);
		}
		
		$this->load->model('order_model');
		$this->load->model('event_model');
		
		$order = $this->order_model->get($orderId);
		if (empty($order)) {
			parent::output(100);
		}
		
		$data['order_status'] = ORDER_STATUS_SUCCEED;
		$data['proceed_date'] = now();
		$this->order_model->update($order['id'], $data);
		
		// out data
		parent::output(array());
	}
	
	public function enroll_info()
	{
		$kind = $this->post_input('kind');
		if (empty($kind)) {
			parent::output(100);
		}
		$challengeId = null;
		if ($kind == AUDIT_KIND_CHALLENGE) {
			$challengeId = $this->post_input('challenge_id');
			if (empty($challengeId)) {
				parent::output(100);
			}
			
			$this->load->model('challenge_model');
			$challenge = $this->challenge_model->get($challengeId);
			$challenge['image'] = getFullUrl($challenge['image']);
			$data['challenge'] = $challenge;
		}
		
		$this->load->model('auditconfig_model');
		$configs = $this->auditconfig_model->getConfig($kind, $challengeId);
		foreach ($configs as $key=>$item) {
			if ($item['value_type'] == 'select') {
				$configs[$key]['values'] = explode("|", $item['values']);
			}
		}
		
		$data['items'] = $configs;
		parent::output($data); 
	}
	
	public function do_enroll()
	{
		$kind = $this->post_input('kind');
		if (empty($kind)) {
			parent::output(100);
		}
		$challengeId = null;
		if ($kind == AUDIT_KIND_CHALLENGE) {
			$data['challenge_id'] = $this->post_input('challenge_id');
			if (empty($data['challenge_id'])) {
				parent::output(100);
			}
			$challengeId = $data['challenge_id'];
		}
		
		log_info(json_encode($this->posts));

		$this->load->model('fileupload_model');
		
		// general information
		/*$data['name'] = $this->post_input('name');
		$data['mobile'] = $this->post_input('mobile');
		$data['gender'] = $this->post_input('gender');
		$data['birthday'] = $this->post_input('birthday');*/

		$this->load->model('auditconfig_model');
		$configs = $this->auditconfig_model->getConfig($kind, $challengeId);
		
		for ($i = 1; $i <= 20; $i++) {
			$key = 'attribute'.$i;
			$data[$key] = $this->post_input($key);

			// if file upload
			if (startsWith($data[$key], 'filedata')) {
				$rslt = $this->fileupload_model->uploadImage($data[$key], 'image/'.nowdate2().'/');
				if (is_int($data[$key])) {
					parent::output($rslt);
				}
				$data[$key] = $rslt;
			}

			// attribute value template :  value|label|target
			foreach ($configs as $config) {
				if ($config['attr_name'] == $key) {
					$data[$key] = $data[$key] . '|' . $config['attr_label'] . '|' . $config['target_field'];
					break;
				}
			}
		}

		$this->load->model('audit_model');
		$id = $this->audit_model->insert($kind, $data);
		if (empty($id)) {
			parent::output(99);
		}
		
		// out data
		parent::output(array());
	}
	
	public function point_prices()
	{
		$this->load->model('config_model');
		$pointPrices = json_decode($this->config_model->getValue('point_prices'), true);
		parent::output(array(
			'point_prices' => $pointPrices
		));
	}
	
	public function buy_point()
	{
		if (empty($this->user)) {
			parent::output(101);
		}

		$point = $this->post_input('point');
		
		if (empty($point)) {
			parent::output(100);
		}
		
		$this->load->model('config_model');
		$this->load->model('order_model');
		$this->load->model('user_model');

		$pointPrices = json_decode($this->config_model->getValue('point_prices'), true);
		$price = 0;
		if (!empty($pointPrices) && is_array($pointPrices)) {
			foreach ($pointPrices as $item) {
				if ($item['point'] == $point) {
					$price = $item['price'];
					break;
				}
			}
		}
		
		if ($price == 0) {
			parent::output(199);
		}

		$order = array();
		$order['user_id'] = $this->user['id'];
		$order['kind'] = ORDER_KIND_BUYPOINT;
		$order['item_id'] = 0;
		$order['item_count'] = 1;
		$order['item_money'] = $price;
		$order['total_money'] = $price;
		$order['pay_point'] = 0;
		$order['gain_point'] = $point;
		$order['description'] = '烟币 - 兑换烟币';
		$order['pay_status'] = PAY_STATUS_UNPAID;
		$order['shipping_status'] = SHIP_STATUS_UNSHIPPED;
		$order['order_status'] = ORDER_STATUS_PROCESSING;
		
		$orderSn = $this->order_model->insert($order);
		if ($orderSn == false) {
			parent::output(102);
		}
		$order['sn'] = $orderSn;
		
		require_once APPPATH . "third_party/WxPayPubHelper/weixin.php";
		
		$wxpay_config = $this->config->item('wxpay_config');
		$prepay = get_prepay($wxpay_config, $order);
		
		parent::output($prepay);
	}
	
	public function yunjifen_link()
	{
		if (empty($this->user)) {
			parent::output(101);
		}
		
		require_once (APPPATH . "third_party/yunjifen/api.php");
		$appkey = $this->config->item('yunjifen_appkey');
		$secret = $this->config->item('yunjifen_secret');
		
		$url = buildCreditAutoLoginRequest($appkey, $secret, $this->user['id'], $this->user['point']);

		parent::output(array(
			'url' => $url
		));
	}
	
	public function app_link()
	{
		$deviceType = $this->post_input('device_type');
		
		$this->load->model('config_model');
		if ($deviceType == DEVICE_TYPE_IPHONE) {
			$appLink = $this->config_model->getValue('iphone_download_url');
		} else {
			$appLink = $this->config_model->getValue('android_download_url');
		}
		
		parent::output(array(
			'url' => $appLink
		));
	}
	
	public function challenge_count()
	{
		$this->load->model('challenge_model');
		$filters['is_show'] = 1;
		$totalCount = $this->challenge_model->getCount($filters);
		parent::output(
			array(
				'count' => $totalCount
			)
		);
	}
	
	public function challenge_list()
	{
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('challenge_model');
		
		$filters['is_show'] = 1;

		// get list count
		$totalCount = $this->challenge_model->getCount($filters);
		
		// get paged list
		$data = $this->challenge_model->getList($filters, null, $page, $size);
		$itemList = array();
		foreach ($data as $item) {
			$rItem['id'] = $item['id'];
			$rItem['title'] = $item['title'];
			$rItem['image'] = getFullUrl($item['image']);
			$itemList[] = $rItem;
		}
		
		// out data
		parent::output(
			array(
				'items' => $itemList
			), 
			array(
				'total' => $totalCount,
				'count' => count($itemList),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
}
