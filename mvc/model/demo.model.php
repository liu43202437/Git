<?php
class demo{
	public $db;
	
	function __construct(){
		$conn = mysqli_connect('localhost','root','','db0427');
		$this->db = $conn;
	}
	function select($sql){
		
		$result = mysqli_query($this->db,$sql);
		
		$datas =  array();
		
		if(!empty($result)){
			while($row=mysqli_fetch_assoc($result)){
				$datas[]=$row;
			}
		}
		mysqli_free_result($result);
		mysqli_close($this->db);
		
		return $datas;
	}
	
	function get_row($sql){

		$result = mysqli_query($this->db,$sql);
		
		$data =array();
		if($result){
			$data = mysqli_fetch_assoc($result);//查出要修改的数据
		}
		return $data;
	}
	
	function doadd($sql){
		
		$result = mysqli_query($this->db,$sql);
		$lastid = 0;
		if($result){
			$lastid = mysqli_insert_id($this->db);
		}
		
		return $lastid;
	}
	
	function doupdate($sql){
		
		$result = mysqli_query($this->db,$sql);
		
		return $result;
	}
	
	function dodel($sql){
		
		$result = mysqli_query($this->db,$sql);
		return $result;
	}
	
	
}