<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/content/';
        $this->load->model('content_model');
    }

    public function lists($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$categoryId = $this->get_input('category');
    	$isShow = $this->get_input('is_show');
    	$title = $this->get_input('title');
    	
    	$this->load->model('comment_model');
    	$this->load->model('category_model');
    	
		$filters = array();
		$orders = array();

		$filters['kind'] = $kind;		
		if ($isShow !== '') {
			$isShow = intval($isShow);
			$filters['is_show'] = $isShow;
		}
		if (!empty($title)) {
			$filters['title%, keywords%'] = $title;
		}
		if (!empty($categoryId)) {
			$filters['category_id'] = $categoryId;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['content_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->content_model->getCount($filters);
    	$rsltList = $this->content_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	foreach ($rsltList as $key=>$item) {
			$rsltList[$key]['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $item['id']);
			if ($kind == CONTENT_KIND_GALLERY) {
				$rsltList[$key]['image_count'] = $this->content_model->getGalleryImageCount($item['id']);
			} else if ($kind == CONTENT_KIND_VIDEO) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rsltList[$key]['duration'] = $extra['duration'];
			} else if ($kind == CONTENT_KIND_LIVE) {
				$extra = $this->content_model->getExtra($item['id'], $item['kind']);
				$rsltList[$key]['point'] = $extra['point'];
			}
		}
		
		$categories = $this->category_model->getAll();
    	if (empty($categories)) {
			$categories = array();
    	}

    	$this->data['kind'] = $kind;
    	$this->data['categoryId'] = $categoryId;
    	$this->data['categories'] = $categories;
    	$this->data['isShow'] = $isShow;
    	$this->data['title'] = $title;
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('content/add');
    	$this->assign_pager($totalCount);
    	$this->assign_message();
    	
		$this->load_view('list');
    }
    
    public function ajax_list()
    {
		$kind = $this->get_input('kind');
		$term = $this->get_input('term');
		if (!empty($kind)) {
			$filter['kind'] = $kind;
		}
		$filter['id, title%'] = $term;
		
		$rsltList = $this->content_model->getList($filter, null, 1, 10);
		$itemList = array();
		foreach ($rsltList as $key=>$item) {
			 $rItem['id'] = $item['id'];
			 $rItem['kind'] = $item['kind'];
			 $rItem['label'] = '[' . $rItem['id'] . '] ' . ellipseStr($item['title'], 20) ;
			 $itemList[] = $rItem;
		}
		
		echo json_encode($itemList);
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		$this->load->model('banner_model');
		$this->load->model('link_model');
		
		foreach ($ids as $id) {
			$this->content_model->delete($id);
			$this->link_model->deleteContentByTarget($id);
		}
		
//		$this->add_log('删除内容', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function toggle_show()
    {
		$id = $this->post_input('id');
		$ids = $this->post_input('ids');
		if (empty($id) && empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		if (empty($ids)) {	// one item operation
			$data['is_show$'] = '1-is_show';
			$this->content_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->content_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$this->load->model('category_model');
    	
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->content_model->getEmptyRow();
			$itemInfo['type'] = 0;
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->content_model->get($id);

			/*$eventIds = $this->content_model->getEventIDs($id);
			if (!empty($eventIds)) {
				$eIds = array();
				foreach ($eventIds as $eventId) {
					$eIds[] = $eventId['event_id'];
				}
				$eventIds = implode(", ", $eIds);
			} else {
				$eventIds = '';
			}
			$itemInfo['eventIds'] = $eventIds;*/
			$itemInfo['events'] = $this->content_model->getEventList($id);
			
			/*$memberIds = $this->content_model->getMemberIDs($id);
			if (!empty($memberIds)) {
				$mIds = array();
				foreach ($memberIds as $memberId) {
					$mIds[] = $memberId['member_id'];
				}
				$memberIds = implode(", ", $mIds);
			} else {
				$memberIds = '';
			}
			$itemInfo['memberIds'] = $memberIds;*/
			$itemInfo['members'] = $this->content_model->getMemberList($id);
		}
		
		$categories = $this->category_model->getAll();
    	if (empty($categories)) {
			$categories = array();
    	}
    	
		$this->data['kind'] = $kind;
		$this->data['itemInfo'] = $itemInfo;
		$this->data['categories'] = $categories;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$kind = $this->post_input('kind');
		$id = $this->post_input('id');
		$title = $this->post_input('title');
		$contentDate = $this->post_input('content_date', now());
		$type = $this->post_input('type');
		$image = $this->post_input('image');
		$thumb = $this->post_input('thumb');
		$keywords = $this->post_input('keywords');
		$categoryId = $this->post_input('category');
		$hits = $this->post_input('hits', 0);
		$eventIds = $this->post_input('event_ids');
		$memberIds = $this->post_input('member_ids');

		if ($kind == CONTENT_KIND_ARTICLE) {
			$extraData['author'] = $this->post_input('author');
			$extraData['type'] = $this->post_input('type');
			$extraData['content'] = $this->post_input('content');
			$extraData['link'] = $this->post_input('link');
		} else if ($kind == CONTENT_KIND_GALLERY) {
			$extraData['image1'] = $this->post_input('image1');
			$extraData['image2'] = $this->post_input('image2');
			$images = $this->post_input('images', array());
			$descriptions = $this->post_input('descriptions', array());
		} else if ($kind == CONTENT_KIND_VIDEO) {
			$extraData['video'] = $this->post_input('video');
			$extraData['introduction'] = $this->post_input('introduction');

			$filename = str_replace(base_url(), '', $extraData['video']);
			if (file_exists($filename)) {
				include_once(APPPATH . 'third_party/getid3/getid3/getid3.php');
				$getID3 = new getID3;
				$file = $getID3->analyze($filename);
				$extraData['duration'] = $file['playtime_string'];
			}
		} else if ($kind == CONTENT_KIND_LIVE) {
			$extraData['source'] = $this->post_input('source');
			$extraData['link'] = $this->post_input('link');
			$extraData['status'] = $this->post_input('status');
			$extraData['point'] = 0;
		} else if ($kind == CONTENT_KIND_ADVERT) {
			$categoryId = 1;
			$extraData['sub_title'] = $this->post_input('sub_title');
			$extraData['image'] = $this->post_input('image');
			$extraData['link'] = $this->post_input('link');
		} else {
			$this->error_redirect('content/lists/1');
		}

		if (empty($id)) {
			// add new content
			$id = $this->content_model->insert($kind, $title, $contentDate, $type, $image, $thumb, $keywords, $categoryId, $hits, $extraData);
			
			$this->add_log('新增内容', array($this->posts));
		} else {
			$data['title'] = $title;
			$data['content_date'] = $contentDate;
			$data['type'] = $type;
			$data['image'] = $image;
			$data['thumb'] = $thumb;
			$data['keywords'] = $keywords;
			$data['category_id'] = $categoryId;
			$data['hits'] = $hits;
			$this->content_model->update($id, $data);
			$this->content_model->updateExtra($id, $kind, $extraData);
			
			$this->add_log('编辑内容', array('id'=>$id));
		}
		
		$this->load->model('event_model');
		$this->event_model->deleteContent(null, $id);
		if (!empty($eventIds)) {
			foreach ($eventIds as $eventId) {
				$eId = intval($eventId);
				if ($eId > 0) {
					$this->event_model->insertContent($eId, $id);
				}
			}
		}

		$this->load->model('member_model');
		$this->member_model->deleteContent(null, $id);
		if (!empty($memberIds)) {
			foreach ($memberIds as $memberId) {
				$mId = intval($memberId);
				if ($mId > 0) {
					$this->member_model->insertContent($mId, $id);
				}
			}
		}

		if ($kind == CONTENT_KIND_GALLERY) {
			$this->content_model->deleteGalleryImages($id);
			foreach ($images as $key=>$image) {
				$desc = isset($descriptions[$key]) ? $descriptions[$key] : '';
				$this->content_model->insertGalleryImage($id, $image, $desc);
			}
		} 

		$this->success_redirect('content/lists/' . $kind);
	}
}
