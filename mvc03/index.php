<?php
// if(!empty($_GET['c']) && !empty($_GET['m'])){
// 	$control=$_GET['c'];
// 	$method=$_GET['m'];
// }
//构造路径
$control=!empty($_GET['c'])?$_GET['c']:'title';
$method=!empty($_GET['m'])?$_GET['m']:'select';
// var_dump($control);
// var_dump($method);
$path="./controller/".$control."Controller.php";
// echo $path;
$className=$control."Controller";
include($path);
$title=new $className();
$title->$method();
?>