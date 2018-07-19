<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>栏目名称</title>
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
	<div class="content-wrapper">
		<div class="title-bar">栏目名称</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_menu_item" method="post" class="form-horizontal">
				<div class="form-group">
					<h3 class="col-xs-12 col-sm-6 col-md-4 text-left p-xxs b-b">
						热&nbsp;&nbsp;门
					</h3>
				</div>
				<div class="form-group">
					<label class="col-xs-6 col-sm-3 col-md-2 text-left">
						名&nbsp;&nbsp;称
					</label>
					<label class="col-xs-6 col-sm-3 col-md-2 text-center">
						内容分类
					</label>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="top_news_label" value="<?= $top_news['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<select class="form-control" name="top_news_category">
						<option value=""></option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category['id'] ?>" <?php if ($top_news['category'] == $category['id']):?>selected="selected"<?php endif; ?>><?= $category['name'] ?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="video_links_label" value="<?= $video_links['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<select class="form-control" name="video_links_category">
						<option value=""></option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category['id'] ?>" <?php if ($video_links['category'] == $category['id']):?>selected="selected"<?php endif; ?>><?= $category['name'] ?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="videos_label" value="<?= $videos['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<select class="form-control" name="videos_category">
						<option value=""></option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category['id'] ?>" <?php if ($videos['category'] == $category['id']):?>selected="selected"<?php endif; ?>><?= $category['name'] ?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="babys_label" value="<?= $babys['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2 hidden">
						<select class="form-control" name="babys_category">
						<option value=""></option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category['id'] ?>" <?php if ($babys['category'] == $category['id']):?>selected="selected"<?php endif; ?>><?= $category['name'] ?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				
				<div class="form-group m-t-lg">
					<h3 class="col-xs-12 col-sm-6 col-md-4 text-left p-xxs b-b">
						赛&nbsp;&nbsp;事
					</h3>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="recent_events_label" value="<?= $recent_events['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="past_events_label" value="<?= $past_events['label'] ?>">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="live_videos_label" value="<?= $live_videos['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="event_matchs_label" value="<?= $event_matchs['label'] ?>">
					</div>
				</div>
				
				<div class="form-group m-t-lg">
					<h3 class="col-xs-12 col-sm-6 col-md-4 text-left p-xxs b-b">
						附&nbsp;&nbsp;近
					</h3>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="club_label" value="<?= $club['label'] ?>">
					</div>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="users_label" value="<?= $users['label'] ?>">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-sm-3 col-md-2">
						<input class="form-control text-center" name="chat_label" value="<?= $chat['label'] ?>">
					</div>
				</div>
				
				<div class="form-group m-t-lg">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>