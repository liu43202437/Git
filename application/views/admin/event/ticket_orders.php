<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>票购买订单</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<style type="text/css">
td.operation {
	width: 120px !important;
}
td.operation a {
	font-size: 12px !important;
}
tr.row-detail td {
	color: #333;
	line-height: 30px !important;
	font-weight: 600;
}
tr.row-detail td label {
	color: #888;
	font-weight: normal;
}
tr.row-detail input {
	height: 30px !important;
	line-height: 30px !important;
	font-weight: normal;
}
tr.row-detail button {
	height: 30px !important;
	line-height: 30px !important;
	padding: 0 10px !important;
	font-size: 12px;
}
</style>

<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	$(".btnViewDetail").click(function() {
		var $tr = $(this).parents("tr");
		if ($tr.next("tr").hasClass("row-detail")) {
			$tr.next("tr").toggle();
			$(this).find("i").toggleClass("fa-angle-double-down").toggleClass("fa-angle-double-up");
		}
	});
	
	$(".btnDeliver").click(function() {
		var $tr = $(this).parents("tr.row-detail");
		var $input = $(this).siblings("input.txtDeliverSn");
		var deliverSn = $input.val();
		var orderId = $input.data("id");
		if ($.trim(deliverSn).length == 0) {
			$.message("warn", "请填写运单号！");
			return false;
		}
		$(this).prop("disabled", true);
		
		$.ajax({
			url: 'do_deliver',
			type: 'post',
			data: {'order_id': orderId, 'deliver_sn': deliverSn},
			dataType: 'json',
			cache: false,
			success: function(message) {
				$.message(message);
				if (message.type == "success") {
					setTimeout(function() {
						location.reload(true);
					}, 1500);
				}
			},
			fail: function() {
				$.message('error', '网路错误！');
			}
		});
		return false;
	});
	
	$(".btnComplete").click(function() {
		var $tr = $(this).parents("tr.row-detail");
		var $input = $(this).siblings("input.txtTicketTakeCode");
		var orderId = $input.data("id");
		$(this).prop("disabled", true);
		
		$.ajax({
			url: 'complete_order',
			type: 'post',
			data: {'order_id': orderId},
			dataType: 'json',
			cache: false,
			success: function(message) {
				$.message(message);
				if (message.type == "success") {
					setTimeout(function() {
						location.reload(true);
					}, 1500);
				}
			},
			fail: function() {
				$.message('error', '网路错误！');
			}
		});
		return false;
	});
	
	$(".btnCancel").click(function() {
		var $tr = $(this).parents("tr.row-detail");
		var $input = $(this).siblings("input.txtDeliverSn");
		var orderId = $input.data("id");
		
		$.dialog({
			type: "warn",
			content: "您确定取消订单？",
			ok: message("admin.dialog.ok"),
			cancel: message("admin.dialog.cancel"),
			onOk: function() {
				$(this).prop("disabled", true);
				
				$.ajax({
					url: 'cancel_order',
					type: 'post',
					data: {'order_id': orderId},
					dataType: "json",
					cache: false,
					success: function(message) {
						$.message(message);
						if (message.type == "success") {
							setTimeout(function() {
								location.reload(true);
							}, 1500);
						}
					}
				});
			}
		});

		return false;
	});
	
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">票购买订单</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="ticket_orders" method="get">
				<input type="hidden" name="event_id" value="<?= $eventId ?>">
				<div class="filter-bar">
					<div>
						<select class="form-control s-lg" name="order_status">
							<option value="">状态</option>
							<option value="<?= ORDER_STATUS_SUCCEED ?>" <?php if ($orderStatus === ORDER_STATUS_SUCCEED): ?>selected="selected"<?php endif; ?>><?= getOrderStatus(ORDER_STATUS_SUCCEED) ?></option>
							<option value="<?= ORDER_STATUS_FAILED ?>" <?php if ($orderStatus === ORDER_STATUS_FAILED): ?>selected="selected"<?php endif; ?>><?= getOrderStatus(ORDER_STATUS_FAILED) ?></option>
							<option value="<?= ORDER_STATUS_PROCESSING ?>" <?php if ($orderStatus === ORDER_STATUS_PROCESSING): ?>selected="selected"<?php endif; ?>><?= getOrderStatus(ORDER_STATUS_PROCESSING) ?></option>
						</select>
						<div class="form-group m-l-sm">
							<select class="form-control" name="time_type">
								<option value="create" <?php if ($timeType == 'create'): ?>selected="selected"<?php endif; ?>>下单时间</option>
								<option value="proceed" <?php if ($timeType == 'proceed'): ?>selected="selected"<?php endif; ?>>完成时间</option>
							</select>
							<input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
							<input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">
						</div>

						<input type="text" class="form-control m-l-sm" name="event_title" value="<?= $eventTitle ?>" placeholder="比赛标题">
						<input type="text" class="form-control m-l-sm" name="user_nickname" value="<?= $userNickname ?>" placeholder="用户昵称">
		                <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
			        </div>
				</div>
				<table id="listTable" class="list table">
					<tr>
						<th class="digit">
							<a href="javascript:;" class="sort" name="sn">订单编号</a>
						</th>
						<?php if (empty($eventId)): ?>
						<th>
							<a href="javascript:;" class="sort" name="item_id">比赛名称</a>
						</th>
						<?php endif; ?>
						<th>
							<a href="javascript:;" class="sort" name="user_id">用户</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="item_money">票价格</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="item_count">购买数量</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="shipping_type">配送方式</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="total_money">总金额</a>
						</th>
						<th class="time">
							<a href="javascript:;" class="sort" name="<?= ($timeType == 'create') ? 'create_date' : 'proceed_date' ?>">操作时间</a>
						</th>
						<th class="status">
							<a href="javascript:;">订单状态</a>
						</th>
						<th>
							<a href="javascript:;"></a>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td>
								<?= $item['sn'] ?>
							</td>
							<?php if (empty($eventId)): ?>
							<td>
								<?= $item['event']['title'] ?>
							</td>
							<?php endif; ?>							
							<td>
								<?= $item['user']['nickname'] ?>
							</td>
							<td>
								<?= $item['item_money'] ?>
							</td>
							<td>
								<?= $item['item_count'] ?>
							</td>
							<td>
								<?= ($item['shipping_type'] == 1) ? ('快递 ￥' . $item['shipping_fee']) : '自取' ?>
							</td>
							<td>
								<?= $item['total_money'] ?>
							</td>
							<td>
								<?php if ($timeType == 'create'): ?>
								<?= $item['create_date'] ?>
								<?php else: ?>
								<?= $item['proceed_date'] ?>
								<?php endif; ?>
							</td>
							<td>
								<?= getOrderStatusDetail($item['order_status'], $item['pay_status'], $item['shipping_status'], $item['shipping_type']) ?>
							</td>
							<td class="operation">
							<? if ($item['order_status'] != ORDER_STATUS_FAILED): ?>
								<? if ($item['shipping_type'] == 1 && $item['order_status'] != ORDER_STATUS_FAILED): ?>								
								<a class="btnViewDetail" href="javascript:;"><i class="fa fa-angle-double-down"></i> 查看详情</a>
								<? else: ?>
								<a class="btnViewDetail" href="javascript:;"><i class="fa fa-angle-double-down"></i> <?= $item['ticket_take_code'] ?></a>
								<? endif; ?>
							<? endif; ?>						
							</td>
						</tr>
						<? if ($item['order_status'] != ORDER_STATUS_FAILED): ?>
							<? if ($item['shipping_type'] == 1): ?>
							<tr class="row-detail" style="display: none;">
								<td>&nbsp;</td>
								<td colspan="9">
									<label>收货人：</label> <?= $item['consignee'] ?>
									<label class="p-l-m">联系电话：</label> <?= $item['phone'] ?>
									<label class="p-l-m">配送地址：</label> <?= $item['area'] ?> <?= $item['address'] ?>
									<br/>
									<? if ($item['order_status'] == ORDER_STATUS_SUCCEED || $item['shipping_status'] == SHIP_STATUS_SHIPPED): ?>
										<label>运单号：</label> <?= $item['deliver_sn'] ?>
										<label class="p-l-m">发货时间：</label> <?= $item['deliver_date'] ?>
										<? if ($item['order_status'] == ORDER_STATUS_SUCCEED): ?>
											<label class="p-l-m">收货时间：</label> <?= $item['proceed_date'] ?>
										<? endif; ?>
									<? else: ?>
										<label>运单号：</label> 
										<input class="form-control txtDeliverSn" type="text" data-id="<?= $item['id'] ?>" placeholder="请填写运单号">
										<button class="btn btn-primary m-l-xs btnDeliver">确认发货</button>
										<? if ($item['pay_status'] == PAY_STATUS_UNPAID): ?>
										<button class="btn btn-danger btn-outline m-l-xs btnCancel">取消订单</button>
										<? endif; ?>
									<? endif; ?>
								</td>
							</tr>
							<? else: ?>
							<tr class="row-detail" style="display: none;">
								<td>&nbsp;</td>
								<td colspan="9">
									<label>自提吗：</label>
									<input class="form-control txtTicketTakeCode" type="text" data-id="<?= $item['id'] ?>" readonly="readonly" value="<?= $item['ticket_take_code'] ?>">
									<? if ($item['order_status'] == ORDER_STATUS_PROCESSING && $item['pay_status'] == PAY_STATUS_PAID && $item['shipping_status'] == SHIP_STATUS_UNSHIPPED): ?>
									<button class="btn btn-primary m-l-xs btnComplete">完成</button>
									<? endif; ?>
								</td>
							</tr>
							<? endif; ?>
						<? endif; ?>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="10">
							<div class="p-lg">没有符合条件的记录！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="10">							
							<?php $this->load->view('admin/pagination'); ?>
						</th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>