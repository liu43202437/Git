<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= $item['title'] ?></title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.lazyload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/share.js"></script>
<link href="<?=base_url()?>resources/css/share.css" rel="stylesheet">
<style type="text/css">
#container {
	_max-width: 1000px;
	max-width: 640px;
	margin: 20px auto;
}
img {
	max-width: 100%;
	border-radius: 6px;
}
video {
	width: 100%;
	max-width: 100%;
}
h3.title {
	text-align:center;
	margin: 20px 0 10px;
}
div.subtitle {
	text-align:center;
	margin-bottom: 5px;
}
div.publish {
	text-align: center;
	font-size: 12px;
	color: #999;
}
div.counterparts {
	margin-top: 10px;
	margin-bottom: 30px;
}
div.counterparts img {
	max-width: 100px;
	max-height: 100px;
}
</style>
<script type="text/javascript">
$().ready(function() {

	$("#container img").lazyload({
		threshold: 100,
		effect: "fadeIn",
		skip_invisible: false
	});
	
});

function onBtnDown() {
	var app_url = "http://47.92.37.141/upload/binary/20170704/1499183841.apk";
	if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i)) {
		app_url = "https://itunes.apple.com/us/app/中维合众/id1220720568?l=zh&ls=1&mt=8";
	}
	
	window.location = app_url;	
}
</script>
</head>
<body>
<?php if (!empty($mobile) && $mobile == 1): ?>
<section class="share_banner">
	<a href="javascript:onBtnDown()">
		<img src="<?=base_url()?>resources/images/logo.png" width="50px">
		<ul class="share_slogan">
			<li class="ss_logo"><img src="<?=base_url()?>resources/images/tone.png" width="70px"></li>
			<li class="ss_slogan"><span>世界级跆拳道职业联赛</span></li>
		</ul>
		<div class="share_down"><span>立即下载</span></div>
	</a>
</section>
<?php endif; ?>
<div id="container">
	<h3 class="title">
		<?= $item['title'] ?>
	</h3>
	<?php if (!empty($item['subtitle'])): ?>
		<div class="subtitle">
			<?= $item['subtitle'] ?>
		</div>
	<?php endif; ?>
	<div class="publish">
		<?= $item['event_date'] ?>&nbsp;&nbsp;
		<?= $item['location'] ?>
	</div>
	<div style="width: 100%; text-align: center;">
		<p>
			<video src="<?= getFullUrl($item['video']) ?>" autoplay poster="<?= getFullUrl($item['image']) ?>">
			Sorry, your browser doesn't support embedded videos, 
			but don't worry, you can <a href="<?= getFullUrl($item['video']) ?>">download it</a>
			and watch it with your favorite video player!
			</video>
		</p>
	</div>
	<div class="counterparts">
	<table style="width: 100%;">
		<?php foreach ($item['counterparts'] as $cpart): ?>
			<tr>
				<td>
					<a target="_blank" href="<?=getPortalUrl($cpart['a_player_id'], PORTAL_KIND_MEMBER)?>"><img src="<?= $cpart['a_player_image'] ?>"></a>
				</td>
				<td style="text-align:center; vertical-align:bottom;">VS</td>
				<td style="text-align: right;">
					<a target="_blank" href="<?=getPortalUrl($cpart['a_player_id'], PORTAL_KIND_MEMBER)?>"><img src="<?= $cpart['b_player_image'] ?>"></a>
				</td>
			</tr>
			<tr>
				<td><?= $cpart['a_player_name'] ?></td>
				<td></td>
				<td style="text-align: right; padding-bottom: 20px;"><?= $cpart['b_player_name'] ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	</div>
</div>
<?php if (!empty($mobile) && $mobile == 1): ?>
<table class="downbar3">
	<tbody>
		<tr>
			<td width="1"><img src="<?=base_url()?>resources/images/logo3.png"></td>
			<td>更多精彩尽在中维合众APP</td>
			<td width="1" align="right"><button onclick="javascript:onBtnDown()">立即下载</button></td>
	    </tr>
	</tbody>
</table>
<?php endif; ?>
</body>
</html>