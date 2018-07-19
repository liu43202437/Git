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
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");
});
</script>
</head>
<body>
	<div class="content-wrapper config">
		<div class="title-bar">基本设置</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li><a href="basis">烟币，经验</a></li>
				<li><a href="about">关于</a></li>
				<li><a href="vocabulary">屏蔽词汇</a></li>
				<li class="active"><a href="javascript:;">点击量</a></li>
				<li><a href="search_word">搜索文字</a></li>
			</ul>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_hits" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						文章点击：
					</label>
					<div class="col-sm-3 col-md-2">
						<input type="number" class="form-control" name="article_hits" value="<?= $articleHits ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						相册点击：
					</label>
					<div class="col-sm-3 col-md-2">
						<input type="number" class="form-control" name="gallery_hits" value="<?= $galleryHits ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						视频点击：
					</label>
					<div class="col-sm-3 col-md-2">
						<input type="number" class="form-control" name="video_hits" value="<?= $videoHits ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						图文点击：
					</label>
					<div class="col-sm-3 col-md-2">
						<input type="number" class="form-control" name="live_hits" value="<?= $liveHits ?>">
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