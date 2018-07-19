<?php

include_once "config.php";
include_once "api.php";

$log_dir = __DIR__;
$log_path = $log_dir."/log.txt";
$log_file = fopen($log_path, "a+");
fwrite($log_file, "credit_add"."\r\n");
fwrite($log_file, json_encode($_REQUEST)."\r\n");
fclose($log_file);

if(parseRequest($appKey, $appSecret, $_REQUEST)) {
    echo json_encode(array("status" => "ok", "errorMessage" => "", "bizId" => "203", "credits" => "2000"));
}
else {
    echo json_encode(array("status" => "fail", "errorMessage" => "some info not match", "bizId" => "", "credits" => ""));
}
