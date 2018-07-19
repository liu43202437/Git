<?php

include './model/demo.model.php';

class DemoController 
{ 	
	public $model;
	
	function __construct(){
		$this->model = new demo();
	}
	
	function index() 
	{ 
		$sql = 'select * from test_content';
		
		$datas = $this->model->select($sql);
		// var_dump($datas);
		require('view/index.tpl.php'); //模板  //incluce
		
	} 
	
	function add() 
	{ 	
		require('view/add.tpl.php'); //模板
	} 
	
	function doadd() 
	{	
		//var_dump($_POST);exit;
		
		$title = $_POST['title'];
		$content = $_POST['content'];
		
		$sql = "insert into test_content(title,content,lanmuid) values('".$title."','".$content."','1')";
		
		$flag = $this->model->doadd($sql);
		
		if($flag){
			
			echo '添加成功';
			header('refresh:5;url=index.php?c=demo&a=index');exit;
			//header('location:index.php?c=demo&a=add');
			exit;
		}else{
			echo '添加失败';
			header('refresh:5;url=index.php?c=demo&a=index');
			exit;
			
		}
		
	}
	
	
	function update(){
		
		$id = $_GET['id'];
		
		$sql = "select * from test_content where id='".$id."'";
		
		$info = $this->model->get_row($sql);
		
		if(empty($info)){
			echo '找不到数据';
			header('refresh:5;url=index.php?c=demo&a=index');
			exit;
		}

		require('view/update.tpl.php'); //模板
	}
	
	function doupdate(){
		
		$id = $_POST['id'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		
		//var_dump($_POST);exit;
	
		$sql = "update test_content set title='".$title."',content='".$content."' where id = '".$id."'";
		$flag = $this->model->doupdate($sql);
		
		if($flag ){
			echo '修改成功';
			header('refresh:10;url=index.php?c=demo&a=index');exit;
		}else{
			echo '修改失败';
			header('refresh:5;url=index.php?c=demo&a=index');exit;
		}
	}
	
	function dodel(){
		
		$id = $_GET['id'];
		
		$sql = "delete from  test_content  where id = '".$id."'";
		
		$flag = $this->model->dodel($sql);
		
		if($flag){
			echo '删除成功';
			header('refresh:5;url=index.php?c=demo&a=index');exit;
		}else{
			echo '删除成功';
			header('refresh:5;url=index.php?c=demo&a=index');exit;
		}
	}
}
