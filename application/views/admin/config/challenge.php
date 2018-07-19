<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>联盟报名</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>

});

function onDelete($obj, result) {
	if (result.type == "success") {
		/*setTimeout(function() {
			location.reload(true);
		}, 1500);*/
	}
}
function onToggleShow(obj, message) {
	var $obj = $(obj);
	var $iChild = $(obj).children("i.fa");
	var $status = $(obj).parent("td").siblings().eq(3);
	if ($iChild.hasClass("fa-eye")) {
		$iChild.removeClass("fa-eye").addClass("fa-eye-slash");
		$obj.attr("title", "开启");
		$status.text("结束");
	} else {
		$iChild.removeClass("fa-eye-slash").addClass("fa-eye");
		$obj.attr("title", "结束");
		$status.text("开启");
	}
}
</script>
</head>
<body>
	<div class="content-wrapper config">
		<div class="title-bar">联盟报名</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li><a href="audit/<?= AUDIT_KIND_PLAYER ?>"><?= getAuditKind(AUDIT_KIND_PLAYER) ?>报名</a></li>
				<li><a href="audit/<?= AUDIT_KIND_REFEREE ?>"><?= getAuditKind(AUDIT_KIND_REFEREE) ?>报名</a></li>
				<li><a href="audit/<?= AUDIT_KIND_COACH ?>"><?= getAuditKind(AUDIT_KIND_COACH) ?>报名</a></li>
				<li><a href="audit/<?= AUDIT_KIND_CLUB ?>"><?= getAuditKind(AUDIT_KIND_CLUB) ?>报名</a></li>
				<li class="active"><a href="javascript:;"><?= getAuditKind(AUDIT_KIND_CHALLENGE) ?>报名</a></li>
			</ul>
		</div>
		<div class="list-wrapper col-xs-12 col-sm-10 col-md-8">
			<form id="listForm" class="form-inline" action="challenge" method="get">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">排序</a>
						</th>
						<th class="image">
							<a href="javascript:;">封面图</a>
						</th>
						<th>
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
						<tr>
							<td>
								<?= $key + 1 ?>
							</td>
							<td>
								<a class="fancybox" href="<?= getFullUrl($item['image']) ?>" title="<?= $item['title'] ?>">
									<img src="<?= getFullUrl($item['image']) ?>" width="120" height="75">
								</a>
							</td>
							<td>
								<?= $item['title'] ?>
							</td>
							<td>
								<?= $item['is_show'] ? '开启' : '结束' ?>
							</td>
							<td class="operation">
								<a role="ajax" data-url="challenge_toggle_show" data-func="onToggleShow" data-reload="false" data-id="<?= $item['id'] ?>" title="<?= $item['is_show'] ? '结束' : '开启'?>"><i class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash'?>"></i></a>
								<a role="button" href="edit_challenge?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a role="button" class="deleteItemBtn" data-url="delete_challenge" data-id="<?= $item['id'] ?>" data-func="onDelete" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="5">
							<div class="p-lg">没有联盟报名！</div>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				<a role="button" class="btn btn-white" href="edit_challenge"><i class="fa fa-plus"></i> 添加联盟报名</a>
			</form>
		</div>
	</div>
</body>
</html>