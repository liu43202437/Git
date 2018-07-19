<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Splash extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/splash/';
        $this->load->model('splash_model');
    }

    public function lists()
    {
    	$totalCount = $this->splash_model->getCount();
    	$rsltList = $this->splash_model->getList(null, null, $this->pager['pageNumber'], $this->pager['pageSize']);
    	$this->data['itemList'] = $rsltList;
    	$this->assign_message();
    	$this->assign_pager($totalCount);
		$this->load_view('list');
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		foreach ($ids as $id) {
			$this->splash_model->delete($id);
		}
		
		$this->add_log('删除Splash', $ids);
		
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
			$this->splash_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->splash_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
    	$this->load->model('area_model');
    	
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->splash_model->getEmptyRow();
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->splash_model->get($id);
			
			$itemInfo['areaLimits'] = $this->area_model->getLimitList(AREA_LIMIT_KIND_SPLASH, $id, true);
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
		
		$this->data['provinces'] = $this->area_model->getProvinceList();
		
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		$data['name'] = $this->post_input('name');
		$data['image'] = $this->post_input('image');
		$data['url'] = $this->post_input('url');
		$data['platform'] = $this->post_input('platform');
		$data['show_count_limit'] = $this->post_input('show_count_limit', null);
		$data['hits_limit'] = $this->post_input('hits_limit', null);
		$data['start_date'] = $this->post_input('start_date', null);
		$data['end_date'] = $this->post_input('end_date', null);
		$tmp = $this->post_input('is_area_limit');
		$data['is_area_limit'] = empty($tmp) ? 0 : 1;
		$data['area_limit_type'] = $this->post_input('area_limit_type', AREA_LIMIT_BLACKLIST);
		$areaIds = $this->post_input('area_ids', array());
		
		$startHour = $this->post_input('start_hour', '');
		$startMinute = $this->post_input('start_minute', '');
		$endHour = $this->post_input('end_hour', '');
		$endMinute = $this->post_input('end_minute', '');
		$data['start_time'] = ($startHour !== '' && $startMinute !== '') ? $startHour . ":" . $startMinute . ":00" : null;
		$data['end_time'] = ($endHour !== '' && $endMinute !== '') ? $endHour . ":" . $endMinute . ":00" : null;
		
		if (empty($id)) {
			// add new banner
			$id = $this->splash_model->insert(
					$data['name'], 
					$data['image'], 
					$data['url'], 
					$data['platform'], 
					$data['show_count_limit'], 
					$data['hits_limit'], 
					$data['start_date'], 
					$data['end_date'], 
					$data['start_time'], 
					$data['end_time'], 
					$data['is_area_limit'], 
					$data['area_limit_type']);
			
			$this->add_log('新增闪播', array($data));
		} else {
			$this->splash_model->update($id, $data);
			
			$this->add_log('编辑闪播', array('id'=>$id));
		}
		
		$this->load->model('area_model');
		$this->area_model->deleteLimits(AREA_LIMIT_KIND_SPLASH, $id);
		foreach ($areaIds as $areaId) {
			$this->area_model->insertLimit($areaId, AREA_LIMIT_KIND_SPLASH, $id);
		}
		
		$this->success_redirect('splash/lists');
	}
}
