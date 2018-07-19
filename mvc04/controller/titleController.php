<?php
class titleController{
	public $mysql;
	function __construct(){
		require("./model/mysql.php");
		$mysql=new mysql();
		$this->mysql=$mysql;
	}
	function jump($msg){
			$msg = $msg;
			$jumpUrl = 'index.php?c=title&m=select';
			$waitSecond = 3;
			include('tips.php');
			exit;
	}
	function select(){
		$sql="select * from tb1";
		$array=$this->mysql->select($sql);
		include('./view/index.tpl.php');
	}
	function dodel(){
		$id=$_GET['id'];
		$sql="delete from tb1 where id='$id'";
		$flag=$this->mysql->delete($sql);
		if($flag){
			$this->jump('删除成功');
		}
	}
	function update(){
		$id=$_GET['id'];
		var_dump($id);
		$sql="select * from tb1 where id='$id'";
		$row=$this->mysql->row($sql);
		include('./view/update.tpl.php');
	}
	function doupdate(){
		$id=$_POST['id'];
		$title=$_POST['title'];
		$content=$_POST['content'];
		$sql="update tb1 set title='$title',content='$content' where id='$id'";
		$flag=$this->mysql->update($sql);
		if($flag){
			$this->jump('修改成功');
		}
	}
	function add(){
		include('./view/add.tpl.php');
	}
	function doadd(){
		$title=$_POST['title'];
		$content=$_POST['content'];
		$sql="insert into tb1 values(null,'$title','$content');";
		$flag= $this->mysql->add($sql);
		if($flag){
			$this->jump('添加成功');
		}
	}
}
?>