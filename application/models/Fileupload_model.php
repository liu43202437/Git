<?php

// fileupload model
class Fileupload_model extends CI_Model {

	protected $uploadPath = '';
		
	function __construct()
    {
    	parent::__construct();
    	
    	$this->uploadPath = $this->config->item('upload_path');
    }
    
    function check_img_type($img_type)
    {
    	$allowed = explode("|", $this->config->item('allowed_image_types'));
    	return in_array($img_type, $allowed);
    }
    
    function check_video_type($img_type)
    {
    	$allowed = explode("|", $this->config->item('allowed_video_types'));
    	return in_array($img_type, $allowed);
    }

    function check_img_ext($extension)
    {
		$allowed = explode("|", $this->config->item('allowed_image_extensions'));
		return in_array(strtolower($extension), $allowed);
    }
    
    function check_video_ext($extension)
    {
		$allowed = explode("|", $this->config->item('allowed_video_extensions'));
		return in_array(strtolower($extension), $allowed);
    }

	function uploadImage($name, $dir)
	{
		$dir = $this->uploadPath . $dir;
		
		if (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0) {

			if (!$this->check_img_type($_FILES[$name]['type'])) {
				return 304;
			}
			
			$extension = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
			if (!$this->check_img_ext($extension)) {
				return 300;
			}
			
			$temp = $_FILES[$name]['tmp_name'];
			// check directory
			if (!is_dir($dir)) {
				if (!mkdir($dir, 0777, true)) {
					return 305;
				}
			}
			// check upload status
			if (!is_uploaded_file($temp)) {
				return 306;
			}

			$upfile = $dir . time() . '.' . $extension;			// . "_" . $_FILES[$name]['name'];
			while (file_exists($upfile)) {
				$upfile = $dir . time() . '.' . $extension;		// . "_" . $_FILES[$name]['name'];
			}

			// move file
			if (!move_uploaded_file($temp, $upfile)) {
				return 307;
			}
			/*$url = base_url() . $upfile;
			return $url;*/
			return $upfile;
		}

		return 302;
	}

	function uploadImage1($name, $dir)
	{
		$dir = "/var/www/download/zhongwei/image/ticket/".$dir;

		if (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0) {

			if (!$this->check_img_type($_FILES[$name]['type'])) {
				return 304;
			}

			$extension = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
			if (!$this->check_img_ext($extension)) {
				return 300;
			}

			$temp = $_FILES[$name]['tmp_name'];
			// check directory
			if (!is_dir($dir)) {
				if (!mkdir($dir, 0777, true)) {
					return 305;
				}
			}
			// check upload status
			if (!is_uploaded_file($temp)) {
				return 306;
			}

			$upfile = $dir . time() . '.' . $extension;			// . "_" . $_FILES[$name]['name'];
			while (file_exists($upfile)) {
				$upfile = $dir . time() . '.' . $extension;		// . "_" . $_FILES[$name]['name'];
			}

			// move file
			if (!move_uploaded_file($temp, $upfile)) {
				return 307;
			}
			/*$url = base_url() . $upfile;
			return $url;*/
			return $upfile;
		}

		return 302;
	}

	function setWatermark($sourceFile)
	{
		try {
			$config['source_image'] = $sourceFile;
			$config['wm_type'] = $this->config->item('watermark_type');
			if ($config['wm_type'] == 'text') {
				$config['wm_text'] = $this->config->item('watermark_text');
				//$config['wm_font_path'] = $this->config->item('watermark_font');
				$config['wm_font_size'] = $this->config->item('watermark_size');
				$config['wm_font_color'] = $this->config->item('watermark_color');
				$config['wm_shadow_color'] = $this->config->item('watermark_shadow');
				$config['wm_shadow_distance'] = 1;
			} else {
				$config['wm_overlay_path'] = $this->config->item('watermark_image');
				$config['wm_opacity'] = $this->config->item('watermark_opacity');
			}
			$config['wm_vrt_alignment'] = $this->config->item('watermark_vrt');
			$config['wm_hor_alignment'] = $this->config->item('watermark_hor');

			$this->load->library('image_lib', $config);
			return $this->image_lib->watermark();
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	function makeThumb($sourceFile)
	{
		// create thumb
	    $config_manip = array(
	        'image_library' => 'gd2',
	        'source_image' => $sourceFile,
	        'maintain_ratio' => TRUE,
	        'create_thumb' => TRUE,
	        'thumb_marker' => '_thumb',
	        'width' => $this->config->item('image_thumb_width'),
	        'height' => $this->config->item('image_thumb_height')
	    );
	    $this->load->library('image_lib', $config_manip);
	    if (!$this->image_lib->resize()) {
	    	return 302;
	    }
	    // clear //
	    $this->image_lib->clear();
	    
	    $parts = pathinfo($sourceFile);
	    $thumbFile = $parts['dirname'] . '/' . $parts['filename'] . '_thumb.' . $parts['extension'];
	    return $thumbFile;
	}
	
	function uploadVideo($name, $dir)
	{
		$dir = $this->uploadPath . $dir;
		
		if (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0) {
			
			/*if (!$this->check_video_type($_FILES[$name]['type'])) {
				log_info($_FILES[$name]['type']);
				return 304;
			}*/
			
			$extension = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
			if (!$this->check_video_ext($extension)) {
				return 300;
			}

			$temp = $_FILES[$name]['tmp_name'];
			// check directory
			if (!is_dir($dir)) {
				if (!mkdir($dir, 0777, true)) {
					return 305;
				}
			}
			// check upload status
			if (!is_uploaded_file($temp)) {
				return 306;
			}
			
			$upfile = $dir . time() . '.' . $extension;			// . "_" . $_FILES[$name]['name'];
			while (file_exists($upfile)) {
				$upfile = $dir . time() . '.' . $extension;		// . "_" . $_FILES[$name]['name'];
			}

			// move file
			if (!move_uploaded_file($temp, $upfile)) {
				return 307;
			}
			/*$url = base_url() . $upfile;
			return $url;*/
			return $upfile;
		}
        
		return 302;
	}
	
	function uploadFile($name, $dir)
	{
		$dir = $this->uploadPath . $dir;
		
		if (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0) {
			
			$extension = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);

			$temp = $_FILES[$name]['tmp_name'];
			// check directory
			if (!is_dir($dir)) {
				if (!mkdir($dir, 0777, true)) {
					return 305;
				}
			}
			// check upload status
			if (!is_uploaded_file($temp)) {
				return 306;
			}
			
			$upfile = $dir . time() . '.' . $extension;			// . "_" . $_FILES[$name]['name'];
			while (file_exists($upfile)) {
				$upfile = $dir . time() . '.' . $extension;		// . "_" . $_FILES[$name]['name'];
			}

			// move file
			if (!move_uploaded_file($temp, $upfile)) {
				return 307;
			}
			/*$url = base_url() . $upfile;
			return $url;*/
			return $upfile;
		}
        
		return 302;
	}
}
?>