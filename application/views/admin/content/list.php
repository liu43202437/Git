<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= getContentKind($kind) ?></title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>

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
			<?= getContentKind($kind) ?>
			<?php if ($isEditable): ?>
			<div class="pull-right op-wrapper">
				<a href="../edit/<?=$kind?>"><i class="fa fa-plus"></i> 添加<?= getContentKind($kind) ?></a>
			</div>
			<?php endif; ?>
		</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="../lists/<?=$kind?>" method="get">
				<div class="filter-bar">
					<select class="form-control s-lg" name="category">
						<option value="">分类</option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category['id'] ?>" <?php if ($category['id'] == $categoryId): ?>selected="selected"<?php endif; ?>><?= $category['name'] ?></option>
						<?php endforeach; ?>
					</select>
					<select class="form-control s-lg" name="is_show">
						<option value="">状态</option>
						<option value="1" <?php if ($isShow === 1): ?>selected="selected"<?php endif; ?>>显示</option>
						<option value="0" <?php if ($isShow === 0): ?>selected="selected"<?php endif; ?>>隐藏</option>
					</select>
					
					<input type="text" class="form-control m-l-sm" name="title" value="<?= $title ?>" placeholder="<?= getContentKind($kind) ?>标题">
		            <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
				</div>

				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="number">
							<a href="javascript:;" class="sort" name="id">ID</a>
						</th>
						<th class="qrcode">
							<a href="javascript:;">&nbsp;&nbsp;二维码</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="title">标题</a>
						</th>
						<?php if ($kind == CONTENT_KIND_GALLERY): ?>
						<th class="digit">
							<a href="javascript:;">图片数</a>
						</th>
						<?php elseif ($kind == CONTENT_KIND_VIDEO): ?>
						<th class="digit">
							<a href="javascript:;">时长</a>
						</th>
						<?php elseif ($kind == CONTENT_KIND_LIVE): ?>
						<th class="digit">
							<a href="javascript:;">烟币</a>
						</th>
						<?php endif; ?>
						<th class="digit">
							<a href="javascript:;" class="sort" name="hits">点击</a>
						</th>
						<th class="digit">
							<a href="javascript:;">评论</a>
						</th>
						<th class="time">
							<a href="javascript:;" class="sort" name="content_date">时间</a>
						</th>
						<th>
							<span>操作</span>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td class="check">
								<input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>" <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?> />
							</td>
							<td>
								<?= $item['id'] ?>
							</td>
							<td>
								<a class="fancybox" href="<?=base_url().'common/qrcode?w=300&t='.getPortalUrl($item['id'], PORTAL_KIND_CONTENT)?>&m=1&ext=.png" title="<?= $item['title'] ?>">
									<img src="<?=base_url().'common/qrcode?w=100&t='.getPortalUrl($item['id'], PORTAL_KIND_CONTENT)?>">
								</a>
							</td>
							<td>
								<?= $item['title'] ?>
							</td>
							<?php if ($kind == CONTENT_KIND_GALLERY): ?>
							<td>
								<?= $item['image_count'] ?>
							</td>
							<?php elseif ($kind == CONTENT_KIND_VIDEO): ?>
							<td>
								<?= $item['duration'] ?>
							</td>
							<?php elseif ($kind == CONTENT_KIND_LIVE): ?>
							<td>
								<?= $item['point'] ?>
							</td>
							<?php endif; ?>
							<td>
								<?= $item['hits'] ?>
							</td>
							<td>
								<a href="<?=base_url()?>admin/comment/lists?target_kind=<?=COMMENT_ITEM_KIND_CONTENT?>&target_id=<?=$item['id']?>"><?= $item['comment_count'] ?></a>
							</td>
							<td>
								<?= $item['content_date'] ?>
							</td>
							<td class="operation">
								<?php if ($isEditable): ?>
								<a role="ajax" data-url="../toggle_show" data-func="onToggleShow" data-reload="false" data-id="<?= $item['id'] ?>" title="<?= $item['is_show'] ? '隐藏' : '显示'?>"><i class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash'?>"></i></a>
								<a href="../edit/<?= $kind ?>?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a href="<?= getPortalUrl($item['id'], PORTAL_KIND_CONTENT) ?>" target="_blank" title="链接"><i class="fa fa-link"></i></a>
								<a class="deleteItemBtn" data-url="../delete" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
								<?php else: ?>
								<a class="disabled" href="javascript:;" title="<?= $item['is_show'] ? '隐藏' : '显示'?>"><i class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash'?>"></i></a>
								<a class="disabled" href="javascript:;" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="disabled" href="javascript:;" title="链接"><i class="fa fa-link"></i></a>
								<a class="disabled" href="javascript:;" title="删除"><i class="fa fa-trash-o"></i></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="9">
							<div class="p-lg">没有符合条件的记录！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="9">
							<span>
								<input type="checkbox" class="i-check" id="selectAll" <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?> />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?><?= getContentKind($kind) ?></span>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="../toggle_show" data-reload="true" data-params="is_show=0">批量隐藏</a>
								<a class="batch-btn btn btn-default btn-outline disabled" id="deleteButton" data-url="../delete">批量删除</a>
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