<?php

// club table model
class Club_model extends Base_Model
{

    protected $tblImage = '';

    // constructor
    public function __construct()
    {
        parent::__construct();

        global $TABLE;
        $this->tbl = $TABLE['club'];
        $this->tblImage = $TABLE['club_image'];
    }


    public function salesRanking($start,$end,$data,$tbl)
    {
        if(empty($data)){
            $sql = "SELECT * from  ( SELECT a.user_id,SUM(total_money) as total,b.`name`,b.city,b.address FROM {$tbl} as a  JOIN tbl_club as b ON a.user_id=b.user_id AND a.order_status=2 AND b.is_show=1 AND a.update_date < '$end' AND a.update_date>'$start' AND a.user_id not in (201032,201043,201039,201090) AND a.area='{$data['province']}'  GROUP BY a.user_id) as lib order by total desc,user_id ";
        }
        else{
            $sql = "SELECT * from  ( SELECT a.user_id,SUM(total_money) as total,b.`name`,b.city,b.address FROM {$tbl} as a  JOIN tbl_club as b ON a.user_id=b.user_id AND a.order_status=2 AND b.is_show=1 AND a.update_date < '$end' AND a.update_date>'$start' AND a.user_id not in (201032,201043,201039,201090) AND a.area='{$data['province']}'   GROUP BY a.user_id ) as lib order by total desc,user_id limit ".(($data['pageIndex']-1)*$data['entryNum']).",{$data['entryNum']}";
        }
        $query = $this->db->query($sql);
        $res = [];
        foreach ($query->result_array() as $row) {
            if (empty($row['avatar_url'])) {
                $row['avatar_url'] = base_url() . $this->config->item('default_avatar');
            }
            $res[] = $row;
        }
        return $res;
    }

    public function getInfoByYancode($yan_code)
    {
        $where = array('yan_code' => $yan_code);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    public function getInfoByNumber($id_number)
    {
        $where = array('id_number' => $id_number);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    public function getInfoByphone($phone)
    {
        $where = array('phone' => $phone);
        $query = $this->db->get_where($this->tbl, $where);
        return $query->row_array();
    }

    public function getInfoByUserId($user_id)
    {
        $sql="select a.*,b.name as sheng from tbl_club a left join tbl_area b on a.area_id = b.id where user_id=$user_id";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


    public function wxinsert($data)
    {
        return $this->_insert($data);
    }


    // get
    public function get($id, $table = null)
    {
        $info = parent::get($id);
        if (empty($info)) {
            return null;
        }
        $info['images'] = $this->getImageList($id);
        $info['image_count'] = (empty($info['images'])) ? 0 : count($info['images']);
        return $info;
    }

    // insert
    public function insert($name, $viewName, $logo, $thumb, $phone, $contact, $contactPhone, $areaId, $city, $address, $longitude, $latitude, $serviceTime, $introduction)
    {
        $data = array(
            'name' => $name,
            'view_name' => $viewName,
            'logo' => $logo,
            'thumb' => $thumb,
            'phone' => $phone,
            'contact' => $contact,
            'contact_phone' => $contactPhone,
            'area_id' => $areaId,
            'city' => $city,
            'address' => $address,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'service_time' => $serviceTime,
            'introduction' => $introduction,
            'is_show' => 1,
            'create_date' => now()
        );
        return $this->_insert($data);
    }

    // set show/hide
    public function setShow($id, $isShow = true)
    {
        $data = array('is_show' => $isShow ? 1 : 0);
        $this->update($id, $data);
    }


    /*** club image functions ***/
    public function getImageCount($clubId)
    {
        return $this->getCount(array('club_id' => $clubId), $this->tblImage);
    }

    public function getImageList($clubId)
    {
        $filter['club_id'] = $clubId;
        $order['orders'] = 'ASC';
        return $this->getAll($filter, $order, $this->tblImage);
    }

    public function insertImage($clubId, $image)
    {
        $data = array(
            'club_id' => $clubId,
            'image' => $image,
            'orders' => 0
        );
        $id = $this->_insert($data, $this->tblImage);
        return $this->update($id, array('orders' => $id), $this->tblImage);
    }

    public function updateImage($imageId, $data)
    {
        return $this->update($imageId, $data, $this->tblImage);
    }

    public function deleteImage($imageId)
    {
        return $this->delete($imageId, $this->tblImage);
    }

    public function deleteImageByClub($clubId)
    {
        $this->db->where('club_id', $clubId);
        return $this->db->delete($this->tblImage);
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
    public function getCredits($user_id,$start,$end){
        $sql = "SELECT SUM(get_credits) as credits_today FROM tbl_ticket_order WHERE order_status=2 AND user_id=$user_id AND update_date>='$start' AND update_date <= '$end'";
        $query = $this->db->query($sql);
        return $query->result_array();
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
    public function get_club_noaudit_num($phone){
        $sql = "select count(*) as num from tbl_club where status = 0 and refuse=0 and question=1 and manager_id = '{$phone}'";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        if(empty($result)){
            return null;
        }
        return $result;

    }

    public function deleteclubs($id)
    {
        try {
            $ids=$id;
            $sql=<<<SQL
select manager_id,status,refuse from tbl_club WHERE id='{$ids}';
SQL;
            $res=$this->db->query($sql);
            $res=$res->row_array();
            if ($res['refuse'] == '1' || $res['status'] == '0'){
                
            }else {
                $ids = $res['manager_id'];

                $sql = <<<SQL
UPDATE tbl_user SET point=point-10 WHERE phone='{$ids}'
SQL;
                $this->db->query($sql);

                $sql = <<<SQL
select id from tbl_user WHERE phone='{$ids}';
SQL;
                $result = $this->db->query($sql);
                $result = $result->row_array();
                $ids = $result['id'];

                $sql = <<<SQL
DELETE FROM tbl_user_credits WHERE user_id='{$ids}' AND type='6' ORDER BY id DESC LIMIT 1
SQL;
                $this->db->query($sql);
            }
                
            $this->db->where('id', $id);
            $this->db->delete('tbl_club');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>
