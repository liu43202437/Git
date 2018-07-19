<?php

// event table model
class Event_model extends Base_Model {

	protected $tblContent = '';
	protected $tblContentRel = '';
	protected $tblCounterpart = '';
	protected $tblTicketPrice = '';
	
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['event'];
		$this->tblContent = $TABLE['content'];
		$this->tblContentRel = $TABLE['event_content'];
		$this->tblCounterpart = $TABLE['event_counterpart'];
		$this->tblTicketPrice = $TABLE['ticket_price'];
	}
	
	// insert
	public function insert($kind, $type, $title, $subtitle, $eventDate, $image, $video, $link, $location, $organizationId, $hasTicket, $ticketTitle, $ticketImage, $ticketPosImage, $ticketNote, $ticketTakeDesc)
	{
		$data = array(
			'kind' => $kind,
			'type' => $type,
			'title' => $title,
			'subtitle' => $subtitle,
			'event_date' => $eventDate,
			'image' => $image,
			'video' => $video,
			'link' => $link,
			'location' => $location,
			'organization_id' => $organizationId,
			'has_ticket' => $hasTicket,
			'ticket_title' => $ticketTitle,
			'ticket_image' => $ticketImage,
			'ticket_pos_image' => $ticketPosImage,
			'ticket_note' => $ticketNote,
			'ticket_take_desc' => $ticketTakeDesc,
			'hits' => 0,
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
	
	// increase hits count
	public function increaseHits($id)
	{
		$data = array('hits$' => 'hits + 1');
		$this->update($id, $data);
	}
	
	// override delete
	public function delete($id, $table = null)
	{
		parent::delete($id, $table);
		$this->deleteContent($id);
		$this->deleteTicketPriceByEvent($id);
		return $this->deleteCounterpartByEvent($id);
	}
	
	
	/*** content table related to the event ***/
	public function getContentCount($eventId)
	{
		$this->db->select("CR.content_id, C.*")
				->from($this->tableName($this->tblContentRel) . ' AS CR')
				->join($this->tableName($this->tblContent) . ' AS C', 'CR.content_id = C.id', 'LEFT')
				->where('CR.event_id', $eventId);
		if (!empty($filters)) {
			foreach ($filters as $key=>$value) {
				$this->db->where('C.' . $key, $value);
			}
		}
		return $this->db->count_all_results();
	}
	
	public function getContentList($eventId, $filters = null, $orders = null, $page = 1, $size = PAGE_SIZE)
	{
		$this->db->select("CR.content_id, C.*")
				->from($this->tableName($this->tblContentRel) . ' AS CR')
				->join($this->tableName($this->tblContent) . ' AS C', 'CR.content_id = C.id', 'LEFT')
				->where('CR.event_id', $eventId);
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
	
	public function insertContent($eventId, $contentId)
	{
		$data = array(
			'event_id' => $eventId,
			'content_id' => $contentId
		);
		return $this->_insert($data, $this->tblContentRel);
	}
	
	public function deleteContent($eventId = null, $contentId = null)
	{
		if ($eventId == null && $contentId == null) {
			return false;
		}
		if ($eventId != null) {
			$filters['event_id'] = $eventId;
		}
		if ($contentId != null) {
			$filters['content_id'] = $contentId;
		}
		$this->db->where($filters);
		return $this->db->delete($this->tblContentRel);
	}

	
	/*** counterpart table for event ***/
	public function getCounterpartCount($eventId)
	{
		return $this->getCount(array('event_id'=>$eventId), $this->tblCounterpart);
	}
	
	public function getCounterpartList($eventId)
	{
		$filter['event_id'] = $eventId;
		$order['orders'] = 'ASC';
		return $this->getAll($filter, $order, $this->tblCounterpart);
	}
	
	public function insertCounterpart($eventId, $aPlayerId, $bPlayerId, $winner, $description)
	{
		$data = array(
			'event_id' => $eventId,
			'a_player_id' => $aPlayerId,
			'b_player_id' => $bPlayerId,
			'winner' => $winner,
			'description' => $description,
			'orders' => 0
		);
		$id = $this->_insert($data, $this->tblCounterpart);
		return $this->update($id, array('orders'=>$id), $this->tblCounterpart);
	}
	
	public function updateCounterpart($id, $data)
	{
		return $this->update($id, $data, $this->tblCounterpart);
	}

	public function deleteCounterpart($id)
	{
		return $this->delete($id, $this->tblCounterpart);
	}
	
	public function deleteCounterpartByEvent($eventId)
	{
		$this->db->where('event_id', $eventId);
		return $this->db->delete($this->tblCounterpart);
	}
	public function deleteCounterpartByMember($memberId)
	{
		$this->db->where('a_player_id', $memberId);
		$this->db->or_where('b_player_id', $memberId);
		return $this->db->delete($this->tblCounterpart);
	}
	
	
	/*** match ticket info for event ***/
	public function getTicketPrice($ticketId)
	{
		return $this->get($ticketId, $this->tblTicketPrice);
	}
	
	public function getTicketPriceList($eventId)
	{
		$filter['event_id'] = $eventId;
		$order['orders'] = 'ASC';
		return $this->getAll($filter, $order, $this->tblTicketPrice);
	}
	
	public function insertTicketPrice($eventId, $name, $price, $count, $color = null)
	{
		$data = array(
			'event_id' => $eventId,
			'name' => $name,
			'price' => $price,
			'color' => $color,
			'count' => $count,
			'orders' => 0
		);
		$id = $this->_insert($data, $this->tblTicketPrice);
		return $this->update($id, array('orders'=>$id), $this->tblTicketPrice);
	}
	
	public function updateTicketPrice($id, $data)
	{
		$this->update($id, $data, $this->tblTicketPrice);
	}
	
	public function deleteTicketPrice($id)
	{
		$this->delete($id, $this->tblTicketPrice);
	}
	public function deleteTicketPriceByEvent($eventId)
	{
		$this->db->where('event_id', $eventId);
		return $this->db->delete($this->tblTicketPrice);
	}

	public function getTicketInfo($price){
		$sql = "select a.id,a.event_id,a.name,a.price,a.count,b.ticket_title from tbl_event_ticket_price as a left join tbl_event as b
               on a.event_id = b.id where a.price = $price ";
		$result = $this->execSQL($sql);
		if(empty($result)){
			return null;
		}
		return $result;

	}
    function execSQL($sql)
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
}
?>
