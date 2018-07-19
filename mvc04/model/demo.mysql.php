<?php
require("./model/mysql.php");
class demo extends mysql{
	public $con;
	function __construct(){
		$con=mysqli_connect('localhost','root','','db0425');
		mysqli_query($con,'set names utf8;');
		$this->con=$con;
	}
	function select($sql){
		$result=mysqli_query($this->con,$sql);
		if($result){
			$array=array();
			while($row=mysqli_fetch_assoc($result)){
				$array[]=$row;
			}
		}
		mysqli_free_result($result);
		mysqli_close($this->con);
		return $array;
	}
	function row($sql){
		$result=mysqli_query($this->con,$sql);
		$row=mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		mysqli_close($this->con);
		return $row;
	}
	function delete($sql){
		$flag=mysqli_query($this->con,$sql);
		return $flag;
	}
	function add($sql){
		$flag=mysqli_query($this->con,$sql);
		return $flag;
	}
	function update($sql){
		$flag=mysqli_query($this->con,$sql);
		return $flag;
	}

}
?>