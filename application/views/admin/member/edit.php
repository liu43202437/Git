<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?><?= getMemberKind($kind) ?>
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/summernote.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/lang/summernote-zh-CN.js"></script>
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
			},
			en_name: {
				required: true
			},
			description: {
				required: true
			},
			image: {
				required: true
			},
			height: {
				min: 50,
				max: 250
			}
		},
		messages: {
			
		},
		submitHandler: function(form) {
			return true;
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var ratioWidth = 1;
	var ratioHeight = 1;
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#mainImage",
		resultElement: "#mainImageUrl",
		fileType: "image",
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight
	});

	<?php if (empty($itemInfo['image'])): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	<?php endif; ?>
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);
	
	// country
	$("select[name=country_id]").chosen();
	
	/*$("input[name='name']").on('input', function() {
		$("input[name='en_name']").val("");
	});*/
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?><?= getMemberKind($kind) ?>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="../save" method="post" class="form-horizontal">
				<input type="hidden" name="kind" value="<?=$kind?>" />
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>
				<?php if (isset($auditItemId)): ?>
				<input type="hidden" name="audit_item_id" value="<?=$auditItemId?>" />
				<?php endif; ?>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						姓&nbsp;&nbsp;名：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="name" class="form-control" value="<?= isset($itemInfo['name']) ? $itemInfo['name'] : ''?>"/>
					</div>
				</div>
				<?php if ($kind == MEMBER_KIND_PLAYER): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						英文名(拼音)：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="en_name" class="form-control" value="<?= isset($itemInfo['en_name']) ? $itemInfo['en_name'] : ''?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						战&nbsp;&nbsp;绩：
					</label>
					<div class="col-sm-8 col-md-4">
						<div class="col-xs-3" style="padding:0 8px 0 0;">
							<div class="input-group">
								<input type="text" class="form-control" name="score_win" value="<?= $itemInfo['score_win'] ?>">
								<div class="input-group-addon">胜</div>
							</div>
						</div>
						<div class="col-xs-3" style="padding:0 8px 0 0;">
							<div class="input-group">
								<input type="text" class="form-control" name="score_loss" value="<?= $itemInfo['score_loss'] ?>">
								<div class="input-group-addon">负</div>
							</div>
						</div>
						<div class="col-xs-3" style="padding:0 8px 0 0;">
							<div class="input-group">
								<input type="text" class="form-control" name="score_draw" value="<?= $itemInfo['score_draw'] ?>">
								<div class="input-group-addon">平</div>
							</div>
						</div>
						<div class="col-xs-3" style="padding:0;">
							<div class="input-group">
								<input type="text" class="form-control" name="score_ko" value="<?= $itemInfo['score_ko'] ?>">
								<div class="input-group-addon">KO</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						备&nbsp;&nbsp;注：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="description" class="form-control" value="<?= isset($itemInfo['description']) ? $itemInfo['description'] : '' ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						相&nbsp;&nbsp;片：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="image-wrapper im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="<?= getFullUrl($itemInfo['image']) ?>">
							<input id="mainImageUrl" name="image" type="hidden" value="<?= $itemInfo['image'] ?>">
							<div class="loading">
								<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
							</div>
						</div>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						支持jpg、png格式
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						认证编号：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="cert_number" class="form-control" value="<?= $isNew ? '' : $itemInfo['cert_number']?>"/>
					</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-md-2 control-label">
                        身份证：
                    </label>
                    <div class="col-sm-6 col-md-4">
                        <input type="text" name="idcard" class="form-control" value="<?= isset($itemInfo['idcard']) ? $itemInfo['idcard'] : ''?>"/>
                    </div>
                </div>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						出生日期：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" class="form-control Wdate Wdate-YMDhms pull-left" name="birthday" value="<?= isset($itemInfo['birthday']) ? $itemInfo['birthday'] : '' ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: new Date()});" placeholder="选择时间">
						<a role="button" class="clear-time" href="javascript:;" onclick="$(this).siblings('input').val('');">清除时间</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						身&nbsp;&nbsp;高：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="height" class="form-control" value="<?= isset($itemInfo['height']) ? $itemInfo['height'] : ''?>"/>
					</div>
				</div>
				<?php if ($kind == MEMBER_KIND_PLAYER): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						量&nbsp;&nbsp;级：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="weight">
							<?php global $PlayerWeightLevels;
							foreach ($PlayerWeightLevels as $weight=>$level):?>
							<option value="<?= $weight ?>" <?php if (intval($itemInfo['weight']) == $weight): ?>selected="selected"<?php endif; ?>><?= $level ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						体&nbsp;&nbsp;系：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="level">
							<?php global $PlayerLevels;
							foreach ($PlayerLevels as $value):?>
							<option value="<?= $value ?>" <?php if ($value == $itemInfo['level']): ?>selected="selected"<?php endif; ?>><?= $value ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<?php else: ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						体&nbsp;&nbsp;重：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="weight" class="form-control" value="<?= isset($itemInfo['weight']) ? $itemInfo['weight'] : ''?>"/>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						学&nbsp;&nbsp;历：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="education">
							<option value="大学" <?php if (isset($itemInfo['education']) && $itemInfo['education'] == '大学'): ?>selected="selected"<?php endif; ?>>大学</option>
							<option value="本科" <?php if (isset($itemInfo['education']) && $itemInfo['education'] == '本科'): ?>selected="selected"<?php endif; ?>>本科</option>
						</select>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						绰&nbsp;&nbsp;号：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="nickname" class="form-control" value="<?= isset($itemInfo['nickname']) ? $itemInfo['nickname'] : ''?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						国&nbsp;&nbsp;家：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="country_id" data-placeholder="请选择省份...">
							<?php foreach ($countries as $country): ?>
								<option value="<?= $country['id'] ?>" <?php if (isset($itemInfo['country_id']) && $itemInfo['country_id'] == $country['id']): ?>selected="selected"<?php endif; ?>><?= $country['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						性&nbsp;&nbsp;别：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="gender">
							<option value="<?= GENDER_MALE ?>" <?php if (isset($itemInfo['gender']) && $itemInfo['gender'] == GENDER_MALE): ?>selected="selected"<?php endif; ?>><?= getUserGender(GENDER_MALE) ?></option>
							<option value="<?= GENDER_FEMALE ?>" <?php if (isset($itemInfo['gender']) && $itemInfo['gender'] == GENDER_FEMALE): ?>selected="selected"<?php endif; ?>><?= getUserGender(GENDER_FEMALE) ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						手&nbsp;&nbsp;机：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="mobile" class="form-control" value="<?= isset($itemInfo['mobile']) ? $itemInfo['mobile'] : ''?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						地&nbsp;&nbsp;址：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="address" class="form-control" value="<?= isset($itemInfo['address']) ? $itemInfo['address'] : ''?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						是否服过兵役：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="military_serve">
							<option value="0">未知</option>
							<option value="2" <?php if (isset($itemInfo['military_serve']) && $itemInfo['military_serve'] == 1): ?>selected="selected"<?php endif; ?>>是</option>
							<option value="1" <?php if (isset($itemInfo['military_serve']) && $itemInfo['military_serve'] == 2): ?>selected="selected"<?php endif; ?>>否</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						简&nbsp;&nbsp;介：
					</label>
					<div class="col-sm-9 col-md-6">
						<textarea class="hidden" name="introduction"><?= isset($itemInfo['introduction']) ? $itemInfo['introduction'] : '' ?></textarea>
						<div id="summernote"><?= isset($itemInfo['introduction']) ? $itemInfo['introduction'] : '' ?></div>
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
