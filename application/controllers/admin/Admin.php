<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        $this->view_path = 'admin/admin/';
        $this->load->model('admin_model');
    }
    
    public function lists()
    {
    	$username = $this->get_input('username', '');

		$filters = array();
		$orders = array();
		
		if (!empty($username)) {
			$filters['username%'] = $username;
		}
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->admin_model->getCount($filters);
    	$itemList = $this->admin_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);

		$this->data['username'] = $username;    	
		$this->data['isEditable'] = $this->auth_role('admin/edit');    	
    	$this->data['itemList'] = $itemList;
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
			$this->admin_model->delete($id);
		}
		
		$this->add_log('删除管理员', $ids);
		echo json_capsule(parent::success_message());
    }
    
    public function toggle_enable()
    {
		$id = $this->post_input('id');
		if (empty($id)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$data['is_enabled$'] = '1-is_enabled';
		$this->admin_model->update($id, $data);
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
		$id = $this->get_input('id');
		
		if (empty($id)) {
			$adminInfo = array();
		} else {
			$adminInfo = $this->admin_model->get($id);
		}
		
		$this->data['itemInfo'] = $adminInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function check_username()
	{
		$username = $this->get_input('username');
		$orgId = $this->get_input('org_id');
		
		$adminInfo = $this->admin_model->getInfoByName($username);
		if (empty($adminInfo) || $adminInfo['id'] == $orgId) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	public function check_email()
	{
		$email = $this->get_input('email');
		$orgId = $this->get_input('org_id');
		
		$adminInfo = $this->admin_model->getInfoByEMail($email);
		if (empty($adminInfo) || $adminInfo['id'] == $orgId) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
    
	public function save()
	{
		$id = $this->post_input('id');
		$username = $this->post_input('username');
		$oldPassword = $this->post_input('oldPassword');
		$password = $this->post_input('password');
		
		$data['email'] = $this->post_input('email');
		$data['description'] = $this->post_input('description');

		if (empty($id)) {		// create new
			// check for existance		
			$adminInfo = $this->admin_model->getInfoByName($username);
			if (!empty($adminInfo)) {
				$this->error_redirect('admin/edit', '账号已存在！');
			}
			$adminInfo = $this->admin_model->getInfoByEMail($data['email']);
			if (!empty($adminInfo)) {
				$this->error_redirect('admin/edit', '邮件已存在！');
			}
			
			// new
			$id = $this->admin_model->insert($username, $password);
			if (empty($id)) {
				$this->error_redirect('admin/edit');
			}
			$this->add_log('添加管理员', array_merge(array('username'=>$username), $data));

		} else {
			// check for existance
			$adminInfo = $this->admin_model->getInfoByName($username);
			if (!empty($adminInfo) && $adminInfo['id'] != $id) {
				$this->error_redirect('admin/edit?id='.$id, '账号已存在！');
			}
			$adminInfo = $this->admin_model->getInfoByEMail($data['email']);
			if (!empty($adminInfo) && $adminInfo['id'] != $id) {
				$this->error_redirect('admin/edit?id='.$id, '邮件已存在！');
			}
			
			$data['username'] = $username;
			// check for password
			if (!empty($password)) {
				$adminInfo = $this->admin_model->get($id);
				if (!password_verify($oldPassword, $adminInfo['password'])) {
					$this->error_redirect('admin/edit?id='.$id, '旧密码不一致！');
				}
				$data['password'] = $password;
			}
			$this->add_log('编辑管理员信息', array_merge(array('id'=>$id), $data));
		}
		 
		// update admin info
		$rslt = $this->admin_model->update($id, $data);
		
		$this->success_redirect('admin/lists');
	}

	public function edit_role()
    {
		$id = $this->get_input('id');
		
		if (empty($id)) {
			$this->error_redirect('admin/lists', '输入参数错误！');
		}
		
		$adminInfo = $this->admin_model->get($id);
		$roles = $this->admin_model->getRoles($id);
		
		$roleList = array();
    	foreach ($this->menu as $key => $submenu) {
    		if ($key == 'home') continue;

    		$roleSubList = array();
    		$roleSubList['label'] = $submenu['label'];
			foreach ($submenu as $url => $label) {
				$item['name'] = $url;
				$item['label'] = $label;
				$item['isPermit'] = $this->auth_role($url, $roles);
				
				$roleSubList[] = $item;
			}
			$roleList[$key] = $roleSubList;
    	}

		$this->data['roles'] = $roleList;
		$this->data['itemInfo'] = $adminInfo;
		$this->assign_message();
		$this->load_view('edit_role');
    }
    	
	public function save_role()
	{
		$id = $this->post_input('id');
		$roles = $this->post_input('roles', array());
		
		if (empty($id)) {
			$this->error_redirect('admin/lists', '输入参数错误！');
		}

		$this->add_log('编辑管理员信息', array('id'=>$id));

		// update admin roles
		foreach ($roles as $k => $action) {
			$this->admin_model->setRole($id, $action, true);
		}
		foreach ($this->menu as $key => $submenu) {
			foreach ($submenu as $action => $label) {
				if ($action != 'label' && !in_array($action, $roles)) {
					$this->admin_model->setRole($id, $action, false);
				}
			}
    	}
    	
		$this->success_redirect('admin/lists');
	}
	
	public function edit_me()
    {
    	$adminInfo = $this->admin_model->get($this->adminId);
    	$this->data['itemInfo'] = $adminInfo;
		$this->assign_message();
		$this->load_view('edit_me');
    }
	
	public function save_me()
	{
		$username = $this->post_input('username');
		$oldPassword = $this->post_input('oldPassword');
		$password = $this->post_input('password');
		
		$data['email'] = $this->post_input('email');
		$data['description'] = $this->post_input('description');

		// check for existance
		$adminInfo = $this->admin_model->getInfoByName($username);
		if (!empty($adminInfo) && $adminInfo['id'] != $this->adminId) {
			$this->error_redirect('admin/edit_me', '账号已存在！');
		}
		$adminInfo = $this->admin_model->getInfoByEMail($data['email']);
		if (!empty($adminInfo) && $adminInfo['id'] != $this->adminId) {
			$this->error_redirect('admin/edit_me', '邮件已存在！');
		}

		$data['username'] = $username;
		// check for password
		if (!empty($password)) {
			$adminInfo = $this->admin_model->get($this->adminId);
			if (!password_verify($oldPassword, $adminInfo['password'])) {
				$this->error_redirect('admin/edit_me', '密码不正确');
			}
			
			$data['password'] = $password;
		}
		 
		// update admin info
		$rslt = $this->admin_model->update($this->adminId, $data);
		
		// update session
		if ($rslt) {
			$this->session->set_userdata('admin_name', $username);
			$this->session->set_userdata('admin_email', $data['email']);
		}
		
		$this->add_log('编辑管理员信息', array_merge(array('id'=>$this->adminId), $data));
		
		$this->success_redirect('home');
	}
}
