<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "lib/WxPay.Api.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

if(isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != ""){
	$transaction_id = $_REQUEST["transaction_id"];
	$total_fee = $_REQUEST["total_fee"];	// 分
	$refund_fee = $_REQUEST["refund_fee"];	// 分
	$input = new WxPayRefund();
	$input->SetTransaction_id($transaction_id);
	$input->SetTotal_fee($total_fee);
	$input->SetRefund_fee($refund_fee);
    $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
    $input->SetOp_user_id(WxPayConfig::MCHID);
	$result = WxPayApi::refund($input);
}

if(isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != ""){
	$out_trade_no = $_REQUEST["out_trade_no"];
	$total_fee = $_REQUEST["total_fee"];	// 分
	$refund_fee = $_REQUEST["refund_fee"];	// 分
	$input = new WxPayRefund();
	$input->SetOut_trade_no($out_trade_no);
	$input->SetTotal_fee($total_fee);
	$input->SetRefund_fee($refund_fee);
    $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
    $input->SetOp_user_id(WxPayConfig::MCHID);
	printf_info(WxPayApi::refund($input));
	exit();
}
?>

