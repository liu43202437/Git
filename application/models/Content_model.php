<?php

// content table model
class Content_model extends Base_Model {

	protected $tblContent = '';
	protected $tblArticle = '';
	protected $tblGallery = '';
	protected $tblGalleryImage = '';
	protected $tblVideo = '';
	protected $tblLive = '';
	protected $tblAdvert = '';
	protected $tblEvent = '';
	protected $tblEventRel = '';
	protected $tblMember = '';
	protected $tblMemberRel = '';
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['content'];
		$this->tblArticle = $TABLE['content_article'];
		$this->tblGallery = $TABLE['content_gallery'];
		$this->tblGalleryImage = $TABLE['gallery_image'];
		$this->tblVideo = $TABLE['content_video'];
		$this->tblLive = $TABLE['content_live'];
		$this->tblAdvert = $TABLE['content_advert'];
		$this->tblEvent = $TABLE['event'];
		$this->tblEventRel = $TABLE['event_content'];
		$this->tblMember = $TABLE['member'];
		$this->tblMemberRel = $TABLE['member_content'];
	}

	// get extra table
	public function tableByKind($kind)
	{
		if ($kind == CONTENT_KIND_ARTICLE) {
			return $this->tblArticle;
		} else if ($kind == CONTENT_KIND_GALLERY) {
			return $this->tblGallery;
		} else if ($kind == CONTENT_KIND_VIDEO) {
			return $this->tblVideo;
		} else if ($kind == CONTENT_KIND_LIVE) {
			return $this->tblLive;
		} else if ($kind == CONTENT_KIND_ADVERT) {
			return $this->tblAdvert;
		}
		return null;
	}

	// override get function
	public function get($id, $table = null)
	{
		$info = parent::get($id);
		if (empty($info)) {
			return null;
		}
		$extra = $this->getExtra($id, $info['kind']);
		if (isset($extra) && !empty($extra)) {
			unset($extra['id']);
			$info = array_merge($info, $extra);
		}
		return $info;
	}

	// get extra info by kind
	public function getExtra($contentId, $kind)
	{
		$extra = array();
		$where['content_id'] = $contentId;
		$extra = $this->db->get_where($this->tableByKind($kind), $where)
						->row_array();
		if ($kind == CONTENT_KIND_GALLERY) {
			$extra['images'] = $this->db->order_by('orders', 'ASC')
										->get_where($this->tblGalleryImage, $where)
										->result_array();
			$extra['image_count'] = count($extra['images']);
		}
		return $extra;
	}
	
	// get gallery image count
	public function getGalleryImageCount($contentId)
	{
		$this->db->where('content_id', $contentId);
		return $this->db->count_all_results($this->tblGalleryImage);
	}
	
	// insert
	public function insert($kind, $title, $contentDate, $type, $image, $thumb, $keywords, $categoryId, $hits, $extraData) 
	{
		$data = array(
			'kind' => $kind,
			'title' => $title,
			'content_date' => $contentDate,
			'type' => $type,
			'image' => $image,
			'thumb' => $thumb,
			'keywords' => $keywords,
			'category_id' => $categoryId,
			'hits' => $hits,
			'is_show' => 1,
			'create_date' => now()
		);
		$id = $this->_insert($data);
		if (empty($id)) {
			return null;
		}

		$extraData['content_id'] = $id;
		$this->_insert($extraData, $this->tableByKind($kind));
		return $id;
	}
	
	// insert extra info
	public function insertExtra($contentId, $kind, $data)
	{
		$data['content_id'] = $contentId;
		return $this->_insert($data, $this->tableByKind($kind));
	}
	
	// insert gallery image
	public function insertGalleryImage($contentId, $image, $description)
	{
		$data = array(
			'content_id' => $contentId,
			'image' => $image,
			'description' => $description,
			'orders' => 0
		);
		$id = $this->_insert($data, $this->tblGalleryImage);
		return $this->update($id, array('orders'=>$id), $this->tblGalleryImage);
	}
	
	// update extra info
	public function updateExtra($contentId, $kind, $data)
	{
		$where['content_id'] = $contentId;
		return $this->db->update($this->tableByKind($kind), $data, $where);
	}
	
	// update gallery image
	public function updateGalleryImage($id, $data)
	{
		return $this->update($id, $data, $this->tblGalleryImage);
	}

	// set show/hide
	public function setShow($id, $isShow = true)
	{
		$data = array('is_show' => $isShow ? 1 : 0);
		$this->update($id, $data);
	}
	
	// increase hits count
	public function increaseHits($id)
	{
		$data = array('hits$' => 'hits + 1');
		$this->update($id, $data);
	}
	
	// override delete
	public function delete($id, $table = null)
	{
		$info = $this->get($id);
		if (empty($info)) {
			return true;
		}

		$where['content_id'] = $id;
		if ($info['kind'] == CONTENT_KIND_ARTICLE) {
			$this->db->delete($this->tblArticle, $where);
		} else if ($info['kind'] == CONTENT_KIND_GALLERY) {
			$this->db->delete($this->tblGallery, $where);
			$this->db->delete($this->tblGalleryImage, $where);
		} else if ($info['kind'] == CONTENT_KIND_VIDEO) {
			$this->db->delete($this->tblVideo, $where);
		} else if ($info['kind'] == CONTENT_KIND_LIVE) {
			$this->db->delete($this->tblLive, $where);
		} else if ($info['kind'] == CONTENT_KIND_ADVERT) {
			$this->db->delete($this->tblAdvert, $where);
		}
		
		$this->load->model('member_model');
		$this->member_model->deleteContent(null, $id);
		
		$this->load->model('event_model');
		$this->event_model->deleteContent(null, $id);
		
		return parent::delete($id);
	}
	
	// delete gallery image
	public function deleteGalleryImages($contentId)
	{
		$where['content_id'] = $contentId;
		return $this->db->delete($this->tblGalleryImage, $where);
	}
	
	// get related event id list
	public function getEventIDs($contentId)
	{
		return $this->db->select("event_id")
				->where("content_id", $contentId)
				->get($this->tblEventRel)
				->result_array();
	}
	
	// get related event list
	public function getEventList($contentId)
	{
		$this->db->select("ER.content_id, E.*")
				->from($this->tableName($this->tblEventRel) . ' AS ER')
				->join($this->tableName($this->tblEvent) . ' AS E', 'ER.event_id = E.id', 'LEFT')
				->where('ER.content_id', $contentId);
		return $this->db->get()->result_array();
	}
	
	// get related member id list
	public function getMemberIDs($contentId)
	{
		return $this->db->select("member_id")
				->where("content_id", $contentId)
				->get($this->tblMemberRel)
				->result_array();
	}
	
	// get related member list
	public function getMemberList($contentId)
	{
		$this->db->select("MR.content_id, M.*")
				->from($this->tableName($this->tblMemberRel) . ' AS MR')
				->join($this->tableName($this->tblMember) . ' AS M', 'MR.member_id = M.id', 'LEFT')
				->where('MR.content_id', $contentId);
		return $this->db->get()->result_array();
	}
}
?>
