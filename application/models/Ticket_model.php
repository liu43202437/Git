<?php

class Ticket_model extends Base_Model {



    // constructor
    public function __construct()
    {
        parent::__construct();

        $this->tbl = 'ticket';

    }

    // get info by price
    public function getTicketInfo($price,$province_id = '')
    {
        $where = array('price' =>$price,'province_id'=>$province_id,'status'=>0);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->result_array();
    }
   //get info by id
    public function getTicketInfoById($id)
    {
        $where = array('id' => $id);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->result_array();
    }
    // add administrator
    public function insert($username, $password)
    {
        $data = array(
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_enabled' => 1,
            'create_date' => now()
        );
        return $this->_insert($data);
    }

    // update info
    public function update($id, $data, $table = null)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::update($id, $data);
    }

    // delete by name
    public function deleteByName($username)
    {
        $where = array(
            'username' => $username
        );
        return $this->db->delete($this->tbl, $where);
    }
    public function execSQL($sql)
    {
        try
        {
            $query = $this->db->query($sql);
            return $query->result();
        }
        catch (Exception $e)
        {
            return null;
        }
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
    public function updateInventory($sql)
    {
        try
        {
            $query = $this->db->query($sql);
            return $query;
        }
        catch (Exception $e)
        {
            return null;
        }
    }


}
?>