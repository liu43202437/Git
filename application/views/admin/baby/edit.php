<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?>图文
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
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
	
	// 表单验证
	$inputForm.validate({
		rules: {
			title: {
				required: true
			},
			image: {
				required: true
			}
		},
		messages: {
			
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var ratioWidth = 16;
	var ratioHeight = 11;
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#mainImage",
		resultElement: "#mainImageUrl",
		fileType: "image",
		ratioWidth: 0,
		ratioHeight: 0
	});
	
	<?php if ($isNew): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	$("input[name=platform]").eq(0).iCheck("check");
	<?php endif; ?>
	$("#ratio").text(ratioWidth + "：" + ratioHeight);
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?>图文
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save" method="post" class="form-horizontal">
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						标&nbsp;&nbsp;题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" id="title" name="title" class="form-control" value="<?= $isNew ? '' : $itemInfo['title']?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						日&nbsp;&nbsp;期：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" class="form-control Wdate Wdate-YMDhms pull-left" id="babyDate" name="baby_date" value="<?= $isNew ? '' : d2dtns($itemInfo['baby_date']) ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm'});" placeholder="选择时间">
						<a role="button" class="clear-time" href="javascript:;" onclick="$(this).siblings('input').val('');">清除时间</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						图&nbsp;&nbsp;片：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="image-wrapper m-r im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['image']) ?>">
							<input id="mainImageUrl" name="image" type="hidden" value="<?= $isNew ? '' : $itemInfo['image'] ?>">
							<div class="loading">
								<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						点击次数：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="number" name="hits" class="form-control" value="<?= $isNew ? '' : $itemInfo['hits'] ?>" min="0"/>
					</div>					
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						额外增加的点击次数
					</div>
				</div>
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
						<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
			<div class="hidden">
				<form id="uploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileMainImg">
				</form>
			</div>
		</div>
	</div>
</body>
</html>