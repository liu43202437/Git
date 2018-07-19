<?php

// order table model
class Order_model extends Base_Model {

	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['order'];
	}
	
	// get
	public function getBySn($sn)
	{
		$this->db->where('sn', $sn);
		return $this->db->get($this->tbl)->row_array();
	}
	
	// get
	public function getByYjfSn($sn)
	{
		$this->db->where('yunjifen_sn', $sn);
		return $this->db->get($this->tbl)->row_array();
	}
	
	// insert
	public function insert($data) 
	{
		$errno = 0;
		do {
			try {
				$data['sn'] = get_order_sn();
				$data['create_date'] = now();
				if (!isset($data['order_status'])) {
					$data['order_status'] = ORDER_STATUS_PROCESSING;
				}
				if (!isset($data['pay_status'])) {
					$data['pay_status'] = PAY_STATUS_UNPAID;
				}
				if (!isset($data['shipping_status'])) {
					$data['shipping_status'] = SHIP_STATUS_UNSHIPPED;
				}
				$this->_insert($data);

			} catch (Exception $e) {
				$errno = $this->db->_error_number();
			}
		} while ($errno == 1062);
		
		return $data['sn'];
	}
//	public function getList($filters, $orders, $pageNumber, $pageSize){
//
//    }
	// set order status
	public function setStatus($id, $orderStatus)
	{
		$data = array('order_status' => $orderStatus);
		$this->update($id, $data);
	}
	
	// delete by user
	public function deleteByUser($userId)
	{
		$where = array('user_id' => $userId);
		return $this->db->delete($this->tbl, $where);
	}
	
	// statistics function
	public function getPointInfo($filter)
	{
		$filter['order_status'] = ORDER_STATUS_SUCCEED;
		
		$this->select('SUM(gain_point) AS gain, SUM(pay_point) AS pay');
		foreach ($filter as $key=>$value) {
			$this->setWhere($key, $value);
		}
		$result = $this->db->get($this->tbl)->row_array();
		
		$info['gain'] = intval($result['gain']);
		$info['pay'] = intval($result['pay']);

		return $info;
	}
	
	public function getCountInfo($filter)
	{
		$this->select('SUM(item_count) AS total_count');
		foreach ($filter as $key=>$value) {
			$this->setWhere($key, $value);
		}
		$result = $this->db->get($this->tbl)->row_array();
		
		return empty($result) ? 0 : intval($result['total_count']);
	}

    public function getHunanCountInfo($machine_id,$starttime , $endtime){

        $sql = "SELECT SUM(IFNULL(real_ticket_num,0)) AS real_ticket_num,SUM(total_fee) AS total_fee FROM tbl_hunan_order WHERE  pay_status = 1 AND machine_id = '$machine_id' AND pay_date BETWEEN '$starttime' AND '$endtime'";

        $query = $this->db->query($sql);
        $result = $query->result_array()[0];
        $data = $result;
        foreach ($result as $key=>$val){
            if($val == null){
                $val = 0;
            }
            $data[$key] = $val;
        }

        $data['Commission'] = $data['total_fee']*0.06;
        return $data;
    }
}
?>
