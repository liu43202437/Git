<?php
class Machinecase_model extends Base_Model{
    protected $tbl_log;
    public function __construct()
    {
        parent::__construct();

        $this->tbl = 'tbl_hunan_machine';
        $this->tbl_log = 'tbl_hunan_ping_log';

    }

    public function updateData($data, $where)
    {
        try
        {
            $this->db->where($where)->update($this->tbl, $data);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function log_insert($data)
    {
        return $this->_insert($data,$this->tbl_log);
    }

    public function get_info_by_machine_id($machine_id)
    {
        $where = array('machine_id' => $machine_id);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }
}