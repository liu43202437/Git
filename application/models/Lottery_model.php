<?php


class Lottery_model extends Base_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->tbl = "tbl_lottery_manager";
    }

    public function fetchOne($where = array()){
        try
        {
            if(!empty($where)){
                $query = $this->db->get_where($this->tbl,$where);
            }
            else{
                $query = $this->db->get($this->tbl);
            }
            return $query->row_array();
        }
        catch (Exception $e)
        {
            return null;
        }
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
}