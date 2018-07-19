<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {

	public function qrcode()
	{
		$text = $this->input->get('t');
		if (empty($text)) {
			exit();
		}
		
		$this->load->helper('qrcode');
		
		$width = $this->input->get('w');
		$size = $this->input->get('s');
		$margin = $this->input->get('m');
		$f = $this->input->get('f');
		
		if (empty($width)) {
			$width = 200;
		}
		if (empty($size)) {
			$size = 15;
		}
		if (empty($margin)) {
			$margin = 2;
		}
		
		if(!defined('IMAGE_WIDTH')) {
	        define('IMAGE_WIDTH', $width);
	        define('IMAGE_HEIGHT', $width);
	    }

	    if ($f == 1) {
	        $file = 'upload/qrcode/' . time() . '.png';
	        QRcode::png($text, $file, QR_ECLEVEL_L, $size, $margin, false);
	        echo base_url() . $file;
		} else {
			QRcode::png($text, false, QR_ECLEVEL_L, $size, $margin, false);
		}
	}
	
	public function city_list()
	{
		$provinceId = $this->input->get('province_id');
		if (empty($provinceId)) {
			$rslt['error'] = 1;
			echo json_encode($rslt);
		}
		
		$this->load->model('area_model');
		$cityList = $this->area_model->getCityList($provinceId);
		
		$rslt['error'] = 0;
		$rslt['result'] = $cityList;
		echo json_encode($rslt);
	}
    public function province_list()
    {
        $this->load->model('area_model');
        $provinceList = $this->area_model->getProvinceList();
        $rslt['error'] = 0;
        $rslt['result'] = $provinceList;
        echo json_encode($rslt);
    }

	
	public function download()
	{
		$this->load->model('config_model');
		$data['androidUrl'] = $this->config_model->getValue('android_download_url');
		$data['iphoneUrl'] = $this->config_model->getValue('iphone_download_url');
		
		$this->load->view('download', $data);
	}
}