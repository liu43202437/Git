<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends Base_MobileController {

	function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
    	$data = array();
        $this->load->view('mobile/test', $data);
    }
    
    public function chat()
    {
    	$act = $this->get_input('act');
    	$userId = $this->get_input('user_id');
    	$eventId = $this->get_input('event_id');
    	$data = $this->get_input('data');
    	
    	$this->load->model('chat_model');
    	
    	if ($act == 'get') {
			$result = $this->chat_model->get($userId);
		} else if ($act == 'reg') {
			$result = $this->chat_model->register($userId, $data);
		} else if ($act == 'del') {
			$result = $this->chat_model->deleteUser($userId);
		} else if ($act == 'nick') {
			$result = $this->chat_model->updateNickname($userId, $data);
		} else if ($act == 'avatar') {
			$result = $this->chat_model->updateAvatar($userId, $data);
		} else if ($act == 'new_group') {
			$result = $this->chat_model->createGroup($eventId);
		} else if ($act == 'group_id') {
			$result = $this->chat_model->getGroupId($eventId);
		} else if ($act == 'join') {
			$groupId = $this->chat_model->getGroupId($eventId);
			$result = $this->chat_model->joinGroup($groupId, $userId);
		} else if ($act == 'quit') {
			$groupId = $this->chat_model->getGroupId($eventId);
			$result = $this->chat_model->quitGroup($groupId, $userId);
		} else if ($act == 'send') {
			$result = $this->chat_model->sendGroupMessage($eventId, $data);
		}
		var_dump($result);
    }
    
    public function pay()
    {
		var_dump($this->config->item('wxpay_config'));
    }
}
