<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Files extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/';
    }

    public function browser()
    {
		$fileType = $this->get_input('fileType');
		$orderType = $this->get_input('orderType');
		$path = $this->get_input('path');
		
		if (empty($path)) {
			$path = "/";
		} else {
			if (!startsWith($path, '/')) {
				$path = "/" . $path;
			}
			if (!endsWith($path, '/')) {
				$path = $path . "/";
			}
		}

		$uploadPath = $this->config->item('upload_path');
		$browsePath = $uploadPath . $path;

		if (strpos($browsePath, "..") !== false) {
			die(json_capsule(array()));
		}
		
		$iterator = new FilesystemIterator($browsePath);
		$filelist = array();
		foreach($iterator as $entry) {
			$item['name'] = $entry->getFilename();
			$item['extension'] = $entry->getExtension();
			$item['url'] = base_url() . $browsePath . $entry->getFilename();
			$item['isDirectory'] = $entry->isDir();
			$item['size'] = $entry->getSize();
			$item['lastModified'] = $entry->getMTime();
			$filelist[] = $item;
		}
		
		if ($orderType == 'size') {
			usort($filelist, function($a, $b) {
			    return $a['size'] - $b['size'];
			});
		} else if ($orderType == 'type') {
			usort($filelist, function($a, $b) {
			    return $a['extension'] - $b['extension'];
			});
		} else {
			usort($filelist, function($a, $b) {
			    return $a['name'] - $b['name'];
			});
		}
		
		echo json_capsule($filelist);
    }
    
    public function upload()
    {
    	$fileType = $this->post_input('file_type');
    	$makeThumb = $this->post_input('make_thumb');
    	$watermark = $this->post_input('watermark');
    	
        $this->load->model('fileupload_model');
    	if (empty($fileType) || $fileType == 'image') {
    		$uploadDir = 'image/' . nowdate2() . '/';
    		$rslt = $this->fileupload_model->uploadImage('file', $uploadDir);
		} else if ($fileType == 'video') {
			$uploadDir = 'video/' . nowdate2() . '/';
			$rslt = $this->fileupload_model->uploadVideo('file', $uploadDir);
		} else {
			$uploadDir = 'binary/' . nowdate2() . '/';
			$rslt = $this->fileupload_model->uploadFile('file', $uploadDir);
		}
		
        if (is_int($rslt)) {
        	$data['message'] = parent::error_message('上传文件出现错误:'.$rslt);
			die(json_capsule($data));
        }
		$this->add_log('文件上传', array('path'=>$rslt));

		if ($fileType == 'image') {
			if ($watermark) {
				if (!$this->fileupload_model->setWatermark($rslt)) {
        			$data['message'] = parent::error_message('图片水印失败');
					die(json_capsule($data));
		        }
			}
			
			if ($makeThumb == 'true') {
				$thumb = $this->fileupload_model->makeThumb($rslt);
				$data['thumb'] = /*base_url().*/ $thumb;
			}
		}
		
		$data['filename'] = $_FILES['file']['name'];
		$data['filesize'] = $_FILES['file']['size'];
		
		$data['message'] = parent::success_message();
		$data['url'] = /*base_url().*/ $rslt;
		echo json_capsule($data);
    }
	
	public function ticket_upload(){
		$fileType = $this->post_input('file_type');
		$makeThumb = $this->post_input('make_thumb');
		$watermark = $this->post_input('watermark');

		$this->load->model('fileupload_model');
		if (empty($fileType) || $fileType == 'image') {
			$uploadDir =nowdate2() . '/';
			$rslt = $this->fileupload_model->uploadImage1('file', $uploadDir);
		}

		if (is_int($rslt)) {
			$data['message'] = parent::error_message('上传文件出现错误:'.$rslt);
			die(json_capsule($data));
		}
		$this->add_log('文件上传', array('path'=>$rslt));

		if ($fileType == 'image') {
			if ($watermark) {
				if (!$this->fileupload_model->setWatermark($rslt)) {
					$data['message'] = parent::error_message('图片水印失败');
					die(json_capsule($data));
				}
			}

			if ($makeThumb == 'true') {
				$thumb = $this->fileupload_model->makeThumb($rslt);
				$data['thumb'] = /*base_url().*/ $thumb;
			}
		}

		$data['filename'] = $_FILES['file']['name'];
		$data['filesize'] = $_FILES['file']['size'];

		$data['message'] = parent::success_message();
		$data['url'] = /*base_url().*/ $rslt;
		echo json_capsule($data);
	}
}
