<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');
class Contents extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
    public function getContent(){
    	$rs = [];
    	$this->load->model('Common_model');
    	$this->Common_model->setTable('tbl_content');
    	$orders = [];
    	$filters = [];
    	$orders['create_date'] = 'desc';
    	$filters['kind'] = 1;
    	$information = $this->Common_model->fetchOne($filters,$orders);
    	$orders = [];
    	$filters = [];
    	$orders['create_date'] = 'desc';
    	$filters['kind'] = 2;
    	$activity = $this->Common_model->fetchOne($filters,$orders);
    	$orders = [];
    	$filters = [];
    	$orders['hits'] = 'desc';
    	$filters['kind'] = 1;
    	$news = $this->Common_model->fetchOne($filters,$orders);
    	$rs['information'] = [];
    	if(!empty($information)){
    		$rs['information']['title'] = $information['title'];
	    	$rs['information']['image'] = base_url().$information['image'];
	    	$rs['information']['thumb'] = base_url().$information['thumb'];
	    	$rs['information']['id'] = $information['content_id'];
    	}
    	$rs['activity'] = [];
    	if(!empty($activity)){
    		$rs['activity']['title'] = $activity['title'];
	    	$rs['activity']['image'] = base_url().$activity['image'];
	    	$rs['activity']['thumb'] = base_url().$activity['thumb'];
	    	$rs['activity']['id'] = $activity['content_id'];
    	}
    	$rs['news'] = [];
    	if(!empty($news)){
    		$rs['news']['title'] = $news['title'];
	    	$rs['news']['image'] = base_url().$news['image'];
	    	$rs['news']['thumb'] = base_url().$news['thumb'];
	    	$rs['news']['id'] = $news['content_id'];
    	}
    	$this->success('成功',$rs);
    }
    public function getArticle(){
    	$rs = [];
    	$id = $this->getparam('id');
    	if(empty($id)){
    		$this->reply('缺少参数');
    		return;
    	}
    	$this->load->model('Common_model');
    	$this->Common_model->setTable('tbl_content_article');
    	$info = $this->Common_model->fetchOne(array('id'=>$id));
    	if(!empty($info)){
    		$rs = $info['link'];
    	}
    	else{
    		$rs = '';
    	}
    	$this->success('成功',$rs);
    }
    public function getBanner(){
    	$rs = [];
    	$this->load->model('Common_model');
    	$this->Common_model->setTable('tbl_banner');
    	$filters['is_show'] = 1;
    	$info = $this->Common_model->fetchAll($filters);
    	foreach ($info as $key => $value) {
    		$rs[$key]['image'] = base_url().$value['image'];
    		$rs[$key]['url'] = $value['url'];
    		$rs[$key]['urlenabled'] = $value['urlenabled'] == 0 ? false : true;
    		$rs[$key]['id'] = $value['content_id'];
    	}
    	$this->success('成功',$rs);
    }
	public function get_list($kind)
	{
		if (!in_array($kind, array("article", "gallery", "video", "live", "advert"))) {
			parent::output(100);
		}
        @$category_id = $_REQUEST['category_id'];
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
	
		$q = $this->post_input('q');

		$this->load->model('content_model');
		$this->load->model('comment_model');
		$this->load->model('config_model');

		$menuItems = json_decode($this->config_model->getValue('app_menu_items'), true);
		$menuItem = null;
		if ($kind == "article") {
			$kind = CONTENT_KIND_ARTICLE;
			$menuItem = $menuItems['top_news'];
		} else if ($kind == "advert") {
			$kind = CONTENT_KIND_ADVERT;
		} else if ($kind == "gallery") {
			$kind = CONTENT_KIND_GALLERY;
		} else if ($kind == "video") {
			$kind = CONTENT_KIND_VIDEO;
			$menuItem = $menuItems['videos'];
		} else {
			$kind = CONTENT_KIND_LIVE;
		}
		
		if (!empty($menuItem) && !empty($menuItem['category'])) {
			$filters['category_id'] = $menuItem['category'];
		}
		if(!empty($category_id)){
            $filters['category_id'] = $category_id;
        }

		if ($kind == CONTENT_KIND_ARTICLE) {		
			$filters['kind'] = array(CONTENT_KIND_ARTICLE, CONTENT_KIND_VIDEO, CONTENT_KIND_GALLERY, CONTENT_KIND_ADVERT);
		} else {
			$filters['kind'] = $kind;
		}

		$filters['is_show'] = 1;
		$orders['content_date'] = 'DESC';

		$filters['title%'] = $q;

		// get list count
		$totalCount = $this->content_model->getCount($filters);

		// base hit counts
		$baseHits[CONTENT_KIND_ARTICLE] = $this->config_model->getBaseHits(CONTENT_KIND_ARTICLE);
		$baseHits[CONTENT_KIND_GALLERY] = $this->config_model->getBaseHits(CONTENT_KIND_GALLERY);
		$baseHits[CONTENT_KIND_VIDEO] = $this->config_model->getBaseHits(CONTENT_KIND_VIDEO);
		$baseHits[CONTENT_KIND_LIVE] = $this->config_model->getBaseHits(CONTENT_KIND_LIVE);

		// get paged list
		$data = $this->content_model->getList($filters, $orders, $page, $size);

		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['title'] = $item['title'];
			$rItem['content_date'] = $item['content_date'];
			$rItem['image'] = getFullUrl($item['image']);
			$rItem['thumb'] = getFullUrl($item['thumb']);
			$rItem['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $item['id']);
			$rItem['share_url'] = getPortalUrl($item['id'], PORTAL_KIND_CONTENT, "mobile");
			
			if ($item['kind'] == CONTENT_KIND_ARTICLE) {
				$rItem['url'] = getPortalUrl($item['id'], PORTAL_KIND_CONTENT);
				$rItem['hits'] = intval($item['hits']) + $baseHits[$item['kind']];
			} else if ($item['kind'] == CONTENT_KIND_VIDEO) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rItem['video'] = getFullUrl($extra['video']);
				$rItem['duration'] = $extra['duration'];
				$rItem['hits'] = intval($item['hits']) + $baseHits[$item['kind']];
			} elseif ($item['kind'] == CONTENT_KIND_GALLERY) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
//				$rItem['image1'] = getFullUrl($extra['image1']);
//				$rItem['image2'] = getFullUrl($extra['image2']);
				if(!empty($extra['images'])){
                    if (isset($extra['image1'])){
                        $rItem['image1'] = getFullUrl($extra['image1']);
                    }else{
                        $rItem['image1'] = base_url();
                    }
                    if (isset($extra['image2'])){
                        $rItem['image2'] = getFullUrl($extra['image2']);
                    }else{
                        $rItem['image2'] = base_url();
                    }
				}
				$rItem['hits'] = intval($item['hits']) + $baseHits[$item['kind']];
			} elseif ($item['kind'] == CONTENT_KIND_LIVE) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rItem['link'] = $extra['link'];
				$rItem['status'] = $extra['status'];
				$rItem['hits'] = intval($item['hits']) + $baseHits[$item['kind']];
			} elseif ($item['kind'] == CONTENT_KIND_ADVERT) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rItem['image'] = getFullUrl($extra['image']);
				$rItem['sub_title'] = $extra['sub_title'];
				$rItem['link'] = $extra['link'];
			} 
			
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList
			),
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}

	public function get_item()
	{
		$itemId = $this->post_input('item_id');
		if (empty($itemId)) {
			parent::output(100);
		}
		
		$this->load->model('content_model');
		$this->load->model('comment_model');
		$this->load->model('config_model');
		
		// get item
		$contentItem = $this->content_model->get($itemId);
		if (empty($contentItem)) {
			parent::output(199);
		}
		
		$contentItem['share_url'] = getPortalUrl($contentItem['id'], PORTAL_KIND_CONTENT, "mobile");
		$contentItem['image'] = getFullUrl($contentItem['image']);
		$contentItem['thumb'] = getFullUrl($contentItem['thumb']);
		
		if ($contentItem['kind'] == CONTENT_KIND_ARTICLE) {
			unset($contentItem['content']);
			$contentItem['url'] = getPortalUrl($contentItem['id'], PORTAL_KIND_CONTENT);
		} else if ($contentItem['kind'] == CONTENT_KIND_VIDEO) {
			$contentItem['video'] = getFullUrl($contentItem['video']);
		} else if ($contentItem['kind'] == CONTENT_KIND_GALLERY) {
			$contentItem['image1'] = getFullUrl($contentItem['image1']);
			$contentItem['image2'] = getFullUrl($contentItem['image2']);
			if (!empty($contentItem['images'])) {
				foreach ($contentItem['images'] as &$image) {
					$image['image'] = getFullUrl($image['image']);
				}
			}
		}
		
		// get comment count
		$contentItem['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $contentItem['id']);

		// hit count		
		$baseHits = $this->config_model->getBaseHits($contentItem['kind']);
		$contentItem['hits'] = intval($contentItem['hits']) + $baseHits;
		
		// add hit count
		//$this->content_model->increaseHits($itemId);

		// out data
		parent::output(
			array(
				'item' => $contentItem
			)
		);
	}
	
	public function increase_hits()
	{
		$itemId = $this->post_input('item_id');
		$this->load->model('content_model');
		$this->content_model->increaseHits($itemId);
		parent::output(array());
	}
	
	public function video_links()
	{
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('link_model');
		$this->load->model('config_model');

		$menuItems = json_decode($this->config_model->getValue('app_menu_items'), true);
		$menuItem = $menuItems['video_links'];
		if (!empty($menuItem) && !empty($menuItem['category'])) {
			$filters['category_id'] = $menuItem['category'];
		}

		$filters['is_show'] = 1;
		$orders['link_date'] = 'DESC';
        
        $q = $this->post_input('q');
        $filters['source%'] = $q;

		// get list count
		$totalCount = $this->link_model->getCount($filters);
		
		// get paged list
		$data = $this->link_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem['id'] = $item['id'];
			$rItem['source'] = $item['source'];
			$rItem['link_date'] = $item['link_date'];
			$rItem['image'] = getFullUrl($item['image']);
			$rItem['thumb'] = getFullUrl($item['thumb']);
			$rItem['contents'] = $this->link_model->getContentList($item['id']);
			if (empty($rItem['contents'])) {
				continue;
			}
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
	
	public function get_link_content()
	{
		$contentId = $this->post_input('link_id');
		if (empty($contentId)) {
			parent::output(100);
		}
		
		// check validation
		$this->load->model('link_model');
		$item = $this->link_model->getContentItem($contentId);
		if (empty($item)) {
			parent::output(199);
		}
		
		$this->load->model('comment_model');
		// get comment count		
		$item['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_LINKS, $item['id']);
		
		// add hit count
		$this->link_model->increaseHits($item['link_id']);
		
		// out data
		parent::output(
			array(
				'item' => $item
			)
		);
	}
	
/*	public function baby_list()
	{
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('baby_model');
		$this->load->model('like_model');
		
		$filters['is_show'] = 1;
		$orders['baby_date'] = 'DESC';

		// get list count
		$totalCount = $this->baby_model->getCount($filters);
		
		// get paged list
		$data = $this->baby_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem['id'] = $item['id'];
			$rItem['title'] = $item['title'];
			$rItem['baby_date'] = $item['baby_date'];
			$rItem['image'] = getFullUrl($item['image']);
			$rItem['hits'] = $item['hits'];
			$rItem['like_count'] = intval($item['hits']) + $this->like_model->getLikeCount(LIKE_ITEM_KIND_BABY, $item['id']);
			$rItem['is_like_item'] = 0;
			if (!empty($this->user)) {
				$rItem['is_like_item'] = $this->like_model->isLikeItem($this->user['id'], LIKE_ITEM_KIND_BABY, $item['id']);
			}
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}*/

	public function baby_list()
	{
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('content_model');
		$this->load->model('config_model');
		$this->load->model('comment_model');

		$filters['category_id'] = 6;
		$filters['kind'] = CONTENT_KIND_GALLERY;
        $filters['is_show'] = 1;
       
        $q = $this->post_input('q');      
        $filters['title%'] = $q;   
		
		$orders['content_date'] = 'DESC';

		// get list count
		$totalCount = $this->content_model->getCount($filters);

		// base hit counts
		$baseHits = $this->config_model->getBaseHits(CONTENT_KIND_GALLERY);

		// get paged list
		$data = $this->content_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['type'] = $item['type'];
			$rItem['title'] = $item['title'];
			$rItem['content_date'] = $item['content_date'];
			$rItem['image'] = getFullUrl($item['image']);
			$rItem['thumb'] = getFullUrl($item['thumb']);
			$rItem['hits'] = intval($item['hits']) + $baseHits;
			$rItem['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $item['id']);
			$rItem['share_url'] = getPortalUrl($item['id'], PORTAL_KIND_CONTENT, "mobile");

			$extra = $this->content_model->getExtra($item['id'], CONTENT_KIND_GALLERY);
			$rItem['image1'] = getFullUrl($extra['image1']);
			$rItem['image2'] = getFullUrl($extra['image2']);

			$extra_images = array();
			if (!empty($extra['images'])) {
				foreach($extra['images'] as $image) {
					$extra_images []= getFullUrl($image['image']);
				}
			}
			$rItem['extra_image_count'] = count($extra_images);
			$rItem['extra'] = $extra_images;			
			
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList
			), 
			array(
				'total' => $totalCount,
				'count' => count($data),
				'more' => ($totalCount > ($page * $size)) ? 1 : 0
			)
		);
	}
}
