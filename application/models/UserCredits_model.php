<?php

/**
 * @Author: liu43
 * @Date:   2017-09-07 10:03:44
 * @Last Modified by:   liuzudong
 * @Last Modified time: 2018-02-05 11:56:13
 */
/**
* 数据库操作类
*/
class UserCredits_model extends CI_Model
{
	private $tbl='tbl_user_credits';
	function __construct()
	{
		parent::__construct();
	}
	public function insertData($data = '')
	{
		try
		{
			$this->db->insert($this->tbl,$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function insertBatch($data = '')
	{
		try
		{
			$this->db->insert_batch($this->tbl,$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function deleteData($where){
		try
		{
			$this->db->delete($this->tbl, $where);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function updateData( $data, $where)
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
	public function queryAll($sql)
	{
		try
		{
			$query = $this->db->query($sql);
			return $query->result_array();
		}
		catch (Exception $e)
		{
			return null;
		}
	}
}