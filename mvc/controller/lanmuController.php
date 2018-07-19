<?php
class DemoController 
{ 
	function index() 
	{ 
		//从数据库取数据
		$data['title']='First Title'; 
		$data['list']=array('A','B','C','D'); 
		
		
		require('view/index.tpl.php'); //模板
	} 
	
	function add() 
	{ 	
		/*
		$data['title']='First Title'; 
		$data['list']=array('A','B','C','D'); 
		
		require('view/index.php'); //模板
		*/
	} 
}
