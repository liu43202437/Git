<?php if ($item['type'] == 0): ?>
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
	max-width: 1000px;
	_max-width: 640px;
	margin: 20px auto;
}
img {
	max-width: 100% !important;
	border-radius: 6px;
}
.publish {
	text-align: center;
	font-size: 12px;
	color: #999;
}

.video_iframe {
//    position: absolute;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
}


.article-entry {
	position: relative;
	padding-bottom: 56.25%;
	padding-top: 30px; 
	height: 0; 
	//overflow: hidden;
}
 
.article-entry .video-container iframe,
.article-entry .video-container object,
.article-entry .video-container embed {
	//position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
</style>
<script type="text/javascript">
$().ready(function() {

	$("#container img").lazyload({
		threshold: 100,
		effect: "fadeIn",
		skip_invisible: false
	});
	
	if (!String.prototype.startsWith) {
		String.prototype.startsWith = function(searchString, position){
			position = position || 0;
			return this.substr(position, searchString.length) === searchString;
		};
	}

	function getFullUrl(url) {
		if (!url.startsWith("http")) {
			url = '<?= base_url() ?>' + url;
		}
		return url;
	}
	
	var imgs = new Array();
	$("#container img").each(function() {
		var url = getFullUrl($(this).attr("src"));
		imgs.push(url);
	});
	
	$("#container img").click(function(){
		if (typeof android_app != 'undefined' && 
		$(this).parent().prop('tagName') != 'A' && 
		$(this).parent().parent().prop('tagName') != 'A' && 
		$(this).attr("src").substr(0,5) != "data:") {
		    var i = 0;
		    for (i = 0; i < imgs.length; i++) {
		        if(imgs[i] == getFullUrl($(this).attr("src"))) break;
		    }
		    if(typeof android_app.showImgIndex != 'undefined'){
		        android_app.showImgIndex(imgs, i.toString());
		    }else if(typeof android_app.setImgIndex != 'undefined'){
		        android_app.setImgIndex(imgs, i.toString());
		    }
		}
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
	<h2 style="text-align:center;"><?= $item['title'] ?></h2>
	<div class="publish">
		<?= $item['content_date'] ?>&nbsp;&nbsp;
		<?php if (!empty($item['author'])): ?>
		编辑：<?= $item['author'] ?>
		<?php endif; ?>
	</div>
	<div class="article-entry" style="width: 100%; margin-top: 30px;"><div class="video-container"> <?= $item['content'] ?></div></div>
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
<?php else: ?>
<script type="text/javascript">
location.href='<?= $item['link'] ?>';
</script>
<?php endif; ?>
