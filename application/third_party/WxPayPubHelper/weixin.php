<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once "WxPayPubHelper.php";

// get prepay info
function get_prepay($wxpay_config, $order)
{
	$APP_ID = $wxpay_config['app_id'];            //APPID
	$APP_SECRET = $wxpay_config['app_secret'];    //appsecret
	$MCH_ID = $wxpay_config['mch_id'];
	$PARTNER_ID = $wxpay_config['partner_id'];
	$NOTIFY_URL = base_url() . $wxpay_config['notify_url'];

	//STEP 1. 构造一个订单。
	$data = array(
		"appid" => $APP_ID,
		"body" => $order['description'],
		//"device_info" => "APP-001",
		"mch_id" => $MCH_ID,
		"nonce_str" => mt_rand(),
		"notify_url" => $NOTIFY_URL,
		"out_trade_no" => $order['sn'],
		"spbill_create_ip" => getIPAddress(),
		"total_fee" => intval($order['total_money'] * 100),
		"trade_type" => "APP"
	);
	ksort($data);

	//STEP 2. 签名
	$sign = "";
	foreach ($data as $key => $value) {
		if ($value && $key != "sign" && $key != "key"){
			$sign .= $key . "=" . $value . "&";
		}
	}
	$sign .= "key=" . $PARTNER_ID;
	$sign = strtoupper(md5($sign));
	//echo $sign.'<br/>';exit;

	//STEP 3. 请求服务器
	$xml = "<xml>";
	foreach ($data as $key => $value) {
		$xml .= "<" . $key . ">" . $value . "</" . $key . ">";
	}
	$xml .= "<sign>" . $sign . "</sign>";
	$xml .= "</xml>";
	//echo $xml.'<br/>';

	$opts = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: text/xml',
			'content' => $xml
		),
		"ssl" => array(
			"verify_peer" => false,
			"verify_peer_name" => false,
		)
	);
	$context = stream_context_create($opts);
	$result = file_get_contents('https://api.mch.weixin.qq.com/pay/unifiedorder', false, $context);
	$result = simplexml_load_string($result, null, LIBXML_NOCDATA);

	if ($result->return_code != 'SUCCESS' || $result->result_code != 'SUCCESS') {
		return 601;
	}
	//var_dump($result);
	
	$prepay = array(
		"appid" => $APP_ID,
		"noncestr" => "" . $result->nonce_str,
		"package" => "Sign=WXPay",
		"partnerid" => $MCH_ID,
		"prepayid" => "" . $result->prepay_id,
		"timestamp" => "" . time(),
		"sign" => ""
	);
	ksort($prepay);
	
	$sign = "";
	foreach ($prepay as $key => $value) {
		if($value && $key != "sign" && $key != "key") {
			$sign .= $key . "=" . $value . "&";
		}
	}
	$sign .= "key=" . $PARTNER_ID;
	$sign = strtoupper(md5($sign));
	$prepay['sign'] = $sign;
	//$prepay['success'] = true;
	$prepay['order_sn'] = $order['sn'];

	return $prepay;
}