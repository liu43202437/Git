<?php

class Test_model extends Base_Model {

    public function getdata($data){
        $this->db->select('user_id');
        $query=$this->db->get_where('session',$data);
        return $query->row_array();
    }


    public function getdataone($data){
//        $this->db->select('attribute1','attribute2','attribute3','attribute4','attribute5','attribute6');
        $query=$this->db->get_where('audit',$data);
        return $query->result_array();
    }
}