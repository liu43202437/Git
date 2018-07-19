<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>审核详情</title>
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
	var $status = $("input[name=status]");
	var $marked = $("input[name=marked]");
	
	$("#btnPass").click(function() {
		$status.val('<?= AUDIT_STATUS_PASSED ?>');
		$inputForm.submit();
	});
	$("#btnReject").click(function() {
		$status.val('<?= AUDIT_STATUS_REJECTED ?>');
		$inputForm.submit();
	});
	$("#btnMark").click(function() {
		$marked.val(1);
		$inputForm.submit();
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">审核详情</div>
		<div class="input-wrapper">
			<form id="inputForm" action="../save" method="post" class="form-horizontal">
				<input type="hidden" name="kind" value="<?= $kind ?>" />
				<input type="hidden" name="id" value="<?= $itemInfo['id'] ?>" />
				<input type="hidden" name="status" value="" />
				<input type="hidden" name="marked" value="" />

				<div class="form-group hidden">
					<label class="col-sm-3 col-md-2 control-label">
						姓&nbsp;&nbsp;名：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="name" class="form-control" value="<?= $itemInfo['name'] ?>"/>
					</div>
				</div>
				<div class="form-group hidden">
					<label class="col-sm-3 col-md-2 control-label">
						手&nbsp;&nbsp;机：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="tel" name="mobile" class="form-control" value="<?= $itemInfo['mobile'] ?>"/>
					</div>
				</div>
				<div class="form-group hidden">
					<label class="col-sm-3 col-md-2 control-label">
						性&nbsp;&nbsp;别：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="gender" class="form-control" value="<?= getUserGender($itemInfo['gender']) ?>"/>
					</div>
				</div>
				<div class="form-group hidden">
					<label class="col-sm-3 col-md-2 control-label">
						出生日期：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="birthday" class="form-control" value="<?= $itemInfo['birthday'] ?>"/>
					</div>
				</div>
				<?php foreach ($attributes as $attr): ?>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							<?= $attr['label'] ?>：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="text" class="form-control" value="<?= $attr['value'] ?>"/>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10">
						<?php if ($itemInfo['status'] == AUDIT_STATUS_REQUESTED): ?>
							<button id="btnPass" type="button" class="btn btn-primary">通&nbsp;&nbsp;过</button>
							<button id="btnReject" type="button" class="btn btn-warning m-l-md">拒&nbsp;&nbsp;绝</button>
							<?php if ($itemInfo['is_marked'] != 1): ?>
							<button id="btnMark" type="button" class="btn btn-info m-l-md">标&nbsp;&nbsp;记</button>
							<?php endif; ?>
						<?php endif; ?>
						<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>