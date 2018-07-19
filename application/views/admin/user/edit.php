<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>用户详情</title>
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
	
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			用户详情
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save" method="post" class="form-horizontal">
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				
				<div class="form-group">
					<label class="col-sm-2 control-label">
						头&nbsp;&nbsp;像:
					</label>
					<div class="col-sm-4">
						<img src="<?= $itemInfo['avatar_url'] ?>" width="80" height="80">
					</div>
				</div>
				<div class="form-group">
					<label for="nickname" class="col-sm-2 control-label">
						昵&nbsp;&nbsp;称：
					</label>
					<div class="col-sm-4">
						<input type="text" name="nickname" class="form-control" value="<?= $itemInfo['nickname'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label for="nickname" class="col-sm-2 control-label">
						性&nbsp;&nbsp;别：
					</label>
					<div class="col-sm-4">
						<input type="radio" class="i-check" name="gender" disabled="disabled" value="<?= GENDER_MALE ?>" <?php if ($itemInfo['gender'] == GENDER_MALE): ?>checked="checked"<?php endif; ?>/>
						<span class="m-r-md"><?= getUserGender(GENDER_MALE) ?></span>
						
						<input type="radio" class="i-check" name="gender" disabled="disabled" value="<?= GENDER_FEMALE ?>" <?php if ($itemInfo['gender'] == GENDER_FEMALE): ?>checked="checked"<?php endif; ?> />
						<?= getUserGender(GENDER_FEMALE) ?>
					</div>
				</div>
				<?php if (empty($itemInfo['weixin'])): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						手&nbsp;&nbsp;机：
					</label>
					<div class="col-sm-4">
						<input type="text" name="username" class="form-control" value="<?= $itemInfo['username'] ?>" readonly="readonly"/>
					</div>
				</div>
				<?php else: ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						微&nbsp;&nbsp;信：
					</label>
					<div class="col-sm-4">
						<input type="text" name="weixin" class="form-control" value="<?= $itemInfo['weixin'] ?>" readonly="readonly"/>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						gps位置：
					</label>
					<div class="col-sm-4">
						<input type="text" name="gps" class="form-control" value="<?= $itemInfo['longitude'] . ', ' . $itemInfo['latitude'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						注册日期：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['create_date'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						最后登录日期：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['login_date'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						等&nbsp;&nbsp;级：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['rank'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						经&nbsp;&nbsp;验：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['exp'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						跆&nbsp;&nbsp;币：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['point'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label for="nickname" class="col-sm-2 control-label">
						手机类型：
					</label>
					<div class="col-sm-4">
						<input type="radio" class="i-check" disabled="disabled" value="<?= DEVICE_TYPE_IPHONE ?>" <?php if ($itemInfo['device_type'] == DEVICE_TYPE_IPHONE): ?>checked="checked"<?php endif; ?>/>
						<span class="m-r-md">苹果</span>
						
						<input type="radio" class="i-check" disabled="disabled" value="<?= DEVICE_TYPE_ANDROID ?>" <?php if ($itemInfo['device_type'] == DEVICE_TYPE_ANDROID): ?>checked="checked"<?php endif; ?> />
						安卓
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						手机UDID：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['device_udid'] ?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						APP版本：
					</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?= $itemInfo['app_version'] ?>" readonly="readonly"/>
					</div>
				</div>
				
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-2 col-sm-4">
						<button type="button" class="btn btn-white" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>