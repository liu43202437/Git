<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Base_AdminController {

	function __construct()
    {
        parent::__construct();
        
        $this->view_path = 'admin/';
    }

    public function index()
    {
    	$day = day_before(0);
    	$filter['create_date >='] = d2bt($day);
			$filter['create_date <='] = d2et($day);
			
    	$this->load->model('content_model');
    	$this->load->model('link_model');
    	$this->load->model('baby_model');
    	$this->load->model('audit_model');
    	$this->load->model('feedback_model');
    	$this->load->model('comment_model');

    	$filters['kind'] = CONTENT_KIND_ARTICLE;
    	$this->data['article'] = $this->content_model->getCount($filters);
    	
    	$filters['kind'] = CONTENT_KIND_GALLERY;
    	$this->data['gallery'] = $this->content_model->getCount($filters);
    	
    	$filters['kind'] = CONTENT_KIND_VIDEO;
    	$this->data['video'] = $this->content_model->getCount($filters);

    	$this->data['link'] = $this->link_model->getCount();
    	
    	$this->data['baby'] = $this->baby_model->getCount();
    	
    	$filters['kind'] = AUDIT_KIND_PLAYER;
    	$this->data['player'] = $this->audit_model->getCount($filters);
    	
    	$filters['kind'] = AUDIT_KIND_REFEREE;
    	$this->data['referee'] = $this->audit_model->getCount($filters);
    	
    	$filters['kind'] = AUDIT_KIND_CHALLENGE;
    	$this->data['challenge'] = $this->audit_model->getCount($filters);
    	
    	$filters['kind'] = AUDIT_KIND_COACH;
    	$this->data['coach'] = $this->audit_model->getCount($filters);
    	
    	$filters['kind'] = AUDIT_KIND_CLUB;
    	$this->data['club'] = $this->audit_model->getCount($filters);
    	
    	$this->data['feedback'] = $this->feedback_model->getCount();
    	$this->data['req_feedback'] = $this->feedback_model->getCount(array('status' => 0));
    	
    	$this->data['comment'] = $this->comment_model->getCount();

    	$this->assign_message();
		$this->load_view('home');
    }
    
    public function visit_info()
    {
    	$startDate = $this->post_input('start_date', day_before(30));
    	$endDate = $this->post_input('end_date', nowdate());
    	
		$this->load->model('visit_model');
		
		$reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
			$filter['create_date >='] = d2bt($day);
			$filter['create_date <='] = d2et($day);
			$item = $this->visit_model->getCountInfo($filter);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;

        $filters['create_date >='] = d2bt(day_before(1));
		$filters['create_date <='] = d2et(day_before(1));
		$data['yesterday'] = $this->visit_model->getCountInfo($filters);
		
		$filters['create_date >='] = stime_type('week');
		$filters['create_date <='] = etime_type('week');
		$data['week'] = $this->visit_model->getCountInfo($filters);
		
		$filters['create_date >='] = stime_type('month');
		$filters['create_date <='] = etime_type('month');
		$data['month'] = $this->visit_model->getCountInfo($filters);
        
        $data['labels'] = array('PV', 'UV');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
    
    public function new_users_info()
    {
    	$startDate = $this->post_input('start_date', day_before(30));
    	$endDate = $this->post_input('end_date', nowdate());
    	
		$this->load->model('user_model');
		
		$reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
			$filter['create_date >='] = d2bt($day);
			$filter['create_date <='] = d2et($day);
            $item = $this->user_model->getCountInfo($filter);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;

        $filters['create_date >='] = d2bt(day_before(1));
		$filters['create_date <='] = d2et(day_before(1));
		$data['yesterday'] = $this->user_model->getCountInfo($filters);
		
		$filters['create_date >='] = stime_type('week');
		$filters['create_date <='] = etime_type('week');
		$data['week'] = $this->user_model->getCountInfo($filters);
		
		$filters['create_date >='] = stime_type('month');
		$filters['create_date <='] = etime_type('month');
		$data['month'] = $this->user_model->getCountInfo($filters);
        
        $data['labels'] = array('苹果', '安卓', '微信', '总数');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
    
    public function users_info()
    {
    	$startDate = $this->post_input('start_date', day_before(30));
    	$endDate = $this->post_input('end_date', nowdate());
    	
		$this->load->model('user_model');
		
		$reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
			$filter['create_date <='] = d2et($day);
            $item = $this->user_model->getCountInfo($filter);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;

		$filters['create_date <='] = d2et(day_before(1));
		$data['yesterday'] = $this->user_model->getCountInfo($filters);
		
		$filters['create_date <='] = stime_type('week');
		$data['week'] = $this->user_model->getCountInfo($filters);
		
		$filters['create_date <='] = stime_type('month');
		$data['month'] = $this->user_model->getCountInfo($filters);
        
        $data['labels'] = array('苹果', '安卓', '微信', '总数');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
    
    public function new_member_info()
    {
    	$startDate = $this->post_input('start_date', day_before(30));
    	$endDate = $this->post_input('end_date', nowdate());
    	
		$this->load->model('member_model');
		
		$reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
			$filter['create_date >='] = d2bt($day);
			$filter['create_date <='] = d2et($day);
            $item = $this->member_model->getCountInfo($filter);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;

        $filters['create_date >='] = d2bt(day_before(1));
		$filters['create_date <='] = d2et(day_before(1));
		$data['yesterday'] = $this->member_model->getCountInfo($filters);
		
		$filters['create_date >='] = stime_type('week');
		$filters['create_date <='] = etime_type('week');
		$data['week'] = $this->member_model->getCountInfo($filters);
		
		$filters['create_date >='] = stime_type('month');
		$filters['create_date <='] = etime_type('month');
		$data['month'] = $this->member_model->getCountInfo($filters);
        
        $data['labels'] = array('运动员', '裁判', '教练', '总数');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
    
    public function member_info()
    {
    	$startDate = $this->post_input('start_date', day_before(30));
    	$endDate = $this->post_input('end_date', nowdate());
    	
		$this->load->model('member_model');
		
		$reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
			$filter['create_date <='] = d2et($day);
            $item = $this->member_model->getCountInfo($filter);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;

		$filters['create_date <='] = d2et(day_before(1));
		$data['yesterday'] = $this->member_model->getCountInfo($filters);
		
		$filters['create_date <='] = stime_type('week');
		$data['week'] = $this->member_model->getCountInfo($filters);
		
		$filters['create_date <='] = stime_type('month');
		$data['month'] = $this->member_model->getCountInfo($filters);
        
        $data['labels'] = array('运动员', '裁判', '教练', '总数');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
    
    public function money_info()
    {
    	$startDate = $this->post_input('start_date', day_before(30));
    	$endDate = $this->post_input('end_date', nowdate());
    	
		$this->load->model('order_model');
		$this->load->model('user_model');
		
		$reportData = array();
        $diff_count = floor((strtotime($endDate) - strtotime($startDate)) / (24 * 60 * 60));
        for($i = $diff_count; $i >= 0; $i--) {
            $day = day_before($i, $endDate);
            $filter['create_date >='] = d2bt($day);
			$filter['create_date <='] = d2et($day);
            $item = $this->order_model->getPointInfo($filter);
            $item['label'] = date("m", strtotime($day)) . "." . date("d", strtotime($day));
            $reportData[] = $item;
        }
        $data['day_report'] = $reportData;

		$filters['create_date >='] = d2bt(day_before(1));
		$filters['create_date <='] = d2et(day_before(1));
		$data['yesterday'] = $this->order_model->getPointInfo($filters);
		
		$filters['create_date >='] = stime_type('week');
		$filters['create_date <='] = etime_type('week');
		$data['week'] = $this->order_model->getPointInfo($filters);
		
		$filters['create_date >='] = stime_type('month');
		$filters['create_date <='] = etime_type('month');
		$data['month'] = $this->order_model->getPointInfo($filters);
        
        $data['labels'] = array('充值', '消耗');
        $data['message'] = parent::success_message();
        echo json_capsule($data);
    }
}
