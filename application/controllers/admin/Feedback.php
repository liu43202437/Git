<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/feedback/';
        $this->load->model('feedback_model');
    }

    public function lists()
    {
    	$status = $this->get_input('status');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$title = $this->get_input('title');
    	
		$filters = array();
		$orders = array();
		
		if ($status !== '') {
			$status = intval($status);
			if ($status == FEEDBACK_STATUS_PROCEED + 1) {
				$filters['is_marked'] = 1;
			} else {
				$filters['status'] = $status;
			}
		}
		if (!empty($startDate)) {
			$filters['create_date >='] = d2bt($startDate);
		}
		if (!empty($endDate)) {
			$filters['create_date <='] = d2et($endDate);
		}
		if (!empty($title)) {
			$filters['title%'] = $title;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->feedback_model->getCount($filters);
    	$rsltList = $this->feedback_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

    	$this->data['status'] = $status;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	$this->data['title'] = $title;
    	$this->data['itemList'] = $rsltList;
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
			$this->feedback_model->delete($id);
		}
		
		$this->add_log('删除反馈', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function update_status()
    {
		$ids = $this->post_input('ids');
		if (empty($id) && empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$type = $this->post_input('type', 'status');
		if ($type == 'status') {
			$data['status'] = FEEDBACK_STATUS_PROCEED;
			$data['proceed_date'] = now(); 
		} else {
			$data['is_marked'] = 1;
		}
		
		foreach ($ids as $id) {
			$this->feedback_model->update($id, $data);
		}
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
		$id = $this->get_input('id');
		$feedback = $this->feedback_model->get($id);
		
		$this->data['feedback'] = $feedback;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		
		
		$this->add_log('解决反馈', array('id'=>$id));
		
		$rslt = $this->feedback_model->update($id, $data);
		$this->success_redirect('feedback/lists');
	}
}
