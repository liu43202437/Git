<?php

require_once(APPPATH . "third_party/OpenIM/TopSdk.php");

// chat model
class Chat_model extends CI_Model {

	protected $chat = null;
	protected $noChat = false;
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
    	
    	$appkey = $this->config->item('openim_appkey');
		$secret = $this->config->item('openim_secret');
		
    	$this->chat = new TopClient;
    	$this->chat->appkey = $appkey;
    	$this->chat->secretKey = $secret;
	}   
	
	public function checkResult($json_result)
	{
		try {
			$rslt = json_decode($json_result, true);
			if (!empty($result['status'])) {
				return $result['error_msg'];
			}
			return true;
		} catch (Exception $e) {
		}
		return false;
	}
	
	public function getGroupName($eventId)
	{
		return CHAT_GROUP_NAME_PREFIX . $eventId;
	}
	public function getGroupUsername($eventId)
	{
		return CHAT_GROUP_USERNAME_PREFIX . $eventId;
	}
	
	public function getUsername($userId)
	{
		return CHAT_USERNAME_PREFIX . $userId;
	}
	public function getPassword($userId)
	{
		return CHAT_GLOBAL_PASSWORD;
	}
	
	// register new user
	public function get($userId)
	{
		if ($this->noChat) { return false; }

		try {
			$username = $this->getUsername($userId);
			
			$req = new OpenimUsersGetRequest;
			$req->setUserids($username);
			$resp = $this->chat->execute($req);
			if (!empty($resp->code) || empty($resp->userinfos)) {
				return false;
			}
			return $resp->userinfos->userinfos;
		} catch (Exception $e) {
		}
		return false;
	}
	
	// register new user
	public function register($userId, $nickname, $avatarUrl = null)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getUsername($userId);
			$password = $this->getPassword($userId);
			
			$req = new OpenimUsersAddRequest;
			$userinfos = new Userinfos;
			$userinfos->userid = $username;
			$userinfos->password = $password;
			$userinfos->nick = $nickname;
			if ($avatarUrl) {
				$userinfos->icon_url = $avatarUrl;
			}
			$req->setUserinfos(json_encode($userinfos));
			$resp = $this->chat->execute($req);
			if (isset($resp->uid_succ) && $resp->uid_succ->string[0] == $username) {
				//return true;
				return $resp;
			}
			return false;
		} catch (Exception $e) {
		}
		return false;
	}
	
	// delete user
	public function deleteUser($userId)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getUsername($userId);
			
			$req = new OpenimUsersDeleteRequest;
			$req->setUserids($username);
			$resp = $this->chat->execute($req);
			if (!empty($resp->code)) {
				return false;
			}
			//return true;
			return $resp;
		} catch (Exception $e) {
		}
		return false;
	}
	
	// register new user for group 
	public function registerGroupUser($eventId)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getGroupUsername($eventId);
			$password = $this->getPassword($eventId);
			$nickname = $username;
				
			$req = new OpenimUsersAddRequest;
			$userinfos = new Userinfos;
			$userinfos->userid = $username;
			$userinfos->password = $password;
			$userinfos->nick = $nickname;
			$req->setUserinfos(json_encode($userinfos));
			$resp = $this->chat->execute($req);
			if (isset($resp->uid_succ) && $resp->uid_succ->string[0] == $username) {
				//return true;
				return $resp;
			}
			return false;
		} catch (Exception $e) {
		}
		return false;
	}
	
	// update nickname
	public function updateNickname($userId, $nickname)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getUsername($userId);
			
			$req = new OpenimUsersUpdateRequest;
			$userinfos = new Userinfos;
			$userinfos->userid = $username;
			$userinfos->nick = $nickname;
			$req->setUserinfos(json_encode($userinfos));
			$resp = $this->chat->execute($req);
			if (isset($resp->uid_succ) && $resp->uid_succ->string[0] == $username) {
				//return true;
				return $resp;
			}
			return false;
		} catch (Exception $e) {
		}
		return false;
	}
	// update nickname
	public function updateAvatar($userId, $avatarUrl)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getUsername($userId);
			
			$req = new OpenimUsersUpdateRequest;
			$userinfos = new Userinfos;
			$userinfos->userid = $username;
			$userinfos->icon_url = $avatarUrl;
			$req->setUserinfos(json_encode($userinfos));
			$resp = $this->chat->execute($req);
			if (isset($resp->uid_succ) && $resp->uid_succ->string[0] == $username) {
				//return true;
				return $resp;
			}
			return false;
		} catch (Exception $e) {
		}
		return false;
	}
	
	// create group
	public function createGroup($eventId)
	{
		if ($this->noChat) { return false; }
		
		try {
			$creator = $this->getGroupUsername($eventId);
			$groupName = $this->getGroupName($eventId);
			
			$this->registerGroupUser($eventId);
			
			$req = new OpenimTribeCreateRequest;
			$user = new OpenImUser;
			$user->uid = $creator;
			$user->taobao_account = "false";
			$user->app_key = $this->chat->appkey;
			$req->setUser(json_encode($user));
			$req->setTribeName($groupName);
			$req->setNotice("tribetypedemp");
			$req->setTribeType("0");
			$resp = $this->chat->execute($req);
			if (!empty($resp->code)) {
				return false;
			}
			$groupId = (array)$resp->tribe_info->tribe_id;
			return $groupId[0];
		} catch (Exception $e) {
		}
		return false;
	}
	
	// get group id
	public function getGroupId($eventId)
	{
		if ($this->noChat) { return false; }
		
		try {
			$creator = $this->getGroupUsername($eventId);
			$groupName = $this->getGroupName($eventId);
			file_put_contents("bb.txt",$creator);
			file_put_contents("cc.txt",$groupName);
			$req = new OpenimTribeGetalltribesRequest;
			$user = new OpenImUser;
			$user->uid = $creator;
			$user->taobao_account = "false";
			$user->app_key = $this->chat->appkey;
			$req->setUser(json_encode($user));
			$req->setTribeTypes("0,1");
			$resp = $this->chat->execute($req);
			$groups = $resp->tribe_info_list->tribe_info;
			if (empty($groups)) {
				return false;
			}
			foreach ($groups as $group) {
				if ($group->name == $groupName) {
					$groupId = (array)$group->tribe_id;
					return $groupId[0];
				}
			} 
		} catch (Exception $e) {
		}
		return false;
	}
	
	// join group
	public function joinGroup($groupId, $userId)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getUsername($userId);
			
			$req = new OpenimTribeJoinRequest;
			$user = new OpenImUser;
			$user->uid = $username;
			$user->taobao_account = "false";
			$user->app_key = $this->chat->appkey;
			$req->setUser(json_encode($user));
			$req->setTribeId($groupId);
			$resp = $this->chat->execute($req);
			if (!empty($resp->code) && $resp->sub_code != 6) {
				return false;
			}
			//return true;
			return $resp;
		} catch (Exception $e) {
		}	
	}
	
	// quit group
	public function quitGroup($groupId, $userId)
	{
		if ($this->noChat) { return false; }
		
		try {
			$username = $this->getUsername($userId);
			
			$req = new OpenimTribeQuitRequest;
			$user = new OpenImUser;
			$user->uid = $username;
			$user->taobao_account = "false";
			$user->app_key = $this->chat->appkey;
			$req->setUser(json_encode($user));
			$req->setTribeId($groupId);
			$resp = $this->chat->execute($req);
			if (!empty($resp->code)) {
				return false;
			}
			//return true;
			return $resp;
		} catch (Exception $e) {
		}
	}
	
	// send message to group
	public function sendGroupMessage($eventId, $message)
	{
		file_put_contents("log.txt",$eventId."============".$message);
		if ($this->noChat) { return false; }
		
		try {
			$creator = $this->getGroupUsername($eventId);
			$groupId = $this->getGroupId($eventId);
			//var_dump($groupId);
			
			$req = new OpenimTribeSendmsgRequest;
			$user = new User;
			$user->uid = $creator;
			$user->taobao_account = "false";
			$user->app_key = $this->chat->appkey;
			$req->setUser(json_encode($user));
			$req->setTribeId($groupId);
			$msg = new TribeMsg;
			$msg->at_flag = "0";
			//$msg->custom_push = "{\"d\":\"custom push\", \"sound\":\"dingdong\", \"title\" : \"title\"}";
			//$msg->media_attrs = "{\"height\": 10, \"width\": 10, \"type\": \"jpg\"}";
			$msg->msg_content = $message;
			$msg->msg_type = "0";
			$msg->push = "true";
			$req->setMsg(json_encode($msg));
			$resp = $this->chat->execute($req);
			if (!empty($resp->code)) {
				return false;
			}
			//return true;
			return $resp;
		} catch (Exception $e) {
		}
		return false;
	}
	
	// send custom message to an user
	public function sendCustomMessage($userId, $message)
	{
		if ($this->noChat) { return false; }
		
		try {
			$toUser = $this->getUsername($userId);
			
			$req = new OpenimCustmsgPushRequest;
			$custmsg = new CustMsg;
			$custmsg->from_user = "user_sender";
			$custmsg->to_users = $toUser;
			$custmsg->summary = "客户端最近消息里面显示的消息摘要";
			$custmsg->data = "push payload";
			$custmsg->aps = "{\"alert\":\"ios apns push\"}";
			$custmsg->apns_param = "apns推送的附带数据";
			$custmsg->invisible = "1";
			$req->setCustmsg(json_encode($custmsg));
			$resp = $this->chat->execute($req);
			if (!empty($resp->code)) {
				return false;
			}
			//return true;
			return $resp;
		} catch (Exception $e) {
		}
		return false;
	}
}
?>
