<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Base_AdminController {
	
	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/member/';
        $this->load->model('member_model');
    }

    public function lists($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$weight = $this->get_input('weight');
    	$gender = $this->get_input('gender');
    	$countryId = $this->get_input('country_id');
    	$level = $this->get_input('level');
    	$searchKey = $this->get_input('search_key');
    	
		$filters = array();
		$orders = array();

		$filters['kind'] = $kind;		
		if (!empty($weight)) {
			$filters['weight'] = $weight;
		}
		if (!empty($gender)) {
			$filters['gender'] = $gender;
		}
		if (!empty($level)) {
			$filters['level'] = $level;
		}
		if (!empty($countryId)) {
			$filters['country_id'] = $countryId;
		}
		if (!empty($searchKey)) {
			$filters['name%, cert_number%'] = $searchKey;
		}
		
		if (!empty($this->pager['orderProperty'])) {
			$orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
		} else {
			$orders['create_date'] = 'DESC';
		}
		    	
    	$totalCount = $this->member_model->getCount($filters);
    	$rsltList = $this->member_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
    	foreach ($rsltList as $key=>$item) {
			 
		}
		
		$this->load->model('area_model');
		$this->data['countries'] = $this->area_model->getCountryList();

    	$this->data['kind'] = $kind;
    	$this->data['weight'] = $weight;
    	$this->data['gender'] = $gender;
    	$this->data['level'] = $level;
    	$this->data['countryId'] = $countryId;
    	$this->data['searchKey'] = $searchKey;
    	$this->data['itemList'] = $rsltList;
    	$this->data['isEditable'] = $this->auth_role('member/add');
    	$this->assign_pager($totalCount);
    	$this->assign_message();
    	
		$this->load_view('list');
    }
    
    public function ajax_list()
    {
		$kind = $this->get_input('kind');
		$term = $this->get_input('term');
		if (!empty($kind)) {
			$filter['kind'] = $kind;
		}
		$filter['id, name%, cert_number%'] = $term;
		
		$rsltList = $this->member_model->getList($filter, null, 1, 10);
		$itemList = array();
		$playerList = array('label' => '选手');
		$refereeList = array('label' => '裁判');
		$coachList = array('label' => '教练');
		foreach ($rsltList as $key=>$item) {
			$rItem = array();
			$rItem['id'] = $item['id'];
			$rItem['kind'] = $item['kind'];
			$rItem['name'] = $item['name'];
			$rItem['label'] = '[' . $rItem['id'] . '] ' . $item['name'];
			if ($item['kind'] == MEMBER_KIND_PLAYER) {
				$playerList['items'][] = $rItem;
			} else if ($item['kind'] == MEMBER_KIND_REFEREE) {
				$refereeList['items'][] = $rItem;
			} else if ($item['kind'] == MEMBER_KIND_COACH) {
				$coachList['items'][] = $rItem;
			}
		}
		if (!empty($playerList['items'])) {
			$itemList[] = $playerList;
		}
		if (!empty($refereeList['items'])) {
			$itemList[] = $refereeList;
		}
		if (!empty($coachList['items'])) {
			$itemList[] = $coachList;
		}
		if (!empty($kind) && !empty($itemList)) {
			$itemList = $itemList[0]['items'];
		}
		echo json_encode($itemList);
    }
    
    public function delete()
    {
		$ids = $this->post_input('ids');
		if (empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		foreach ($ids as $id) {
			$this->member_model->delete($id);
		}
		
		$this->add_log('删除人物信息', $ids);
		
		$data = parent::success_message();
		echo json_capsule($data);
    }
    
    public function toggle_show()
    {
		$id = $this->post_input('id');
		$ids = $this->post_input('ids');
		if (empty($id) && empty($ids)) {
			$data = parent::error_message('输入参数错误');
			die(json_capsule($data));
		}
		
		if (empty($ids)) {	// one item operation
			$data['is_show$'] = '1-is_show';
			$this->member_model->update($id, $data);

		} else {			// batch item operation
			$data['is_show'] = $this->post_input('is_show', 0);
			foreach ($ids as $id) {
				$this->member_model->update($id, $data);
			}
		}
		
		echo json_capsule(parent::success_message());
    }
    
    public function edit($kind)
    {
    	if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$id = $this->get_input('id');
		if (empty($id)) {
			$this->data['isNew'] = true;
			$itemInfo = $this->member_model->getEmptyRow();
			
		} else {
			$this->data['isNew'] = false;
			$itemInfo = $this->member_model->get($id);
		}
		
		$this->load->model('area_model');
		$this->data['countries'] = $this->area_model->getCountryList();
		
		$this->data['kind'] = $kind;
        $this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function edit_from_audit($kind)
    {
		if (empty($kind)) {
			show_errorpage();
    	}
    	
    	$id = $this->get_input('id');
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('audit_model');
		$auditInfo = $this->audit_model->get($id);
		
		if ($kind == AUDIT_KIND_CHALLENGE) {
			$kind = MEMBER_KIND_PLAYER;
		}
		
		$itemInfo = $this->member_model->getEmptyRow();
		$itemInfo['kind'] = $kind;
		/*$itemInfo['name'] = $auditInfo['name'];
		$itemInfo['gender'] = $auditInfo['gender'];
		$itemInfo['birthday'] = $auditInfo['birthday'];
		$itemInfo['mobile'] = $auditInfo['mobile'];*/
		
		for ($i = 1; $i <= 20; $i++) {
			$value = $auditInfo['attribute'.$i];
			if (empty($value)) {
				break;
			}
			$parts = explode('|', $value);
			if (count($parts) < 2) {
				break;
			}
			$value = $parts[0];
			if (isset($parts[2]) && !empty($parts[2])) {
				$itemInfo[$parts[2]] = $parts[0];
			}
		}
		
		$this->load->model('area_model');
		$this->data['countries'] = $this->area_model->getCountryList();
		
		$this->data['isNew'] = true;
		$this->data['auditItemId'] = $id;
		$this->data['kind'] = $kind;
		$this->data['itemInfo'] = $itemInfo;
		$this->assign_message();
		$this->load_view('edit');
    }
    
    public function save()
	{
		$kind = $this->post_input('kind');
		$id = $this->post_input('id');
		$auditItemId = $this->post_input('audit_item_id');
		$data['name'] = $this->post_input('name');
		$data['en_name'] = $this->post_input('en_name');
		$data['description'] = $this->post_input('description');
		$data['introduction'] = $this->post_input('introduction');
		$data['image'] = $this->post_input('image');
		$data['cert_number'] = $this->post_input('cert_number');
		$data['idcard'] = $this->post_input('idcard');
		$data['birthday'] = $this->post_input('birthday', null);
		$data['height'] = $this->post_input('height');
		$data['weight'] = $this->post_input('weight');
		$data['level'] = $this->post_input('level');
		$data['education'] = $this->post_input('education');
		$data['nickname'] = $this->post_input('nickname');
		$data['country_id'] = $this->post_input('country_id');
		$data['gender'] = $this->post_input('gender');
		$data['mobile'] = $this->post_input('mobile');
		$data['address'] = $this->post_input('address');
		$data['military_serve'] = $this->post_input('military_serve');
		$data['club_id'] = $this->post_input('club_id');
		$data['score_win'] = $this->post_input('score_win');
		$data['score_loss'] = $this->post_input('score_loss');
		$data['score_draw'] = $this->post_input('score_draw');
		$data['score_ko'] = $this->post_input('score_ko');
		
		if ($auditItemId) {
			$this->load->model('audit_model');
			
			$aData['status'] = AUDIT_STATUS_PASSED;
			$aData['audit_date'] = now();
			$this->audit_model->update($auditItemId, $aData);
		}

		if (empty($id)) {
			// add new content
			$id = $this->member_model->insert(
					$kind, 
					$data['name'],
					$data['en_name'],
					$data['description'],
					$data['introduction'],
					$data['image'],
					$data['cert_number'],
					$data['idcard'],
					$data['birthday'],
					$data['height'],
					$data['weight'],
					$data['level'],
					$data['education'],
					$data['nickname'],
					$data['country_id'],
					$data['gender'],
					$data['mobile'],
					$data['address'],
					$data['military_serve'],
					$data['club_id'],
					$data['score_win'],
					$data['score_loss'],
					$data['score_draw'],
					$data['score_ko']);
			
			$this->add_log('新增认证', $data);
		} else {
			$this->member_model->update($id, $data);
			
			$this->add_log('编辑认证', array('id'=>$id));
		}
		
		$this->success_redirect('member/lists/' . $kind);
	}
}
