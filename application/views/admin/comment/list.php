<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>评论</title>
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
		<div class="title-bar">评论</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="lists" method="get">
				<div class="filter-bar">
					<select class="form-control s-lg" name="status">
						<option value="">状态</option>
						<option value="<?= COMMENT_STATUS_PASSED ?>" <?php if ($status === COMMENT_STATUS_PASSED): ?>selected="selected"<?php endif; ?>><?= getCommentStatus(COMMENT_STATUS_PASSED) ?></option>
						<option value="<?= COMMENT_STATUS_REJECTED ?>" <?php if ($status === COMMENT_STATUS_REJECTED): ?>selected="selected"<?php endif; ?>><?= getCommentStatus(COMMENT_STATUS_REJECTED) ?></option>
						<option value="<?= COMMENT_STATUS_REQUESTED ?>" <?php if ($status === COMMENT_STATUS_REQUESTED): ?>selected="selected"<?php endif; ?>><?= getCommentStatus(COMMENT_STATUS_REQUESTED) ?></option>
					</select>
					<div class="form-group m-l-sm">
						<select class="form-control">
							<option>评论时间</option>
						</select>
						<input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
						<input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">
					</div>
					
					<input type="hidden" name="target_kind" value="<?= $targetKind ?>">
					<input type="hidden" name="target_id" value="<?= $targetId ?>">
					<input type="text" class="form-control m-l-sm" name="title" value="<?= $title ?>" placeholder="标题">
		            <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
				</div>

				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="name">
							<a href="javascript:;" class="sort" name="user_id">用户</a>
						</th>
						<th width="180">
							<a href="javascript:;" class="sort" name="target_id">评论</a>
						</th>
						<th>
							<a href="javascript:;">评论内容</a>
						</th>
						<th class="time">
							<a href="javascript:;" class="sort" name="create_date">提交时间</a>
						</th>
						<th class="status">
							<a href="javascript:;" class="sort" name="status">状态</a>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td>
								<input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>"/>
							</td>
							<td>
								<?= $item['username'] ?>
							</td>
							<td>
								<?= $item['target'] ?>
							</td>
							<td>
								<?= $item['content'] ?>
							</td>
							<td>
								<?= $item['create_date'] ?>
							</td>
							<td>
								<?= getCommentStatus($item['status']) ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="6">
							<div class="p-lg">没有符合条件的记录！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="6">
							<span>
								<input type="checkbox" class="i-check" id="selectAll" />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?>评论</span>								
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="update_status" data-reload="true" data-params="type=status&status=<?=COMMENT_STATUS_REJECTED?>">批量未通过</a>
								<a class="batch-btn btn btn-default btn-outline disabled" id="deleteButton" data-url="delete">批量删除</a>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="update_status" data-reload="true" data-params="type=marked">批量标记</a>
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