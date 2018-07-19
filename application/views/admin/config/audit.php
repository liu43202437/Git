<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>报名设置</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var configs = [
		<?php foreach ($itemList as $item): ?>
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
	
	var $dlgEdit = $("#dlgEdit");
	
	function clearDlgInfo() {
		$("#valueWrapper").empty();
		$(".tab-btn").removeClass("active");
		$(".tab-pane").removeClass("active");
		$("input[name=r_value_type]").filter("[value=date]").iCheck("check");
		$("select[name=i_value_type]").val("string");
		
		$dlgEdit.find("#itemId").val(0);
		$dlgEdit.find("#attrName").val("");
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
	
	$(".editItemBtn").click(function() {
		clearDlgInfo();
		var $parent = $(this).parents("tr");
		index = $parent.index() - 5;
		$dlgEdit.find(".modal-title").text("编辑报名选项");
		$dlgEdit.find("#itemId").val($(this).data("id"));
		$dlgEdit.find("#attrName").val(configs[index].attr_name);
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
			return true;
		}
	});
	
	$("#listTable").sortable({
		items: "tr.movable",
		update: function(event, ui) {
			$.ajax({
				url: "<?=base_url()?>admin/config/change_audit_order",
				type: "post",
				data: $("#listTable input[name^='ids']").serialize(),
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

function onDelete($obj, result) {
	if (result.type == "success") {
		setTimeout(function() {
			location.reload(true);
		}, 1500);
	}
}
</script>
</head>
<body>
	<div class="content-wrapper config">
		<div class="title-bar">报名设置</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">


				<li class="<?= ($kind == AUDIT_KIND_PLAYER) ? 'active' : ''?>"><a href="<?= AUDIT_KIND_PLAYER ?>">公益票专卖申请</a></li>
				<!--<li class="<?= ($kind == AUDIT_KIND_REFEREE) ? 'active' : ''?>"><a href="<?= AUDIT_KIND_REFEREE ?>"><?= getAuditKind(AUDIT_KIND_REFEREE) ?>报名</a></li>
				<li class="<?= ($kind == AUDIT_KIND_COACH) ? 'active' : ''?>"><a href="<?= AUDIT_KIND_COACH ?>"></a></li>
				<li class="<?= ($kind == AUDIT_KIND_CLUB) ? 'active' : ''?>"><a href="<?= AUDIT_KIND_CLUB ?>">公益票专卖申请</a></li>
				<li><a href="../challenge"><?= getAuditKind(AUDIT_KIND_CHALLENGE) ?>报名</a></li>-->
			</ul>
		</div>
		<div class="list-wrapper col-xs-12 col-sm-10 col-md-8">
			<form id="listForm" class="form-inline" action="<?= $kind ?>" method="get">
				<table id="listTable" class="list table">
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
					<?php foreach ($itemList as $key=>$item): ?>
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
								<a role="button" class="editItemBtn" data-id="<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a role="button" class="deleteItemBtn" data-url="../delete_audit_item" data-id="<?= $item['id'] ?>" data-func="onDelete" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr class="bottom-bar">
						<th colspan="5">							
							<button type="button" class="btn btn-white" id="addButton"><i class="fa fa-plus"></i> 添加<?= getAuditKind($kind) ?>报名选项</button>
						</th>
					</tr>
				</table>
			</form>
		</div>
		
		<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title">添加选项</h3>
					</div>
					<div class="modal-body">
					<form id="editForm" action="../save_audit_item" class="form-horizontal" method="post">
						<input type="hidden" name="kind" value="<?= $kind ?>" />
						<input type="hidden" name="id" id="itemId" />
						<input type="hidden" name="attr_name" id="attrName" />
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
									<option value="image">头 像</option>
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
	</div>
</body>
</html>