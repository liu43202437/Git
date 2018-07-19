<?php
class demoController{
	function index(){
		// $con=mysqli_connect("myhost","root",'','db0425');
		// mysqli_query($con,'set names utf8');
		$sql="select * from tb1 limit 10;";
		// $result=mysqli_query($con,$sql);
		// $datas=array();
		// if(!empty($result)){
		// 	while($row=mysqli_fetch_assoc($result)){
		// 		$datas[]=$row;
		// 	}
		// }
		// mysqli_free_result($result);
		// mysqli_close($con);
		include("./model/mysqli.php");
		$mysqli=new mysqli02();
		$datas=$mysqli->select($sql);
		include("./view/index.tpl.php");
	}
	function dodel(){
		$id=$_GET['id'];
		// $con=mysqli_connect("myhost","root",'','db0425');
		$sql="delete from tb1 where id='$id'";
		// $flag=mysqli_query($con,$sql);
		include("./model/mysqli.php");
		$mysqli=new mysqli02();
		$flag=$mysqli->del($sql); 
		if($flag){
			echo "删除成功";
		}
		header('refresh=3;uri=index.php?c=demo&m=index');
	}
	function add(){
		
		include('./view/add.tpl.php');
	}
	function doadd(){
		// $con=mysqli_connect('myhost','root','','db0425');
		// mysqli_query($con,'set names utf8;');
		$title=$_POST['title'];
		var_dump($title);
		$content=$_POST['content'];
		$sql="insert into tb1 values(null,'$title','$content')";
		include("./model/mysqli.php");
		$mysqli=new mysqli02();
		$flag=$mysqli->insert($sql);
		// $flag=mysqli_query($con,$sql);
		if($flag){
			echo "插入成功";
			header('refresh:3;url=index.php?c=demo&m=index');
		}else{
			echo "插入失败";
			header('refresh:3;url=index.php?c=demo&m=index');
		}
	}
	function update(){
		$id=$_GET['id'];
		// $con=mysqli_connect('myhost','root','','db0425');
		// mysqli_query($con,'set names utf8;');
		$sql="select * from tb1 where id='$id'";
		// $result=mysqli_query($con,$sql);
		// if($result){
		// 	$info=mysqli_fetch_assoc($result);
		// }
		// mysqli_free_result($result);
		// mysqli_close($con);

		// include("./model/mysqli.php");
		// $mysqli=new mysqli02();
		// $info=$mysqli->select($sql);
		// foreach ($info as $key => $value) {
		// 	if($value){
		// 		$info=$value;
		// 	}
		// }

		include("./model/mysqli.php");
		$mysqli=new mysqli02();
		$info=$mysqli->row($sql);
		// var_dump($info);
		include("./view/update.tpl.php");
	}
	function doupdate(){
		$id=$_POST['id'];
		$title=$_POST['title'];
		$content=$_POST['content'];
		// $con=mysqli_connect('myhost','root','','db0425');
		// mysqli_query($con,'set names utf8;');
		$sql="update tb1 set title='$title',content='$content' where id='$id'";
		// $flag=mysqli_query($con,$sql);
		include("./model/mysqli.php");
		$mysqli=new mysqli02();
		$flag=$mysqli->update($sql);
		if($flag){
			echo "更新成功";
			header('refresh:3;url=index.php?c=demo&m=index');
		}
	}
}
?>