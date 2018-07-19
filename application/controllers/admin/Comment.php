<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/comment/';
        $this->load->model('comment_model');
    }

    public function lists()
    {
    	$targetKind = $this->get_input('target_kind');
    	$targetId = $this->get_input('target_id');
    	$status = $this->get_input('status');
    	$startDate = $this->get_input('start_date');
    	$endDate = $this->get_input('end_date');
    	$title = $this->get_input('title');

		$this->load->model('content_model');
		$this->load->model('link_model');
		$this->load->model('user_model');
		    	
		$filters = array();
		$orders = array();
		
		if (!empty($targetId) && !empty($targetKind)) {
			$filters['target_kind'] = $targetKind;
			if ($targetKind == COMMENT_ITEM_KIND_CONTENT) {
				$filters['target_id'] = $targetId;
			} else {
				$ids = $this->link_model->getContentIds($targetId);
				$filters['target_id'] = $ids;
			}
		}
		if ($status !== '') {
			$status = intval($status);
			$filters['status'] = $status;
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
		
    	$totalCount = $this->comment_model->getCount($filters);
    	$rsltList = $this->comment_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	foreach ($rsltList as $key=>$item) {
    		$u = $this->user_model->get($item['user_id']);
			$rsltList[$key]['username'] = $u['nickname'] ? $u['nickname'] : $u['username'];
			
    		$rsltList[$key]['target'] = '';
			if ($item['target_kind'] == COMMENT_ITEM_KIND_CONTENT) {
				$target = $this->content_model->get($item['target_id']);
				if (!empty($target)) {
					$rsltList[$key]['target'] = $target['title'];
				}
			} else {
				$target = $this->link_model->getContentItem($item['target_id']);
				if (!empty($target)) {
					$rsltList[$key]['target'] = $target['title'];
				}
			}
    	}

    	$this->data['targetKind'] = $targetKind;
    	$this->data['targetId'] = $targetId;
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
			$this->comment_model->delete($id);
		}
		
		$this->add_log('删除评论', $ids);
		
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
			$data['status'] = $this->post_input('status', COMMENT_STATUS_PASSED);
		} else {
			$data['is_marked'] = 1;
		}
		
		foreach ($ids as $id) {
			$this->comment_model->update($id, $data);
		}
		echo json_capsule(parent::success_message());
    }
}
