<?php
/**
 * CodeIgniter Base Model
 *
 * @author kkk10
 * @copyright	2016 - 2017, STSOFT Team
 * @since	Version 1.0.0
 */
class Base_Model extends CI_Model {

	protected $tbl = '';

	// constructor
	public function __construct()
	{
		parent::__construct();
	}

	protected function table($table = null)
	{
		return ($table === null) ? $this->tbl : $table;
	}

	// get table name
	public function tableName($table = null)
	{
		return $this->db->dbprefix($this->table($table));
	}

	// select 
	public function select($select)
	{
		return $this->db->select($select);
	}

	// get
	public function get($id, $table = null)
	{
		$this->db->where('id', $id);
		$query = $this->db->get($this->table($table));
		return $query->row_array();
	}

	// get empty row (structure)
	public function getEmptyRow($table = null)
	{
		$row = array();
		$fields = $this->db->field_data($this->table($table));

		foreach ($fields as $field)
		{
			$row[$field->name] = null;
		}
		return $row;
	}

	// get top item
	public function getTopRow($filters = null, $orders = null, $table = null)
	{
		$result = $this->getList($filters, $orders, 1, 1, $table);
		if (empty($result)) {
			return null;
		}
		return $result[0];
	}

	public function setWhere($key, $value, $or = false) {
		if (is_array($value)) {
			$this->db->group_start();
			foreach ($value as $val) {
				$this->db->or_where($key, $val);
			}
			$this->db->group_end();
		} else {
			if (endsWith($key, '%')) {
				$key = substr($key, 0, strlen($key) - 1);
				if ($or) {
					$this->db->or_like($key, $value);
				} else {
					$this->db->like($key, $value);
				}
			} else {
				if ($or) {
					$this->db->or_where($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}
	}
	// get count by conditions
	public function getCount($filters = null, $table = null)
	{
		if (!empty($filters)) {
			foreach ($filters as $key=>$value) {
				if (strpos($key, ",") === false) {
					$this->setWhere($key, $value);
				} else {
					$keys = preg_split("/[\s,]+/", $key, 0, PREG_SPLIT_NO_EMPTY);
					$this->db->group_start();
					foreach ($keys as $k) {
						$this->setWhere($k, $value, true);
					}
					$this->db->group_end();
				}
			}
		}
		return $this->db->count_all_results($this->table($table));
	}
	public function getCountTwo($filters = null , $area_code = NULL, $table = null,$area=null)
	{
		if (!empty($filters)) {
			foreach ($filters as $key=>$value) {
				if (strpos($key, ",") === false) {
					$this->setWhere($key, $value);
				} else {
					$keys = preg_split("/[\s,]+/", $key, 0, PREG_SPLIT_NO_EMPTY);
					$this->db->group_start();
					foreach ($keys as $k) {
						$this->setWhere($k, $value, true);
					}
					$this->db->group_end();
				}
			}
		}
		if(!empty($area_code)){
			foreach ($area_code as $key => $value) {
				$this->setWhere($key, $value, false);
			}
		}
		if ($area != null){
			$this->db->group_by($area);
		}
		return $this->db->count_all_results($this->table($table));
	}

	// get list by conditions
	public function getList($filters = null, $orders = null, $page = 1, $size = PAGE_SIZE, $table = null)
	{
		try {
			if (!empty($filters)) {
				foreach ($filters as $key=>$value) {
					if (strpos($key, ",") === false) {
						$this->setWhere($key, $value);
					} else {
						$keys = preg_split("/[\s,]+/", $key, 0, PREG_SPLIT_NO_EMPTY);
						$this->db->group_start();
						foreach ($keys as $k) {
							$this->setWhere($k, $value, true);
						}
						$this->db->group_end();
					}
				}
			}
			if (!empty($orders)) {
				foreach ($orders as $key=>$value) {
					$this->db->order_by($key, $value);
				}
			} else {
				//$this->db->order_by('create_date', 'DESC');
			}

			if ($size != -1) {
				if ($page < 1) {
					$page = 1;
				}
				$this->db->limit($size, ($page - 1) * $size);
			}
			$query = $this->db->get($this->table($table));
			return $query->result_array();
		} catch (Exception $e) {
			return null;
		}
	}
	public function getListTwo($filters = null, $orders = null, $page = 1, $size = PAGE_SIZE, $area_code = NULL, $table = null,$area=null)
	{
		try {
			if (!empty($filters)) {
				foreach ($filters as $key=>$value) {
					if (strpos($key, ",") === false) {
						$this->setWhere($key, $value);
					} else {
						$keys = preg_split("/[\s,]+/", $key, 0, PREG_SPLIT_NO_EMPTY);
						$this->db->group_start();
						foreach ($keys as $k) {
							$this->setWhere($k, $value, true);
						}
						$this->db->group_end();
					}
				}
			}
			if(!empty($area_code)){
				foreach ($area_code as $key => $value) {
					$this->setWhere($key, $value, false);
				}
			}
			if (!empty($orders)) {
				foreach ($orders as $key=>$value) {
					$this->db->order_by($key, $value);
				}
			} else {
				//$this->db->order_by('create_date', 'DESC');
			}

			if ($size != -1) {
				if ($page < 1) {
					$page = 1;
				}
				$this->db->limit($size, ($page - 1) * $size);
			}
			if ($area != null){
				$this->db->select('*');
				$this->db->select_sum('total_money');
				$this->db->group_by($area);
			}
			$query = $this->db->get($this->table($table));
//			if ($area != null){
//				$sql="select *,COUNT(*)AS number from {$query}";
//				$query=$this->db->querry($sql);
//			}
			return $query->result_array();
		} catch (Exception $e) {
			return null;
		}
	}

	// get all
	public function getAll($filters = null, $orders = null, $table = null)
	{
		return $this->getList($filters, $orders, 1, -1, $table);
	}

	// update info
	public function _insert($data, $table = null)
	{
		foreach ($data as $key=>$value) {
			if (endsWith($key, '$')) {
				$key = substr($key, 0, strlen($key) - 1);
				$this->db->set($key, $value, FALSE);
			} else {
				$this->db->set($key, $value);
			}
		}
		$this->db->insert($this->table($table));
		return $this->db->insert_id();
	}

	// update info
	public function update($id, $data, $table = null)
	{
		try {
			$this->db->where('id', $id);
			foreach ($data as $key=>$value) {
				if (endsWith($key, '$')) {
					$key = substr($key, 0, strlen($key) - 1);
					$this->db->set($key, $value, FALSE);
				} else {
					$this->db->set($key, $value);
				}
			}
			$this->db->update($this->table($table));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	// update by where
	public function _update($data, $filters = null, $table = null)
	{
		try {
			if (!empty($filters)) {
				foreach ($filters as $key=>$value) {
					if (strpos($key, ",") === false) {
						$this->setWhere($key, $value);
					} else {
						$keys = preg_split("/[\s,]+/", $key, 0, PREG_SPLIT_NO_EMPTY);
						$this->db->group_start();
						foreach ($keys as $k) {
							$this->setWhere($k, $value, true);
						}
						$this->db->group_end();
					}
				}
			}
			foreach ($data as $key=>$value) {
				if (endsWith($key, '$')) {
					$key = substr($key, 0, strlen($key) - 1);
					$this->db->set($key, $value, FALSE);
				} else {
					$this->db->set($key, $value);
				}
			}
			$this->db->update($this->table($table));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	// delete
	public function delete($id, $table = null)
	{
		try {
			$this->db->where('id', $id);
			return $this->db->delete($this->table($table));
		} catch (Exception $e) {
			return false;
		}
	}

	// clear table
	public function clear($table = null)
	{
		try {
			return $this->db->empty_table($this->table($table));
		} catch (Exception $e) {
			return false;
		}
	}


	//综合各地区表订单
	public function neworder(){
		$arr=array();
		for ($i = 1 ; $i<= 100 ; $i++){
			$sql=<<<SQL
show tables LIKE 'tbl_ticket_order_{$i}'
SQL;
			$res=$this->db->query($sql);
			$res=$res->row_array();
			if (!empty($res)){
				$arr[$i]=$res;
			}
		}
		$a='select * from tbl_ticket_order';
		$count=1;
		foreach ($arr as $key => $item) {
			if ($count == 1) {
				$a .= " union select * from tbl_ticket_order_{$key}";
			}else{
				$a .= " union select * from tbl_ticket_order_{$key}";
			}
			$count++;
		}
		return $a;
	}

    //综合各地区表订单
    public function neworder_num(){
        $arr=array();
        for ($i = 1 ; $i<= 100 ; $i++){
            $sql=<<<SQL
show tables LIKE 'tbl_order_num_{$i}'
SQL;
            $res=$this->db->query($sql);
            $res=$res->row_array();
            if (!empty($res)){
                $arr[$i]=$res;
            }
        }
        $a='select * from tbl_order_num';
        $count=1;
        foreach ($arr as $key => $item) {
            if ($count == 1) {
                $a .= " union select * from tbl_order_num_{$key}";
            }else{
                $a .= " union select * from tbl_order_num_{$key}";
            }
            $count++;
        }
        return $a;
    }
}
?>
