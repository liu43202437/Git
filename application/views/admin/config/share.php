<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>分享二维码</title>
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
	
	$inputForm.on("input", "#shareContent", function() {
		$("#shareQRImage").attr("src", "<?= base_url() ?>common/qrcode?w=200&m=1&t=" + $(this).val());
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">分享二维码</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_share" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						安卓版下载地址：
					</label>
					<div class="col-sm-8 col-md-6">
						<input class="form-control" name="android_url" value="<?= $androidUrl ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						苹果版下载地址：
					</label>
					<div class="col-sm-8 col-md-6">
						<input class="form-control" name="iphone_url" value="<?= $iphoneUrl ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						分享二维码内容：
					</label>
					<div class="col-sm-8 col-md-6">
						<input class="form-control" id="shareContent" name="share_content" value="<?= $shareContent ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
					</label>
					<div class="col-sm-8 col-md-6">
						<img id="shareQRImage" src="<?= base_url() . 'common/qrcode?w=200&m=1&t=' . $shareContent ?>">
					</div>
				</div>
				
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>