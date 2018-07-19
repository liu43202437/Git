<?php

/**
 * @Author: liuzudong
 * @Date:   2018-01-11 17:48:41
 * @Last Modified by:   liuzudong
 * @Last Modified time: 2018-01-30 11:00:21
 */
/**
* 
*/
require_once "application/config/main_config.php";
class Token_model
{
	const APPID = MainConfig::APPID;
	const APPSERCET = MainConfig::APPSECRET;
	private $url;
	private $tokenSavePath;
	function __construct()
	{
		$this->tokenSavePath = dirname(__FILE__).'/';
		$this->ticketSavePath = dirname(__FILE__).'/';
		// var_dump($this->tokenSavePath);
	}
	public function getAccessToken(){
		if(file_exists($this->tokenSavePath.'token.json')){
			$info = file_get_contents($this->tokenSavePath.'token.json');
			$temp = json_decode($info,true);
			if(time() - $temp['time'] > 6000){
				$token = $this->post($this->getUrl());
				$this->setToken($token);
			}
			else{
				$token = $info;
			}
		}
		else{
			$token = $this->post($this->getUrl());
			$this->setToken($token);
		}
		$token = json_decode($token,true)['access_token'];
		/* 调用别的接口判断access_token是否过期,不想用直接注释掉即可
		*/
		// if(!$this->judgeExpire($token)){
		// 	$token = $this->getNewAccessToken();
		// }
		/* 调用别的接口判断access_token是否过期,不想用直接注释掉即可
		*/
		return $token;
	}
	public function getTicket(){
		if(file_exists($this->ticketSavePath.'ticket.json')){
			$info = file_get_contents($this->ticketSavePath.'ticket.json');
			$temp = json_decode($info,true);
			if(time() - $temp['time'] > 6000){
				$token = $this->getNewAccessToken();
				$ticket = $this->post($this->getTicketUrl($token));
				$this->setTicket($ticket);
			}
			else{
				$ticket = $info;
			}
		}
		else{
			return $this->getNewTicket();
		}
		$rs = json_decode($ticket,true)['ticket'];
		return $rs;
	}
	public function getNewAccessToken(){
		$token = $this->post($this->getUrl());
		$this->setToken($token);
		return json_decode($token,true)['access_token'];
	}
	public function getNewTicket(){
		$token = $this->getNewAccessToken();
		$ticket = $this->post($this->getTicketUrl($token));
		$this->setTicket($ticket);
		return json_decode($ticket,true)['ticket'];
	}
	//调用无上限接口判断access_token是否过期
	public function judgeExpire($token){
		$url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token={$token}";
		$res = $this->post($url);
		if(strpos($res, 'ip_list')){
			return true;
		}
		else{
			return false;
		}
	}
	public function post($url){
		$curl= curl_init();
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function getUrl(){
		return "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".self::APPID."&secret=".self::APPSERCET;
	}
	public function getTicketUrl($token){
		return "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$token;
	}
	public function setToken($string){
		$info = json_decode($string,true);
		$rs['access_token'] = $info['access_token'];
		$rs['time'] = time();
		$jsonstring = json_encode($rs);
		file_put_contents($this->tokenSavePath.'token.json', $jsonstring);
	}
	public function setTicket($string){
		$info = json_decode($string,true);
		$rs['ticket'] = $info['ticket'];
		$rs['time'] = time();
		$jsonstring = json_encode($rs);
		file_put_contents($this->tokenSavePath.'ticket.json', $jsonstring);
	}
}
