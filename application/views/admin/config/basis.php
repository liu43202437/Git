<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>基本设置</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");
	
	$inputForm.validate({
		rules: {
			exp_per_money: {
				required: true,
				min: 0
			},
			exp_per_point: {
				required: true,
				min: 0
			},
			'points[]': {
				required: true,
				min: 1
			},
			'prices[]': {
				required: true,
				min: 0.01
			}
		},
		errorPlacement: function(error, element) {
			if (element.attr("name") == 'points[]') {
				error.appendTo($("#pointError"));
			} else if (element.attr("name") == 'prices[]') {
				error.appendTo($("#priceError"));
			} else {
				error.insertAfter(element);
			}		
		},
		submitHandler: function() {
			return true;
		}
	});
	
	$("#addButton").click(function() {
		var html = '<tr>';
		html += '<td><input type="text" class="text-center form-control" name="points[]" value=""></td>';
		html += '<td><input type="text" class="text-center form-control" name="prices[]" value=""></td>';
		html += '<td><a role="button" class="delete" title="删除"><i class="fa fa-trash-o"></i></a></td>';
		html += '</tr>';
		$(html).insertBefore($("#listTable").find("#errorRow"));
	});
	
	$("#listTable").on("click", "a.delete", function() {
		$(this).parents("tr").remove();
	});
});
</script>
</head>
<body>
	<div class="content-wrapper config">
		<div class="title-bar">基本设置</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li class="active"><a href="javascript:;">烟币，经验</a></li>
				<li><a href="about">关于</a></li>
				<li><a href="vocabulary">屏蔽词汇</a></li>
				<li><a href="hits">点击量</a></li>
				<li><a href="search_word">搜索文字</a></li>
			</ul>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_basis" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="col-xs-2 control-label">
						消耗1分 = 
					</label>
					<div class="col-xs-9">
						<input type="number" class="form-control pull-left" name="exp_per_money" value="<?= $expPerMoney ?>" style="width:150px;">
						<span class="pull-left m-l" style="line-height:34px;">经验</span>
					</div>
				</div>
				<div class="form-group hidden">
					<label class="col-xs-2 control-label">
						消耗1两 = 
					</label>
					<div class="col-xs-9">
						<input type="number" class="form-control pull-left" name="exp_per_point" value="<?= $expPerPoint ?>" style="width:150px;">
						<span class="pull-left m-l" style="line-height:34px;">经验</span>
					</div>
				</div>
				<div class="form-group hidden">
					<label class="col-xs-2 control-label">
						人民币1元 = 
					</label>
					<div class="col-xs-9">
						<input type="number" class="form-control pull-left" name="point_per_money" value="<?= $pointPerMoney ?>" style="width:150px;">
						<span class="pull-left m-l" style="line-height:34px;">烟币</span>
					</div>
				</div>
				<div class="list-wrapper col-sm-8 col-md-5 m-t-lg">
					<div class="font-bold m-b-md">
						烟币兑换设置
						<a role="button" class="pull-right m-t-sm" style="color:#666" id="addButton"><i class="fa fa-plus"></i> 添加设置</a>
					</div>
					<table id="listTable" class="list table">
						<tr>
							<th>
								<a href="javascript:;">烟币（币）</a>
							</th>
							<th>
								<a href="javascript:;">价格（￥）</a>
							</th>
							<th width="60">
								<a href="javascript:;">操作</a>
							</th>
						</tr>
						<?php if (!empty($pointPrices)): ?>
						<?php foreach ($pointPrices as $key=>$item): ?>
							<tr>
								<td>
									<input type="text" class="text-center form-control" name="points[]" value="<?= $item['point'] ?>">
								</td>
								<td>
									<input type="text" class="text-center form-control" name="prices[]" value="<?= $item['price'] ?>">
								</td>
								<td>
									<?php if ($key > 0): ?>
									<a role="button" class="delete" title="删除"><i class="fa fa-trash-o"></i></a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td>
									<input type="text" class="text-center form-control" name="points[]" value="">
								</td>
								<td>
									<input type="text" class="text-center form-control" name="prices[]" value="">
								</td>
								<td>&nbsp;</td>
							</tr>
						<?php endif; ?>
						<tr id="errorRow">
							<td id="pointError"></td>
							<td id="priceError"></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
				<div class="form-group m-t-lg">
					<div class="col-xs-offset-2 col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>