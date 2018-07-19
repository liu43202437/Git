<?php

/**
 * @Author: liuzudong
 * @Date:   2018-04-08 10:22:25
 * @Last Modified by:   liuzudong
 */
class Logrotate extends Base_WechatPay
{
	const ROTATEPATH = '/mnt/nas/www/download/zhongwei/fulei/';
	// const ROTATEPATH = 'D:/fulei/log2/';
	function __construct()
    {
        parent::__construct();
        
    }
    //为了程序的可移植性，这里暂时使用php 清理日志，而非logrotate
    public function rotate(){
    	set_time_limit(60);
    	$random = mt_rand(10,20);
    	sleep($random);
    	if(!is_dir(self::ROTATEPATH)){
    		return;
    	}
    	$arr = scandir(self::ROTATEPATH);
    	foreach ($arr as $key => $value) {
    		if($value == '..' || $value == '.'){
    			continue;
    		}
    		$date = substr($value, 0,10);
    		$flag = time() - strtotime($date) > 3600*24*30 ? true : false;
    		if($flag){
    			$res = @unlink(self::ROTATEPATH.$value);
    		}
    	}
    }
    public function productFile(){
    	$this->createDir(self::ROTATEPATH);
    	$arr = range(1, 31);
    	foreach ($arr as $key => $value) {
    		if(strlen($value) == 1){
    			$value = str_pad($value, 2,'0',STR_PAD_LEFT);
    		}
    		file_put_contents(self::ROTATEPATH.'2018-02-'.$value.'.log', '刘祖栋');
    	}
    }
    public function createDir($dir){
        if(gettype($dir) == 'string'){
            $flag = is_dir($dir) || mkdir($dir,0777,true);
            return $flag;
        }
        else{
            $this->reply('参数错误，code:99');
            die;
        }
    }
}