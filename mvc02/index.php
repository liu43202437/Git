<?php
// if(!empty($_GET['control']) && !empty($_GET['method'])){
// 	$control=$_GET['control'];
// 	$method=$_GET['method'];
// }
// // $path="./controller/".$control.".tpl.php";
// $path="/controller/$control.tpl.php";
// echo $path."<br>";
// include($path);
// $title=new $control();
// $title->add();

header("content-type:text/html;charset=utf-8;");
if(!empty($_GET['c']) && !empty($_GET['m'])){
	$control=$_GET['c'];
	$method=$_GET['m'];
}
//拼装方法路径
$path="./controller/".$control."Controller.php";
echo $path;
$className=$control."Controller";
include($path);
$title=new $className();
$title->$method();
?>