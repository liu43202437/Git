<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Baby extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/baby/';
        $this->load->model('baby_model');
    }

    public function lists()
    {
    	$isShow = $this->get_input('is_show');
    	$title = $this->get_input('title');
    	
		$filters = array();
		$orders = array();

		if ($isShow !== '') {
			$isShow = intval($isShow);
			$filters['is_show'] = $isShow;
		}
		if (!empty($title)) {
			$filters['title%'] = $title;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['baby_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->baby_model->getCount($filters);
    	$rsltList = $this->baby_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

    	$this->data['isShow'] = $isShow;
    	$this->data['title'] = $title;
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('content/add');
    	$this->assign_pager($totalCount);
    	$this->assign_message();
    	
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
			$this->baby_model->delete($id);
		}
		
		$this->add_log('删除图文', $ids);
		
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
			$this->baby_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->baby_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->baby_model->getEmptyRow();;
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->baby_model->get($id);
		}
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		$data['title'] = $this->post_input('title');
		$data['baby_date'] = $this->post_input('baby_date', now());
		$data['image'] = $this->post_input('image');
		$data['hits'] = $this->post_input('hits', 0);
		
		if (empty($id)) {
			// add new content
			$id = $this->baby_model->insert($data['title'], $data['baby_date'], $data['image'], $data['hits']);
			
			$this->add_log('新增图文', array($this->posts));
		} else {
			$this->baby_model->update($id, $data);
			
			$this->add_log('编辑图文', array('id'=>$id));
		}
		
		$this->success_redirect('baby/lists');
	}
}
