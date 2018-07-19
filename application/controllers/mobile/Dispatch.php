<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dispatch extends Base_MobileController
{
    protected $filepath ='';
    function __construct()
    {
        parent::__construct();
    }
    public function transpond($url = '',$postData = ''){
        $url = $this->post_input('url');
        if(empty($url)){
            $url = $this->get_input('url');
        }
        $postData = $this->post_input('postData');
        if(empty($postData)){
            $postData = $this->get_input('postData');
        }
        $post = $this->post_input('post');
        if(empty($post)){
            $post = $this->get_input('post');
        }
        if(empty($post)){
            $post = 1;
        }
        if($post == 1){
            $response = $this->post($url,$postData);
        }
        else{
            $response = $this->get($url);
        }
        echo $response;
    }

    public function post($url,$postData){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    public function get($url){
        $ch= curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER,0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  
        $response = curl_exec($ch);
        curl_close($ch);
    }
}
