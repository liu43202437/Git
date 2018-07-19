<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>权限管理</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");
	
	var $parent = $("input.parent");
	var $children = $("input.child");
	
	$parent.on("ifChanged", function() {
		var $this = $(this);
		var key = $this.data("key");
		var $myChildren = $children.filter("[data-key='" + key + "']");
		if ($this.prop("checked")) {
			$myChildren.iCheck("check");
		} else {
			$myChildren.iCheck("uncheck");
		}
	});
	
	$children.on("ifChanged", function() {
		var $this = $(this);
		var key = $this.data("key");
		var $myParent = $parent.filter("[data-key='" + key + "']");
		if ($this.prop("checked")) {
			var $siblings = $children.filter("[data-key='" + key + "']");
			if ($siblings.length == $siblings.filter(":checked").length) {
				$myParent.prop("checked", true);
			}
		} else {
			$myParent.prop("checked", false);
		}
		$myParent.iCheck("update");
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">权限管理 ：<?= $itemInfo['username'] ?></div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_role" method="post" class="form-horizontal">
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				
				<?php foreach ($roles as $key=>$subRoles): ?>
					<div class="form-group">
						<div class="col-sm-12">
							<input type="checkbox" class="parent i-check" data-key="<?=$key?>"> <?=$subRoles['label']?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 col-sm-12 col-md-8 m-l-md">
							<?php foreach ($subRoles as $k=>$item): ?>
								<?php if ($k == 'label') continue; ?>
								<div class="col-xs-4 col-sm-3 m-b-sm">
									<input type="checkbox" name="roles[]" class="child i-check" data-key="<?=$key?>" value="<?=$item['name']?>" <?php if ($item['isPermit']): ?>checked="checked"<?php endif; ?> /> <?=$item['label']?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
				
				<div class="form-group m-t-lg">
					<div class="m-l-xl">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
						<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>