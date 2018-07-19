<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
class Js_interface extends Base_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->view_path = 'admin/area_manager/';
        $this->load->model('token_model');
    }

    public function getToken(){
        $rs = [];
        $rs = $this->token_model->getAccessToken();
        echo json_encode($rs);
    }
}
