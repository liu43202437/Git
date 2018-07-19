<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Base_MobileController {
	
	function __construct()
    {
        parent::__construct();
    }
    
    public function wxpay_notify()
    {
    	require_once APPPATH . "third_party/WxPayPubHelper/WxPayPubHelper.php";

		//使用通用通知接口
		$notify = new Notify_pub();

		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);

		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code", "FAIL");//返回状态码
			$notify->setReturnParameter("return_msg", "签名失败");//返回信息
		} else {
			$notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;

		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

		$this->load->model('order_model');
		$this->load->model('config_model');
		$this->load->model('user_model');
		$this->load->model('userrank_model');
		$this->load->model('config_model');
		$this->load->model('event_model');
		
		//以log文件形式记录回调信息
		log_info("[weixin notify]:\n" . $xml . "\n");
		
		if($notify->checkSign() == TRUE) {

			if ($notify->data["return_code"] == "FAIL") {
				
				//此处应该更新一下订单状态，商户自行增删操作
				//log_info("【通信出错】:\n" . $xml . "\n");
				
			} elseif ($notify->data["result_code"] == "FAIL") {

				//此处应该更新一下订单状态，商户自行增删操作
				//log_info("【业务出错】:\n" . $xml . "\n");
				
			} else {
				//此处应该更新一下订单状态，商户自行增删操作
				//log_info("【支付成功】:\n" . $xml . "\n");

				$out_trade_no = $notify->data['out_trade_no'];
				$trade_no = $notify->data['transaction_id'];
				
				$order = $this->order_model->getBySn($out_trade_no);
				
				if($order['order_status'] == ORDER_STATUS_PROCESSING && $order['pay_status'] == PAY_STATUS_UNPAID) {
					$data['pay_status'] = PAY_STATUS_PAID;
					$data['order_status'] = ORDER_STATUS_SUCCEED;
					if ($order['kind'] == ORDER_KIND_TICKET/* && $order['shipping_type'] == 1*/) {
						$data['order_status'] = ORDER_STATUS_PROCESSING;
						$data['shipping_status'] = SHIP_STATUS_UNSHIPPED;
						
						$ticket = $this->event_model->getTicketPrice($order['ticket_id']);
						if (!empty($ticket)) {
							$ticketCount = intval($ticket['count']) - intval($order['item_count']);
							$ticketCount = ($ticketCount >= 0) ? $ticketCount : 0; 
							$this->event_model->updateTicketPrice($order['ticket_id'], array('count' => $ticketCount));
						}
					} else {
						$data['shipping_status'] = SHIP_STATUS_SHIPPED;
					}
					
					//$data['pay_time'] = time();
				    $this->order_model->update($order['id'], $data);

				    $user = $this->user_model->get($order['user_id']);
				    
				    $expPerMoney = $this->config_model->getExpPerMoney();
				    $expPerPoint = $this->config_model->getExpPerPoint();
				    
				    $data = array();
				    $data['exp'] = intval($user['exp']) + intval($order['total_money'] * 100) * $expPerMoney + intval($order['pay_point']) * $expPerPoint;
				    $data['point'] = intval($user['point']) - intval($order['pay_point']) + intval($order['gain_point']);
				    
				    $rank = $this->userrank_model->getByExp($data['exp']);
				    if (!empty($rank)) {
						if (intval($rank['rank']) > intval($user['rank'])) {
							$data['rank'] = $rank['rank'];
						}
				    }
				    $this->user_model->update($user['id'], $data);
				}
			}
		}
    }
    
    public function consume_point()
    {
		require_once (APPPATH . "third_party/yunjifen/api.php");
		$appkey = $this->config->item('yunjifen_appkey');
		$secret = $this->config->item('yunjifen_secret');
		
		log_info(json_encode($this->gets));
		
		if (!parseCreditConsume($appkey, $secret, $this->gets)) {
			die(json_encode(array("status" => "fail", "errorMessage" => "sign verify failed", "bizId" => "", "credits" => "")));
		}
		
		$this->load->model('user_model');
		$this->load->model('order_model');
		
		$user = $this->user_model->get($this->get_input('uid'));
		if (empty($user)) {
			die(json_encode(array("status" => "fail", "errorMessage" => "user doesn't exist", "bizId" => "", "credits" => "")));
		}
		
		$payPoint = $this->get_input('credits', 0);
		if ($user['point'] < $payPoint) {
			die(json_encode(array("status" => "fail", "errorMessage" => "not enough point!", "bizId" => "", "credits" => "")));
		}
		
		$order = array();
		$order['user_id'] = $user['id'];
		$order['kind'] = ORDER_KIND_YUNJIFEN;
		$order['yunjifen_sn'] = $this->get_input('orderNum');
		$order['item_id'] = 0;
		$order['item_count'] = 1;
		$order['item_money'] = $this->get_input('actualPrice', 0) / 100;
		$order['total_money'] = $this->get_input('needPrice', 0) / 100;
		$order['pay_point'] = $payPoint;
		$order['gain_point'] = 0;
		$order['description'] = '云积分 - ' . $this->get_input('description');
		$order['pay_status'] = PAY_STATUS_PAID;
		$order['shipping_status'] = SHIP_STATUS_SHIPPED;
		$order['order_status'] = ORDER_STATUS_SUCCEED;

		$orderSn = $this->order_model->insert($order);
		if ($orderSn == false) {
			die(json_encode(array("status" => "fail", "errorMessage" => "order creation failed", "bizId" => "", "credits" => "")));
		}
		

		$userPoint = $user['point'] - $payPoint;
		$this->user_model->update($user['id'], array('point' => $userPoint));


		echo json_encode(array("status" => "ok", "bizId" => $orderSn, "credits" => $user['point'] - $payPoint));
    }
    
    public function yjf_notify()
    {
    	require_once (APPPATH . "third_party/yunjifen/api.php");
		$appkey = $this->config->item('yunjifen_appkey');
		$secret = $this->config->item('yunjifen_secret');
		
		log_info(json_encode($this->gets));
		
		if (!parseCreditNotify($appkey, $secret, $this->gets)) {
			die("fail");	
		}
		
		$this->load->model('user_model');
		$this->load->model('order_model');
		
		$order = $this->order_model->getByYjfSn($this->get_input('orderNum'));
		if (empty($order)) {
			die("fail");
		}
		/*if ($order['order_status'] != ORDER_STATUS_PROCESSING) {
			die("fail");
		}*/
		$user = $this->user_model->get($order['user_id']);
		if (empty($user)) {
			die("fail");
		}
		
		$gainPoint = $this->get_input('bonusCredits', 0);
		if ($this->get_input('success')) {			
			//$userPoint = $user['point'] + $gainPoint - $order['pay_point'];
			$userPoint = $user['point'] + $gainPoint;
			if ($userPoint < 0) {
				die("fail");
			}
			
			//$data['pay_status'] = PAY_STATUS_PAID;
			//$data['order_status'] = ORDER_STATUS_SUCCEED;
			$data =  null;
			if ($gainPoint) {
				$data['gain_point'] = $gainPoint;
			}
			$this->order_model->update($order['id'], $data);			
			$this->user_model->update($user['id'], array('point' => $userPoint));
		} else {
			$data['order_status'] = ORDER_STATUS_FAILED;
			$this->order_model->update($order['id'], $data);
			
			$userPoint = $user['point'] - $order['pay_point'];
			$this->user_model->update($user['id'], array('point' => $userPoint));
		}

		echo "ok";
    }
}
