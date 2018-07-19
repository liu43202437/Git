<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>添加联盟报名</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/summernote.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/lang/summernote-zh-CN.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">

function getAuditType(type)
{
	if (type == "") {
		return "";
	}
	<?php global $AuditValueTypes;
	foreach ($AuditValueTypes as $type=>$label): 
	?>
	else if (type == "<?= $type ?>") {
		return '<?= $label ?>';
	}
	<?php endforeach; ?>
}

$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var configs = [
		<?php foreach ($configs as $item): ?>
		{
			id: '<?= $item['id'] ?>',
			attr_name: '<?= $item['attr_name'] ?>',
			attr_label: '<?= $item['attr_label'] ?>',
			attr_hint: '<?= $item['attr_hint'] ?>',
			value_type: '<?= $item['value_type'] ?>',
			target_field: '<?= $item['target_field'] ?>'
			<?php if ($item['value_type'] == 'select'): ?>
			,values: [
			<?php $values = explode('|', $item['values']);
			foreach ($values as $value): ?>
				'<?= $value ?>',
			<?php endforeach; ?>
			]
			<?php endif; ?>
		},
		<?php endforeach; ?>
	];

	
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
		submitHandler: function(form) {
			if (configs == null || configs.length == 0) {
				//$.message("error", "选项不能为空！");
				//return false;
			}
			$("input[name=configs]").val(JSON.stringify(configs));
			return true;
		}
	});

	var ratioWidth = 10;
	var ratioHeight = 6;
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#mainImage",
		resultElement: "#mainImageUrl",
		fileType: "image",
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight,
		ratioScope: 0.5
	});
	
	<?php if ($isNew): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	<?php endif; ?>
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);
	
	
	// edit audit config
	var $dlgEdit = $("#dlgEdit");
	
	function clearDlgInfo() {
		$("#valueWrapper").empty();
		$(".tab-btn").removeClass("active");
		$(".tab-pane").removeClass("active");
		$("input[name=r_value_type]").filter("[value=date]").iCheck("check");
		$("select[name=i_value_type]").val("string");
		
		$dlgEdit.find("#index").val(-1);
		$dlgEdit.find("#attrLabel").val("");
		$dlgEdit.find("#attrHint").val("");
		$dlgEdit.find("#valueType").val("string");
		$dlgEdit.find("#targetField").val("");
		$dlgEdit.find("input[name^=values]").val("");
	}
	
	$("#addValue").click(function() {
		var html = '<div class="form-group">';
		html += '<label class="control-label col-xs-3">选择' + ($("#valueWrapper").children().length + 3) + '：</label>';
		html += '<div class="col-xs-8">';
		html += '<input type="text" class="form-control" name="values[]">';
		html += '</div>';
		html += '</div>';
		$("#valueWrapper").append(html);
	});
	
	$("#addButton").click(function() {
		clearDlgInfo();
		$dlgEdit.find(".modal-title").text("添加报名选项");
		$(".tab-btn").eq(0).addClass("active");
		$(".tab-pane").eq(0).addClass("active");
		$dlgEdit.modal('show');
	});
	
	$("#listConfigs").on("click", ".editItemBtn", function() {
		clearDlgInfo();
		var $parent = $(this).parents("tr");
		index = $parent.index() - 5;
		$dlgEdit.find(".modal-title").text("编辑报名选项");
		$dlgEdit.find("#index").val(index);
		$dlgEdit.find("#attrLabel").val(configs[index].attr_label);
		$dlgEdit.find("#attrHint").val(configs[index].attr_hint);
		$dlgEdit.find("#valueType").val(configs[index].value_type);
		$dlgEdit.find("#targetField").val(configs[index].target_field);
		
		var valueType = configs[index].value_type;
		if (valueType == 'string' || valueType == 'integer' || valueType == 'float') {
			$(".tab-btn").eq(0).addClass("active");
			$(".tab-pane").eq(0).addClass("active");
			$("select[name=i_value_type]").val(valueType);
		} else if (valueType == 'select') {
			$(".tab-btn").eq(1).addClass("active");
			$(".tab-pane").eq(1).addClass("active");
			for (var i = 0; i < configs[index].values.length; i++) {
				if (i >= 2) {
					$("#addValue").click();
				}
				$dlgEdit.find("input[name^=values]").eq(i).val(configs[index].values[i]);
			}
		} else {
			$(".tab-btn").eq(2).addClass("active");
			$(".tab-pane").eq(2).addClass("active");
			$("input[name=r_value_type]").filter("[value=" + valueType + "]").iCheck("check");
		}
		$dlgEdit.modal('show');
	});
	
	$("#listConfigs").on("click", ".deleteItemBtn", function() {
		var $parent = $(this).parents("tr");
		configs.splice($parent.index() - 5, 1);
		$parent.remove();
	});
	
	$("input[name=r_value_type]").on("ifChanged", function() {
		$dlgEdit.find("#valueType").val($(this).val());
	});
	
	$("select[name=i_value_type]").change(function() {
		$dlgEdit.find("#valueType").val($(this).val());
	});
	
	$(".tab-btn").click(function() {
		var index = $(this).index();
		var valueType = 'string';
		if (index == 0) {
			valueType = $("select[name=i_value_type]").val();
		} else if (index == 1) {
			valueType = 'select';
		} else {
			valueType = $("input[name=r_value_type]:checked").val();
		}
		$dlgEdit.find("#valueType").val(valueType);
	});
	
	var $editForm = $("#editForm");
	$editForm.validate({
		rules: {
			attr_label: {
				required: true,
				minlength: 1,
				maxlength: 50
			}
		},
		submitHandler: function(form) {
			if ($dlgEdit.find("#valueType").val() == 'select') {
				valueCount = 0;
				$dlgEdit.find("input[name^=values]").each(function() {
					if ($.trim($(this).val()).length > 0) {
						valueCount++;
					}
				});
				if (valueCount <= 1) {
					alert('请输入2以上选择项！');
					return false;
				}
			}
		
			var index = $dlgEdit.find("#index").val();
			var attrLabel = $dlgEdit.find("#attrLabel").val();
			var attrHint = $dlgEdit.find("#attrHint").val();
			var valueType = $dlgEdit.find("#valueType").val();
			var targetField = $dlgEdit.find("#targetField").val();
			
			var item = {
				attr_label: attrLabel,
				attr_hint: attrHint,
				value_type: valueType,
				target_field: targetField,
				values: []
			};
			if (valueType == 'select') {
				var values = new Array();
				$dlgEdit.find("input[name^=values]").each(function() {
					var val = $.trim($(this).val());
					if (val.length > 0) {
						values.push(val);
					}
				});
				item.values = values;
			}
			
			if (index == -1) {
				var html = '<tr>';
				html += '<td>' + ($("#listConfigs").find("tr").size()) + '</td>';
				html += '<td>' + attrLabel + '</td>';
				html += '<td>' + (attrHint.length > 0 ? attrHint : '无') + '</td>';
				html += '<td>' + getAuditType(valueType) + '</td>';
				html += '<td class="operation">';
				html += '<a class="editItemBtn" title="编辑"><i class="fa fa-edit"></i></a>';
				html += '<a class="deleteItemBtn" title="删除"><i class="fa fa-trash-o"></i></a>';
				html += '</td>';
				html += '</tr>';
				$("#listConfigs").append(html);
				
				configs.push(item);
			} else {
				index = parseInt(index);
				var $tr = $("#listConfigs").find("tr").eq( (index+5) );
				$tr.children().eq(1).text(attrLabel);				
				$tr.children().eq(2).text((attrHint.length > 0 ? attrHint : '无'));
				$tr.children().eq(3).text(getAuditType(valueType));

				configs[index] = item;
			}
			$dlgEdit.modal('hide');
			return false;
		}
	});
	
	$("#listConfigs").sortable({
		items: "tr.movable",
		update: function(event, ui) {
			$.ajax({
				url: "<?=base_url()?>admin/config/change_audit_order",
				type: "post",
				data: $("#listConfigs input[name^='ids']").serialize(),
				dataType: "json",
				cache: false,
				success: function(message) {
					$.message(message);
					if (message.type == "success") {
						$("tr.movable").each(function(index) {
							$(this).children().eq(0).text(index + 1);
						});
					} else {
						setTimeout(function() {
							location.reload(true);
						}, 1500);
					}
				}
			});
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">联盟报名</div>
		<form id="inputForm" action="save_challenge" method="post" class="form-horizontal">
		<input type="hidden" name="configs" value="">
		<?php if (!$isNew): ?>
		<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
		<?php endif; ?>
		<div class="input-wrapper">
			<div class="form-group">
				<label class="col-sm-2 col-md-1 control-label required">
					标&nbsp;&nbsp;题：
				</label>
				<div class="col-sm-6 col-md-4">
					<input type="text" class="form-control" name="title" value="<?=$itemInfo['title']?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 col-md-1 control-label required">
					封面图：
				</label>
				<div class="col-sm-4 col-md-4">
					<div class="image-wrapper im" role="button" onclick="$('#fileMainImg').click()">
						<img class="preview" id="mainImage" src="<?= getFullUrl($itemInfo['image']) ?>">
						<input id="mainImageUrl" name="image" type="hidden" value="<?= $itemInfo['image'] ?>">
						<div class="loading">
							<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 col-md-1 control-label">
					介&nbsp;&nbsp;绍：
				</label>
				<div class="col-sm-8 col-md-6">
					<textarea class="hidden" name="introduction"><?=$itemInfo['introduction']?></textarea>
					<div id="summernote" class="simple"><?=$itemInfo['introduction']?></div>
				</div>
			</div>
		</div>
		
		<div class="list-wrapper col-sm-10 col-md-8 m-t-xs">
			<table id="listConfigs" class="list table">
				<tr>
					<th class="number_f">
						<a href="javascript:;">排序</a>
					</th>
					<th>
						<a href="javascript:;">标题</a>
					</th>
					<th>
						<a href="javascript:;">提示</a>
					</th>
					<th>
						<a href="javascript:;">类型</a>
					</th>
					<th>
						<span>操作</span>
					</th>
				</tr>
				<tr class="hidden">
					<td>1</td>
					<td>姓名</td>
					<td>请输入您的姓名</td>
					<td><?= getAuditValueType('string') ?></td>
					<td class="operation">
					</td>
				</tr>
				<tr class="hidden">
					<td>2</td>
					<td>性别</td>
					<td>无</td>
					<td><?= getAuditValueType('select') ?></td>
					<td class="operation">
					</td>
				</tr>
				<tr class="hidden">
					<td>3</td>
					<td>出生日期</td>
					<td>无</td>
					<td><?= getAuditValueType('date') ?></td>
					<td class="operation">
					</td>
				</tr>
				<tr class="hidden">
					<td>4</td>
					<td>手机</td>
					<td>请输入您的手机号码</td>
					<td><?= getAuditValueType('string') ?></td>
					<td class="operation">
					</td>
				</tr>
				<?php foreach ($configs as $key=>$item): ?>
					<tr class="movable">
						<td>
							<?= $key + 1 ?>
						</td>
						<td>
							<?= $item['attr_label'] ?>
							<input type="hidden" name="ids[]" value="<?= $item['id'] ?>">
						</td>
						<td>
							<?= $item['attr_hint'] ? $item['attr_hint'] : '无' ?>
						</td>
						<td>
							<?= getAuditValueType($item['value_type']) ?>
						</td>
						<td class="operation">
							<a role="button" class="editItemBtn" title="编辑"><i class="fa fa-edit"></i></a>
							<a role="button" class="deleteItemBtn" title="删除"><i class="fa fa-trash-o"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<a role="button" class="btn btn-white" id="addButton"><i class="fa fa-plus"></i> 添加联盟报名选项</a>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 m-t-lg m-b-lg">
				<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
				<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
			</div>
		</div>
		</form>

		<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title">添加选项</h3>
					</div>
					<div class="modal-body">
					<form id="editForm" action="" class="form-horizontal" method="post">
						<input type="hidden" name="index" id="index" />
						<input type="hidden" name="value_type" id="valueType" />
						<ul class="nav nav-tabs m-b-md" role="tablist" style="padding-left: 25%;">
							<li role="presentation" class="tab-btn active">
								<a href="#typeInput" aria-controls="typeInput" role="tab" data-toggle="tab">输 入</a>
							</li>
							<li role="presentation" class="tab-btn m-l-lg">
								<a href="#typeSelect" aria-controls="typeSelect" role="tab" data-toggle="tab">选 择</a>
							</li>
							<li role="presentation" class="tab-btn m-l-lg">
								<a href="#typeOther" aria-controls="typeOther" role="tab" data-toggle="tab">固 定</a>
							</li>
						</ul>
						<div class="form-group">
							<label class="control-label col-xs-3">标&nbsp;&nbsp;题：</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="attr_label" id="attrLabel">
							</div>
						</div>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="typeInput">
								<div class="form-group">
									<label class="control-label col-xs-3">提&nbsp;&nbsp;示：</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="attr_hint" id="attrHint">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-xs-3">类&nbsp;&nbsp;型：</label>
									<div class="col-xs-8">
										<select class="form-control" name="i_value_type" id="iValueType">
											<option value="string"><?= getAuditValueType('string') ?></option>
											<option value="integer"><?= getAuditValueType('integer') ?></option>
											<option value="float"><?= getAuditValueType('float') ?></option>
										</select>
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="typeSelect">
								<div class="form-group">
									<label class="control-label col-xs-3">选择1：</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="values[]">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-xs-3">选择2：</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="values[]">
									</div>
								</div>
								<div id="valueWrapper">
									
								</div>
								<div class="form-group">
									<div class="col-xs-offset-3 col-xs-8">
										<a id="addValue" href="javascript:;"><i class="fa fa-plus"></i> 增加选择</a>
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="typeOther">
								<div class="form-group">
									<div class="col-xs-offset-3 col-xs-8">
										<input type="radio" class="i-check" name="r_value_type" value="date" /> 日&nbsp;&nbsp;期
									</div>
								</div>
								<div class="form-group">
									<div class="col-xs-offset-3 col-xs-8">
										<input type="radio" class="i-check" name="r_value_type" value="datetime" /> 日期时间
									</div>
								</div>
								<div class="form-group">
									<div class="col-xs-offset-3 col-xs-8">
										<input type="radio" class="i-check" name="r_value_type" value="file" /> 图片上传
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-3">对应列：</label>
							<div class="col-xs-8">
								<select class="form-control" name="target_field" id="targetField">
									<option value=""></option>
									<option value="name">姓 名</option>
									<option value="gender">性 别</option>
									<option value="birthday">出生日期</option>
									<option value="mobile">手机</option>
									<option value="weight">体 重</option>
									<option value="height">身 高</option>
									<option value="level">体 系</option>
									<option value="nickname">绰 号</option>
									<option value="description">备 注</option>
									<option value="address">地 址</option>
									<option value="education">学 历</option>
									<option value="military_serve">是否服过兵役</option>
								</select>
							</div>
						</div>
					</form>
					</div>
					<div class="modal-footer" style="text-align:center;">
						<button type="button" class="btn btn-default" data-dismiss="modal">取&nbsp;&nbsp;消</button>
						<button type="submit" class="btn btn-primary" onclick="$('#editForm').submit();">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</div>
		</div>
			
		<div class="hidden">
			<form id="uploadForm" method="post" enctype="multipart/form-data">
				<input type="file" name="file" id="fileMainImg">
			</form>
		</div>
	</div>
</body>
</html>