<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>中维合众</title>

	<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">

	<style type="text/css">
	html, body {
		width: 100%;
		height: 100%;
		margin: 0;
	}
	
	a {
		text-decoration: none;
	}
	
	body {
		background: url('resources/images/bb.jpeg') top no-repeat;
		background-size: 100% auto;
		position: relative;
		background-color: #b4b5b9;
	}
	
	header {
		height: 45px;
		line-height: 45px;
		padding: 30px;
	}
	header img {
		float: left;
		/*box-shadow: 0px 0px 10px 2px #fff;
		border-radius: 10px;*/
	}
	header .logo-text {
		display: none;
		float: left;
		font-size: 32px;
		color: #E01919;
		font-weight: 800;
		margin-left: 15px;
		font-style: italic;
		text-shadow: 2px 2px 2px #fff;
	}
	
	footer {
		position: fixed;
		width: 100%;
		bottom: 40px;
		text-align: center;
		line-height: 20px;
		font-size: 14px;
		color: #fff; 
	}
	
	footer a {
		width: 160px;
		height: 50px;
		line-height: 50px;
		display: inline-block;
		font-size: 16px;
		border-radius: 25px;
		background: #fff;
		color: #222;
		opacity: .9;
		filter: alpha(opacity=90);
		text-align: center;
		margin-left: 80px;
	}
	footer a:first-child {
		margin-left: 0;
	}
	
	footer div.actions {
		margin-bottom: 130px;
		position: relative;
	}
	
	footer div.qrcode {
		position: absolute;
		background: #fff;
		padding: 0px;
		border: 3px solid #999;
		border-radius: 5px;
		display: none;
		margin-left: 20px;
		margin-top: -60px;
	}
	
	a#btn_qrcode:hover ~ div.qrcode {
		display: inline-block;
	} 
	
	</style>
	
	<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
	<script type="text/javascript">
	
	</script>
</head>
<body>
<header>
<!--	<img src="resources/images/logo2.png" alt="" height="60" width="auto"> -->
<!--	<span class="logo-text">踢 王 决</span>-->
</header>
<footer>
	<div class="actions">
		<a href="<?= $iphone ?>"><i class="fa fa-apple m-r-xs"></i> IOS下载</a>
		<a href="<?= $android ?>"><i class="fa fa-android m-r-xs"></i> 安卓下载</a>
		<a href="javascript:;" id="btn_qrcode"><i class="fa fa-qrcode m-r-xs"></i> 扫描下载</a>
		<div class="qrcode">
			<img class="img-thumbnail" src="<?=base_url().'common/qrcode?w=150&t='.$share?>">
		</div>
	</div>
	
<!--	<div class="copyright">Copyright @ 2017 北京冠军人生教育科技有限公司 版权所有  |  京ICP备17005746号</div>-->
</footer>
</body>
</html>