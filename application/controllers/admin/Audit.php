<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/audit/';
        $this->load->model('audit_model');
    }

    public function lists($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$status = $this->get_input('status');
    	$timeType = $this->get_input('time_type', 'create');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$name = $this->get_input('name');
    	
		$filters = array();
		$orders = array();

		$filters['kind'] = $kind;		
		if ($status !== '') {
			$status = intval($status);
			$filters['status'] = $status;
		}
		if (!empty($title)) {
			$filters['name%'] = $name;
		}
		if ($timeType == 'create') {
			$field = 'create_date';
		} else {
			$field = 'audit_date';
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
		    	
    	$totalCount = $this->audit_model->getCount($filters);
    	$rsltList = $this->audit_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	if ($kind == AUDIT_KIND_CHALLENGE) {
    		$this->load->model('challenge_model');
			foreach ($rsltList as &$item) {
				$challenge = $this->challenge_model->get($item['challenge_id']);
				$item['challenge_title'] = $challenge ? $challenge['title'] : '';
			}
    	}

    	$this->data['kind'] = $kind;
    	$this->data['status'] = $status;
    	$this->data['timeType'] = $timeType;
    	$this->data['startDate'] = $startDate;
    	$this->data['endDate'] = $endDate;
    	$this->data['name'] = $name;
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
			$this->audit_model->delete($id);
		}
		
		$this->add_log('删除报名', $ids);
		
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
			$data['status'] = AUDIT_STATUS_REJECTED;
			$data['audit_date'] = now(); 
		} else {
			$data['is_marked'] = 1;
		}
		
		foreach ($ids as $id) {
			$this->audit_model->update($id, $data);
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
			show_errorpage();
		}
		$item = $this->audit_model->get($id);
		
		$attributes = array();
		for ($i = 1; $i <= 20; $i++) {
			$value = $item['attribute'.$i];
			if (empty($value)) {
				break;
			}
			$parts = explode('|', $value);
			if (count($parts) < 2) {
				break;
			}
			$attr['label'] = $parts[1];
			$attr['value'] = $parts[0];
			$attr['target_field'] = isset($parts[2]) ? $parts[2] : '';
			$attributes[] = $attr;
		}
		$this->data['attributes'] = $attributes;
		
		$this->data['kind'] = $kind;
		$this->data['itemInfo'] = $item;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$kind = $this->post_input('kind');
		$id = $this->post_input('id');
		$status = $this->post_input('status', AUDIT_STATUS_REQUESTED);
		$marked = $this->post_input('marked');
		if ($marked == 1) {
			$data['is_marked'] = 1;
		} else {
			$data['status'] = $status;
			$data['audit_date'] = now();
		}

		if ($status == AUDIT_STATUS_PASSED) {
			if ($kind == AUDIT_KIND_CLUB) {
				$url = 'club/edit_from_audit?id=' . $id;
			} else if ($kind == AUDIT_KIND_CHALLENGE) {
				$rslt = $this->audit_model->update($id, $data);
				$url = 'audit/lists/' . $kind;
			} else {
				$url = 'member/edit_from_audit/' . $kind . '?id=' . $id;
			}
			$this->success_redirect($url, null, false);
		} else {
			$rslt = $this->audit_model->update($id, $data);
			$this->success_redirect('audit/lists/' . $kind);
		}
	}
}
