<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>烟币</title>
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
		<div class="title-bar">烟币</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="consume_history" method="get">
				<input type="hidden" name="id" value="<?= $userId ?>">

				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">ID</a>
						</th>
						<th class="time">
							<a href="javascript:;" class="sort" name="create_date">操作时间</a>
						</th>
						<th>
							<a href="javascript:;">内容</a>
						</th>
						<th>
							<a href="javascript:;">消费烟币</a>
						</th>
						<th>
							<a href="javascript:;">获得烟币</a>
						</th>
						<th>
							<a href="javascript:;">金额</a>
						</th>
						<th class="status">
							<a href="javascript:;" class="sort" name="order_status">状态</a>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td>
								<?= $item['id'] ?>
							</td>
							<td>
								<?= $item['create_date'] ?>
							</td>
							<td>
								<?= $item['description'] ?>
							</td>
							<td>
								<?= $item['pay_point'] ?>
							</td>
							<td>
								<?= $item['gain_point'] ?>
							</td>
							<td>
								<?= $item['total_money'] ?>
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