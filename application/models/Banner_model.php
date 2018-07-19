<?php

// banner table model
class Banner_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['banner'];
	}
	
	// override function
	public function getList($filters = null, $orders = null, $page = 1, $size = PAGE_SIZE, $table = null)
	{
		if ($orders == null) {
			$orders['orders'] = 'ASC';
		}
		return parent::getList($filters, $orders, $page, $size, $table);
	} 
	
	// insert
	public function insert($title, $bannerKind, $itemKind, $itemInfo, $image, $platform, $isShowLimit, $startDate, $endDate, $startTime, $endTime, $isOpenDate, $openDate, $isAreaLimit, $areaLimitType) 
	{
		$data = array(
			'title' => $title,
			'banner_kind' => $bannerKind,
			'item_kind' => $itemKind,
			'item_info' => $itemInfo,
			'image' => $image,
			'platform' => $platform,
			'orders' => 0,
			'is_show' => 1,
			'is_listed' => 0,
			'is_show_limit' => $isShowLimit,
			'start_date' => $isShowLimit ? $startDate : null,
			'end_date' => $isShowLimit ? $endDate : null,
			'start_time' => ($isShowLimit && $startTime && $endTime) ? $startTime : null,
			'end_time' => ($isShowLimit && $startTime && $endTime) ? $endTime : null,
			'is_open_date' => $isOpenDate,
			'open_date' => ($isOpenDate && $openDate) ? $openDate : null,
			'is_area_limit' => $isShowLimit ? $isAreaLimit : 0,
			'area_limit_type' => ($isShowLimit && $isAreaLimit) ? $areaLimitType : null,
			'create_date' => now()
		);
		$id = $this->_insert($data);
		return $this->update($id, array('orders'=>$id));
	}

	// set show/hide
	public function setShow($id, $isShow = true)
	{
		$data = array('is_show' => $isShow ? 1 : 0);
		$this->update($id, $data);
	}
	
	// get available bannerrs
	public function getAvailableBanners($platform)
	{
		$this->db->select('id, title, item_kind, item_info, image, is_show_limit, is_area_limit, area_limit_type, is_open_date, open_date')
				->where('is_show', 1)
				->where('is_listed', 1)
				->where('banner_kind', BANNER_MAIN)
				->group_start()
					->or_where('platform', $platform)
					->or_where('platform', DEVICE_TYPE_ALL)
				->group_end()
				->group_start()
					->or_where('is_show_limit', 0)
					->or_group_start()
						->where('is_show_limit', 1)
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
					->group_end()
				->group_end()
				->order_by('orders', 'ASC')
				->limit(6, 0);
		$banners = $this->db->get($this->tbl)->result_array();
		
		return $banners;
	}
}
?>
