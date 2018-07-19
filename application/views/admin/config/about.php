<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>基本设置</title>
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
<script type="text/javascript" src="<?=base_url()?>resources/plugins/kindeditor/kindeditor.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");
	$inputForm.validate({
		rules: {
			about_content: {
				required: true
			}
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper config">
		<div class="title-bar">基本设置</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li><a href="basis">烟币，经验</a></li>
				<li class="active"><a href="javascript:;">关于</a></li>
				<li><a href="vocabulary">屏蔽词汇</a></li>
				<li><a href="hits">点击量</a></li>
				<li><a href="search_word">搜索文字</a></li>
			</ul>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_about" method="post" class="form-horizontal">
				<div class="form-group">
					<div class="col-sm-10 col-md-8">
						<textarea id="editor" name="about_content" class="editor" style="width:100%;"><?= $aboutContent ?></textarea>
					</div>
				</div>
				
				<div class="form-group m-t-lg">
					<div class="col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>