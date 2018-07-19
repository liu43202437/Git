<?php
class mysqli02{
	public $con;
	function __construct(){
		$con=mysqli_connect("myhost","root",'','db0425');
		mysqli_query($con,'set names utf8');
		$this->con=$con;
	}
	function select($sql){
		$result=mysqli_query($this->con,$sql);
		$datas=array();
		while($row=mysqli_fetch_assoc($result)){
			$datas[]=$row;
		}
		mysqli_free_result($result);
		mysqli_close($this->con);
		return $datas;
	}
	function del($sql){
		$flag= mysqli_query($this->con,$sql);
		return $flag;
	}
	function insert($sql){
		$flag= mysqli_query($this->con,$sql);
		return $flag;
	}
	function row($sql){
		$result=mysqli_query($this->con,$sql);
		$row=mysqli_fetch_assoc($result);
		return $row;
	}
	function update($sql){
		$flag=mysqli_query($this->con,$sql);
		return $flag;
	}
}
?>