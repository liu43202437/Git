<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?>排行
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/ajax-chosen.js"></script>
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
			name: {
				required: true
			}
		},
		messages: {
			
		}
	});

	for (var i = 1; i <= 15; i++) {
		var id = '#member_id_' + i;
		// member ids
		$(id).ajaxChosen({
			minTermLength: 1,
		    type: 'GET',
		    url: "<?= base_url() ?>admin/member/ajax_list",
		    data: {kind: '<?= MEMBER_KIND_PLAYER ?>'},
		    dataType: 'json'
		}, function (data) {
		    var results = [];
			$.each(data, function (i, val) {
				results.push({ value: val.id, text: val.label });
			});
			return results;
		});
	}
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?>排行
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_ranking" method="post" class="form-horizontal">
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						标&nbsp;&nbsp;题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="name" class="form-control" value="<?= $itemInfo['name']?>"/>
					</div>
				</div>
				<?php foreach ($memberList as $key=>$item): ?>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							<?= $item['index'] ?>：
						</label>
						<div class="col-sm-6 col-md-4">
							<select name="member_id_<?=$item['index']?>" id="member_id_<?=$item['index']?>" class="form-control" data-placeholder="请输入姓名或认证编号">
								<?php if (!empty($item['id'])): ?>
								<option value="<?= $item['id'] ?>" selected="selected">[<?= $item['id'] ?>] <?= $item['name'] ?></option>
								<?php endif; ?>
							</select>
						</div>
						<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						</div>
					</div>
				<?php endforeach; ?>
				
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
						<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>