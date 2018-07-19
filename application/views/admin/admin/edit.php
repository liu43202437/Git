<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?php if (empty($itemInfo)): ?>添加管理员<?php else: ?>管理员详情<?php endif; ?>
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");
	
	// 表单验证
	$inputForm.validate({
		rules: {
			username: {
				required: true,
				pattern: /^[0-9a-z_A-Z\u4e00-\u9fa5]+$/,
				minlength: 3,
				maxlength: 20,
				remote: {
					<?php if (empty($itemInfo)): ?>
					url: "check_username",
					<?php else: ?>
					url: "check_username?org_id=<?=$itemInfo['id']?>",
					<?php endif; ?>
					cache: false
				}
			},
			password: {
				<?php if (empty($itemInfo)): ?>
				required: true,
				<?php endif; ?>
				pattern: /^[^\s&\"<>]+$/,
				minlength: 6,
				maxlength: 50
			},
			rePassword: {
				<?php if (empty($itemInfo)): ?>
				required: true,
				<?php endif; ?>
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true,
				remote: {
					<?php if (empty($itemInfo)): ?>
					url: "check_email",
					<?php else: ?>
					url: "check_email?org_id=<?=$itemInfo['id']?>",
					<?php endif; ?>
					cache: false
				}
			}
		},
		messages: {
			username: {
				pattern: "非法字符",
				remote: "账号已存在"
			},
			password: {
				pattern: "非法字符"
			},
			email: {
				remote: "邮件已存在"
			},
		}
	});

});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?php if (empty($itemInfo)): ?>添加管理员<?php else: ?>管理员详情<?php endif; ?>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save" method="post" class="form-horizontal">
				<?php if (!empty($itemInfo)): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>

				<div class="form-group">
					<label for="username" class="col-sm-2 control-label required">
						用 户 名：
					</label>
					<div class="col-sm-4">
						<input type="text" id="username" name="username" class="form-control" maxlength="50" value="<?= empty($itemInfo)?'':$itemInfo['username']?>"/>
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-2 control-label required">
						用户邮件：
					</label>
					<div class="col-sm-4">
						<input type="email" id="email" name="email" class="form-control" value="<?= empty($itemInfo)?'':$itemInfo['email']?>"/>
					</div>
				</div>
				<?php if (!empty($itemInfo)): ?>
				<div class="form-group">
					<label for="oldPassword" class="col-sm-2 control-label">
						旧 密 码：
					</label>
					<div class="col-sm-4">
						<input type="password" id="oldPassword" name="oldPassword" class="form-control" maxlength="50" autocomplete="off"/>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label for="password" class="col-sm-2 control-label <?php if (empty($itemInfo)):?>required<?php endif; ?>">
						新 密 码：
					</label>
					<div class="col-sm-4">
						<input type="password" id="password" name="password" class="form-control" maxlength="50" autocomplete="off"/>
					</div>
				</div>
				<div class="form-group">
					<label for="rePassword" class="col-sm-2 control-label <?php if (empty($itemInfo)):?>required<?php endif; ?>">
						确认密码：
					</label>
					<div class="col-sm-4">
						<input type="password" id="rePassword" name="rePassword" class="form-control" maxlength="50" autocomplete="off"/>
					</div>
				</div>
				<div class="form-group">
					<label for="description" class="col-sm-2 control-label">
						描&nbsp;&nbsp;述：
					</label>
					<div class="col-sm-4">
						<input type="text" id="description" name="description" class="form-control" value="<?= empty($itemInfo)?'':$itemInfo['description']?>"/>
					</div>
				</div>
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-2 col-sm-4">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
						<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>