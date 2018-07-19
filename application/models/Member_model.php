<?php

// member table model
class Member_model extends Base_Model {

	protected $tblContentRel = '';
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['member'];
		$this->tblContent = $TABLE['content'];
		$this->tblContentRel = $TABLE['member_content'];
	}
	
	// insert
	public function insert($kind, $name, $enName, $description, $introduction, $image, $certNumber, $idcard, $birthday, $height, $weight, $level, $education, $nickname, $countryId, $gender, $mobile, $address, $militaryServe, $clubId, $scoreWin, $scoreLoss, $scoreDraw, $scoreKo) 
	{
		$data = array(
			'kind' => $kind,
			'name' => $name,
			'en_name' => $enName,
			'description' => $description,
			'introduction' => $introduction,
			'image' => $image,
			'cert_number' => $certNumber,
			'idcard' => $idcard,
			'birthday' => $birthday,
			'height' => $height,
			'weight' => $weight,
			'level' => $level,
			'education' => $education,
			'nickname' => $nickname,
			'country_id' => $countryId,
			'gender' => $gender,
			'mobile' => $mobile,
			'address' => $address,
			'military_serve' => $militaryServe,
			'club_id' => $clubId,
			'score_win' => $scoreWin,
			'score_loss' => $scoreLoss,
			'score_draw' => $scoreDraw,
			'score_ko' => $scoreKo,
			'is_show' => 1,
			'create_date' => now()
		);
		return $this->_insert($data);
	}

	// set show/hide
	public function setShow($id, $isShow = true)
	{
		$data = array('is_show', $isShow ? 1 : 0);
		$this->update($id, $data);
	}
	
	// override delete
	public function delete($id, $table = null)
	{
		$this->load->model('event_model');
		$this->event_model->deleteCounterpartByMember($id);

		parent::delete($id, $table);
		return $this->deleteContent($id);
	}
	
	
	/*** content table related to the member functions ***/
	public function getContentCount($memberId)
	{
		$this->db->select("CR.content_id, C.*")
				->from($this->tableName($this->tblContentRel) . ' AS CR')
				->join($this->tableName($this->tblContent) . ' AS C', 'CR.content_id = C.id', 'LEFT')
				->where('CR.member_id', $memberId);
		if (!empty($filters)) {
			foreach ($filters as $key=>$value) {
				$this->db->where('C.' . $key, $value);
			}
		}
		return $this->db->count_all_results();
	}
	
	public function getContentList($memberId, $filters = null, $orders = null, $page = 1, $size = PAGE_SIZE)
	{
		$this->db->select("CR.content_id, C.*")
				->from($this->tableName($this->tblContentRel) . ' AS CR')
				->join($this->tableName($this->tblContent) . ' AS C', 'CR.content_id = C.id', 'LEFT')
				->where('CR.member_id', $memberId);
		if (!empty($filters)) {
			foreach ($filters as $key=>$value) {
				$this->db->where('C.' . $key, $value);
			}
		}
		if (!empty($orders)) {
			foreach ($orders as $key=>$value) {
				$this->db->order_by('C.' . $key, $value);
			}
		}
		if ($size != -1) {
			if ($page < 1) {
				$page = 1;
			}
			$this->db->limit($size, ($page - 1) * $size);
		}
		return $this->db->get()->result_array();
	}
	
	public function insertContent($memberId, $contentId)
	{
		$data = array(
			'member_id' => $memberId,
			'content_id' => $contentId
		);
		return $this->_insert($data, $this->tblContentRel);
	}
	
	public function deleteContent($memberId = null, $contentId = null)
	{
		if ($memberId == null && $contentId == null) {
			return false;
		}
		if ($memberId != null) {
			$filters['member_id'] = $memberId;
		}
		if ($contentId != null) {
			$filters['content_id'] = $contentId;
		}
		$this->db->where($filters);
		return $this->db->delete($this->tblContentRel);
	}

	
	// statistics function
	public function getCountInfo($filters)
	{
		$this->select("kind, COUNT(*) as cnt");
		foreach ($filters as $key=>$value) {
			$this->setWhere($key, $value);
		}
		$this->db->group_by("kind");
		$result = $this->db->get($this->tbl)->result_array();

		$total = 0;
		$counts = array(0, 0, 0, 0);
		if (!empty($result)) {
			foreach ($result as $item) {
				$counts[$item['kind']] = $item['cnt'];
				$total += $item['cnt'];
			}
		}
		$info['player'] = $counts[MEMBER_KIND_PLAYER];
		$info['referee'] = $counts[MEMBER_KIND_REFEREE];
		$info['coach'] = $counts[MEMBER_KIND_COACH];
		$info['total'] = $total;

		return $info;
	}
}
?>
