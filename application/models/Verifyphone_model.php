<?php
// 개발자등록을 위한 전화번호인증코드 관리
class Verifyphone_model extends Base_Model {
	// constructor
	public function __construct() {
		parent::__construct();
	}
	// 같은 인증코드가 존재하면 true, 존재하지 않으면 false를 귀환
	public function is_exist_code($phone, $code)
	{
		$where = array(
			'phone' => $phone,
			'code' => $code
		);

        $this->db->where($where);
        //return count($this->db->get($tbl));
        return $this->db->count_all_results('verify_phone');
	}


	public function get_RegisterTimeByPhoneAndCode($phone, $code) {
		if ($phone == null || $phone == '') return '';
		if ($code == null || $code == '') return '';

		$where = array(
			'phone' => $phone,
			'code' => $code
		);
		$orderby = array(
			'register_time' => 'DESC'
		);
		$verify = $this->get_where('verify_phone', $where, $orderby);
		if ($verify == null)
			return '';
		return $verify[0]->register_time;
	}
	//
    function get_where($tbl, $where, $orderby=array())
    {
        try
        {
            foreach ($orderby as $key=>$value)
            {
                $this->db->order_by($key, $value);
            }
            $query = $this->db->get_where($tbl, $where);
            return $query->result();
        }
        catch (Exception $e)
        {
            return null;
        }
    }
    //
	public function add_new_code($phone, $code) {
		$data = array(
			'phone' => $phone,
			'code' => $code,
			'register_time' => now()
		);
		return $this->db->insert('verify_phone', $data);
	}

	public function delete_byPhone($phone)
	{
		$where = array(
			'phone' => $phone
		);
		return $this->db->delete('verify_phone', $where);
	}


}
?>
