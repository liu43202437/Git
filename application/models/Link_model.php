<?php

// link table model
class Link_model extends Base_Model {

	protected $tblContent = '';
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['link'];
		$this->tblContent = $TABLE['link_content'];
	}
	
	// insert
	public function insert($source, $linkDate, $image, $thumb, $keywords, $categoryId) 
	{
		$data = array(
			'source' => $source,
			'link_date' => $linkDate,
			'image' => $image,
			'thumb' => $thumb,
			'keywords' => $keywords,
			'category_id' => $categoryId,
			'is_show' => 1,
			'create_date' => now()
		);
		return $this->_insert($data);
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
	
	public function delete($id, $table = null)
	{
		$this->deleteContentByLink($id);		
		return parent::delete($id, $table);
	}
	
	
	/*** link content detail functions ***/
	public function getContentItem($id)
	{
		return $this->get($id, $this->tblContent);
	}
	
	public function getContentCount($linkId)
	{
		return $this->getCount(array('link_id'=>$linkId), $this->tblContent);
	}
	
	public function getContentIds($linkId)
	{
		$contents = $this->getContentList($linkId);
		$ids = array();
		if (!empty($contents)) {
			foreach ($contents as $item) {
				$ids[] = $item['id'];
			}
		}
		return $ids;
	}
	
	public function getContentList($linkId)
	{
		$filter['link_id'] = $linkId;
		$order['orders'] = 'ASC';
		return $this->getAll($filter, $order, $this->tblContent);
	}
	
	public function insertContent($linkId, $title, $url, $targetKind, $targetId)
	{
		$data = array(
			'link_id' => $linkId,
			'title' => $title,
			'url' => $url,
			'target_kind' => $targetKind,
			'target_id' => $targetId,
			'orders' => 0
		);
		$id = $this->_insert($data, $this->tblContent);
		return $this->update($id, array('orders'=>$id), $this->tblContent);
	}
	
	public function updateContent($id, $data)
	{
		return $this->update($id, $data, $this->tblContent);
	}

	public function deleteContent($id)
	{
		return $this->delete($id, $this->tblContent);
	}
	
	public function deleteContentByLink($linkId)
	{
		$this->db->where('link_id', $linkId);
		return $this->db->delete($this->tblContent);
	}
	
	public function deleteContentByTarget($targetId)
	{
		$this->db->where('target_id', $targetId);
		return $this->db->delete($this->tblContent);
	}
}
?>
