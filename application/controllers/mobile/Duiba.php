<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Duiba extends Base_MobileController
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
        $this->load->model('session_model');
    }

    public function login()
    {
        $sid = $this->get_input('sid');//用户ID
        $session = $this->session_model->getInfoBySId($sid);
        $userId = $session['user_id'];

        if (is_null($session)) {
            parent::output(101);
        }

        $this->user = $this->user_model->get($userId);
        require_once(APPPATH . "third_party/yunjifen/api.php");
        $appkey = $this->config->item('yunjifen_appkey');
        $secret = $this->config->item('yunjifen_secret');

        $url = buildCreditAutoLoginRequest($appkey, $secret, $userId, $this->user['point']);

        header("location:".$url);
    }

}
