<?php

/**
 * @Author: liu43
 * @Date:   2017-09-07 10:03:44
 * @Last Modified by:   liu43
 * @Last Modified time: 2017-09-07 15:21:38
 */
/**
* 数据库操作类
*/
class Mydb_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function insertData($tbl,$data = '')
	{
		try
		{
			$this->db->insert($tbl,$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function deleteData($tbl,$where){
		try
		{
			$this->db->delete($tbl, $where);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function updateData($tbl, $data, $where)
	{
		try
		{
			$this->db->where($where)->update($tbl, $data);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function fetchAll($tbl,$where = array()){
		try
		{
			if(!empty($where)){
				$query = $this->db->get_where($tbl,$where);
			}
			else{
				$query = $this->db->get($tbl);
			}
			return $query->result_array();
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	public function fetchOne($tbl,$where = array()){
		try
		{
			if(!empty($where)){
				$query = $this->db->get_where($tbl,$where);
			}
			else{
				$query = $this->db->get($tbl);
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