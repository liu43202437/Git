<?php

// area table model
class Area_model extends Base_Model {

	protected $tblLimit = '';
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['area'];
		$this->tblLimit = $TABLE['area_limit'];
	}
	
	public function getList($filters = null, $orders = null, $page = 1, $size = PAGE_SIZE, $table = null) 
	{
		if ($orders == null) {
			$orders['id'] = 'ASC';
		}
		return parent::getList($filters, $orders, $page, $size, $table);
	}
	
	// get area info (country, province, city, district) 
	public function getAreaInfo($id)
	{
		$info = $this->get($id);
		if (empty($info)) {
			return null;
		}
		$result = array();
		$fullname = '';
		do {
			$result[$info['type']] = $info;
			$fullname = $info['name'] . $fullname;
			if ($info['type'] != AREA_TYPE_COUNTRY) {
				$info = $this->get($info['parent_id']);
			} else {
				$info = null;
			}
		} while ($info != null);
		
		$result['fullname'] = $fullname;
		return $result;
	}
	
	// get country list
	public function getCountryList()
	{
		$filter['type'] = AREA_TYPE_COUNTRY;
		return $this->getAll($filter);
	}
	
	// province list of a country
	public function getProvinceList($countryId = null)
	{
		if ($countryId != null) {
			$filter['parent_id'] = $countryId;
		}
		$filter['type'] = AREA_TYPE_PROVINCE;
		return $this->getAll($filter);
	}
	
	// city list of a province
	public function getCityList($provinceId = null)
	{
		if ($provinceId != null) {
			$filter['parent_id'] = $provinceId;
		}
		$filter['type'] = AREA_TYPE_CITY;
		return $this->getAll($filter);
	}
	
	// district list of a city
	public function getDistrictList($cityId = null)
	{
		if ($cityId != null) {
			$filter['parent_id'] = $cityId;
		}
		$filter['type'] = AREA_TYPE_DISTRICT;
		return $this->getAll($filter);
	}

	
	// limit operations
	public function getLimitList($itemKind, $itemId, $isDetail = false)
	{
		if (!$isDetail) {
			
			$filters['item_id'] = $itemId;
			$filters['item_kind'] = $itemKind;
			return $this->getAll($filters, null, $this->tblLimit);
			
		} else {
			
			$this->db->select("A.*")
					->from($this->tableName($this->tblLimit) . ' AS AL')
					->join($this->tableName($this->tbl) . ' AS A', 'AL.area_id = A.id', 'LEFT')
					->where('AL.item_kind', $itemKind)
					->where('AL.item_id', $itemId);
			return $this->db->get()->result_array();
		}
	}
    public function getProvince($id) {

        $where = array(
            'id' => $id
        );
        $query = $this->db->get_where($this->tbl, $where);
        $order = $query->result_array();
        if ($order == null)
            return 0;
        return $order[0]['name'];
    }
	
	public function insertLimit($areaId, $itemKind, $itemId)
	{
		$data = array(
			'area_id' => $areaId,
			'item_kind' => $itemKind,
			'item_id' => $itemId
		);
		return $this->_insert($data, $this->tblLimit);
	}
	
	public function deleteLimits($itemKind, $itemId)
	{
		$this->db->where('item_kind', $itemKind);
		$this->db->where('item_id', $itemId);
		return $this->db->delete($this->tblLimit);
	}
	public function fetchAll($where = array()){
		try
		{
			if(!empty($where)){
				$query = $this->db->get_where($this->tbl,$where);
			}
			else{
				$query = $this->db->get($this->tbl);
			}
			return $query->result_array();
		}
		catch (Exception $e)
		{
			return null;
		}
	}

	public function recursion_get_code($parent_code = '(0)')
	{
        $sql = "select area_id from tbl_new_area where parent_id in $parent_code";
        $query = $this->db->query($sql);
        $cities = $query->result_array();
        $city_str = '(';
        foreach ($cities as $city){
            $city_str.= $city['area_id'].',';
        }
        $city_str = substr($city_str,0,-1).')';
        return $city_str;
	}

    public  function get_area_code_name($selectprovince = 0,$selectcity = 0,$selectcounty = 0)
	{
		if($selectprovince == 0){
            $province_code_str = $this->recursion_get_code('(1)');
            $code_str = $this->recursion_get_code($province_code_str);
            $sql = "select area_id,name from tbl_new_area where parent_id in $code_str or parent_id in (1)";
		}
		if($selectprovince != 0 && $selectcity == 0){
          $code_str = $this->recursion_get_code("($selectprovince)");
          $sql = "select area_id,name from tbl_new_area where parent_id in $code_str or parent_id in (1) ";
		}
		if($selectprovince != 0 && $selectcity != 0){
            $sql = "select area_id,name from tbl_new_area where parent_id in ($selectcity,1)";
		}

        $query = $this->db->query($sql);
        return $query->result_array();
     }
}
?>
