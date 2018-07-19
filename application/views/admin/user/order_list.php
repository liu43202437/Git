<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>订单</title>
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
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">订单</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="order_list" method="get">
				<input type="hidden" name="id" value="<?= $userId ?>">
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
		                <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
			        </div>
				</div>
				<table id="listTable" class="list table">
					<tr>
						<th>
							<a href="javascript:;" class="sort" name="sn">订单编号</a>
						</th>
						<th>
							<a href="javascript:;">内容</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="total_money">消费金额</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="pay_point">消费烟币</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="gain_point">获得烟币</a>
						</th>
						<th class="time">
							<a href="javascript:;" class="sort" name="<?= ($timeType == 'create') ? 'create_date' : 'proceed_date' ?>">操作时间</a>
						</th>
						<th class="status">
							<a href="javascript:;" class="sort" name="order_status">状态</a>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td>
								<?= $item['sn'] ?>
							</td>
							<td>
								<?= $item['description'] ?>
							</td>
							<td>
								<?= $item['total_money'] ?>
							</td>
							<td>
								<?= $item['pay_point'] ?>
							</td>
							<td>
								<?= $item['gain_point'] ?>
							</td>
							<td>
								<?php if ($timeType == 'create'): ?>
								<?= $item['create_date'] ?>
								<?php else: ?>
								<?= $item['proceed_date'] ?>
								<?php endif; ?>
							</td>
							<td>
								<?= getOrderStatus($item['order_status']) ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="7">
							<div class="p-lg">没有符合条件的记录！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="7">							
							<?php $this->load->view('admin/pagination'); ?>
						</th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>