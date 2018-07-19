<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= $item['name'] ?></title>
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
.publish {
	text-align: center;
	font-size: 12px;
	color: #999;
}

p.top {
	text-align: center;
	background: #4E473D;
	background: -webkit-linear-gradient(#020202, #a0a0a0);
	background:    -moz-linear-gradient(#020202, #a0a0a0);
	background:         linear-gradient(#020202, #a0a0a0);
	padding: 30px 20px;
}

span.score {
	display: block;
	margin-top: 10px;
	font-weight: normal;
	font-size: 14px;
}

table {
	width: 100%;
	font-size: 14px;
}
table tr {
	
}
table th {
	padding: 0;
	text-align: right;
	border-top: 1px solid #e0e0e0;
	line-height: 40px;
	height: 40px;
}
table td {
	text-align: left;
	border-top: 1px solid #e0e0e0;
	line-height: 40px;
	height: 40px;
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
	<p class="top">
		<img src="<?= getFullUrl($item['image']) ?>" alt="" style="/*border-radius:50%;*/ max-width:200px; max-height:200px;">
		<img style="float: right;" width="50" height="30" class="flag" src="<?= base_url() . 'resources/images/flags/' . $item['country_id'] . '.jpg' ?>" alt="">
	</p>
	<h3 style="text-align:center;">
		<?= $item['name'] ?>
		<?php if (!empty($item['en_name'])): ?>
		(<?= $item['en_name'] ?>) 
		<?php endif; ?>
		<?php if ($item['kind'] == MEMBER_KIND_PLAYER): ?>
		<span class="score">
			<?= intval($item['score_win']) ?>胜 - <?= intval($item['score_loss']) ?>败 - <?= intval($item['score_draw']) ?>平  <?= intval($item['score_ko']) ?>KO
		</span>
		<?php endif; ?>
	</h3>
	<div style="width: 100%; text-align: center;">
		<table class="table">
			<tr>
				<th>身高：</th><td><?= $item['height'] ?>cm</td>
				<th>年龄：</th><td>
					<?php if (!empty($item['birthday'])): ?>
						<?= nowyear() - date("Y", strtotime($item['birthday'])) ?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>				
				<?php if ($item['kind'] == MEMBER_KIND_PLAYER): ?>
					<th>量级：</th><td><?= getPlayerWeightLevel($item['weight']) ?></td>
				<?php else: ?>
					<th>体重：</th><td><?= $item['weight'] ?>kg</td>
				<?php endif; ?>
				
				<th>国家：</th><td><?= $item['country'] ?></td>
			</tr>
			<?php if ($item['kind'] == MEMBER_KIND_PLAYER): ?>
			<tr>
				<th>体系：</th><td><?= $item['level'] ?></td>
				<th>绰号：</th><td><?= $item['nickname'] ?></td>
			</tr>
			<?php else: ?>
			<tr>
				<th>学历：</th><td colspan="3"><?= $item['education'] ?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>认证编号：</th><td colspan="3"><?= $item['cert_number'] ?></td>
			</tr>
			<tr>
				<th style="vertical-align: top;">简介：</th>
				<td colspan="3"></td>
			</tr>			
			<tr>
				<td colspan="4" style="border-top:none; padding-left: 40px;"><?= $item['introduction'] ?></td>
			</tr>
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