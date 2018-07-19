<?php
require_once(APPPATH . 'third_party/Notification.php');
			
// message table model
class Message_model extends Base_Model {

	protected $notification = null;
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['message'];
		
		$iosAppKey = $this->config->item('umeng_upush_ios_appkey');
		$iosAppSecret = $this->config->item('umeng_upush_ios_secret');
		$androidAppKey = $this->config->item('umeng_upush_android_appkey');
		$androidsAppSecret = $this->config->item('umeng_upush_android_secret');
		
		$this->notification = new Notification($iosAppKey, $iosAppSecret, $androidAppKey, $androidsAppSecret);
	}
	
	// insert
	public function insert($title, $content, $isDraft, $receiverType, $receiverId, $receiverName) {
		$data = array(
			'title' => $title,
			'content' => $content,
			'is_draft' => $isDraft,
			'receiver_id' => $receiverId,
			'receiver_type' => $receiverType,
			'receiver_name' => $receiverName,
			'create_date' => now()
		);
		$this->_insert($data);
	}

	// delete by user
	public function deleteBySender($userId)
	{
		$where = array(
			'sender_id' => $userId
		);
		return $this->db->delete($this->tbl, $where);
	}
	public function deleteByReceiver($userId)
	{
		$where = array(
			'receiver_id' => $userId
		);
		return $this->db->delete($this->tbl, $where);
	}

	
	// send push notifications
	public function send($id)
	{
		if (empty($this->notification)) {
			return false;
		}

		$message = $this->get($id);
		if (empty($message) || $message['is_draft'] == 1) {
			return false;
		}

		$result = null;
		if ($message['receiver_type'] == RECEIVER_TYPE_ALL) {
			
			$result = $this->notification->sendIOSBroadcast($message['title'], $message['content']);
			//$this->notification->sendAndroidBroadcast($message['title'], $message['content']);
			
		} else if ($message['receiver_type'] == RECEIVER_TYPE_SINGLE) {
			
			$this->load->model('user_model');
			$receiver = $this->user_model->get($message['receiver_id']);
			
			if (!empty($receiver['device_udid'])) {
		        if ($receiver['device_type'] == DEVICE_TYPE_IPHONE) {
					$result = $this->notification->sendIOSUnicast($receiver['device_udid'], $message['title'], $message['content']);
				} else if ($receiver['device_type'] == DEVICE_TYPE_ANDROID) {
					//$this->notification->sendAndroidUnicast($receiver['device_udid'], $message['title'], $message['content']);
				}
			}
			
		} else {

			$gender = ($message['receiver_type'] == RECEIVER_TYPE_MALE) ? 'male' : 'female';
			$where = array('tag' => $gender);
			$result = $this->notification->sendIOSGroupcast($where, $message['title'], $message['content']);
			//$this->notification->sendAndroidGroupcast($where, $message['title'], $message['content']);

		}
		
		$data['send_date'] = now();
		if (!empty($result) && is_array($result)) {
			if (isset($result['ret']) && $result['ret'] == 'SUCCESS') {
				$data['is_failed'] = 0;
				$data['error_desc'] = '';				
			} else {
				$data['is_failed'] = 1;
				if (isset($result['data']) && isset($result['data']['error_code'])) {
					$data['error_desc'] = '错误码 ：' . $result['data']['error_code'];
				} else if (isset($result['data']) && isset($result['data']['error_msg'])) {
					$data['error_desc'] = $result['data']['error_msg'];
				} else {
					$data['error_desc'] = '未知错误';
				}
			}
		} else {
			$data['is_failed'] = 1;
			$data['error_desc'] = '未知错误';
		}
		
		$this->update($id, $data);		
	}
}
?>
