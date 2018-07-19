<?php
header("content-type:text/html;charset=utf-8;");

$c_str=$_GET['c']; //获取要运行的controller  //demo
$c_name=$c_str.'Controller';   //demoController

$c_path='controller/'.$c_name.'.php'; //准确的controller文件路径
// echo $c_path;

$method=$_GET['a']; //获取要运行的action  //index

require($c_path); //加载controller文件 

$controller = new $c_name(); //实例化
//var_dump($controller );exit;

$controller->$method(); // demoController->index();
// var_dump($c_str);
// var_dump($method);

