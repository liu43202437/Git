<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Autodm extends CI_Controller {
    protected $file_path = '';
    function __construct()
    {
        parent::__construct();

       
        $this->file_path = get_instance()->config->config['log_path_file'];
        $this->load->model('ticket_order_model');
    }
    public function autoDetermineOrder(){
        $command = $this->input->get("command");
        if($command != 'determine'){
            return;
        }
        $province_arr =[7,14];
        foreach ($province_arr as $province_val) {


            $order_rs = $this->ticket_order_model->get_all_no_determine_order(1, 1,$province_val);
            $this->load->model('user_model');
            if (!empty($order_rs)) {
                foreach ($order_rs as $value) {
                    $where = array(
                        'id' => $value['id'],
                        'order_status' => 1
                    );

                    $data['order_status'] = 2;
                    $data['update_date'] = date('Y-m-d H:i:s');

                    $result = $this->ticket_order_model->get_order_update($data, $where,$province_val);
                    $userId = $value['user_id'];

                    //find all about order of people
                    $this->load->model("club_model");
                    $this->load->model('consumer_model');
                    $club_info = $this->club_model->getInfoByUserId($userId);
                    $managerinfo = $this->consumer_model->getInfoByManagerid($club_info['manager_id']);

                    if (!empty($managerinfo)) {
                        $manager_user_id = $managerinfo['consumer_userid'];
                        $area_manager_user_id = $managerinfo['area_user_id'];
                        $bazaar_user_id = $managerinfo['bazaar_user_id'];

                    } else {
                        $manager_user_id = '';
                        $area_manager_user_id = '';
                        $bazaar_user_id = '';
                    }

                  $total_money =  round($value['total_money']);;
                    $address = $value['area'] . $value['city'] . $value['address'];
                    $trade_no = $value['trade_no'];
                    $create_date = $value['create_date'];
                    /*店铺积分记录
                     * */
                    $club_data['trade_no'] = $trade_no;
                    $club_data['user_id'] = $userId;
                    $club_data['create_date'] = $create_date;
                    $club_data['credits'] = round($total_money * 1 / 100);
                    $club_data['type'] = 1;
                    $club_data['status'] = 1;
                    $club_data['add_time'] = time();
                    $club_user_credits = round($total_money * 1 / 100);

                    /*客户经理
                     * */

                    $manager_user_id = $manager_user_id;
                    $manager_credits = round($total_money * 1 / 200);
                    $manager_data['trade_no'] = $trade_no;
                    $manager_data['user_id'] = $manager_user_id;
                    $manager_data['create_date'] = $create_date;
                    $manager_data['credits'] = $manager_credits;
                    $manager_data['type'] = 2;
                    $manager_data['status'] = 1;
                    $manager_data['add_time'] = time();
                    $manager_data['address'] = $address;
                    $manager_data['name'] = $club_info['name'];

                    /*市场经理
                     * */
                    $area_manager_user_id = $area_manager_user_id;
                    $area_manager_credits = round($total_money * 1 / 300);
                    $area_manager_data['trade_no'] = $trade_no;
                    $area_manager_data['user_id'] = $area_manager_user_id;
                    $area_manager_data['create_date'] = $create_date;
                    $area_manager_data['credits'] = $area_manager_credits;
                    $area_manager_data['type'] = 4;
                    $area_manager_data['status'] = 1;
                    $area_manager_data['add_time'] = time();
                    # $area_manager_data['address'] = $address;
                    $area_manager_data['name'] = $club_info['manager_name'];

                    /*区域经理
                     * */
                    $bazaar_user_id = $bazaar_user_id;
                    $bazaar_credits = round($total_money * 1 / 300);
                    $bazaar_data['trade_no'] = $trade_no;
                    $bazaar_data['user_id'] = $bazaar_user_id;
                    $bazaar_data['create_date'] = $create_date;
                    $bazaar_data['credits'] = $bazaar_credits;
                    $bazaar_data['type'] = 5;
                    $bazaar_data['status'] = 1;
                    $bazaar_data['add_time'] = time();
                    $bazaar_data['name'] = $managerinfo['area_name'];

                    //判断是否为首单 是更新club order_status为2
                    if ($club_info['order_status'] == 1) {
                        $club_flag = $this->club_model->updateData(array('order_status' => 2), array('user_id' => $userId));
                        $fps = $this->file_path . "auto_update_club_" . date("Y-m-d", time()) . ".log";
                        if (!$club_flag) {
                            $contents = '{"club_userid:"' . $userId . ',"order_status:2","date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":0}' . PHP_EOL;
                            file_put_contents($fps, $contents, FILE_APPEND | LOCK_EX);
                        } else {
                            $contents = '{"club_userid:"' . $userId . ',"order_status:2","date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":1}' . PHP_EOL;
                            file_put_contents($fps, $contents, FILE_APPEND | LOCK_EX);
                        }
                    } elseif ($club_info['order_status'] == 3) {
                        $club_flag = $this->club_model->updateData(array('order_status' => 4), array('user_id' => $userId));
                        $fps = $this->file_path . "auto_update_club_" . date("Y-m-d", time()) . ".log";
                        if (!$club_flag) {
                            $contents = '{"club_userid:"' . $userId . ',"order_status:4","date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":0}' . PHP_EOL;
                            file_put_contents($fps, $contents, FILE_APPEND | LOCK_EX);
                        } else {
                            $contents = '{"club_userid:"' . $userId . ',"order_status:4","date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":1}' . PHP_EOL;
                            file_put_contents($fps, $contents, FILE_APPEND | LOCK_EX);
                        }
                    }

                    if ($result <= 0) {

                    } else {
                        $user_re = $this->user_model->updateCredits($userId, $club_user_credits);
                        $fp = $this->file_path . "auto_add_credits_" . date("Y-m-d", time()) . ".log";
                        /* $fp =  "D:/add_credits_".date("Y-m-d",time()).".log";*/
                        $this->load->model("user_credits_model");
                        if ($user_re) {
                            $contents = '{"club_userid:"' . $userId . ',"credits:"' . $club_user_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":1}' . PHP_EOL;

                            file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            $this->user_credits_model->insertData($club_data);
                        } else {
                            $contents = '{"club_userid:"' . $userId . ',"credits:"' . $club_user_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":0}' . PHP_EOL;
                            $club_data['status'] = 0;
                            file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            $this->user_credits_model->insertData($club_data);

                        }

                        if ($manager_user_id != null) {
                            $manager_re = $this->user_model->updateCredits($manager_user_id, $manager_credits);
                            if ($manager_re) {
                                $contents = '{"manager:"' . $manager_user_id . ',"credits:"' . $manager_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":1}' . PHP_EOL;
                                $this->user_credits_model->insertData($manager_data);
                                file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            } else {
                                $contents = '{"manager:"' . $manager_user_id . ',"credits:"' . $manager_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":0}' . PHP_EOL;
                                $manager_data['status'] = 0;
                                $this->user_credits_model->insertData($manager_data);
                                file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            }
                        }


                        if ($area_manager_user_id != '') {
                            $area_manager_re = $this->user_model->updateCredits($area_manager_user_id, $area_manager_credits);
                            if ($area_manager_re) {
                                $contents = '{"area_manager:"' . $area_manager_user_id . ',"credits:"' . $area_manager_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":1}' . PHP_EOL;
                                $this->user_credits_model->insertData($area_manager_data);
                                file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            } else {
                                $contents = '{"area_manager:"' . $area_manager_user_id . ',"credits:"' . $area_manager_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":0}' . PHP_EOL;
                                $area_manager_data['status'] = 0;
                                $this->user_credits_model->insertData($area_manager_data);
                                file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            }
                        }

                        if ($bazaar_user_id != '') {
                            $bazaar_manager_re = $this->user_model->updateCredits($bazaar_user_id, $bazaar_credits);
                            if ($bazaar_manager_re) {
                                $contents = '{"bazaar_manager:"' . $bazaar_user_id . ',"credits:"' . $bazaar_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":1}' . PHP_EOL;
                                $this->user_credits_model->insertData($bazaar_data);
                                file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            } else {
                                $contents = '{"bazaar_manager:"' . $bazaar_user_id . ',"credits:"' . $bazaar_credits . ',"date_time":"' . date("Y-m-d H:i:s", time()) . ',"status":0}' . PHP_EOL;
                                $bazaar_data['status'] = 0;
                                $this->user_credits_model->insertData($bazaar_data);
                                file_put_contents($fp, $contents, FILE_APPEND | LOCK_EX);
                            }
                        }

                    }


                }

            }

        }
       
    }
    

}

