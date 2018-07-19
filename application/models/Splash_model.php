<?php

// splash table model
class Splash_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['splash'];
	}
	
	// insert
	public function insert($name, $image, $url, $platform, $showCountLimit, $hitsLimit, $startDate, $endDate, $startTime, $endTime, $isAreaLimit, $areaLimitType) 
	{
		$data = array(
			'name' => $name,
			'image' => $image,
			'url' => $url,
			'platform' => $platform,
			'show_count_limit' => $showCountLimit,
			'show_count' => 0,
			'hits_limit' => $hitsLimit,
			'is_show' => 1,
			'hits' => 0,
			'start_date' => $startDate,
			'end_date' => $endDate,
			'start_time' => ($startTime && $endTime) ? $startTime : null,
			'end_time' => ($startTime && $endTime) ? $endTime : null,
			'is_area_limit' => $isAreaLimit,
			'area_limit_type' => ($isAreaLimit) ? $areaLimitType : null,
			'create_date' => now()
		);
		$id = $this->_insert($data);
	}
	
	// increase hits count
	public function increaseHits($id)
	{
		$data = array('hits$' => 'hits + 1');
		$this->update($id, $data);
	}
	
	// increase show count
	public function increaseShowCount($id)
	{
		$data = array('show_count$' => 'show_count + 1');
		$this->update($id, $data);
	}
	
	// custom get 
	public function getAvailableSplashs($platform)
	{
		$this->db->select('id, name, image, url, is_area_limit, area_limit_type')
				->where('is_show', 1)
				->group_start()
					->or_where('platform', $platform)
					->or_where('platform', DEVICE_TYPE_ALL)
				->group_end()
				->group_start()
					->or_where('show_count_limit', null)
					->or_where('show_count_limit', 0)
					->or_where('show_count_limit >', 'show_count')
				->group_end()
				->group_start()
					->or_where('hits_limit', null)
					->or_where('hits_limit', 0)
					->or_where('hits_limit >', 'hits')
				->group_end()
				->group_start()
					->or_where('start_date', null)
					->or_group_start()
						->where('start_date <=', nowdate())
						->where('end_date >=', nowdate())
					->group_end()
				->group_end()
				->group_start()
					->or_where('start_time', null)
					->or_group_start()
						->where('start_time <=', nowtime())
						->where('end_time >=', nowtime())
					->group_end()
				->group_end()
				->order_by('create_date', 'DESC')
				->order_by('is_area_limit', 'ASC')
				->limit(1);
		$splash = $this->db->get($this->tbl)->result_array();
		return $splash;
	}
}
?>
