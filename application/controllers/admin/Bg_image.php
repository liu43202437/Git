<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bg_image extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/bgimage/';
        $this->load->model('bgimage_model');
    }

    public function lists()
    {
    	$totalCount = $this->bgimage_model->getCount();
    	$rsltList = $this->bgimage_model->getList(null, null, $this->pager['pageNumber'], $this->pager['pageSize']);
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
			$this->bgimage_model->delete($id);
		}
		
		$this->add_log('删除背景', $ids);
		
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
			$this->bgimage_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->bgimage_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->bgimage_model->getEmptyRow();
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->bgimage_model->get($id);
		}
    	
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		$data['name'] = $this->post_input('name');
		$data['image'] = $this->post_input('image');

		if (empty($id)) {
			// add new banner
			$id = $this->bgimage_model->insert(
					$data['name'], 
					$data['image']);
			
			$this->add_log('新增闪播', array($data));
		} else {
			$this->bgimage_model->update($id, $data);
			
			$this->add_log('编辑闪播', array('id'=>$id));
		}
		
		$this->success_redirect('bg_image/lists');
	}
}