<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>页面内容</title>
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
	$("#visibleItems").sortable({
		items: "tr.movable",
		connectWith: "#invisibleItems",
		update: function(event, ui) {
			if (ui.item.hasClass("separater")) {
				ui.item.children().empty();
			}
			
			$.ajax({
				url: "save_layout_discover",
				type: "post",
				data: $("#visibleItems input[name^='items']").serialize(),
				dataType: "json",
				cache: false,
				success: function(message) {
					$.message(message);
					if (message.type == "success") {
						
					} else {
						setTimeout(function() {
							location.reload(true);
						}, 1500);
					}
				}
			});
		}
	});
	
	$("#invisibleItems").sortable({
		items: "tr.movable",
		connectWith: "#visibleItems",
		start: function(event, ui) {
			if (ui.item.hasClass("separater")) {
				ui.item.text("");
			}
		},
		update: function(event, ui) {
			
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper config layout">
		<div class="title-bar">页面内容</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li><a href="layout">头条</a></li>
				<li class="active"><a href="javascript:;">发现</a></li>
			</ul>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_layout_discover" method="post" class="form-horizontal">
				<div class="panel panel-default pull-left">
					<div class="panel-heading">
						<span class="font-bold">发现自定义设置，是否显示</span>
					</div>
					<div class="panel-body">
						<table class="table" id="visibleItems">
							<?php foreach ($visibleItems as $item): ?>
								<tr class="movable">
									<?php if ($item['name'] == 'separater'): ?>
										<td class="separater"><input type="hidden" name="items[]" value="separater"></td>
									<?php else: ?>
										<td class="text-center">
											<input type="hidden" name="items[]" value="<?= $item['name'] ?>">								
											<?= $item['label'] ?>
										</td>
									<?php endif;?>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
				<div class="pull-left m-l-sm m-r-sm" style="padding-top: 250px;">
					<i class="fa fa-exchange fa-2x"></i>
				</div>
				<div class="panel panel-default pull-left">
					<div class="panel-heading">
						<span class="font-bold">可以拖动选择，为添加拖动到左边</span>
					</div>
					<div class="panel-body">
						<table class="table" id="invisibleItems">
							<?php foreach ($invisibleItems as $item): ?>
								<tr class="movable">
									<?php if ($item['name'] == 'separater'): ?>
										<td class="text-center separater"><input type="hidden" name="items[]" value="separater"></td>
									<?php else: ?>
										<td class="text-center">
											<input type="hidden" name="items[]" value="<?= $item['name'] ?>">								
											<?= $item['label'] ?>
										</td>
									<?php endif;?>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>