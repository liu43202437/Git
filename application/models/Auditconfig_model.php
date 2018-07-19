<?php

// audit_config table model
class Auditconfig_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['audit_config'];
	}
	
	// get info by id
	public function getConfig($kind, $challengeId = null)
	{
		$filter['kind'] = $kind;
		if (!empty($challengeId)) {
			$filter['challenge_id'] = $challengeId;
		}
		$order['attr_name'] = 'ASC';
		//$order['CAST(SUBSTRING(attr_name, 10) AS INT)'] = 'ASC';
		//$this->db->order_by('CAST(SUBSTRING(attr_name, 10) AS INT)', 'ASC', false);
		$result = $this->getAll($filter, $order);
		if (empty($result)) {
			return null;
		}
		
		$rsltList = array();
		for ($i = 1; $i <= 20; $i++) {
			foreach ($result as $item) {
				if ($item['attr_name'] == "attribute".$i) {
					$rsltList[] = $item;
					break;
				}
			}
		}
		
		return $rsltList;
	}

	// insert
	public function insert($kind, $challengeId, $attrName, $attrLabel, $attrHint, $valueType, $values, $targetField) 
	{
		$data = array(
			'kind' => $kind,
			'challenge_id' => $challengeId,
			'attr_name' => $attrName,
			'attr_label' => $attrLabel,
			'attr_hint' => $attrHint,
			'value_type' => $valueType,
			'values' => $values,
			'target_field' => $targetField
		);
		return $this->_insert($data);
	}

	// delete by target
	public function deleteByKind($kind, $challengeId = null)
	{
		$where['kind'] = $kind;
		if ($challengeId) {
			$where['challenge_id'] = $challengeId;
		}
		return $this->db->delete($this->tbl, $where);
	}

    public function getById($id)
    {
        $where = array(
            'id' => $id
        );
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
}
?>
