<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banner extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/banner/';
        $this->load->model('banner_model');
    }

    public function lists()
    {
    	$rsltList = $this->banner_model->getAll(array('banner_kind'=>BANNER_MAIN));
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('content/add');
    	$this->assign_message();
		$this->load_view('list');
    }

    public function lists2()
    {
    	$rsltList = $this->banner_model->getAll(array('banner_kind'=>BANNER_NEARBY));
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('content/add');
    	$this->assign_message();
		$this->load_view('list2');
    }
    
    public function change_order()
    {
    	$ids = $this->post_input('ids');
		if (empty($ids)) {
			die(json_capsule(parent::error_message()));
		}
		foreach ($ids as $key=>$id) {
			$data['orders'] = $key + 1;
			$this->banner_model->update($id, $data);
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		foreach ($ids as $id) {
			$this->banner_model->delete($id);
		}
		
		$this->add_log('删除Banner', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function toggle_show()
    {
		$id = $this->post_input('id');
		if (empty($id)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$data['is_show$'] = '1-is_show';
		$this->banner_model->update($id, $data);

		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
    	$this->load->model('area_model');
    	
    	$id = $this->get_input('id');
    	$kind = $this->get_input('kind');

		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->banner_model->getEmptyRow();
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->banner_model->get($id);
			if ($itemInfo['item_kind'] != BANNER_KIND_URL) {
				$itemInfo['item_info_label'] = '[' . $itemInfo['item_info'] . '] ';
				if ($itemInfo['item_kind'] == BANNER_KIND_EVENT) {
					$this->load->model('event_model');
					$e = $this->event_model->get($itemInfo['item_info']);
					$itemInfo['item_info_label'] .= ellipseStr($e['title'], 20);
				} else if ($itemInfo['item_kind'] == BANNER_KIND_MEMBER) {
					$this->load->model('member_model');
					$e = $this->member_model->get($itemInfo['item_info']);
					$itemInfo['item_info_label'] .= $e['name'];
				} else {
					$this->load->model('content_model');
					$e = $this->content_model->get($itemInfo['item_info']);
					$itemInfo['item_info_label'] .= ellipseStr($e['title'], 15);
				}
			}
			$kind = $itemInfo['banner_kind'];

			$itemInfo['areaLimits'] = $this->area_model->getLimitList(AREA_LIMIT_KIND_BANNER, $id, true);
		}
		
		if (empty($itemInfo['start_time']) || empty($itemInfo['end_time'])) {
			$itemInfo['start_hour'] = null;
			$itemInfo['start_minute'] = null;
			$itemInfo['end_hour'] = null;
			$itemInfo['end_minute'] = null;
		} else {
			$itemInfo['start_hour'] = date("G", strtotime($itemInfo['start_time']));
			$itemInfo['start_minute'] = date("i", strtotime($itemInfo['start_time']));
			$itemInfo['end_hour'] = date("G", strtotime($itemInfo['end_time']));
			$itemInfo['end_minute'] = date("i", strtotime($itemInfo['end_time']));
		}

		if (empty($itemInfo['is_open_date']) || $itemInfo['is_open_date'] == 0) {
			$itemInfo['is_open_date'] = 0;
			$itemInfo['open_date'] = null;
			$itemInfo['open_hour'] = null;
			$itemInfo['open_minute'] = null;
			$itemInfo['open_second'] = null;
		} else {
			$itemInfo['is_open_date'] = 1;
			if (!empty($itemInfo['open_date'])) {
				$itemInfo['open_hour'] = date("H", strtotime($itemInfo['open_date']));
				$itemInfo['open_minute'] = date("i", strtotime($itemInfo['open_date']));
				$itemInfo['open_second'] = date("s", strtotime($itemInfo['open_date']));
				$itemInfo['open_date'] = date("Y-m-d", strtotime($itemInfo['open_date']));	
			}
		}
			
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		
		if ($kind == BANNER_MAIN) {			
			$this->data['provinces'] = $this->area_model->getProvinceList();
			$this->load_view('edit');
		} else {
			$this->load_view('edit2');
		}
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		$data['title'] = $this->post_input('title');
		$data['banner_kind'] = $this->post_input('banner_kind', BANNER_MAIN);
		$data['item_kind'] = $this->post_input('item_kind');
		$data['image'] = $this->post_input('image');
		$data['platform'] = $this->post_input('platform');
		$tmp = $this->post_input('is_show_limit');
		$data['is_show_limit'] = empty($tmp) ? 0 : 1;
		$data['start_date'] = $this->post_input('start_date', null);
		$data['end_date'] = $this->post_input('end_date', null);
		$tmp = $this->post_input('is_area_limit');
		$data['is_area_limit'] = empty($tmp) ? 0 : 1;
		$data['area_limit_type'] = $this->post_input('area_limit_type', AREA_LIMIT_BLACKLIST);
		if ($data['item_kind'] == BANNER_KIND_URL) {
			$data['item_info'] = $this->post_input('item_info_url');
		} else {
			$data['item_info'] = $this->post_input('item_info_id');
		}
		$areaIds = $this->post_input('area_ids', array());
		
		$startHour = $this->post_input('start_hour', '');
		$startMinute = $this->post_input('start_minute', '');
		$endHour = $this->post_input('end_hour', '');
		$endMinute = $this->post_input('end_minute', '');

		$data['start_time'] = ($startHour !== '' && $startMinute !== '') ? $startHour . ":" . $startMinute . ":00" : null;
		$data['end_time'] = ($endHour !== '' && $endMinute !== '') ? $endHour . ":" . $endMinute . ":00" : null;
		
		$tmp = $this->post_input('is_open_date', '');
		$data['is_open_date'] = empty($tmp) ? 0 : 1;

		if ($data['is_open_date'] == 1) {
			$open_date = $this->post_input('open_date', '');
			$open_hour = $this->post_input('open_hour', '');
			$open_minute = $this->post_input('open_minute', '');
			$open_second = $this->post_input('open_second', '');

			$open_time = ($open_hour !== '' && $open_minute !== '' && $open_second !== '') ? $open_hour . ":" . $open_minute . ":" . $open_second : '';

			$data['open_date'] = ($open_date !== '') ? $open_date . " " . $open_time : null;
		} else {
			$data['open_date'] = null;	
		}
		
		if (empty($id)) {
			// add new banner
			$id = $this->banner_model->insert(
					$data['title'], 
					$data['banner_kind'], 
					$data['item_kind'], 
					$data['item_info'], 
					$data['image'], 
					$data['platform'], 
					$data['is_show_limit'], 
					$data['start_date'], 
					$data['end_date'], 
					$data['start_time'], 
					$data['end_time'], 
					$data['is_open_date'], 
					$data['open_date'], 
					$data['is_area_limit'], 
					$data['area_limit_type']);
			
			$this->add_log('新增Banner', array($data));
		} else {
			$this->banner_model->update($id, $data);
			
			$this->add_log('编辑Banner', array('id'=>$id));
		}
		
		$this->load->model('area_model');
		$this->area_model->deleteLimits(AREA_LIMIT_KIND_BANNER, $id);
		foreach ($areaIds as $areaId) {
			$this->area_model->insertLimit($areaId, AREA_LIMIT_KIND_BANNER, $id);
		}

		if ($data['banner_kind'] == BANNER_MAIN) {		
			$this->success_redirect('banner/lists');
		} else {
			$this->success_redirect('banner/lists2');
		}
	}
}
