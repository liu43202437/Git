<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends Base_MobileController {

	public function contents($id, $share="web")
	{
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('content_model');
		$item = $this->content_model->get($id);
		if (empty($item)) {
			show_errorpage();
		}
		
		$data['item'] = $item;
		$data['mobile'] = 0;

		if (!empty($share) && $share == "mobile") {
			$data['mobile'] = 1;
		}
		
		$view = 'article';
		if ($item['kind'] == CONTENT_KIND_ARTICLE) {
			$view = 'article';
		} else if ($item['kind'] == CONTENT_KIND_VIDEO) {
			$view = 'video';
		} else if ($item['kind'] == CONTENT_KIND_GALLERY) {
			$view = 'gallery';
		} else {
			redirect($item['link']);
		}
		
		$this->load->view('portal/'.$view, $data);
	}
	
	public function links($id, $share="web")
	{
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('link_model');
		$item = $this->link_model->get($id);
		if (empty($item)) {
			show_errorpage();
		}
		$item['contents'] = $this->link_model->getContentList($id);
		
		$data['item'] = $item;
		$data['mobile'] = 0;

		if (!empty($share) && $share == "mobile") {
			$data['mobile'] = 1;
		}
		$this->load->view('portal/link', $data);
	}
	
	public function events($id, $share="web")
	{
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('event_model');
		$this->load->model('member_model');
		
		$item = $this->event_model->get($id);
		if (empty($item)) {
			show_errorpage();
		}
		
		// get counter part table
		$counterparts = $this->event_model->getCounterpartList($item['id']);
		foreach ($counterparts as $key=>$citem) {
			$aPlayer = $this->member_model->get($citem['a_player_id']);
			$bPlayer = $this->member_model->get($citem['b_player_id']);
			$counterparts[$key]['a_player_name'] = $aPlayer['name'];
			$counterparts[$key]['a_player_enname'] = $aPlayer['en_name'];
			$counterparts[$key]['a_player_image'] = getFullUrl($aPlayer['image']);
			$counterparts[$key]['b_player_name'] = $bPlayer['name'];
			$counterparts[$key]['b_player_enname'] = $bPlayer['en_name'];
			$counterparts[$key]['b_player_image'] = getFullUrl($bPlayer['image']);
		}		
		$item['counterparts'] = $counterparts;
		if ($item['has_ticket']) {
			$item['ticket_prices'] = $this->event_model->getTicketPriceList($item['id']);
		}
		
		$data['item'] = $item;
		$data['mobile'] = 0;

		if (!empty($share) && $share == "mobile") {
			$data['mobile'] = 1;
		}
		$this->load->view('portal/event', $data);
	}
	
	public function members($id, $share="web")
	{
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('member_model');
		$this->load->model('area_model');
		
		$item = $this->member_model->get($id);
		if (empty($item)) {
			show_errorpage();
		}
		
		$area = $this->area_model->getAreaInfo($item['country_id']);
		if (empty($area)) {
			$item['country'] = '';
		} else {
			$item['country'] = $area[AREA_TYPE_COUNTRY]['name'];
		}
		
		$data['item'] = $item;
		$data['mobile'] = 0;

		if (!empty($share) && $share == "mobile") {
			$data['mobile'] = 1;
		}
		$this->load->view('portal/member', $data);
	}
	
	public function clubs($id, $share="web")
	{
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('club_model');
		$item = $this->club_model->get($id);
		if (empty($item)) {
			show_errorpage();
		}
		
		$data['item'] = $item;
		$data['mobile'] = 0;

		if (!empty($share) && $share == "mobile") {
			$data['mobile'] = 1;
		}
		$this->load->view('portal/club', $data);
	}
	
	public function organs($id, $share="web")
	{
		if (empty($id)) {
			show_errorpage();
		}
		
		$this->load->model('organization_model');
		$item = $this->organization_model->get($id);
		if (empty($item)) {
			show_errorpage();
		}
		
		$data['item'] = $item;
		$data['mobile'] = 0;

		if (!empty($share) && $share == "mobile") {
			$data['mobile'] = 1;
		}
		$this->load->view('portal/organization', $data);
	}
	
	public function about()
	{
		$this->load->model('config_model');
		$about = $this->config_model->getValue('about_content');
		$data['content'] = $about;
		$this->load->view('about', $data);
    }
 public function verify()
    {
        $type = $this->post_input('type');
        $num = $this->post_input('num');

        $data['item'] = '';
        if(isset($type) && !empty($num)) {
            $this->load->model('member_model');
            $this->load->model('area_model');

            $curVersion = $this->post_input('version');
            $this->load->model('appversion_model');

            if($type=='cert_number'){
                $item = $this->member_model->getTopRow(array('cert_number' => $num), array('create_date' => 'DESC'));
            } elseif($type=='idcard') {
                $item = $this->member_model->getTopRow(array('idcard' => $num), array('create_date' => 'DESC'));
            } else{
                $item = null;
            }

            if (!empty($item)) {
                $area = $this->area_model->getAreaInfo($item['country_id']);
                if (empty($area)) {
                    $item['country'] = '';
                } else {
                    $item['country'] = $area[AREA_TYPE_COUNTRY]['name'];
                }
            }
            $data['item'] = $item;
        }

        $data['mobile'] = 0;
        if (!empty($share) && $share == "mobile") {
            $data['mobile'] = 1;
        }

        //var_dump($type,$num,$data['item']);

        $this->load->view('verify', $data);
    }
}
