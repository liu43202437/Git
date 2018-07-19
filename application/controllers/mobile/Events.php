<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
    
	public function competition_list($kind)
	{
		if (!in_array($kind, array('recent', 'past'))) {
			parent::output(100);
		}
		
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('event_model');
		
		$filters['is_show'] = 1;
		$filters['kind'] = EVENT_KIND_COMPETITION;
		if ($kind == 'recent') {
			//$filters['event_date >='] =  date('Y-m-1 00:00:00', strtotime("-6 month"));
			$filters['type'] = EVENT_COMPETITION_TYPE1;
		} else {
			//$filters['event_date <'] =  date('Y-m-1 00:00:00', strtotime("-6 month"));
			$filters['type'] = EVENT_COMPETITION_TYPE2;
		}
		$orders['event_date'] = 'DESC';

        $q = $this->post_input('q');      
        $filters['title%'] = $q;   

		// get list count
		$totalCount = $this->event_model->getCount($filters);
		
		// get paged list
		$data = $this->event_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['title'] = $item['title'];
			$rItem['subtitle'] = $item['subtitle'];
			$rItem['event_date'] = $item['event_date'];
			$rItem['location'] = $item['location'];
			$rItem['has_ticket'] = $item['has_ticket'];
			if ($key == 0) {
				$rItem['image'] = getFullUrl($item['image']);
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
	
	public function competition_info()
	{
		$itemId = $this->post_input('item_id');
		if (empty($itemId)) {
			parent::output(100);
		}
		if (empty($this->user)) {
			parent::output(101);
		}
		
		$this->load->model('event_model');
		$this->load->model('member_model');
		$this->load->model('chat_model');
		
		// get item
		$eventItem = $this->event_model->get($itemId);
		if (empty($eventItem)) {
			parent::output(199);
		}
		
		$eventItem['image'] = getFullUrl($eventItem['image']);
		$eventItem['video'] = getFullUrl($eventItem['video']);
		$eventItem['ticket_image'] = getFullUrl($eventItem['ticket_image']);
		$eventItem['ticket_pos_image'] = getFullUrl($eventItem['ticket_pos_image']);
		$eventItem['share_url'] = getPortalUrl($eventItem['id'], PORTAL_KIND_EVENT);
		
		// get counter part table
		$counterparts = $this->event_model->getCounterpartList($eventItem['id']);
		foreach ($counterparts as $key=>$item) {
			$aPlayer = $this->member_model->get($item['a_player_id']);
			$bPlayer = $this->member_model->get($item['b_player_id']);
			$counterparts[$key]['a_player_name'] = $aPlayer['name'];
			$counterparts[$key]['a_player_enname'] = $aPlayer['en_name'];
			$counterparts[$key]['a_player_image'] = getFullUrl($aPlayer['image']);
			$counterparts[$key]['b_player_name'] = $bPlayer['name'];
			$counterparts[$key]['b_player_enname'] = $bPlayer['en_name'];
			$counterparts[$key]['b_player_image'] = getFullUrl($bPlayer['image']);
		}		
		$eventItem['counterparts'] = $counterparts;
		if ($eventItem['has_ticket']) {
			$eventItem['ticket_prices'] = $this->event_model->getTicketPriceList($eventItem['id']);
		}
		
		// add hit count
		$this->event_model->increaseHits($itemId);

		// out data
		parent::output(
			array(
				'item' => $eventItem
			)
		);
	}
	
	public function join_chat_group()
	{
		$eventId = $this->post_input('item_id');
		
		if (empty($eventId)) {
			parent::output(100);
		}
		if (empty($this->user)) {
			parent::output(101);
		}
		
		$this->load->model('event_model');
		$this->load->model('chat_model');
		
		$eventItem = $this->event_model->get($eventId);
		if (empty($eventItem)) {
			parent::output(199);
		}
		
		// get group id
		$groupId = $this->chat_model->getGroupId($eventId);
		file_put_contents("aa.txt",$groupId);
		if ($groupId === false) {
			// group not exist - create new group
			$groupId = $this->chat_model->createGroup($eventId);
		}
		
		$result = $this->chat_model->joinGroup($groupId, $this->user['id']);
		if ($result === false) {
			parent::output(501);
		}
		
		// out data
		parent::output(
			array(
				'chat_group' => $groupId
			)
		);
	}
	
	public function quit_chat_group()
	{
		$eventId = $this->post_input('item_id');
		if (empty($eventId)) {
			parent::output(100);
		}
		if (empty($this->user)) {
			parent::output(101);
		}
		
		$this->load->model('chat_model');
		$groupId = $this->chat_model->getGroupId($eventId);
		$this->chat_model->quitGroup($groupId, $this->user['id']);
		
		parent::output(array());
	}
	
	public function competition_news()
	{
		$itemId = $this->post_input('item_id');
		if (empty($itemId)) {
			parent::output(100);
		}
		
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('event_model');
		$this->load->model('content_model');
		$this->load->model('comment_model');
		
		$filters['is_show'] = 1;
		$orders['content_date'] = 'DESC';

		// get list count
		$totalCount = $this->event_model->getContentCount($itemId, $filters);
		
		// get paged list
		$data = $this->event_model->getContentList($itemId, $filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['title'] = $item['title'];
			$rItem['thumb'] = getFullUrl($item['thumb']);
			$rItem['hits'] = $item['hits'];
			$rItem['content_date'] = $item['content_date'];
			$rItem['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $item['id']);

			if ($item['kind'] == CONTENT_KIND_ARTICLE) {
				$rItem['url'] = getPortalUrl($item['id'], PORTAL_KIND_CONTENT);
			} else if ($item['kind'] == CONTENT_KIND_VIDEO) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				//$rItem['video'] = getFullUrl($extra['video']);
				$rItem['duration'] = $extra['duration'];
			} elseif ($item['kind'] == CONTENT_KIND_GALLERY) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rItem['image1'] = getFullUrl($extra['image1']);
				$rItem['image2'] = getFullUrl($extra['image2']);
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
	
	public function match_calendar()
	{
		$yearMonth = $this->post_input('year_month');
		if (empty($yearMonth)) {
			$yearMonth =  nowYearMonth();
		}
		
		$this->load->model('event_model');
		
		$matchList = array();
		
		$year = date("Y", strtotime($yearMonth));
		$month = date("n", strtotime($yearMonth));
		$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for($i = 1; $i <= $days; $i++) {
        	$day = "$year-$month-$i";
			$filters['event_date >='] = d2bt($day);
			$filters['event_date <='] = d2et($day);
			$filters['is_show'] = 1;
			$filters['kind'] = EVENT_KIND_MATCH;
			$count = $this->event_model->getCount($filters);

			if (empty($count)) {
				$matchList[] = 0;
			} else {
				$matchList[] = $i;
			}
        }
        parent::output(
			array(
				'items' => $matchList
			)
		);
	}
	
	public function match_list()
	{
		$matchMonth = $this->post_input('match_month');
		$matchDate = $this->post_input('match_date');
		if (!empty($matchMonth)) {
			$startDate = month_start_time($matchMonth);
			$endDate = month_end_time($matchMonth);
		} else {
			$startDate = d2bt($matchDate);
			$endDate = d2et($matchDate);
		}

		$this->load->model('event_model');
		$this->load->model('member_model');
		
		$filters['is_show'] = 1;
		$filters['kind'] = EVENT_KIND_MATCH;
		$filters['event_date >='] = $startDate;
		$filters['event_date <='] = $endDate;
		$orders['event_date'] = 'ASC';

		// get all list
		$data = $this->event_model->getAll($filters, $orders);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['title'] = $item['title'];
			$rItem['event_date'] = $item['event_date'];
			$rItem['location'] = $item['location'];
			$rItem['image'] = getFullUrl($item['image']);
			$rItem['player_images'] = array();
			
			// get counter part table
			$counterparts = $this->event_model->getCounterpartList($item['id']);
			foreach ($counterparts as $key=>$part) {
				$aPlayer = $this->member_model->get($part['a_player_id']);
				$bPlayer = $this->member_model->get($part['b_player_id']);
				$rItem['player_images'][] = getFullUrl($aPlayer['image']);
				$rItem['player_images'][] = getFullUrl($bPlayer['image']);
				if (count($rItem['player_images']) >= 6) {
					break;
				}
			}		
			
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList,
				'count' => count($data)
			)
		);
	}
	
	public function organization_match_list()
	{
		$organizationId = $this->post_input('organization_id');
		if (empty($organizationId)) {
			parent::output(100);
		}

		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('event_model');
		$this->load->model('organization_model');
		$this->load->model('member_model');
		
		$organization = $this->organization_model->get($organizationId);
		if (empty($organization)) {
			parent::output(199);
		}
		
		$organization['logo'] = getFullUrl($organization['logo']);
		$organization['thumb'] = getFullUrl($organization['thumb']);
		
		$filters['is_show'] = 1;
		$filters['kind'] = EVENT_KIND_MATCH;
		$filters['organization_id'] = $organizationId;
		$orders['event_date'] = 'ASC';

		// get list count
		$totalCount = $this->event_model->getCount($filters);
		
		// get all list
		$data = $this->event_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['title'] = $item['title'];
			$rItem['event_date'] = $item['event_date'];
			$rItem['location'] = $item['location'];
			$rItem['image'] = getFullUrl($item['image']);
			
			// get counter part table
			$counterparts = $this->event_model->getCounterpartList($item['id']);
			foreach ($counterparts as $key=>$part) {
				$aPlayer = $this->member_model->get($part['a_player_id']);
				$bPlayer = $this->member_model->get($part['b_player_id']);
				$rItem['player_images'][] = getFullUrl($aPlayer['image']);
				$rItem['player_images'][] = getFullUrl($bPlayer['image']);
				if (count($rItem['player_images']) >= 6) {
					break;
				}
			}
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList,
				'organization' => $organization
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function match_info()
	{
		$itemId = $this->post_input('item_id');
		if (empty($itemId)) {
			parent::output(100);
		}
		
		$this->load->model('event_model');
		$this->load->model('member_model');
		
		// get item
		$eventItem = $this->event_model->get($itemId);
		if (empty($eventItem)) {
			parent::output(199);
		}
		
		$eventItem['image'] = getFullUrl($eventItem['image']);
		$eventItem['video'] = getFullUrl($eventItem['video']);
		$eventItem['ticket_image'] = getFullUrl($eventItem['ticket_image']);
		$eventItem['share_url'] = getPortalUrl($eventItem['id'], PORTAL_KIND_EVENT);
		
		// get counter part table
		$counterparts = $this->event_model->getCounterpartList($eventItem['id']);
		foreach ($counterparts as $key=>$item) {
			$aPlayer = $this->member_model->get($item['a_player_id']);
			$bPlayer = $this->member_model->get($item['b_player_id']);
			$counterparts[$key]['a_player_name'] = $aPlayer['name'];
			$counterparts[$key]['a_player_nickname'] = $aPlayer['nickname'];
			$counterparts[$key]['a_player_image'] = getFullUrl($aPlayer['image']);
			$counterparts[$key]['b_player_name'] = $bPlayer['name'];
			$counterparts[$key]['b_player_nickname'] = $bPlayer['nickname'];
			$counterparts[$key]['b_player_image'] = getFullUrl($bPlayer['image']);
		}		
		$eventItem['counterparts'] = $counterparts;

		// get match ticket info
		if ($eventItem['has_ticket']) {
			$eventItem['ticket_prices'] = $this->event_model->getTicketPriceList($eventItem['id']);
		}
		
		// add hit count
		$this->event_model->increaseHits($itemId);

		// out data
		parent::output(
			array(
				'item' => $eventItem
			)
		);
	}
	
	public function buy_ticket()
	{
		if (empty($this->user)) {
			parent::output(101);
		}

		$ticketId = $this->post_input('ticket_id');
		$itemCount = $this->post_input('count', 1);
		$shippingType = $this->post_input('shipping_type');
		
		if (empty($ticketId)) {
			parent::output(100);
		}
		
		$this->load->model('event_model');
		$this->load->model('order_model');
		$this->load->model('user_model');

		$ticket = $this->event_model->getTicketPrice($ticketId);
		if ($ticket['count'] <= 0) {
			parent::output(201);
		}
		
		$event = $this->event_model->get($ticket['event_id']);

		$order = array();
		$order['user_id'] = $this->user['id'];
		$order['kind'] = ORDER_KIND_TICKET;
		$order['item_id'] = $event['id'];
		$order['item_count'] = $itemCount;
		$order['item_money'] = $ticket['price'];
		$order['total_money'] = $ticket['price'] * $itemCount;
		$order['pay_point'] = 0;
		$order['gain_point'] = 0;
		$order['shipping_type'] = $shippingType;
		$order['shipping_fee'] = 0;
		$order['description'] = '比赛 - ' . $event['title'];
		$order['pay_status'] = PAY_STATUS_UNPAID;
		$order['order_status'] = ORDER_STATUS_PROCESSING;
		$order['shipping_status'] = SHIP_STATUS_UNSHIPPED;
		
		$order['ticket_id'] = $ticketId;
		if ($shippingType == 1) {
			$order['consignee'] = $this->user['consignee'];
			$order['phone'] = $this->user['phone'];
			$order['area'] = $this->user['area'];
			$order['address'] = $this->user['address'];
			$order['shipping_fee'] = $this->config->item('default_shipping_fee');
			$order['total_money'] += intval($this->config->item('default_shipping_fee'));
		} else {
			$order['ticket_take_code'] = gen_rand_str(6);
		}
		
		$orderSn = $this->order_model->insert($order);
		if ($orderSn == false) {
			parent::output(102);
		}
		$order['sn'] = $orderSn;
		
		require_once APPPATH . "third_party/WxPayPubHelper/weixin.php";
		
		$wxpay_config = $this->config->item('wxpay_config');
		$prepay = get_prepay($wxpay_config, $order);
		if (!is_int($prepay)) {
			//$this->event_model->updateTicketPrice($ticketId, array('count' => $ticket['count'] - $itemCount));
		}
		
		parent::output($prepay);
	} 
}
