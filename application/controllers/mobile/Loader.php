<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Loader extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
    
    public function app_launch()
    {
    	$deviceType = $this->post_input('device_type');
		$deviceUdid = $this->post_input('device_udid');

		$this->load->model('visit_model');
    	$this->visit_model->insert($deviceUdid);

    	parent::output(array());
    } 
    
    public function get_splash()
    {
    	$deviceType = $this->post_input('device_type');
    	$longitude = $this->post_input('longitude');
		$latitude = $this->post_input('latitude');
		
    	$this->load->model('splash_model');
    	$this->load->model('area_model');
    	
		$splashs = $this->splash_model->getAvailableSplashs($deviceType);
		if (empty($splashs)) {
			parent::output(array('splash' => null));
		}
		
		$province = '';
		$city = '';
		if (!empty($longitude) && $latitude) {
			try {
				$addrInfo = getAddress($this->config->item('baidu_map_js_appkey'), $longitude, $latitude);
				if (!empty($addrInfo)) {
					$province = $addrInfo['province'];
					$city = $addrInfo['city'];
				}
			} catch (Exception $e) {}
		}
		
		$splash = null;
		foreach ($splashs as $item) {
			if ($item['is_area_limit'] != 1 || empty($province)) {
				$splash = $item;
				break;
			} else {
				$areaList = $this->area_model->getLimitList(AREA_LIMIT_KIND_SPLASH, $item['id'], true);
				
				if ($item['area_limit_type'] == AREA_LIMIT_BLACKLIST) {
					$isAvailable = true;
				} else {
					$isAvailable = false;
				}
				
				foreach ($areaList as $area) {
					$isContained = false;
					if ($area['type'] == AREA_TYPE_PROVINCE) {
						if ($area['name'] == $province) {
							$isContained = true;
						}
					} else {
						$provinceArea = $this->area_model->get($area['parent_id']);
						if ($area['name'] == $city && $provinceArea['name'] = $province) {
							$isContained = true;
						}
					}
					
					if ($isContained) {
						if ($item['area_limit_type'] == AREA_LIMIT_BLACKLIST) {
							$isAvailable = false;
						} else {
							$isAvailable = true;
						}
						break;
					}
				}
				if ($isAvailable) {
					$splash = $item;
					break;
				}
			}
		} 
		if (!empty($splash)) {
			$splash['image'] = getFullUrl($splash['image']);
		}		
		parent::output(array('splash' => $splash));
    }

    public function get_bgimage()
    {
    	$this->load->model('bgimage_model');
    	
		$bgimages = $this->bgimage_model->getAvailableBgimages();
		if (empty($bgimages)) {
			parent::output(array('bgimage' => null));
		}
		
		$image = $bgimages[0];
		if (!empty($image)) {
			$image['image'] = getFullUrl($image['image']);
		}		
		parent::output(array('image' => $image));
    }
    
    public function show_splash()
    {
		$splashId = $this->post_input('splash_id');
		$this->load->model('splash_model');
		$this->splash_model->increaseShowCount($splashId);
		parent::output(array());
    }
    
    public function hit_splash()
    {
		$splashId = $this->post_input('splash_id');
		$this->load->model('splash_model');
		$this->splash_model->increaseHits($splashId);
		parent::output(array());
    }
    
	public function get_menu_items()
	{
		$this->load->model('config_model');
		$menuItems = json_decode($this->config_model->getValue('app_menu_items'), true);
		$resultItems = array();
		foreach ($menuItems as $key=>$item) {
			$resultItems[$key] = $item['label'];
		}
		parent::output(array(
			'menu_items' => $resultItems
		));
	}
	
	public function get_discover_items()
	{
		$this->load->model('config_model');
		$discoverItems = json_decode($this->config_model->getValue('custom_discover_items'), true);
		parent::output(array(
			'discover_items' => $discoverItems
		));
	}
	
	public function common_info()
	{
		$this->load->model('config_model');
		$menuItems = json_decode($this->config_model->getValue('app_menu_items'), true);
		$resultItems = array();
		foreach ($menuItems as $key=>$item) {
			$resultItems[$key] = $item['label'];
		}
		$discoverItems = json_decode($this->config_model->getValue('custom_discover_items'), true);
		parent::output(array(
			'menu_items' => $resultItems,
			'discover_items' => $discoverItems
		));
	}
	
	public function check_app_version()
	{
		$curVersion = $this->post_input('version');
		$this->load->model('appversion_model');
		$newVersion = $this->appversion_model->getTopRow(array('version >' => $curVersion), array('create_date' => 'DESC'));
		if (!empty($newVersion)) {
			parent::output(
				array(
					'new_version_exist' => 1, 
					'version' => $newVersion['version'], 
					'url' => getFullUrl($newVersion['url']),
					'description' => $newVersion['description']
				)
			);
		}
		parent::output(array('new_version_exist' => 0));
	}
	
	public function banner_list()
	{
		$deviceType = $this->post_input('device_type');
		$longitude = $this->post_input('longitude');
		$latitude = $this->post_input('latitude');
		
		$this->load->model('area_model');
		$this->load->model('banner_model');
		$banners = $this->banner_model->getAvailableBanners($deviceType);
		
		$province = '';
		$city = '';
		if (!empty($longitude) && $latitude) {
			try {
				$addrInfo = getAddress($this->config->item('baidu_map_js_appkey'), $longitude, $latitude);
				if (!empty($addrInfo)) {
					$province = $addrInfo['province'];
					$city = $addrInfo['city'];
				}
			} catch (Exception $e) {}
		}
		
		$rsltList = null;
		foreach ($banners as $item) {
			if ($item['is_show_limit'] != 1 || $item['is_area_limit'] != 1 || empty($province)) {
				unset($item['is_show_limit']);
				unset($item['is_area_limit']);
				unset($item['area_limit_type']);
				$item['image'] = getFullUrl($item['image']);
				$rsltList[] = $item;
			} else {
				$areaList = $this->area_model->getLimitList(AREA_LIMIT_KIND_BANNER, $item['id'], true);
				
				if ($item['area_limit_type'] == AREA_LIMIT_BLACKLIST) {
					$isAvailable = true;
				} else {
					$isAvailable = false;
				}
						
				foreach ($areaList as $area) {
					$isContained = false;
					if ($area['type'] == AREA_TYPE_PROVINCE) {
						if ($area['name'] == $province) {
							$isContained = true;
						}
					} else {
						$provinceArea = $this->area_model->get($area['parent_id']);
						if ($area['name'] == $city && $provinceArea['name'] = $province) {
							$isContained = true;
						}
					}
					
					if ($isContained) {
						if ($item['area_limit_type'] == AREA_LIMIT_BLACKLIST) {
							$isAvailable = false;
						} else {
							$isAvailable = true;
						}
						break;
					}
				}
				if ($isAvailable) {
					unset($item['is_show_limit']);
					unset($item['is_area_limit']);
					unset($item['area_limit_type']);
					$item['image'] = getFullUrl($item['image']);
					$rsltList[] = $item;
				}
			}
		}
		
		// get banner2 
		$banner2 = null;
		$filter['banner_kind'] = BANNER_NEARBY;
		$filter['is_show'] = 1;
		$order['create_date'] = 'DESC';
		$banners = $this->banner_model->getList($filter, $order, 1, 1);
		if (!empty($banners)) {
			foreach ($banners as $b2) {
				if ($b2['platform'] == DEVICE_TYPE_ALL || $b2['platform'] == $deviceType) {
					$banner2 = $b2;
					$banner2['image'] = getFullUrl($b2['image']);
					break;
				}
			}
		}
		
		// get top item
		$this->load->model('config_model');
    	$topItem = json_decode($this->config_model->getValue('top_item'), true);
    	if (empty($topItem) || empty($topItem['item_id'])) {
			$topItem = null;
    	} else {
			if ($topItem['item_kind'] == BANNER_KIND_ARTICLE || $topItem['item_kind'] == BANNER_KIND_GALLERY || $topItem['item_kind'] == BANNER_KIND_VIDEO || $topItem['item_kind'] == BANNER_KIND_LIVE) {
				$this->load->model('content_model');
				$e = $this->content_model->get($topItem['item_id']);
				if (empty($e)) {
					$topItem = null;
				} else {
					$e['image'] = getFullUrl($e['image']);
					$e['thumb'] = getFullUrl($e['thumb']);
					$topItem['info'] = $e;
					$topItem['title'] = $e['title'];
					$topItem['image'] = $e['image'];
					if ($topItem['item_kind'] == BANNER_KIND_GALLERY) {
						$topItem['info']['image1'] = getFullUrl($e['image1']);
						$topItem['info']['image2'] = getFullUrl($e['image2']);
						unset($topItem['info']['images']);
					}
				}
			} else if ($topItem['item_kind'] == BANNER_KIND_MEMBER) {
				$this->load->model('member_model');
				$e = $this->member_model->get($topItem['item_id']);
				if (empty($e)) {
					$topItem = null;
				} else {
					$e['image'] = getFullUrl($e['image']);
					$topItem['info'] = $e;
					$topItem['title'] = $e['name'];
					$topItem['image'] = $e['image'];
				}
			} else if ($topItem['item_kind'] == BANNER_KIND_EVENT) {
				$this->load->model('event_model');
				$e = $this->event_model->get($topItem['item_id']);
				if (empty($e)) {
					$topItem = null;
				} else {
					if (!$e['has_ticket']) {
						$topItem['is_ticket'] = 0;
					} else {
						$e['ticket_prices'] = $this->event_model->getTicketPriceList($e['id']);
					}
					$e['image'] = getFullUrl($e['image']);
					$e['video'] = getFullUrl($e['video']);
					$e['ticket_image'] = getFullUrl($e['ticket_image']);
					$e['ticket_pos_image'] = getFullUrl($e['ticket_pos_image']);
					$topItem['title'] = $e['title'];
					$topItem['image'] = $e['image'];
					$topItem['info'] = $e;
				}
			}
    	}
    	
		parent::output(array(
			'top_item' => $topItem,
			'banners' => $rsltList,
			'banner2' => $banner2,
			'count' => count($rsltList)
		));
	}
}
