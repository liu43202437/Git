<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= $item['view_name'] ?></title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.lazyload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/share.js"></script>
<link href="<?=base_url()?>resources/css/share.css" rel="stylesheet">
<style type="text/css">
body {
	background: #f5f5f5;
	margin: 0;
}
#container {
	max-width: 1000px;
	_max-width: 640px;
	margin: 0 auto 30px;
}
img {
	max-width: 100%;
	border-radius: 6px;
}

div.top {
	border-bottom: 1px solid #eee;
	padding: 15px;
	background: #fff;
}
div.top > div {
	margin: 0 auto 20px;
	width: 400px;	
}
div.top img {
	float: left;
	max-width: 180px;
	max-height: 180px;
	margin-right: 15px;
}
div.top > div > div {
	float: left;
	line-height: 30px;
}
div.top h3 {
	margin: 15px 0 5px;
}

div.publish {
	clear: both;
	text-align: center;
	font-size: 14px;
	color: #999;
	margin-top: 10px;
	line-height: 35px;
	height: 35px;
	background: #fff;
	border-top: 1px solid #eee;
	border-bottom: 1px solid #eee;
}

div.gallery {
	clear: both;
	margin-top: 10px;
	background: #fff;
	border-top: 1px solid #eee;
	border-bottom: 1px solid #eee;
}
div.gallery a {
	display: inline-block;
	width: 46%;
	max-height: 300px;
	margin: 1.5%;
}
div.gallery a img {
	max-height: 300px;
}

div.service_time, div.location, div.introduction {
	clear: both;
	font-size: 14px;
	margin-top: 10px;
	line-height: 35px;
	height: 35px;
	background: #fff;
	border-top: 1px solid #eee;
	border-bottom: 1px solid #eee;
	text-align: center;
}
label {
	color: #333;
	font-weight: 600;
}
div.service_time span, div.location span, div.introduction span {
	color: #999;
}

div.introduction {
	text-align: left;
	padding-left: 20px;
}
div.introduction2 {
	background: #fff;
	min-height: 35px;
	padding: 5px 15px;
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
	<div class="top">
		<div>
			<img src="<?= getFullUrl($item['logo']) ?>" alt="">
			<div>
				<h3><?= $item['view_name'] ?></h3>
				<div><?= $item['city'] ?></div>
			</div>
		</div>
		<p style="clear:both;"></p>
	</div>
	
	<div class="publish">
		<label>联系人：</label><?= $item['contact'] ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<label>联系电话：</label><?= $item['contact_phone'] ?>
	</div>
	<div class="location">
		<label>详细地址：</label>
		<span><?= $item['address'] ?></span>
	</div>
	<div class="service_time">
		<label>营业时间：</label>
		<span><?= $item['service_time'] ?></span>
	</div>
	
	<?php if (!empty($item['images'])): ?>
	<div class="gallery">
<?php foreach ($item['images'] as $image): ?>
<a href="<?= getFullUrl($image['image']) ?>" target="_blank"><img src="<?= getFullUrl($image['image']) ?>"></a>
<?php endforeach; ?>
		<div style="clear:both"></div>
	</div>
	<?php endif; ?>
	
	<div class="introduction">
		<label>道馆简介：</label>
	</div>
	<div class="introduction2">
		<?= $item['introduction'] ?>
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