<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
    
    public function member_info()
	{
		$memberId = $this->post_input('member_id');
		if (empty($memberId)) {
			parent::output(100);
		}
		
		$this->load->model('member_model');
		$this->load->model('area_model');
		
		// get item
		$member = $this->member_model->get($memberId);
		if (empty($member)) {
			parent::output(199);
		}
		
		$member['image'] = getFullUrl($member['image']);
		
		$area = $this->area_model->getAreaInfo($member['country_id']);
		if (empty($area)) {
			$member['country'] = '';
		} else {
			$member['country'] = $area[AREA_TYPE_COUNTRY]['name'];
		}
		$member['country_img'] = base_url() . 'resources/images/flags/' . $member['country_id'] . '.jpg';
		
		if ($member['kind'] == MEMBER_KIND_PLAYER) {
			$member['score'] = intval($member['score_win']).'-'.intval($member['score_loss']).'-'.intval($member['score_draw']).' '.intval($member['score_ko']) . 'KO';
			$member['weight_level'] = getPlayerWeightLevel($member['weight']);
		}
		  
		// out data
		parent::output(
			array(
				'member' => $member
			)
		);
	}
	
	public function member_videos()
	{
		$memberId = $this->post_input('member_id');
		if (empty($memberId)) {
			parent::output(100);
		}
		
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('member_model');
		$this->load->model('content_model');
		$this->load->model('comment_model');
		
		$filters['is_show'] = 1;
		//$filters['kind'] = CONTENT_KIND_VIDEO;
		$orders['content_date'] = 'DESC';

		// get list count
		$totalCount = $this->member_model->getContentCount($memberId, $filters);
		
		// get paged list
		$data = $this->member_model->getContentList($memberId, $filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['title'] = $item['title'];
			$rItem['thumb'] = getFullUrl($item['thumb']);
			$rItem['hits'] = $item['hits'];
			$rItem['content_date'] = $item['content_date'];
			$rItem['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $item['id']);
			
			if ($item['kind'] == CONTENT_KIND_ARTICLE) {
				$rItem['url'] = getPortalUrl($item['id'], PORTAL_KIND_CONTENT);
			} else if ($item['kind'] == CONTENT_KIND_VIDEO) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rItem['video'] = getFullUrl($extra['video']);
				$rItem['duration'] = $extra['duration'];
			} elseif ($item['kind'] == CONTENT_KIND_GALLERY) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rItem['image1'] = getFullUrl($extra['image1']);
				$rItem['image2'] = getFullUrl($extra['image2']);
			} elseif ($item['kind'] == CONTENT_KIND_LIVE) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
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
	
	public function ranking_list()
	{
		$this->load->model('ranking_model');
		
		$data = $this->ranking_model->getAll();
		$itemList = array();
		foreach ($data as $item) {
			$rItem['id'] = $item['id'];
			$rItem['name'] = $item['name'];
			$itemList[] = $rItem;
		}

		// out data
		parent::output(
			array(
				'items' => $itemList
			)
		);
	}
	
	public function member_ranking()
	{
		$rankingId = $this->post_input('ranking_id');
		if (empty($rankingId)) {
			parent::output(100);
		}
		
		$this->load->model('ranking_model');
		$this->load->model('member_model');
		
		$itemList = array();
		
		$ranking = $this->ranking_model->get($rankingId);
		for ($index = 1; $index <= 15; $index++) {
			$memberId = $ranking['member_id_'.$index];
			if (empty($memberId)) {
				continue;
			}
			
			$member = $this->member_model->get($memberId);
			if (empty($member)) {
				continue;
			}
			$rItem['id'] = $member['id'];
			$rItem['kind'] = $member['kind'];
			$rItem['name'] = $member['name'];
			$rItem['en_name'] = $member['en_name'];
			$rItem['image'] = getFullUrl($member['image']);
			$rItem['score'] = intval($member['score_win']).'-'.intval($member['score_loss']).'-'.intval($member['score_draw']).' '.intval($member['score_ko']) . 'KO';
			$itemList[] = $rItem;
		}
		
		// out data
		parent::output(
			array(
				'items' => $itemList,
				'count' => count($itemList)
			)
		);
	}
	
	public function referee_list()
	{
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('member_model');
		
		$filters['is_show'] = 1;
		$filters['kind'] = MEMBER_KIND_REFEREE;
		$orders['id'] = 'ASC';

		// get list count
		$totalCount = $this->member_model->getCount($filters);
		
		// get paged list
		$data = $this->member_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $item) {
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['name'] = $item['name'];
			$rItem['image'] = getFullUrl($item['image']);
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
	
	public function coach_list()
	{
		// paging info
		$page = $this->pagination['page'];
		$size = $this->pagination['count'];
		
		$this->load->model('member_model');
		
		$filters['is_show'] = 1;
		$filters['kind'] = MEMBER_KIND_COACH;
		$orders['id'] = 'ASC';

		// get list count
		$totalCount = $this->member_model->getCount($filters);
		
		// get paged list
		$data = $this->member_model->getList($filters, $orders, $page, $size);
		$itemList = array();
		foreach ($data as $item) {
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['name'] = $item['name'];
			$rItem['image'] = getFullUrl($item['image']);
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
