<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organization extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/organization/';
        $this->load->model('organization_model');
    }

    public function lists()
    {
    	$searchKey = $this->get_input('search_key');
    	
		$filters = array();
		$orders = array();

		if (!empty($searchKey)) {
			$filters['name%, view_name%, phone, contact'] = $searchKey;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->organization_model->getCount($filters);
    	$rsltList = $this->organization_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	foreach ($rsltList as $key=>$item) {
			 
		}
		
    	$this->data['searchKey'] = $searchKey;
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('member/add');
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
			$this->organization_model->delete($id);
		}
		
		$this->add_log('删除举办方信息', $ids);
		
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
			$this->organization_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->organization_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->organization_model->getEmptyRow();
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->organization_model->get($id);
		}
		
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		$data['name'] = $this->post_input('name');
		$data['view_name'] = $this->post_input('view_name');
		$data['logo'] = $this->post_input('logo');
		$data['thumb'] = $this->post_input('thumb');
		$data['phone'] = $this->post_input('phone');
		$data['contact'] = $this->post_input('contact');
		$data['contact_phone'] = $this->post_input('contact_phone');

		if (empty($id)) {
			// add new content
			$id = $this->organization_model->insert(
					$data['name'],
					$data['view_name'],
					$data['logo'],
					$data['thumb'],
					$data['phone'],
					$data['contact']);
			
			$this->add_log('新增举办方', $data);
		} else {
			$this->organization_model->update($id, $data);
			
			$this->add_log('编辑举办方', array('id'=>$id));
		}

		$this->success_redirect('organization/lists');
	}
}
