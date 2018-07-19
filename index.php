<?php
//单例模式01
class one{
	private static $temp;
	private function __construct(){}
	private function __clone(){}
	static function getnew(){
		if(!self::$temp || !self::$temp instanceof self){
			$newclass=new self;
			self::$temp=$newclass;
		}
		return self::$temp;
	}
}
$class01=one::getnew();
var_dump($class01);
//单例模式02
class one02{
	private function __construct(){}
	private function __clone(){}
	static function getnew(){
		static $temp;
		if(empty($temp)){
			$newclass=new self;
			$temp=$newclass;
		}
		return $temp;
	}
}
$class02=one02::getnew();
var_dump($class02);

?>