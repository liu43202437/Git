<?php

// config table model
class Config_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['config'];
	}
	
	// get
	public function getValue($name)
	{
		$this->db->where('name', $name);
		$row = $this->db->get($this->tbl)->row_array();
		return (empty($row) ? null : $row['value']);
	}
	
	// get all
	public function getAllConfig($cache = true)
	{
		$result = array();
		if ($cache) {
			$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
			$result = $this->cache->get('config');
			if (empty($result)) {
				$result = $this->getAll(null, array('id'=>'ASC'));
				$this->cacheAll($result);
			}
		} else {
			$result = $this->getAll(null, array('id'=>'ASC'));
		}
		return $result;
	}
	
	// set config
	public function set($name, $value) 
	{
		$this->db->where('name', $name);
		$row = $this->db->get($this->tbl)->row_array();
		if (empty($row)) {
			$data = array(
				'name' => $name,
				'value' => $value
			);
			return $this->_insert($data);
		} else {
			return $this->update($row['id'], array('value'=>$value));
		}		
	}
	
	// write config info to cache file
	public function cacheAll($info = null)
	{
		$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		if ($info == null) {
			$info = $this->getAll(false);
		}
		$this->cache->save('config', $info, 86400 * 30);
	}

	
	// custom get functions for reapeat usage
	public function getBaseHits($content_kind)
	{
		$baseHits = 0;
		if ($content_kind == CONTENT_KIND_ARTICLE) {
			$baseHits = $this->getValue('base_article_hits');
		} else if ($content_kind == CONTENT_KIND_GALLERY) { 
			$baseHits = $this->getValue('base_gallery_hits');
		} else if ($content_kind == CONTENT_KIND_VIDEO) {
			$baseHits = $this->getValue('base_video_hits');
		} else if ($content_kind == CONTENT_KIND_LIVE) {
			$baseHits = $this->getValue('base_live_hits');
		}
		return intval($baseHits);
	}
	
	public function getExpPerPoint()
	{
		return intval($this->getValue('exp_per_point'));
	}
	
	public function getExpPerMoney()
	{
		return intval($this->getValue('exp_per_money'));
	}
	
	public function getPointName()
	{
		return intval($this->getValue('point_name'));
	}
	
	public function getPointUnit()
	{
		return intval($this->getValue('point_unit'));
	}
}
?>
