<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();

        $this->view_path = 'admin/user/';
        $this->load->model('user_model');
    }
    
    public function lists()
    {
    	$nickname = $this->get_input('nickname');
    	$gender = $this->get_input('gender');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$registerType = $this->get_input('register_type');
    	$isEnabled = $this->get_input('is_enabled', '');
    	$city = $this->get_input('city');
    	
		$filters = array();
		$orders = array();
		
		if (!empty($nickname)) {
			$filters['nickname%'] = $nickname;
		}
		if (!empty($gender)) {
			$filters['gender'] = $gender;
		}
		if (!empty($startDate)) {
			$filters['create_date >='] = d2bt($startDate);
		}
		if (!empty($endDate)) {
			$filters['create_date <='] = d2et($endDate);
		}
		if (!empty($registerType)) {
			if ($registerType == 'mobile') {
				$filters['weixin'] = null;
			} else {
				$filters['weixin !='] = null;
			}
		}
		if ($isEnabled !== '') {
			$isEnabled = intval($isEnabled);
			$filters['is_enabled'] = $isEnabled;
		}
		if (!empty($city)) {
			$filters['city'] = $city;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->user_model->getCount($filters);
    	$itemList = $this->user_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	
    	foreach ($itemList as $key=>$item) {
			
    	}
    	
    	$this->load->model('area_model');
		$this->data['cities'] = $this->area_model->getCityList();

    	$this->data['nickname'] = $nickname;
    	$this->data['gender'] = $gender;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	$this->data['registerType'] = $registerType;
    	$this->data['isEnabled'] = $isEnabled;
    	$this->data['city'] = $city;
    	
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($totalCount);
    	$this->assign_message();
		$this->load_view('list');
    }
    
    public function add_point()
    {
		$userId = $this->post_input('user_id');
		$point = $this->post_input('point');
		if (empty($userId) || empty($point)) {
			$data['message'] = parent::error_message();
			die(json_capsule($data));
		}
		$user = $this->user_model->get($userId);
		$data['point'] = intval($user['point']) + intval($point);
		$rslt = $this->user_model->update($userId, $data);
		if (empty($rslt)) {
			$data['message'] = parent::error_message();
			die(json_capsule($data));
		}
		
		$order = array();
		$order['user_id'] = $userId;
		$order['kind'] = ORDER_KIND_MANUALPOINT;
		$order['item_id'] = 0;
		$order['item_count'] = 1;
		$order['item_money'] = 0;
		$order['total_money'] = 0;
		$order['pay_point'] = 0;
		$order['gain_point'] = $point;
		$order['description'] = '烟币 - 手动增加烟币';
		$order['pay_status'] = PAY_STATUS_PAID;
		$order['shipping_status'] = SHIP_STATUS_SHIPPED;
		$order['order_status'] = ORDER_STATUS_SUCCEED;
		
		$this->load->model('order_model');
		$orderSn = $this->order_model->insert($order);
		
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('feedback_model');
		$this->load->model('session_model');
		
		foreach ($ids as $id) {
			$userInfo = $this->user_model->get($id);
			
			$this->session_model->deleteByUser($id);
			$this->feedback_model->deleteByUser($id);
			$this->user_model->delete($id);
		}
		
		$this->add_log('删除用户', $ids);

		echo json_capsule(parent::success_message());
    }
    
    public function toggle_enable()
    {
		$id = $this->post_input('id');
		$ids = $this->post_input('ids');
		if (empty($id) && empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		if (empty($ids)) {	// one item operation
			$data['is_enabled$'] = '1-is_enabled';
			$this->user_model->update($id, $data);

		} else {			// batch item operation
			$data['is_enabled'] = $this->post_input('is_enabled', 0);
			foreach ($ids as $id) {
				$this->user_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
		$id = $this->get_input('id');
		$userInfo = $this->user_model->get($id);
		if (empty($userInfo)) {
			$this->error_redirect('user/lists', '信息不正确！');
		}
			
		$this->data['itemInfo'] = $userInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
	public function order_list()
    {
    	$userId = $this->get_input('id');
    	if (empty($userId)) {
			$this->error_redirect('user/lists');
    	}
    	
    	$orderStatus = $this->get_input('order_status', '');
    	$timeType = $this->get_input('time_type', 'create');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');

		$filters = array();
		$orders = array();

		$filters['user_id'] = $userId;
		$filters['kind !='] = ORDER_KIND_MANUALPOINT;
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
		
    	$totalCount = $this->order_model->getCount($filters);
    	$itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	
    	foreach ($itemList as $key=>$item) {
			
    	}

    	$this->data['userId'] = $userId;
    	$this->data['orderStatus'] = $orderStatus;
    	$this->data['timeType'] = $timeType;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($totalCount);
    	$this->assign_message();
		$this->load_view('order_list');
    }
    
    public function consume_history()
    {
    	$userId = $this->get_input('id');
    	if (empty($userId)) {
			$this->error_redirect('user/lists');
    	}
    	
		$filters = array();
		$orders = array();

		$filters['user_id'] = $userId;
		$filters['kind'] = array(ORDER_KIND_YUNJIFEN, ORDER_KIND_TICKET, ORDER_KIND_BUYPOINT);
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		}
		
		$this->load->model('order_model');
		
    	$totalCount = $this->order_model->getCount($filters);
    	$itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	
    	foreach ($itemList as $key=>$item) {
			
    	}

    	$this->data['userId'] = $userId;
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($totalCount);
    	$this->assign_message();
		$this->load_view('consume_history');
    }
    
    public function point_history()
    {
    	$userId = $this->get_input('id');
    	if (empty($userId)) {
			$this->error_redirect('user/lists');
    	}
    	
		$filters = array();
		$orders = array();

		$filters['user_id'] = $userId;
		$filters['kind'] = array(ORDER_KIND_YUNJIFEN, ORDER_KIND_GIFT, ORDER_KIND_BUYPOINT, ORDER_KIND_MANUALPOINT);
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		}
		
		$this->load->model('order_model');
		
    	$totalCount = $this->order_model->getCount($filters);
    	$itemList = $this->order_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	
    	foreach ($itemList as $key=>$item) {
			
    	}

    	$this->data['userId'] = $userId;
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($totalCount);
    	$this->assign_message();
		$this->load_view('point_history');
    }
    
    public function ranks()
    {
    	$orders = array();
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		}
		
		$this->load->model('userrank_model');
		
    	$itemList = $this->userrank_model->getAll(null, $orders);
    	$count = count($itemList);
    	
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($count, $count);
    	$this->assign_message();
		$this->load_view('ranks');
    }
    
    public function edit_rank()
    {
		$id = $this->post_input('id');
		$data['name_male'] = $this->post_input('name_male');
		$data['name_female'] = $this->post_input('name_female');
		$data['rank'] = $this->post_input('rank');
		$data['min_exp'] = $this->post_input('min_exp');
		
		$this->load->model('userrank_model');
		$orgRank = $this->userrank_model->getByRank($data['rank']);
		if (!empty($orgRank) && $orgRank['id'] != $id) {
			$this->error_redirect('user/ranks', '等级不能重复使用！');
		}
		
		if (empty($id)) {
			$this->userrank_model->insert($data['name_male'], $data['name_female'], $data['rank'], $data['min_exp']);
		} else {
			$this->userrank_model->update($id, $data);
		}
		$this->success_redirect('user/ranks');
    }
    public function manager_lists()
    {
    	$nickname = $this->get_input('nickname');
    	$gender = $this->get_input('gender');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$registerType = $this->get_input('register_type');
    	$isEnabled = $this->get_input('is_enabled', '');
    	$city = $this->get_input('city');
    	
		$filters = array();
		$orders = array();
		
		if (!empty($nickname)) {
			$filters['nickname%'] = $nickname;
		}
		if (!empty($gender)) {
			$filters['gender'] = $gender;
		}
		if (!empty($startDate)) {
			$filters['create_date >='] = d2bt($startDate);
		}
		if (!empty($endDate)) {
			$filters['create_date <='] = d2et($endDate);
		}
		if (!empty($registerType)) {
			if ($registerType == 'mobile') {
				$filters['weixin'] = null;
			} else {
				$filters['weixin !='] = null;
			}
		}
		if ($isEnabled !== '') {
			$isEnabled = intval($isEnabled);
			$filters['is_enabled'] = $isEnabled;
		}
		if (!empty($city)) {
			$filters['city'] = $city;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->user_model->getCount($filters);
    	$itemList = $this->user_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	
    	foreach ($itemList as $key=>$item) {
			
    	}
    	
    	$this->load->model('area_model');
		$this->data['cities'] = $this->area_model->getCityList();

    	$this->data['nickname'] = $nickname;
    	$this->data['gender'] = $gender;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	$this->data['registerType'] = $registerType;
    	$this->data['isEnabled'] = $isEnabled;
    	$this->data['city'] = $city;
    	
    	$this->data['itemList'] = $itemList;
    	$this->assign_pager($totalCount);
    	$this->assign_message();
		$this->load_view('manager_list');
    }
}
