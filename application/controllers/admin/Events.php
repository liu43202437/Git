<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/event/';
        $this->load->model('event_model');
    }

    public function lists($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$type = $this->get_input('type');
    	$location = $this->get_input('location');
    	$timeType = $this->get_input('time_type', 'event');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$title = $this->get_input('title');
    	
		$filters = array();
		$orders = array();

		$filters['kind'] = $kind;
		if ($kind == EVENT_KIND_COMPETITION) {
			if (!empty($type)) {
				$filters['type'] = $type;
			}
		}		
		if (!empty($location)) {
			$filters['location'] = $location;
		}
		if ($timeType == 'create') {
			$field = 'create_date';
		} else {
			$field = 'event_date';
		}
		if (!empty($startDate)) {
			$filters[$field . ' >='] = d2bt($startDate);
		}
		if (!empty($endDate)) {
			$filters[$field . ' <='] = d2et($endDate);
		}
		if (!empty($title)) {
			$filters['title%'] = $title;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		
		$this->load->model('order_model');
		    	
    	$totalCount = $this->event_model->getCount($filters);
    	$rsltList = $this->event_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	foreach ($rsltList as $key=>$item) {
			 $rsltList[$key]['ticket_orders'] = '';
			 if (!empty($item['ticket_title'])) {
			 	 $oFilter['kind'] = ORDER_KIND_TICKET;
			 	 $oFilter['item_id'] = $item['id'];
			 	 $oFilter['pay_status'] = PAY_STATUS_PAID;
				 $rsltList[$key]['ticket_orders'] = $this->order_model->getCountInfo($oFilter);
			 }
		}

    	$this->data['kind'] = $kind;
    	$this->data['type'] = $type;
    	$this->data['location'] = $location;
    	$this->data['timeType'] = $timeType;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	$this->data['title'] = $title;
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('events/add');
    	$this->assign_pager($totalCount);
    	$this->assign_message();
    	
		$this->load_view('list');
    }
    
    public function ajax_list()
    {
		$kind = $this->get_input('kind');
		$term = $this->get_input('term');
		if (!empty($kind)) {
			$filter['kind'] = $kind;
		}
		$filter['id, title%'] = $term;
		
		$rsltList = $this->event_model->getList($filter, null, 1, 10);
		$itemList = array();
		foreach ($rsltList as $key=>$item) {
			 $rItem['id'] = $item['id'];
			 $rItem['kind'] = $item['kind'];
			 $rItem['label'] = '[' . $rItem['id'] . '] ' . ellipseStr($item['title'], 20) ;
			 $itemList[] = $rItem;
		}
		echo json_encode($itemList);
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		foreach ($ids as $id) {
			$this->event_model->delete($id);
		}
		
		$this->add_log('删除活动', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function toggle_show()
    {
		$id = $this->post_input('id');
		$ids = $this->post_input('ids');
		if (empty($id) && empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		if (empty($ids)) {	// one item operation
			$data['is_show$'] = '1-is_show';
			$this->event_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->event_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->event_model->getEmptyRow();
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->event_model->get($id);

			$this->load->model('member_model');			
			$counterparts = $this->event_model->getCounterpartList($id);
			foreach ($counterparts as $key=>$item) {
				$aPlayer = $this->member_model->get($item['a_player_id']);
				$bPlayer = $this->member_model->get($item['b_player_id']);
				$counterparts[$key]['a_player'] = '[' . $item['a_player_id'] . '] ' . $aPlayer['name'];
				$counterparts[$key]['b_player'] = '[' . $item['b_player_id'] . '] ' . $bPlayer['name'];
				$counterparts[$key]['winner_name'] = '';
				if ($item['winner'] == 'a') {
					$counterparts[$key]['winner_name'] = $counterparts[$key]['a_player'];
				} else if ($item['winner'] == 'b') {
					$counterparts[$key]['winner_name'] = $counterparts[$key]['b_player'];
				}
			}
			$itemInfo['counterparts'] = $counterparts;
			$itemInfo['ticket_prices'] = $this->event_model->getTicketPriceList($id);				
		}
		
		$this->data['kind'] = $kind;
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$kind = $this->post_input('kind');
		$id = $this->post_input('id');
		$data['type'] = $this->post_input('type');
		$data['title'] = $this->post_input('title');
		$data['subtitle'] = $this->post_input('subtitle');
		$data['event_date'] = $this->post_input('event_date', now());
		$data['image'] = $this->post_input('image');
		$data['video'] = $this->post_input('video');
		$data['link'] = $this->post_input('link');
		$data['location'] = $this->post_input('location');
		$data['organization_id'] = $this->post_input('organization');
		$hasTicket = $this->post_input('has_ticket');
		$data['has_ticket'] = empty($hasTicket) ? 0 : 1;
		$data['ticket_title'] = $this->post_input('ticket_title');
		$data['ticket_image'] = $this->post_input('ticket_image', null);
		$data['ticket_pos_image'] = $this->post_input('ticket_pos_image', null);
		$data['ticket_note'] = $this->post_input('ticket_note');
		$data['ticket_take_desc'] = $this->post_input('ticket_take_desc');
		$data['longitude'] = $this->post_input('longitude');
		$data['latitude'] = $this->post_input('latitude');
		$counterparts = json_decode($this->post_input('counterparts', ''), true);
		$ticketprices = json_decode($this->post_input('ticket_prices', ''), true);
		$priceChanged = $this->post_input('price_changed', 0);
		
		if ($kind == EVENT_KIND_MATCH) {
			if (!empty($data['location'])) {
				try {
					$loc = getLocationFromAddr($this->config->item('baidu_map_js_appkey'), $data['location']);
					if ($loc != false) {
						$data['longitude'] = $loc['lng'];
						$data['latitude'] = $loc['lat'];
					}
				} catch (Exception $e) {
				}
			}
		}
		
		if (empty($id)) {
			// add new content
			$id = $this->event_model->insert(
					$kind, 
					$data['type'],
					$data['title'],
					$data['subtitle'],
					$data['event_date'],
					$data['image'],
					$data['video'],
					$data['link'],
					$data['location'],
					$data['organization_id'],
					$data['has_ticket'],
					$data['ticket_title'],
					$data['ticket_image'],
					$data['ticket_pos_image'],
					$data['ticket_note'],
					$data['ticket_take_desc']);
			
			$this->add_log('新增活动', $data);
		} else {
			$this->event_model->update($id, $data);
			
			$this->add_log('编辑活动', array('id'=>$id));
		}
		
		$this->event_model->deleteCounterpartByEvent($id);
		if (!empty($counterparts) && is_array($counterparts)) {
			foreach ($counterparts as $part) {
				$this->event_model->insertCounterpart($id, $part['a_player_id'], $part['b_player_id'], $part['winner'], $part['description']);
			}
		}
		
		if ($priceChanged) {
			$this->event_model->deleteTicketPriceByEvent($id);
			if (!empty($ticketprices) && is_array($ticketprices)) {
				foreach ($ticketprices as $price) {
					$color = (isset($price['color']) && !empty($price['color'])) ? $price['color'] : null;
					$this->event_model->insertTicketPrice($id, $price['name'], $price['price'], $price['count'], $color);
				}
			}
		}
		
		$this->success_redirect('events/lists/' . $kind);
	}
	
	public function ticket_orders()
	{
    	$eventId = $this->get_input('event_id');
    	$orderStatus = $this->get_input('order_status', '');
    	$timeType = $this->get_input('time_type', 'create');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$eventTitle = $this->get_input('event_title');
    	$userNickname = $this->get_input('user_nickname');

		$filters = array();
		$orders = array();

		$filters['kind'] = ORDER_KIND_TICKET;
		$filters['pay_status'] = PAY_STATUS_PAID;
		if (!empty($eventId)) {
			$filters['item_id'] = $eventId;
		}
		if ($orderStatus !== '') {
			$orderStatus = intval($orderStatus);
			$filters['order_status'] = $orderStatus;
		}
		if ($timeType == 'create') {
			$field = 'create_date';
		} else {
			$field = 'proceed_date';
		}
		if (!empty($startDate)) {
			$filters[$field . ' >='] = d2bt($startDate);
		}
		if (!empty($endDate)) {
			$filters[$field . ' <='] = d2et($endDate);
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		
		$this->load->model('order_model');
		$this->load->model('user_model');
		$this->load->model('event_model');
		
		if (!empty($eventTitle)) {
			$eFilter['title%'] = $eventTitle;
			$events = $this->event_model->getAll($eFilter);
			if (!empty($events)) {
				$eIds = array();
				foreach ($events as $e) {
					$eIds[] = $e['id'];
				}
				$filters['item_id'] = $eIds;
			} else {
				$filters['item_id'] = 0;
			}
		}
		if (!empty($userNickname)) {
			$uFilter['nickname%'] = $userNickname;
			$users = $this->user_model->getAll($uFilter);
			if (!empty($users)) {
				$uIds = array();
				foreach ($users as $u) {
					$uIds[] = $u['id'];
				}
				$filters['user_id'] = $uIds;
			} else {
				$filters['user_id'] = 0;
			}
		}
		
    	$totalCount = $this->order_model->getCount($filters);
    	$itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	
    	foreach ($itemList as $key=>$item) {
			$itemList[$key]['user'] = $this->user_model->get($item['user_id']);
			$itemList[$key]['event'] = $this->event_model->get($item['item_id']);
    	}
    	
    	$this->data['eventId'] = $eventId;
    	$this->data['event'] = $this->event_model->get($eventId);
    	$this->data['orderStatus'] = $orderStatus;
    	$this->data['timeType'] = $timeType;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	$this->data['eventTitle'] = $eventTitle;
    	$this->data['userNickname'] = $userNickname;
    	
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($totalCount);
    	$this->assign_message();
		$this->load_view('ticket_orders');
	}
	
	public function do_deliver()
    {
		$orderId = $this->post_input('order_id');
		$deliverSn = $this->post_input('deliver_sn');
		
		if (empty($orderId) && empty($deliverSn)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('order_model');
		$order = $this->order_model->get($orderId);
		if (empty($order)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$data['deliver_sn'] = $deliverSn;
		$data['deliver_date'] = now();
		$data['shipping_status'] = SHIP_STATUS_SHIPPED;
		$this->order_model->update($orderId, $data);		
		
		echo json_capsule(parent::success_message());
    } 
    
    public function cancel_order()
    {
		$orderId = $this->post_input('order_id');
		
		if (empty($orderId)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('order_model');
		$order = $this->order_model->get($orderId);
		if (empty($order)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$data['order_status'] = ORDER_STATUS_FAILED;
		$data['proceed_date'] = now();
		$this->order_model->update($orderId, $data);		
		
		echo json_capsule(parent::success_message());
    }
    
    public function complete_order()
    {
		$orderId = $this->post_input('order_id');
		
		if (empty($orderId)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('order_model');
		$order = $this->order_model->get($orderId);
		if (empty($order)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$data['order_status'] = ORDER_STATUS_SUCCEED;
		$data['shipping_status'] = SHIP_STATUS_SHIPPED;
		$data['proceed_date'] = now();
		$this->order_model->update($orderId, $data);		
		
		echo json_capsule(parent::success_message());
    }
}
