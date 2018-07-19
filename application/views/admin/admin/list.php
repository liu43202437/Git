<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>管理员列表</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>

});

function onToggleEnabled(obj, message) {
	var $obj = $(obj);
	var $iChild = $(obj).children("i.fa");
	if ($iChild.hasClass("fa-ban")) {
		$iChild.removeClass("fa-ban").addClass("fa-circle-o");
		$obj.attr("title", "停用");
	} else {
		$iChild.removeClass("fa-circle-o").addClass("fa-ban");
		$obj.attr("title", "启用");
	}
}

</script>
</head>
<body class="">
	<div class="content-wrapper">
		<div class="title-bar">管理员</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="lists" method="get">
				<div class="filter-bar">
					<input type="text" class="form-control" name="username" value="<?= $username ?>" placeholder="请输入管理员名">
					<button class="btn btn-white" type="submit">帅 选</button>			
				</div>
				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="number">
							<span>编号</span>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="username">管理员名</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="description">管理员描述</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="email">邮箱</a>
						</th>
						<th class="time">
							<a href="javascript:;" class="sort" name="create_date">注册日期</a>
						</th>
						<th>
							<span>操作</span>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr>
							<td>								
								<input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>" <?php if (!$isEditable || $item['id'] == 1): ?>disabled="disabled"<?php endif; ?>/>
							</td>
							<td>
								<?= $key + 1 + ($pager['pageSize'] * ($pager['pageNumber'] - 1)) ?>
							</td>
							<td>
								<?= $item['username'] ?>
							</td>
							<td>
								<?= $item['description'] ?>
							</td>
							<td>
								<?= $item['email'] ?>
							</td>
							<td>
								<?= $item['create_date'] ?>
							</td>
							<td class="operation">
								<?php if ($isEditable && $item['id'] != 1): ?>
									<a href="edit?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
									<a href="edit_role?id=<?= $item['id'] ?>" title="权限"><i class="fa fa-filter"></i></a>
									<a role="ajax" data-url="toggle_enable" data-func="onToggleEnabled" data-reload="false" data-id="<?= $item['id'] ?>" title="<?= $item['is_enabled'] ? '停用' : '启用'?>"><i class="fa <?= $item['is_enabled'] ? 'fa-circle-o' : 'fa-ban'?>"></i></a>
									<a class="deleteItemBtn" data-url="delete" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
								<?php else: ?>
									<a class="disabled" href="javascript:;" title="编辑"><i class="fa fa-edit"></i></a>
									<a class="disabled" href="javascript:;" title="权限"><i class="fa fa-filter"></i></a>
									<a class="disabled" href="javascript:;" title="<?= $item['is_enabled'] ? '停用' : '启用'?>"><i class="fa <?= $item['is_enabled'] ? 'fa-circle-o' : 'fa-ban'?>"></i></a>
									<a class="disabled" href="javascript:;" title="删除"><i class="fa fa-trash-o"></i></a>
								<?php endif; ?>
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
							<span>
								<input type="checkbox" class="i-check" id="selectAll" <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?> />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?>个项目</span>
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