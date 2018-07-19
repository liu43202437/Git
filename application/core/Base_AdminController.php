<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_AdminController extends CI_Controller {

	protected $gets = array();
	protected $posts = array();	

    protected $adminId = null;
    protected $adminName = null;
    protected $data = array();
    protected $pager = array();
    
    protected $view_path = '';

	// admin navigation menu    
    protected $menu = array(
    	'home' => array(
    		'label'	=> '首页',
    	),
    	'content' => array(
    		'label'	=> '内容', 
    		'banner/lists'		=> 'Banner',
    		'banner/lists2'		=> 'Banner2',
    		'content/lists/1'	=> '文章',
    		'content/lists/2'	=> '图集',
    		'content/lists/3'	=> '视频',
    		'video_link/lists'	=> '链接',
    		'content/lists/4'	=> '直播',
    		'baby/lists'		=> '图文',
            'content/lists/8'   => '广告',
    		'content/add'		=> '添加'
    	),
    	'member' => array(
    		'label'	=> '认证', 
    		'club/lists'		=> '零售店',
            'club/saves'        => '零售店编辑',
    		'manager/lists'=> '客户经理',
            'manager/do_edit'=> '客户经理编辑',
            // 'area_manager/lists'=> '市场经理',
            // 'area_manager/do_edit'=> '市场经理编辑',
            // 'bazaar_manager/lists'=> '区域经理',
            // 'bazaar_manager/do_edit'=> '区域经理编辑',
    		'member/add'		=> '添加',
            // 'club/batchCheck'=> '零售店批量审核',
    	),
        'lottery' =>array(
            'label' => '访销',
            'lottery/lists' => '访销经理管理'
        ),
        'ticket' => array(
            'label' => '票券', 
            'ticket/lists'    => '票券管理',
            'ticket/add'    => '添加票券',
            'ticket/do_edit'    => '编辑票券'
        ),
        'redeem' => array(
            'label' => '兑奖',
            'redeem/redeemLists'    => '兑奖记录',
            'redeem/receiptLists'    => '提现记录',
            'redeem/checking'    => '对账管理'
        ),
    	'events' => array(
    		'label'	=> '活动', 
    		'events/lists/1'	=> '赛事',
    		'events/lists/2'	=> '比赛',
    		'events/ticket_orders'	=> '售票',
    		'events/add'		=> '添加'
    	),
    	'audit' => array(
    		'label'	=> '审核', 
    		'feedback/lists'	=> '意见反馈',
    		'comment/lists'		=> '评论',
    		'audit/lists/1'		=> '运动员报名',
    		'audit/lists/2'		=> '裁判报名',
    		'audit/lists/3'		=> '教练报名',
    		'audit/lists/5'		=> '联盟报名',
    		'audit/lists/4'		=> '道馆加盟'
    	),
    	'user' => array(
    		'label'	=> '用户', 
    		'user/lists'		=> '用户列表',
            // 'user/manager_lists'=> '客户经理列表',
    		'user/ranks'		=> '等级'
    	),
    	'admin' => array(
    		'label'	=> '管理员', 
    		'admin/lists'		=> '管理员',
    		'admin/edit'		=> '添加管理员'
    	),
    	'config' => array(
    		'label'	=> '配置',
    		'config/menu_item'	=> '栏目名称',
    		'config/category'	=> '分类管理',
    		'config/version'	=> '版本更新',
    		'splash/lists'		=> '启动闪播',
            'bg_image/lists'      => '刷新背景',
    		'config/audit/1'	=> '报名设置',
    		'config/gift'		=> '礼物管理',
    		'config/basis'		=> '基本设置',
    		'config/layout'		=> '页面内容',
    		'config/share'		=> '分享二维码',
    		'config/ranking'	=> '排行耪'
    	),
        'order' => array(
            'label'     => '订单',
            'order/lists' => '订单列表',
            'order/area'  => '报表导出',
            'order/statistics' => '订单报表统计',
            'order/area'  => '报表导出',
            'order/lottery'  => '彩票统计'
        ),
		'machine' =>array(
			'label'  => '彩票机',
			'machine/lists' => '基本信息',
			'machine/order' => '订单',
		)
    );
    
    protected static $error = array(
        1   => 'xx'
    );
    
    public static $success_message = array(
    	'type' => 'success',
    	'content' => '操作成功！'
    );
    
    public static $error_message = array(
    	'type' => 'error',
    	'content' => '操作失败！'
    );
    
    public static $warn_message = array(
    	'type' => 'warn',
    	'content' => '操作警告！'
    );
	
	function __construct()
    {
        parent::__construct();
        
        // customize requests
        $this->gets = $this->input->get();
        $this->posts = $this->input->post();

        if ($this->auth_admin()) {
	        $this->pager['pageNumber'] = $this->get_post_input('pageNumber', 1);
	        $this->pager['pageSize'] = $this->get_post_input('pageSize', PAGE_SIZE);

			$this->pager['orderProperty'] = $this->get_post_input('orderProperty');
			$this->pager['orderDirection'] = $this->get_post_input('orderDirection', 'asc');

	        $this->data['adminId'] = $this->session->userdata('admin_id');
	        $this->data['adminEmail'] = $this->session->userdata('admin_email');
	        $this->data['adminName'] = $this->session->userdata('admin_name');
		}
    }
    
    public function get_input($key, $default = '')
    {
    	if (empty($this->gets)) {
			return $default;
    	}
    	if (!isset($this->gets[$key])) {
			return $default;
    	}
	    return getValueByDefault($this->gets[$key], $default);
    }
    
    public function post_input($key, $default = '')
    {
	    if (empty($this->posts)) {
			return $default;
    	}
    	if (!isset($this->posts[$key])) {
			return $default;
    	}
	    return getValueByDefault($this->posts[$key], $default);
    }
    
    public function get_post_input($key, $default = '')
    {
		$value = $this->get_input($key, $default);
		return $this->post_input($key, $value);
    }
    
    /* ======================== session authenticate function ==========================*/
    public function auth_admin()
    {
    	if (!$this->session->has_userdata('admin_email') ||
    		!$this->session->has_userdata('admin_id')) {
            $url = base_url() . 'admin/login?redirect_url=' . urlencode(current_url());
            redirect($url);
        }
        
        $this->adminId = $this->session->userdata('admin_id');
        $this->adminName = $this->session->userdata('admin_name');
        
        $this->load->model("admin_model");
        $admin = $this->admin_model->get($this->adminId);
        if ($admin == null) {
            show_errorpage();
            return false;
        }
		if (!$this->auth_role()) {
            echo "<script>alert('您没有权限')</script>";
            echo "<script>history.back();</script>";
			// show_errorpage();
            die;               
		}

        return true;
    }
    
    /* ====================== authenticate the roles of admin =========================*/
    public function auth_role($url = null, $roles = null)
    {
    	if ($roles == null) {
			$roles = $this->session->userdata('admin_roles');
    	}
		if (empty($roles)) {
			return true;
		}
		
		if ($url == null) {
			$url = current_url();
		}
		$url = str_replace(base_url().'admin/', '', $url);
		$pos = strpos('?', $url);
		if ($pos !== false) {
			$url = substr($url, 0, $pos);
		}
		if (startsWith($url, '/')) {
			$url = substr($url, 1);
		}
		// check roles  -  if contains - not permitted  else permitted
		foreach ($roles as $role) {
			if ($role['action'] == $url) {
				return false;
			}
		}
		return true;
    }
    
    /* ============================= Load View Functions ===============================*/
    public function load_view($view, $data = null)
    {
    	if ($data == null) {
			$data = $this->data;
    	}
		$this->load->view($this->view_path . $view, $data);
    }

    /* ======================= Message Set And Assign Functions ========================*/
    public function set_message($message)
    {
		$this->session->set_flashdata('message', $message);
    }
    
    public function assign_message()
    {
    	$message = $this->session->flashdata('message');
		if (!empty($message)) {
			$this->data['message'] = $message;
        }
    }
    
    /* =========================== Pager Assigning Function ============================*/
    public function assign_pager($totalCount, $pageSize = null)
    {
    	if ($pageSize != null) {
			$this->pager['pageSize'] = $pageSize;
    	}
		$pager = array();
		
		$this->pager['total'] = $totalCount;
		$this->pager['totalPages'] = ceil($totalCount / $this->pager['pageSize']);
		
		$this->pager['isFirst'] = ($this->pager['pageNumber'] == 1);
		$this->pager['hasPrevious'] = ($this->pager['pageNumber'] > 1);
		$this->pager['hasNext'] = ($this->pager['pageNumber'] < $this->pager['totalPages']);
		$this->pager['isLast'] = ($this->pager['pageNumber'] == $this->pager['totalPages']);
		
		$this->pager['firstPageNumber'] = 1;
		$this->pager['previousPageNumber'] = $this->pager['pageNumber'] - 1;
		$this->pager['nextPageNumber'] = $this->pager['pageNumber'] + 1;
		$this->pager['lastPageNumber'] = $this->pager['totalPages'];

		$segmentCount = 5;		
		$startSegmentPageNumber = $this->pager['pageNumber'] - floor(($segmentCount - 1) / 2);
		$endSegmentPageNumber = $this->pager['pageNumber'] + ceil(($segmentCount - 1) / 2);
		if ($startSegmentPageNumber < 1) {
			$startSegmentPageNumber = 1;
		}
		if ($endSegmentPageNumber > $this->pager['totalPages']) {
			$endSegmentPageNumber = $this->pager['totalPages'];
		}
		$segment = array();
		for ($i = $startSegmentPageNumber; $i <= $endSegmentPageNumber; $i++) {
			$segment[] = $i;
		}
		$this->pager['segment'] = $segment;
		
		$this->data['pager'] = $this->pager;
    }
    
    /* ======================== Admin operation log function ==========================*/
    public function add_log($operation, $content)
    {
		$this->load->model('adminlog_model');
		if (is_array($content)) {
			$content = json_encode($content);
		}
		$this->adminlog_model->insert($this->adminId, $this->adminName, $operation, $content);
    }
    
    /* ========================= Message Creation Functions ===========================*/
    public static function error_message($content = null)
    {
    	if ($content == null) {
			return self::$error_message;
    	}
    	
		$message['type'] = 'error';
		$message['content'] = $content;
		return $message;
    }
    
    public static function warn_message($content = null)
    {
    	if ($content == null) {
			return self::$warn_message;
    	}
    	
		$message['type'] = 'warn';
		$message['content'] = $content;
		return $message;
    }
    
    public static function success_message($content = null)
    {
    	if ($content == null) {
			return self::$success_message;
    	}
    	
		$message['type'] = 'success';
		$message['content'] = $content;
		return $message;
    }
    
    /* ========================= Redirect ===========================*/
    public function error_redirect($target, $msg = null, $withMessage = true)
    {
    	if ($withMessage) {
			$message = self::error_message($msg);
			$this->set_message($message);
		}

		//if (!startsWith($target, 'admin/')) {
			$target = 'admin/' . $target;
		//}		
		$url = base_url() . $target;
		redirect($url);
    }
    
    public function success_redirect($target, $msg = null, $withMessage = true)
    {
    	if ($withMessage) {
			$message = self::success_message($msg);
			$this->set_message($message);
		}
		
		//if (!startsWith($target, 'admin/')) {
			$target = 'admin/' . $target;
		//}
		$url = base_url() . $target;
		redirect($url);
    }
}
