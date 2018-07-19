<?php

// club table model
class Email_model extends Base_Model
{

    // constructor
    public function __construct()
    {
        parent::__construct();

        $this->tbl = 'tbl_email';
    }
    public function get_need_email_by_province($province = NULL)
    {
        if(empty($province)) return false;
        $sql = "select email from {$this->tbl} where (area_id = {$province} or area_id = 0) and status = 1";
        $query = $this->db->query($sql);
        $result = $query->result_array();

        return empty($result)?false:$result;
    }

}
