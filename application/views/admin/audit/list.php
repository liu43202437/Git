<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= getAuditKind($kind) ?>报名</title>
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
		<div class="title-bar"><?= getAuditKind($kind) ?>报名</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="<?=$kind?>" method="get">
				<div class="filter-bar">
					<select class="form-control s-lg" name="status">
						<option value="">状态</option>
						<option value="<?= AUDIT_STATUS_PASSED ?>" <?php if ($status === AUDIT_STATUS_PASSED): ?>selected="selected"<?php endif; ?>><?= getAuditStatus(AUDIT_STATUS_PASSED) ?></option>
						<option value="<?= AUDIT_STATUS_REJECTED ?>" <?php if ($status === AUDIT_STATUS_REJECTED): ?>selected="selected"<?php endif; ?>><?= getAuditStatus(AUDIT_STATUS_REJECTED) ?></option>
						<option value="<?= AUDIT_STATUS_REQUESTED ?>" <?php if ($status === AUDIT_STATUS_REQUESTED): ?>selected="selected"<?php endif; ?>><?= getAuditStatus(AUDIT_STATUS_REQUESTED) ?></option>
					</select>
					<div class="form-group m-l-sm">
						<select class="form-control" name="time_type">
							<option value="create" <?php if ($timeType == 'create'): ?>selected="selected"<?php endif; ?>>报名时间</option>
							<option value="audit" <?php if ($timeType == 'audit'): ?>selected="selected"<?php endif; ?>>审核时间</option>
						</select>
						<input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
						<input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">
					</div>
					
					<input type="text" class="form-control m-l-sm hidden" name="name" value="<?= $name ?>" placeholder="姓名">
		            <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
				</div>

				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="number">
							<a href="javascript:;" class="sort" name="id">ID</a>
						</th>
						<th class="name hidden">
							<a href="javascript:;" class="sort" name="name">姓名</a>
						</th>
						<th class="name hidden">
							<a href="javascript:;" class="sort" name="gender">性别</a>
						</th>
						<th class="phone hidden">
							<a href="javascript:;">手机</a>
						</th>
						<?php if ($kind == AUDIT_KIND_CHALLENGE): ?>
						<th>
							<a href="javascript:;" class="sort" name="challenge_id">联盟标题</a>
						</th>
						<?php endif; ?>
						<th class="time">
							<a href="javascript:;" class="sort" name="create_date">提交时间</a>
						</th>
						<th class="status">
							<a href="javascript:;" class="sort" name="status">状态</a>
						</th>
						<th>
							<span>操作</span>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td>
								<?php if ($item['status'] == AUDIT_STATUS_REQUESTED): ?>
								<input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>"/>
								<?php endif; ?>
							</td>
							<td>
								<?= $item['id'] ?>
							</td>
							<td class="hidden">
								<?= $item['name'] ?>
							</td>
							<td class="hidden">
								<?= getUserGender($item['gender']) ?>
							</td>
							<td class="hidden">
								<?= $item['mobile'] ?>
							</td>
							<?php if ($kind == AUDIT_KIND_CHALLENGE): ?>
							<td>
								<?= $item['challenge_title'] ?>
							</td>
							<?php endif; ?>
							<td>
								<?= $item['create_date'] ?>
							</td>
							<td>
								<?= getAuditStatus($item['status']) ?>
							</td>
							<td class="operation">
								<?php if ($item['status'] == AUDIT_STATUS_REQUESTED): ?>
								<a href="../edit/<?= $kind ?>?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<?php else: ?>
								<a href="../edit/<?= $kind ?>?id=<?= $item['id'] ?>" style="font-size: 12px;">查看</a>
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
								<input type="checkbox" class="i-check" id="selectAll" />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?>报名</span>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="../update_status" data-reload="true" data-params="type=status">批量拒绝</a>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="../update_status" data-reload="true" data-params="type=marked">批量标记</a>
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