<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Banner</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $listForm = $("#listForm");
	var $listTable = $("#listTable");
	
});

function onToggleShow(obj, message) {
	var $obj = $(obj);
	var $iChild = $(obj).children("i.fa");
	if ($iChild.hasClass("fa-eye")) {
		$iChild.removeClass("fa-eye").addClass("fa-eye-slash");
		$obj.attr("title", "显示");
	} else {
		$iChild.removeClass("fa-eye-slash").addClass("fa-eye");
		$obj.attr("title", "隐藏");
	}
}
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			Banner2
			<?php if ($isEditable): ?>
			<div class="pull-right op-wrapper">
				<a href="edit?kind=<?= BANNER_NEARBY ?>"><i class="fa fa-plus"></i> 添加Banner2</a>
			</div>
			<?php endif; ?>
		</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="" method="post">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">编号</a>
						</th>
						<th class="image">
							<a href="javascript:;">缩略图</a>
						</th>
						<th>
							<a href="javascript:;">标题</a>
						</th>
						<th>
							<a href="javascript:;">类型</a>
						</th>
						<th>
							<span>操作</span>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr class="item">
							<td>
								<?= $key + 1 ?>
							</td>
							<td>
								<input type="hidden" name="ids[]" value="<?= $item['id'] ?>">
								<a class="fancybox" href="<?= getFullUrl($item['image']) ?>" title="<?= $item['title'] ?>">
									<img src="<?= getFullUrl($item['image']) ?>" width="250" height="75">
								</a>
							</td>
							<td>
								<?= $item['title'] ?>
							</td>
							<td>
								<?= getBannerKind($item['item_kind']) ?>
							</td>
							<td class="operation">
								<?php if ($isEditable): ?>
								<a role="ajax" data-url="toggle_show" data-func="onToggleShow" data-reload="false" data-id="<?= $item['id'] ?>" title="<?= $item['is_show'] ? '隐藏' : '显示'?>"><i class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash'?>"></i></a>
								<a href="edit?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
								<?php else: ?>
								<a class="disabled" href="javascript:;" title="<?= $item['is_show'] ? '隐藏' : '显示'?>"><i class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash'?>"></i></a>
								<a class="disabled" href="javascript:;" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="disabled" href="javascript:;" title="删除"><i class="fa fa-trash-o"></i></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="5">
							<div class="p-lg">没有符合条件的记录！</div>
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</form>
		</div>
	</div>
</body>
</html>