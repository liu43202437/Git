<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video_link extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/video_link/';
        $this->load->model('link_model');
    }

    public function lists()
    {
    	$categoryId = $this->get_input('category');
    	$isShow = $this->get_input('is_show');
    	$source = $this->get_input('title');
    	
		$filters = array();
		$orders = array();

		if ($isShow !== '') {
			$isShow = intval($isShow);
			$filters['is_show'] = $isShow;
		}
		if (!empty($source)) {
			$filters['source%'] = $source;
		}
		if (!empty($categoryId)) {
			$filters['category_id'] = $categoryId;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['link_date'] = 'DESC';
		}
		
		$this->load->model('comment_model');
		$this->load->model('category_model');
		$this->load->model('content_model');
		    	
    	$totalCount = $this->link_model->getCount($filters);
    	$rsltList = $this->link_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
		foreach ($rsltList as $key=>$item) {
			$cItems = $this->link_model->getContentList($item['id']);
			if (empty($cItems)) {
				$rsltList[$key]['first_item'] = array();
				$rsltList[$key]['first_item']['target_kind'] = LINK_CONTENT_KIND_URL;
				$rsltList[$key]['first_item']['hits'] = 0;
				$rsltList[$key]['first_item']['url'] = '';
				$rsltList[$key]['comment_count'] = 0;
				continue;
			}
			
			$cItem = $cItems[0];
			if ($cItem['target_kind'] != LINK_CONTENT_KIND_URL/*$cItem['target_id']*/) {
				$rsltList[$key]['first_item'] = $this->content_model->get($cItem['target_id']);
				$rsltList[$key]['first_item']['target_kind'] = $cItem['target_kind'];
				$rsltList[$key]['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_CONTENT, $cItem['target_id']);
			} else {
				$cItem['hits'] = 0;
				$rsltList[$key]['first_item'] = $cItem;
				$rsltList[$key]['comment_count'] = $this->comment_model->getCommentCount(COMMENT_ITEM_KIND_LINKS, $cItem['id']);
			}
		}
		
		$categories = $this->category_model->getAll();
    	if (empty($categories)) {
			$categories = array();
    	}
		
		$this->data['categoryId'] = $categoryId;
    	$this->data['categories'] = $categories;
    	$this->data['isShow'] = $isShow;
    	$this->data['source'] = $source;
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('content/add');
    	$this->assign_pager($totalCount);
    	$this->assign_message();
    	
		$this->load_view('list');
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		foreach ($ids as $id) {
			$this->link_model->delete($id);
			$this->link_model->deleteContentByLink($id);
		}
		
		$this->add_log('删除链接', $ids);
		
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
			$this->link_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->link_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit()
    {
    	$this->load->model('content_model');

    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->link_model->getEmptyRow();
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->link_model->get($id);
			$contents = $this->link_model->getContentList($id);
			$rContents = array();
			foreach ($contents as $item) {
				$rItem = $item;
				if ($item['target_kind'] != LINK_CONTENT_KIND_URL) {
					$citem = $this->content_model->get($item['target_id']);
					if (!empty($citem)) {
						$rItem['target_label'] = '[' . $citem['id'] . '] ' . ellipseStr($citem['title'], 20);
					}
				} else {
					$rItem['target_label'] = '';
				}
				
				$rContents[] = $rItem;
			}
			$itemInfo['contents'] = $rContents;
		}
		
		$this->load->model('category_model');
		$categories = $this->category_model->getAll();
    	if (empty($categories)) {
			$categories = array();
    	}
		
		$this->data['itemInfo'] = $itemInfo;
		$this->data['categories'] = $categories;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$id = $this->post_input('id');
		$data['source'] = $this->post_input('source');
		$data['link_date'] = $this->post_input('link_date', now());
		$data['image'] = $this->post_input('image');
		$data['thumb'] = $this->post_input('thumb');
		$data['keywords'] = $this->post_input('keywords');
		$data['category_id'] = $this->post_input('category');
		$contents = json_decode($this->post_input('contents', ''), true);

		if (empty($id)) {
			// add new content
			$id = $this->link_model->insert($data['source'], $data['link_date'], $data['image'], $data['thumb'], $data['keywords'], $data['category_id']);
			
			$this->add_log('新增链接', array($data));
		} else {
			$this->link_model->update($id, $data);
			
			$this->add_log('编辑链接', array('id'=>$id));
		}

		$this->link_model->deleteContentByLink($id);
		foreach ($contents as $content) {
			$this->link_model->insertContent($id, $content['title'], $content['url'], $content['target_kind'], $content['target_id']);
		}
		
		$this->success_redirect('video_link/lists');
	}
}
