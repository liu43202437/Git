<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>排行耪</title>
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
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	$("#listTable").sortable({
		items: "tr.movable",
		update: function(event, ui) {
			$.ajax({
				url: "change_ranking_order",
				type: "post",
				data: $("#listTable input[name^='ids']").serialize(),
				dataType: "json",
				cache: false,
				success: function(message) {
					$.message(message);
					if (message.type == "success") {
						$("tr.movable").each(function(index) {
							$(this).children().eq(1).text(index + 1);
						});
					} else {
						setTimeout(function() {
							location.reload(true);
						}, 1500);
					}
				}
			});
		}
	});
	
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">排行耪</div>
		<div class="list-wrapper col-xs-9 col-md-6 m-t-md">
			<form id="listForm" class="form-inline" action="category" method="get">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">ID</a>
						</th>
						<th class="number">
							<a href="javascript:;">排序</a>
						</th>
						<th>
							<a href="javascript:;">名称</a>
						</th>
						<th>
							<a href="javascript:;">操作</a>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr class="movable">
							<td>
								<?= $item['id'] ?>
								<input type="hidden" name="ids[]" value="<?= $item['id'] ?>">
							</td>
							<td>
								<?= $key + 1 ?>
							</td>
							<td>
								<?= $item['name'] ?>
							</td>
							<td class="operation">
								<a href="edit_ranking?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete_ranking" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="4">
							<div class="p-lg">没有排行！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="4">							
							<a role="button" class="btn btn-white btn-outline" id="addButton" href="edit_ranking"><i class="fa fa-plus"></i> 添加排行</a>
						</th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>