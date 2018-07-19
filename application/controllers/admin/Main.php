<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/';
    }

    public function index()
    {
    	$roles = array();
    	foreach ($this->menu as $key => $submenu) {
    		$subRoles = array();
			foreach ($submenu as $url => $label) {
				if ($url == 'label') continue;
				if ($this->auth_role($url)) {
					$subRoles[] = $url;
				}
			}
			if (count($subRoles) > 0) {
				$roles[] = $key;
				$roles = array_merge($roles, $subRoles);
			}
    	}
    	$this->data['roles'] = $roles;
		$this->load_view('main');
    }
}
