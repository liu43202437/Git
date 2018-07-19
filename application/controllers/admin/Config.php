<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/config/';
    }

    public function menu_item()
    {
    	$this->load->model('config_model');
    	$this->load->model('category_model');
    	
		$this->data = array_merge($this->data, json_decode($this->config_model->getValue('app_menu_items'), true));
		$this->data['categories'] = $this->category_model->getAll();

		$this->assign_message();
		$this->load_view('menu_item');
    }
    public function save_menu_item()
    {
    	$menuItems['top_news'] = array(
    		'label' => $this->post_input('top_news_label'),
    		'category' => intval($this->post_input('top_news_category'))
    	);
    	$menuItems['video_links'] = array(
    		'label' => $this->post_input('video_links_label'),
    		'category' => intval($this->post_input('video_links_category'))
    	);
    	$menuItems['videos'] = array(
    		'label' => $this->post_input('videos_label'),
    		'category' => intval($this->post_input('videos_category'))
    	);
    	$menuItems['babys'] = array(
    		'label' => $this->post_input('babys_label'),
    		'category' => intval($this->post_input('babys_category'))
    	);
    	$menuItems['recent_events'] = array(
    		'label' => $this->post_input('recent_events_label')
    	);
    	$menuItems['past_events'] = array(
    		'label' => $this->post_input('past_events_label')
    	);
    	$menuItems['live_videos'] = array(
    		'label' => $this->post_input('live_videos_label')
    	);
    	$menuItems['event_matchs'] = array(
    		'label' => $this->post_input('event_matchs_label')
    	);
    	$menuItems['club'] = array(
    		'label' => $this->post_input('club_label')
    	);
    	$menuItems['users'] = array(
    		'label' => $this->post_input('users_label')
    	);
    	$menuItems['chat'] = array(
    		'label' => $this->post_input('chat_label')
    	);
    	
    	$this->load->model('config_model');
    	$rslt = $this->config_model->set('app_menu_items', json_encode($menuItems));
    	if (empty($rslt)) {
			$this->error_redirect('config/menu_item');
    	}
		$this->success_redirect('config/menu_item');
    }
    
    public function category()
    {
    	$this->load->model('category_model');
		$this->data['itemList'] = $this->category_model->getAll();

		$this->assign_message();
		$this->load_view('category');
    }
    public function save_category()
    {
		$id = $this->post_input('id');
		$data['name'] = $this->post_input('name');
		
		$this->load->model('category_model');
		if (empty($id)) {
			$rslt = $this->category_model->insert($data['name']);
		} else {
			$rslt = $this->category_model->update($id, $data);
		}
		if (empty($rslt)) {
			$this->error_redirect('config/category');
		}
		$this->success_redirect('config/category');
    }
    public function delete_category()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('category_model');
		foreach ($ids as $id) {
			$this->category_model->delete($id);
		}
		$this->add_log('删除分类', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function version()
    {
    	$this->load->model('appversion_model');
		$this->data['itemList'] = $this->appversion_model->getAll(null, array('create_date'=>'DESC'));
		$totalCount = $this->appversion_model->getCount();
		
		$this->assign_message();
		$this->assign_pager($totalCount);
		$this->load_view('version');
    }
    public function save_version()
    {
    	$filename = $this->post_input('filename');
    	$filesize = $this->post_input('filesize');
    	$version = $this->post_input('version_1');
    	$version .= "." . $this->post_input('version_2');
    	$version .= "." . $this->post_input('version_3');
    	$url = $this->post_input('url');
    	$description = $this->post_input('description');
    	
    	$this->load->model('appversion_model');
    	$rslt = $this->appversion_model->insert($filename, $filesize, $version, $url, $description, $this->adminId, $this->adminName);
    	if (empty($rslt)) {
			$this->error_redirect('config/version');
    	}    	
		$this->success_redirect('config/version');
    }
    public function delete_version()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('appversion_model');
		foreach ($ids as $id) {
			$this->appversion_model->delete($id);
		}
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function audit($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	$this->load->model('auditconfig_model');
    	$this->data['itemList'] = $this->auditconfig_model->getConfig($kind);
    	$this->data['kind'] = $kind;
    	$this->assign_message();
		$this->load_view('audit');
    }
    public function save_audit_item()
    {
		$kind = $this->post_input('kind');
		$id = $this->post_input('id');
		//$data['attr_name'] = $this->post_input('attr_name');
		$data['attr_label'] = $this->post_input('attr_label');
		$data['attr_hint'] = $this->post_input('attr_hint');
		$data['value_type'] = $this->post_input('value_type');
		$data['target_field'] = $this->post_input('target_field');
		$values = $this->post_input('values');
		if ($data['value_type'] == 'select') {
			$data['values'] = implode("|", $values);
		} else {
			$data['values'] = null;
		}
		if ($data['value_type'] != 'string' && $data['value_type'] != 'integer' && $data['value_type'] != 'float') {
			$data['attr_hint'] = null;
		}
		
		$this->load->model('auditconfig_model');
		if (empty($id)) {
			$attrCount = $this->auditconfig_model->getCount(array('kind' => $kind));
			$data['attr_name'] = 'attribute' . ($attrCount + 1);
			$rslt = $this->auditconfig_model->insert(
				$kind,
				null,
				$data['attr_name'],
				$data['attr_label'],
				$data['attr_hint'],
				$data['value_type'],
				$data['values'],
				$data['target_field']);
		} else {
			$rslt = $this->auditconfig_model->update($id, $data);
		}
		if (empty($rslt)) {
			$this->error_redirect('config/audit/'.$kind);
		}
		$this->success_redirect('config/audit/'.$kind);
    }
    public function delete_audit_item()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('auditconfig_model');
		$id = $ids[0];
		
		$auditItem = $this->auditconfig_model->get($id);
		$nameIndex = intval(substr($auditItem['attr_name'], 9));
		$rslt = $this->auditconfig_model->delete($id);
		if (empty($rslt)) {
			die(json_capsule(parent::error_message()));
		}
		
		$configs = $this->auditconfig_model->getConfig($auditItem['kind']);
		foreach ($configs as $item) {
			$iNameIndex = intval(substr($item['attr_name'], 9));
			if ($iNameIndex > $nameIndex) {
				$data['attr_name'] = 'attribute' . ($iNameIndex - 1);
				$this->auditconfig_model->update($item['id'], $data);
			}
		}
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    public function change_audit_order()
    {
    	$ids = $this->post_input('ids');
		if (empty($ids)) {
			die(json_capsule(parent::error_message()));
		}

		$this->load->model('auditconfig_model');
		foreach ($ids as $key=>$id) {
			$data['attr_name'] = "attribute" . ($key+1);
			$this->auditconfig_model->update($id, $data);
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function challenge()
    {
    	$this->load->model('challenge_model');
    	$this->data['itemList'] = $this->challenge_model->getAll();
    	$this->assign_message();
		$this->load_view('challenge');
    }
    public function delete_challenge()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('challenge_model');
		$id = $ids[0];
		
		$this->challenge_model->delete($id);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    public function challenge_toggle_show()
    {
		$id = $this->post_input('id');
		if (empty($id)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}

		$this->load->model('challenge_model');
		
		$data['is_show$'] = '1-is_show';
		$this->challenge_model->update($id, $data);
		
		echo json_capsule(parent::success_message());
    }
    public function edit_challenge()
    {
    	$this->load->model('challenge_model');
    	$this->load->model('auditconfig_model');
    	
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$this->data['configs'] = array();
			
			$itemInfo = $this->challenge_model->getEmptyRow();
		} else {
			$this->data['isNew'] = false;
			$this->data['configs'] = $this->auditconfig_model->getConfig(AUDIT_KIND_CHALLENGE, $id);
			$itemInfo = $this->challenge_model->get($id);
		}
		
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit_challenge');
    }
    public function save_challenge()
    {
		$id = $this->post_input('id');
		$title = $this->post_input('title');
		$image = $this->post_input('image');
		$introduction = $this->post_input('introduction');
		$configs = json_decode($this->post_input('configs', ''), true);
		
		$this->load->model('challenge_model');
    	$this->load->model('auditconfig_model');
    	
    	if (empty($id)) {
			$id = $this->challenge_model->insert($title, $image, $introduction);
			if (empty($id)) {
				parent::error_redirect('config/edit_challenge');
			}
    	} else {
			$data['title'] = $title;
			$data['image'] = $image;
			$data['introduction'] = $introduction;
			$rslt = $this->challenge_model->update($id, $data);
			if (empty($rslt)) {
				parent::error_redirect('config/edit_challenge?id=' . $id);
			}
    	}
    	
    	$this->auditconfig_model->deleteByKind(AUDIT_KIND_CHALLENGE, $id);
    	foreach ($configs as $key=>$item) {
			$attrName = 'attribute' . ($key + 1);
			if ($item['value_type'] == 'select') {
				$values = implode("|", $item['values']);
			} else {
				$values = null;
			}
		
			$this->auditconfig_model->insert(
				AUDIT_KIND_CHALLENGE, $id,
				$attrName, $item['attr_label'], $item['attr_hint'], $item['value_type'], $values, $item['target_field']
			);
    	}
		$this->success_redirect('config/challenge');
    }
    
    
    public function gift()
    {
    	$searchName = $this->get_input('search_name');
    	
    	$filters = null;
    	$orders = null;
    	
    	if (!empty($searchName)) {
			$filters['name%'] = $searchName;
    	}
    	if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		}
		
    	$this->load->model('gift_model');
    	$this->load->model('config_model');
    	
    	$totalCount = $this->gift_model->getCount($filters);
    	$this->data['itemList'] = $this->gift_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	$this->data['searchName'] = $searchName;
    	$this->data['isOpenGift'] = $this->config_model->getValue('is_open_gift');
    	
		$this->assign_message();
		$this->assign_pager($totalCount);
		$this->load_view('gift');
    }
    public function toggle_open_gift()
    {
    	$isOpen = $this->post_input('is_open_gift');
    	$isOpen = ($isOpen == 'true') ? '1' : '0';
    	
		$this->load->model('config_model');
		$this->config_model->set('is_open_gift', $isOpen);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    public function save_gift()
    {
		$id = $this->post_input('id');
		$data['name'] = $this->post_input('name');
		$data['price'] = $this->post_input('price');
		$data['exp'] = $this->post_input('exp');
		$data['image'] = $this->post_input('image');
		
		$this->load->model('gift_model');
		if (empty($id)) {
			$rslt = $this->gift_model->insert($data['name'], $data['price'], $data['exp'], $data['image']);
		} else {
			$rslt = $this->gift_model->update($id, $data);
		}
		if (empty($rslt)) {
			$this->error_redirect('config/gift');
		}
		$this->success_redirect('config/gift');
    }
    public function delete_gift()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('gift_model');
		foreach ($ids as $id) {
			$this->gift_model->delete($id);
		}
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function basis()
    {
    	$this->load->model('config_model');
		$this->data['expPerPoint'] = $this->config_model->getValue('exp_per_point');
		$this->data['expPerMoney'] = $this->config_model->getValue('exp_per_money');
		$this->data['pointPerMoney'] = $this->config_model->getValue('point_per_money');
		$pointPrices = $this->config_model->getValue('point_prices');
		if (empty($pointPrices)) {
			$pointPrices = '';
		}
		$this->data['pointPrices'] = json_decode($pointPrices, true);
		
		$this->assign_message();
		$this->load_view('basis');
    }
    public function save_basis()
    {
		$expPerPoint = $this->post_input('exp_per_point');
		$expPerMoney = $this->post_input('exp_per_money');
		$pointPerMoney = $this->post_input('point_per_money');
		$points = $this->post_input('points', array());
		$prices = $this->post_input('prices', array());
		
		$pointPrices = array();
		foreach ($points as $key=>$point) {
			$pointPrices[] = array(
				'point' => $point,
				'price' => $prices[$key]
			);
		}
		
		$this->load->model('config_model');
    	$this->config_model->set('exp_per_point', $expPerPoint);
    	$this->config_model->set('exp_per_money', $expPerMoney);
    	$this->config_model->set('point_per_money', $pointPerMoney);
    	$this->config_model->set('point_prices', json_encode($pointPrices));
    	
    	$this->success_redirect('config/basis');
    }
    
    public function about()
    {
		$this->load->model('config_model');
		$this->data['aboutContent'] = $this->config_model->getValue('about_content');
		
		$this->assign_message();
		$this->load_view('about');
    }
    public function save_about()
    {
		$aboutContent = $this->post_input('about_content');
		
		$this->load->model('config_model');
    	$this->config_model->set('about_content', $aboutContent);
    	
    	$this->success_redirect('config/about');
    }
    
    public function hits()
    {
    	$this->load->model('config_model');
		$this->data['articleHits'] = $this->config_model->getValue('base_article_hits');
		$this->data['galleryHits'] = $this->config_model->getValue('base_gallery_hits');
		$this->data['videoHits'] = $this->config_model->getValue('base_video_hits');
		$this->data['liveHits'] = $this->config_model->getValue('base_live_hits');
		
		$this->assign_message();
		$this->load_view('hits');
    }
    public function save_hits()
    {
    	$articleHits = $this->post_input('article_hits');
    	$galleryHits = $this->post_input('gallery_hits');
    	$videoHits = $this->post_input('video_hits');
    	$liveHits = $this->post_input('live_hits');
    	
    	$this->load->model('config_model');
    	$this->config_model->set('base_article_hits', intval($articleHits));
    	$this->config_model->set('base_gallery_hits', intval($galleryHits));
    	$this->config_model->set('base_video_hits', intval($videoHits));
    	$this->config_model->set('base_live_hits', intval($liveHits));
    	
		$this->success_redirect('config/hits');
    }

    public function search_word()
    {
    	$this->load->model('config_model');
		$this->data['search_word'] = $this->config_model->getValue('search_word');
		
		$this->assign_message();
		$this->load_view('search_word');
    }
    public function save_search_word()
    {
    	$search_word = $this->post_input('search_word');
    	
    	$this->load->model('config_model');
    	$this->config_model->set('search_word', $search_word);
    	
		$this->success_redirect('config/search_word');
    }
    
    public function vocabulary()
    {
    	$this->load->model('restrictvocab_model');
    	$totalCount = $this->restrictvocab_model->getCount();
    	$this->data['itemList'] = $this->restrictvocab_model->getList(null, null, $this->pager['pageNumber'], $this->pager['pageSize']);
		$this->assign_message();
		$this->assign_pager($totalCount);
		$this->load_view('vocabulary');
    }
    public function save_vocab()
    {
		$id = $this->post_input('id');
		$data['vocab'] = $this->post_input('vocab');
		
		$this->load->model('restrictvocab_model');
		if (empty($id)) {
			$rslt = $this->restrictvocab_model->insert($data['vocab']);
		} else {
			$rslt = $this->restrictvocab_model->update($id, $data);
		}
		if (empty($rslt)) {
			$this->error_redirect('config/vocabulary');
		}
		$this->success_redirect('config/vocabulary');
    }
    public function delete_vocab()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('restrictvocab_model');
		foreach ($ids as $id) {
			$this->restrictvocab_model->delete($id);
		}
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function layout()
    {
    	$this->load->model('banner_model');
    	$this->load->model('config_model');
    	
    	$filter['banner_kind'] = BANNER_MAIN;
    	$filter['is_listed'] = 1;
    	$listedItems = $this->banner_model->getAll($filter);
    	
    	$filter['is_listed'] = 0;
    	$filter['is_show'] = 1;
    	$unlistedItems = $this->banner_model->getAll($filter);
    	
    	$this->data['listedItems'] = $listedItems;
    	$this->data['unlistedItems'] = $unlistedItems;
    	
    	$topItem = json_decode($this->config_model->getValue('top_item'), true);
    	if (empty($topItem)) {
			$topItem = array('item_kind'=>'', 'item_id'=>'');
    	}
    	if (empty($topItem['item_id'])) {
			$topItem['item_label'] = '';
    	} else {
    		$topItem['item_label'] = '[' . $topItem['item_id'] . '] '; 
			if ($topItem['item_kind'] == BANNER_KIND_ARTICLE || $topItem['item_kind'] == BANNER_KIND_GALLERY || $topItem['item_kind'] == BANNER_KIND_VIDEO || $topItem['item_kind'] == BANNER_KIND_LIVE) {
				$this->load->model('content_model');
				$e = $this->content_model->get($topItem['item_id']);
				$topItem['item_label'] .= ellipseStr($e['title'], 20);
			} else if ($topItem['item_kind'] == BANNER_KIND_MEMBER) {
				$this->load->model('member_model');
				$e = $this->member_model->get($topItem['item_id']);
				$topItem['item_label'] .= $e['name'];
			} else if ($topItem['item_kind'] == BANNER_KIND_EVENT) {
				$this->load->model('event_model');
				$e = $this->event_model->get($topItem['item_id']);
				$topItem['item_label'] .= ellipseStr($e['title'], 20);
			}
    	}
    	
    	$this->data['topItem'] = $topItem;
    	
    	$this->assign_message();
		$this->load_view('layout');
    }
    public function save_layout()
    {
    	$this->load->model('banner_model');

    	$ids = $this->post_input('ids', array());

    	$data['is_listed'] = 0;
    	$filter['banner_kind'] = BANNER_MAIN;
    	$filter['is_listed'] = 1;
    	$this->banner_model->_update($data, $filter);

    	foreach ($ids as $key=>$id) {
    		$data['is_listed'] = 1;
    		$data['orders'] = $key + 1;
    		$this->banner_model->update($id, $data);
    	}
    	
		echo json_capsule(parent::success_message());
    }
    public function save_top_item()
    {
		$topItem['item_kind'] = $this->post_input('item_kind');
		$topItem['item_id'] = $this->post_input('item_id');
		if ($topItem['item_kind'] == BANNER_KIND_EVENT) {
			$topItem['is_ticket'] = $this->post_input('is_ticket');
		}
		
		$this->load->model('config_model');
		$rslt = $this->config_model->set('top_item', json_encode($topItem));
		if (empty($rslt)) {
			die(json_capsule(parent::error_message()));
    	}
		echo json_capsule(parent::success_message());
    }
    
    public function layout_discover()
    {
    	$this->load->model('config_model');
    	$discoverItems = json_decode($this->config_model->getValue('discover_items'), true);
    	$visibleItems = json_decode($this->config_model->getValue('custom_discover_items'), true);
    	
    	$separatorCount = 0;
    	$invisibleItems = array();
    	foreach ($discoverItems as $item) {
    		$visible = false;
			foreach ($visibleItems as $vItem) {
				if ($item['name'] == $vItem['name']) {
					$visible = true;
					break;
				}
			}
			if (!$visible) {
				$invisibleItems[] = $item;
			}
    	}
    	for ($separatorCount; $separatorCount < 3; $separatorCount++) {
    		$invisibleItems[] = array('name'=>'separater');
		} 
    	
    	$this->data['visibleItems'] = $visibleItems;
    	$this->data['invisibleItems'] = $invisibleItems;
    	
    	$this->assign_message();
		$this->load_view('layout_discover');
    }
    public function save_layout_discover()
    {
    	$this->load->model('config_model');
    	$discoverItems = json_decode($this->config_model->getValue('discover_items'), true);
    	
    	$items = $this->post_input('items', array());
    	$visibleItems = array();
    	foreach ($items as $item) {
    		foreach ($discoverItems as $dItem) {
				if ($dItem['name'] == $item) {
					$visibleItems[] = $dItem;
					break;
				}
    		}
    	}

    	$rslt = $this->config_model->set('custom_discover_items', json_encode($visibleItems));
    	if (empty($rslt)) {
			die(json_capsule(parent::error_message()));
    	}
		echo json_capsule(parent::success_message());
    }
    
    public function share()
    {
    	$this->load->model('config_model');
		$this->data['androidUrl'] = $this->config_model->getValue('android_download_url');
		$this->data['iphoneUrl'] = $this->config_model->getValue('iphone_download_url');
		$this->data['shareContent'] = $this->config_model->getValue('share_content');
		
		$this->assign_message();
		$this->load_view('share');
    }
    public function save_share()
    {
		$androidUrl = $this->post_input('android_url');
		$iphoneUrl = $this->post_input('iphone_url');
		$shareContent = $this->post_input('share_content');
		
		$this->load->model('config_model');
		$this->config_model->set('android_download_url', $androidUrl);
		$this->config_model->set('iphone_download_url', $iphoneUrl);
		$this->config_model->set('share_content', $shareContent);
		
		$this->success_redirect('config/share');
    }
    
    
    public function ranking()
    {
    	$this->load->model('ranking_model');
		$this->data['itemList'] = $this->ranking_model->getAll();

		$this->assign_message();
		$this->load_view('ranking');
    }
    public function edit_ranking()
    {
    	$this->load->model('ranking_model');
    	$this->load->model('member_model');

		$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->ranking_model->getEmptyRow();
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->ranking_model->get($id);
		}
		
		$itemList = array();
		for ($index = 1; $index <= 15; $index++) {
			$rItem = array();
			$rItem['index'] = $index;
			$rItem['id'] = 0;
			
			$memberId = $itemInfo['member_id_'.$index];
			if (!empty($memberId)) {
				$member = $this->member_model->get($memberId);
				if (!empty($member)) {
					$rItem['id'] = $member['id'];
					$rItem['name'] = $member['name'];
				}
			}
			
			$itemList[] = $rItem;
		}
		
		$this->data['itemInfo'] = $itemInfo;
		$this->data['memberList'] = $itemList;
		$this->load_view('edit_ranking');
    }
    public function save_ranking()
    {
		$id = $this->post_input('id');
		$data['name'] = $this->post_input('name');
		
		for ($index = 1; $index <= 15; $index++) {
			$key = 'member_id_'.$index;
			$data[$key] = $this->post_input($key);
		}
		
		$this->load->model('ranking_model');
		if (empty($id)) {
			$rslt = $this->ranking_model->insert($data['name'], $data);
		} else {
			$rslt = $this->ranking_model->update($id, $data);
		}
		if (empty($rslt)) {
			$this->error_redirect('config/ranking');
		}
		$this->success_redirect('config/ranking');
    }
    public function delete_ranking()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('ranking_model');
		foreach ($ids as $id) {
			$this->ranking_model->delete($id);
		}
		$this->add_log('删除排行', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    public function change_ranking_order()
    {
    	$ids = $this->post_input('ids');
		if (empty($ids)) {
			die(json_capsule(parent::error_message()));
		}

		$this->load->model('ranking_model');
		foreach ($ids as $key=>$id) {
			$data['orders'] = $key + 1;
			$this->ranking_model->update($id, $data);
		}
		
		echo json_capsule(parent::success_message());
    }
}
