<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/message/';
        $this->load->model('message_model');
    }

    public function lists()
    {
		$filters = array();
		$orders = array();
		
		$receiverType = $this->get_input('receiverType');
		$isDraft = $this->get_input('isDraft');
		
		if ($receiverType !== '') {
			$receiverType = intval($receiverType);
			$filters['receiver_type'] = $receiverType;
		}
		if ($isDraft === '0' || $isDraft == '1') {
			$filters['is_draft'] = $isDraft;
		}
		if (!empty($this->pager['searchProperty']) && !empty($this->pager['searchValue'])) {
			$filters[$this->pager['searchProperty'] . '%'] = $this->pager['searchValue'];
		}
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		}
		    	
    	$totalCount = $this->message_model->getCount($filters);
    	$rsltList = $this->message_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

		$this->data['isEditable'] = $this->auth_role('message/edit');    	
    	$this->data['messageList'] = $rsltList;
    	$this->data['receiverType'] = $receiverType;
    	$this->data['isDraft'] = $isDraft;
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
			$this->message_model->delete($id);
		}

		$this->add_log('删除消息', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function edit()
    {
		$id = $this->get_input('id');
		if (empty($id)) {
			$item = $this->message_model->getEmptyRow();
			$this->data['isNew'] = true;
			$this->data['isEditable'] = true;
		} else {
			$item = $this->message_model->get($id);
			$this->data['isNew'] = false;
			if ($item['is_draft'] == 0) {
				$this->data['isEditable'] = false;
			} else {
				$this->data['isEditable'] = true;
			}
		}
		
		$this->data['messageItem'] = $item;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function find_user()
    {
		$q = $this->get_input('q');
		
		$this->load->model('user_model');
		$userList = $this->user_model->getMessageCandidates($q, 20);
		if (empty($userList)) {
			$userList = array();
		}
    	
    	echo json_capsule($userList);
    }
    
    public function save()
	{
		$this->load->model('user_model');
		
		$id = $this->post_input('id');
		$data['is_draft'] = $this->post_input('is_draft', 0);
		$data['receiver_type'] = $this->post_input('receiver_type');
		$data['receiver_id'] = $this->post_input('receiver_id');
		$data['title'] = $this->post_input('title');
		$data['content'] = $this->post_input('content');
		
		$data['receiver_name'] = null;
		if ($data['receiver_type'] == RECEIVER_TYPE_SINGLE) {
			if (empty($data['receiver_id'])) {
				$this->error_redirect('message/edit?id='.$id);
			}
			$userInfo = $this->user_model->get($data['receiver_id']);
			$data['receiver_name'] = $userInfo['username'];
		}
		
		if (empty($id)) {
			$id = $this->message_model->insert($data['title'], $data['content'], $data['is_draft'], $data['receiver_type'], $data['receiver_id'], $data['receiver_name']);
			$this->add_log('新增消息', $data);
		} else {
			$rslt = $this->message_model->update($id, $data);
			$this->add_log('编辑消息草稿', array_merge(array('id'=>$id), $data));
		}
		
		if ($this->message_model->send($id)) {
			$this->add_log('发送信息', array('message_id'=>$id));
		}
		
		$this->set_message(parent::success_message());
		redirect(base_url() . 'admin/message/lists');
	}
}
