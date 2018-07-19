<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// default informations
$config['default_avatar'] 			= 'upload/avatar100.png';
$config['default_shipping_fee'] 	= 15.00;

// upload config
$config['upload_path'] 				= 'upload/';
$config['allowed_image_extensions']	= 'gif|jpg|png|jpeg|bmp|assetbundle';
$config['allowed_image_types'] 		= 'image/pjpeg|image/x-png|image/png|image/gif|image/jpeg|application/octet-stream';
$config['allowed_video_extensions'] = 'mpeg|mpg|3gp|mp4|flv|f4v';
$config['allowed_video_types'] 		= 'video/mpeg|video/msvideo|video/x-msvideo|video/3gpp|video/mp4|application/octet-stream';
$config['max_size'] 				= 1024 * 1024;		// kilobytes
$config['max_width'] 				= 0;				// pixel
$config['max_height'] 				= 0;				// pixel
$config['min_width'] 				= 0;				// pixel
$config['min_height'] 				= 0;				// pixel
$config['max_filename'] 			= 0;				// max filename length
$config['max_filename_increment'] 	= 100;
$config['file_ext_tolower'] 		= true;				// extension to lower
$config['overwrite'] 				= false;
$config['encrypt_name'] 			= true;				// random name
$config['remove_spaces'] 			= true;

$config['image_thumb_width'] 		= 200;
$config['image_thumb_height'] 		= 150;

// image watermarking
$config['watermark_type']			= 'overlay';			// text, overlay(image)
$config['watermark_vrt']			= 'bottom';			// watermark vertical position - top, middle, bottom
$config['watermark_hor']			= 'right';			// watermark horizontal position - left, center, right

$config['watermark_text']			= 'Copyright 2017. T-One';
$config['watermark_color']			= 'ffffff';			// text font color
$config['watermark_shadow']			= '666666';			// text drop shadow color
$config['watermark_size']			= 20;				// text font size
$config['watermark_font']			= '';				// text font file path

$config['watermark_image']			= 'upload/watermark.png';				// image file path
$config['watermark_opacity']		= 50;				// image overlay opacity	1 ~ 100


// third-party sdk information

// sms auth code config
$config['auth_code_length'] 			= 6;
$config['auth_code_expire'] 			= 5;				// minutes
$config['sms_3rdparty_epid'] 			= '122298';
$config['sms_3rdparty_username'] 		= 'sjwl51';
$config['sms_3rdparty_password'] 		= 'Sjwl90561';

$config['umeng_upush_ios_appkey'] 		= '';
$config['umeng_upush_ios_secret'] 		= '';
$config['umeng_upush_android_appkey'] 	= '';
$config['umeng_upush_android_secret'] 	= '';

$config['baidu_map_js_appkey']			= 'dZ5K1m0VW7ZEa9QQLgy1XjpxC7Y6Vgsy';

$config['openim_appkey']				= '23634183';
$config['openim_secret']				= 'df50270fb1dd1e11a4e0bc7b0db0bc50';

$config['yunjifen_appkey']				= 'EIY1qP9fvvrtvCUagwywGx1eV7M';
$config['yunjifen_secret']				= 'K3JoIrmJScT7HozbUc55sCDZia0w';
//$config['yunjifen_appkey']				= 'vBqtVvNPWM7HECe4ELvp2pLGnim';			//-- local
//$config['yunjifen_secret']				= 'mOD8pT9riJ250V2sZynndeKca1nL';

// weixin payment config
$config['wxpay_config'] = array(
	'app_id' 			=> 'wx7f72d279397d7758',
	'app_secret' 		=> '1e09f7c574aafb993219d2442912c19c',
	'mch_id' 			=> '1454334702',
	'partner_id' 		=> '795167aea2dedcf8919790f6b77db3ea',	//tiwangjue2017
	'notify_url' 		=> 'mobile/order/wxpay_notify'
);
