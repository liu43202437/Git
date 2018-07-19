<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>关于</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.lazyload.js"></script>
<style type="text/css">
#container {
	max-width: 1000px;
	_max-width: 640px;
	margin: 20px auto;
}
img {
	max-width: 100%;
	border-radius: 10px;
}
</style>
<script type="text/javascript">
$().ready(function() {
});
</script>
</head>
<body>
<div id="container">
	<div style="width: 100%; margin-top: 30px;">
		<?= $content ?>
	</div>
</div>
</body>
</html>