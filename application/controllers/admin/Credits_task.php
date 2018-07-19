<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credits_task extends CI_Controller
{
    protected $file_path = '';

    function __construct()
    {
        parent::__construct();


        $this->file_path = get_instance()->config->config['log_path_file'];
        $this->load->model('porder_model');
    }
    public function creditsTask(){
        $command = $this->input->get("command");
        if($command != 'credits'){
            return;
        }
        $this->load->model('user_model');
        $this->load->model('consumer_model');



        $this->load->model("user_credits_model");
        $start = 1518105601;
        $end = 1518278402;
        $all_orders = $this->user_credits_model->get_all_task_orders($start,$end,2);

        if (!empty($all_orders)){
            foreach ($all_orders as $one_orders){
                 $club_info = $this->consumer_model->getInfoByConsumerUserId($one_orders['user_id']);
                $managerinfo = $this->consumer_model->getInfoByManagerid($club_info['manager_id']);


                if(!empty($managerinfo)){
                    $manager_user_id = $managerinfo['consumer_userid'];
                    $area_manager_user_id = $managerinfo['area_user_id'];
                    $bazaar_user_id = $managerinfo['bazaar_user_id'];

                }else{
                    $manager_user_id = '';
                    $area_manager_user_id = '';
                    $bazaar_user_id = '';
                }
                $total_money = 3000;
                $address = $one_orders['address'];
                $trade_no = $one_orders['trade_no'];
                $create_date = $one_orders['create_date'];

                /*市场经理
                 * */
                $area_manager_user_id = $area_manager_user_id;
                $area_manager_credits = round($total_money*1/300);
                $area_manager_data['trade_no'] = $trade_no;
                $area_manager_data['user_id'] = $area_manager_user_id;
                $area_manager_data['create_date'] = $create_date;
                $area_manager_data['credits'] =  $area_manager_credits;
                $area_manager_data['type'] = 4;
                $area_manager_data['status'] = 1;
                $area_manager_data['add_time'] = time();
                # $area_manager_data['address'] = $address;
                $area_manager_data['name'] = $club_info['manager_name'];

                /*区域经理
                 * */
                $bazaar_user_id = $bazaar_user_id;
                $bazaar_credits = round($total_money*1/300);
                $bazaar_data['trade_no'] = $trade_no;
                $bazaar_data['user_id'] = $bazaar_user_id;
                $bazaar_data['create_date'] = $create_date;
                $bazaar_data['credits'] =  $bazaar_credits;
                $bazaar_data['type'] = 5;
                $bazaar_data['status'] = 1;
                $bazaar_data['add_time'] = time();
                $bazaar_data['name'] = $managerinfo['area_name'];
                $fp =  $this->file_path."auto_fixed_credits_".date("Y-m-d",time()).".log";
                /* $fp =  "D:/add_credits_".date("Y-m-d",time()).".log";*/
                if($area_manager_user_id != ''){
                    $area_manager_re = $this->user_model->updateCredits($area_manager_user_id,$area_manager_credits);
                    if($area_manager_re){
                        $contents = '{"area_manager:"'.$area_manager_user_id.',"credits:"'.$area_manager_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                        $this->user_credits_model->insertData($area_manager_data);
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }else{
                        $contents = '{"area_manager:"'.$area_manager_user_id.',"credits:"'.$area_manager_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                        $area_manager_data['status'] = 0;
                        $this->user_credits_model->insertData($area_manager_data);
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }
                }

                if($bazaar_user_id != ''){
                    $bazaar_manager_re = $this->user_model->updateCredits($bazaar_user_id,$bazaar_credits);
                    if($bazaar_manager_re){
                        $contents = '{"bazaar_manager:"'.$bazaar_user_id.',"credits:"'.$bazaar_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":1}'.PHP_EOL;
                        $this->user_credits_model->insertData($bazaar_data);
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }else{
                        $contents = '{"bazaar_manager:"'.$bazaar_user_id.',"credits:"'.$bazaar_credits.',"date_time":"'.date("Y-m-d H:i:s",time()).',"status":0}'.PHP_EOL;
                        $bazaar_data['status'] = 0;
                        $this->user_credits_model->insertData($bazaar_data);
                        file_put_contents($fp,$contents,FILE_APPEND|LOCK_EX);
                    }
                }

            }
        }





    }

}