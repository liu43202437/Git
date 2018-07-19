<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/config/main_config.php";
class Js_interface extends Base_MobileController
{
    function __construct()
    {
        parent::__construct();
        $this->view_path = 'admin/area_manager/';
        $this->load->model('token_model');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: ' . 3600 * 24);
    }
    public function getToken(){
        $rs = [];
        $rs = $this->token_model->getAccessToken();
        echo json_encode($rs);
    }
    public function getNewAccessToken(){
        $rs = [];
        $rs = $this->token_model->getNewAccessToken();
        echo json_encode($rs);
    }
    public function getTicket(){
        $rs = [];
        $ticket = $this->token_model->getTicket();
        $rs['ticket'] = $ticket;
        $rs['appid'] = MainConfig::APPID;
        echo json_encode($rs);
    }
    public function getNewTicket(){
        $rs = [];
        $ticket = $this->token_model->getNewTicket();
        $rs['ticket'] = $ticket;
        $rs['appid'] = MainConfig::APPID;
        echo json_encode($rs);
    }
}
