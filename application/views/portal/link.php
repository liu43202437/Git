<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= $item['source'] ?></title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.lazyload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/share.js"></script>
<link href="<?=base_url()?>resources/css/share.css" rel="stylesheet">
<style type="text/css">
#container {
	max-width: 1000px;
	_max-width: 640px;
	margin: 20px auto;
}
img {
	max-width: 100%;
	border-radius: 6px;
}
video {
	max-width: 100%;
}
.publish {
	text-align: center;
	font-size: 12px;
	color: #999;
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
	<h2 style="text-align:center;">
		<?= $item['source'] ?>
	</h2>
	<div class="publish">
		<?= $item['link_date'] ?>
	</div>
	<div style="width: 100%;">
		<p>
			<img src="<?= getFullUrl($item['image']) ?>">
		</p>
		<p>
			<?php foreach ($item['contents'] as $content): ?>
				<a href="<?= $content['url'] ?>"><?= $content['title'] ?></a><br/>
			<?php endforeach; ?>
		</p>
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