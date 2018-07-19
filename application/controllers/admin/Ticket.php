<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "application/third_party/CCPRestSmsSDK.php";
class Ticket extends Base_AdminController
{

    function __construct()
    {
        parent::__construct();

        $this->view_path = 'admin/ticket/';
        $this->load->model('ticket2_model');
    }

    public function lists()
    {
        $this->load->model('audit_model');
        $this->load->model('auditconfig_model');
        $this->load->model('area_model');
        $this->load->model('user_model');

        $province_id = $this->get_input('province_id');
        if(empty($province_id)){
            $province_id = 7;
        }

        $filters = array();
        $orders = array();
        $this->pager['pageSize'] = 20;

        if(!empty($province_id) && $province_id != 1 ){
            empty($province_id) ? $filters['province_id'] = 7 : $filters['province_id'] = $province_id;
        }

        if (!empty($this->pager['orderProperty'])) {
            $orders[$this->pager['orderProperty']] = $this->pager['orderDirection'];
        } else {
            $orders['create_date'] = 'DESC';
        }

        $totalCount = $this->ticket2_model->getCount($filters);
        $rsltList = $this->ticket2_model->getList($filters, $orders, $this->pager['pageNumber'], $this->pager['pageSize']);
        // var_dump($rsltList);
        //获取省份列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1 || $value['parent_id'] == 0){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        //获取省份列表
        foreach ($rsltList as $key => &$value) {
            $value['province'] = $provinces_temp[$value['province_id']];
        }
        
        $this->data['provinces'] = $provinces_temp;
        $this->data['province_id'] = $province_id;
        $this->data['itemList'] = $rsltList;
        $this->assign_pager($totalCount);
        $this->assign_message();
        $this->load_view('list');
    }
    public function edit()
    {
        $this->load->model('area_model');

        $id = $this->get_input('id');

        //获取省份列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if( ($value['parent_id'] == 1 || $value['parent_id'] == 0) &&  $value['parent_id'] < 100){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        //获取票券详情
        $info = $this->ticket2_model->fetchOne(['id'=>$id]);
        $info['province'] = $provinces_temp[$info['province_id']];
        
        $this->data['itemInfo'] = $info;
        $this->data['provinces'] = $provinces_temp;
        $this->data['province_id'] = $info['province_id'];

        $this->load_view('edit');
    }
    public function do_edit(){
        $id = $this->post_input('id');
        $title = $this->post_input('title');
        $count_price = $this->post_input('count_price');
        $price = $this->post_input('price');
        $size = $this->post_input('size');
        $land = $this->post_input('land');
        $img = $this->post_input('image');
        $inventory = $this->post_input('inventory');
        $status = $this->post_input('status');
        $province_id = $this->post_input('province_id');
        $create_date = $this->post_input('create_date');
        $description = $this->post_input('description');

        $updateData['title'] = $title;
        $updateData['count_price'] = $count_price;
        $updateData['price'] = $price;
        $updateData['size'] = $size;
        $updateData['land'] = $land;
        $updateData['image'] = $img;
        $updateData['inventory'] = $inventory;
        $updateData['status'] = $status;
        $updateData['province_id'] = $province_id;
        $updateData['create_date'] = $create_date;
        $updateData['description'] = str_replace('&nbsp;', ' ', strip_tags($description));

        $flag = $this->ticket2_model->updateData($updateData,['id'=>$id]);
        $this->add_log('编辑票券', $id);
        $this->success_redirect('./ticket/lists');

    }
    public function add(){
        $this->load->model('area_model');

        //获取省份列表
        $provinces = $this->area_model->fetchAll();
        $provinces_temp = [];
        foreach ($provinces as $key => $value) {
            if($value['parent_id'] == 1 && $value['parent_id'] < 100){
                $provinces_temp[$value['id']] = $value['name'];
            }
        }
        $this->data['provinces'] = $provinces_temp;

        $this->load_view('add');
    }
    public function do_add(){
        $this->load->model('area_model');

        $title = $this->post_input('title');
        $count_price = $this->post_input('count_price');
        $price = $this->post_input('price');
        $size = $this->post_input('size');
        $land = $this->post_input('land');
        $img = $this->post_input('image');
        $province_id = $this->post_input('province_id');
        $create_date = $this->post_input('create_date');
        $description = $this->post_input('description');

        $province = $this->area_model->getProvince($province_id);
        
        $inserdata['title'] = $title;
        $inserdata['count_price'] = $count_price;
        $inserdata['price'] = $price;
        $inserdata['size'] = $size;
        $inserdata['land'] = $land;
        $inserdata['image'] = $img;
        $inserdata['province_id'] = $province_id;
        $inserdata['create_date'] = $create_date;
        $inserdata['description'] = str_replace('&nbsp;', ' ', strip_tags($description));
        $inserdata['province'] = $province;
        $flag = $this->ticket2_model->insertData($inserdata);
        if($flag){
            $this->add_log('添加票券', $flag);
            $this->success_redirect('./ticket/lists');
        }
    } 
}
