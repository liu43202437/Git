<?php
class DemoController 
{ 
	function index() 
	{ 
		//�����ݿ�ȡ����
		$data['title']='First Title'; 
		$data['list']=array('A','B','C','D'); 
		
		
		require('view/index.tpl.php'); //ģ��
	} 
	
	function add() 
	{ 	
		/*
		$data['title']='First Title'; 
		$data['list']=array('A','B','C','D'); 
		
		require('view/index.php'); //ģ��
		*/
	} 
}
