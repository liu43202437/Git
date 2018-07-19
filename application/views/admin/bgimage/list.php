<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>启动闪播</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
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

});

function onToggleShow(obj, message) {
	var $obj = $(obj);
	var $iChild = $(obj).children("i.fa");
	if ($iChild.hasClass("fa-eye")) {
		$iChild.removeClass("fa-eye").addClass("fa-eye-slash");
		$obj.attr("title", "启动");
	} else {
		$iChild.removeClass("fa-eye-slash").addClass("fa-eye");
		$obj.attr("title", "下架");
	}
}
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			刷新背景
			<div class="pull-right op-wrapper">
				<a href="edit"><i class="fa fa-plus"></i> 添加背景</a>
			</div>
		</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="splash" method="post">
				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="number">
							<a href="javascript:;">ID</a>
						</th>
						<th class="image">
							<a href="javascript:;">图片</a>
						</th>
						<th class="image">
							<a href="javascript:;">标题</a>
						</th>
						<th class="status">
							<a href="javascript:;">状态</a>
						</th>
						<th>
							<span>操作</span>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr class="item">
							<td>
								<input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>"/>
							</td>
							<td>
								<?= $item['id'] ?>
							</td>
							<td>
								<a class="fancybox" href="<?= getFullUrl($item['image']) ?>" title="<?= $item['name'] ?>">
									<img src="<?= getFullUrl($item['image']) ?>" width="80" height="120">
								</a>
							</td>
							<td>
								<?= $item['name'] ?>
							</td>
							<td>
								<?= $item['is_show'] == 1 ? '启动' : '下架' ?>
							</td>
							<td class="operation">
								<a role="ajax" data-url="toggle_show" data-func="onToggleShow" data-reload="false" data-id="<?= $item['id'] ?>" title="<?= $item['is_show'] ? '下架' : '启动'?>"><i class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash'?>"></i></a>
								<a href="edit?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="8">
							<div class="p-lg">没有符合条件的记录！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="8">
							<span>
								<input type="checkbox" class="i-check" id="selectAll" />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?>闪播</span>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="toggle_show" data-reload="true" data-params="is_show=0">批量下架</a>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="toggle_show" data-reload="true" data-params="is_show=1">批量启动</a>
								<a class="batch-btn btn btn-default btn-outline disabled" id="deleteButton" data-url="delete">批量删除</a>
							</span>
							
							<?php $this->load->view('admin/pagination'); ?>
						</th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>