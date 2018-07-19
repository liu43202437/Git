<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
		$this->login();
    }
    
    public function login()
    {
		$redirectUrl = $this->input->get("redirect_url");

		$data['redirectUrl'] = empty($redirectUrl) ? '' : $redirectUrl;
		
    	$csrf = array(
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		);
		$data['csrf'] = $csrf;
		
		if (count($this->session->flashdata()) > 0) {
			$data['errorMessage'] = $this->session->flashdata('error_message');
        }
		
		$this->load->view('admin/login', $data);
	}
	
	public function do_login()
	{
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		$redirectUrl = $this->input->post("redirect_url");

		$email = $this->security->xss_clean($email);
		$password = $this->security->xss_clean($password);
		$redirectUrl = $this->security->xss_clean($redirectUrl);

		$this->load->model("admin_model");

		// check info
		$admin = $this->admin_model->getInfoByEMail($email);
		if ($admin == null) {     											// login fail
			$this->session->set_flashdata('error_message', '该账号不存在');
			$url = base_url() . 'admin/login?redirect_url=' . urlencode($redirectUrl);
            redirect($url, 'refresh');
		}
		
		if (!password_verify($password, $admin['password'])) {     			// login fail
			$this->session->set_flashdata('error_message', '账号或者密码错误');
			$url = base_url() . 'admin/login?redirect_url=' . urlencode($redirectUrl);
            redirect($url, 'refresh');
		}
		
		$data['login_date'] = now();
		$data['login_ip'] = $this->input->ip_address();
		$this->admin_model->update($admin['id'], $data);
			
		// succeed
		$this->session->set_userdata('admin_id', $admin['id']);
		$this->session->set_userdata('admin_name', $admin['username']);
		$this->session->set_userdata('admin_email', $admin['email']);
		
		$adminRoles = $this->admin_model->getRoles($admin['id']);
		$this->session->set_userdata('admin_roles', $adminRoles);
		
		if ($redirectUrl == null || $redirectUrl == '') {
		    redirect(base_url(). 'admin/main');
		} else {
		    redirect(urldecode($redirectUrl));
		}
	}
	
	public function register()
	{
		die('no_featured_page');
		
		$username = $this->input->post("username");
		$password = $this->input->post("password");
		
		$this->load->model("admin_model");
		$this->admin_model->insert($username, $password);
	}
	
	public function logout() 
	{
		$this->load->model("admin_model");
		$this->session->unset_userdata('admin_id');
		$this->session->unset_userdata('admin_name');
		$this->session->unset_userdata('admin_email');
		$this->session->unset_userdata('admin_roles');
		    
		redirect(base_url(). 'admin/login');
    }
}
