<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?>链接
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
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
	
	var contents = [
		<?php if (!$isNew && !empty($itemInfo['contents'])): ?>
			<?php foreach ($itemInfo['contents'] as $key=>$content): ?>
				{
					id: '<?= $content['id'] ?>',
					title: '<?= $content['title'] ?>',
					target_kind: '<?= $content['target_kind'] ?>',
					target_id: '<?= $content['target_id'] ?>',
					target_label: '<?= $content['target_label'] ?>',
					url: '<?= $content['url'] ?>'
				},
			<?php endforeach; ?>
		<?php endif; ?>
	];
	
	var $inputForm = $("#inputForm");
	
	// 表单验证
	$inputForm.validate({
		rules: {
			source: {
				required: true
			},
			image: {
				required: true
			}
		},
		messages: {
			
		},
		submitHandler: function(form) {
			if (contents == null || contents.length == 0) {
				alert('内容不能为空！');
				return false;
			}
			$("input[name=contents]").val(JSON.stringify(contents));
			return true;
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var ratioWidth = 10;
	var ratioHeight = 6;
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#mainImage",
		resultElement: "#mainImageUrl",
		thumbElement: "#mainThumbUrl",
		fileType: "image",
		makeThumb: true,
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight
	});
	
	<?php if ($isNew): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	$("input[name=platform]").eq(0).iCheck("check");
	<?php endif; ?>
	$("#ratio").text(ratioWidth + "：" + ratioHeight);
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);

	
	var kindUrl = '<?= LINK_CONTENT_KIND_URL ?>';
	
	var $listContent = $("#listContent");
	var $dlgEditContent = $("#dlgEditContent");
	
	function refreshOrder() {
		contents = new Array();
		$("tr.item").each(function(index) {
			$(this).children().eq(0).text(index + 1);
			contents.push({
				title: $.trim($(this).children().eq(1).text()), 
				target_kind: $(this).data("tkind"), 
				target_id: $(this).data("tid"), 
				target_label: $(this).data("tlabel"), 
				url: $.trim($(this).children().eq(2).text())
			});
		});
	}
	
	$(".addItemBtn").click(function() {
		$dlgEditContent.find(".modal-title").text("添加内容");
		$dlgEditContent.find("#index").val(-1);
		$dlgEditContent.find("#title").val("");
		$dlgEditContent.find("#url").val("");
		$dlgEditContent.find("#targetKind").val("<?= CONTENT_KIND_ARTICLE ?>");
		$dlgEditContent.find("#targetId").val("");
		$dlgEditContent.find("#targetId").find("option").remove();
		$dlgEditContent.find("#targetId").trigger("chosen:updated");
		
		$("#url").prop("readonly", true);
		$("#targetIdWrapper").show();
			
		$dlgEditContent.modal('show');
	});
	
	$listContent.on("click", ".editItemBtn", function() {
		var $parent = $(this).parents("tr");
		var index = $parent.index() - 1;
		$dlgEditContent.find(".modal-title").text("编辑内容");
		$dlgEditContent.find("#index").val(index);
		$dlgEditContent.find("#title").val(contents[index].title);
		$dlgEditContent.find("#url").val(contents[index].url);
		$dlgEditContent.find("#targetKind").val(contents[index].target_kind);
		$dlgEditContent.find("#targetId").find("option").remove();
		if (contents[index].target_kind == kindUrl) {
			$("#url").prop("readonly", false);
			$("#targetIdWrapper").hide();
		} else {
			$("#url").prop("readonly", true);
			$("#targetIdWrapper").show();

			if ($dlgEditContent.find("#targetId").find("option[value=" + contents[index].target_id + "]").length == 0) {
				var html = "<option value='" + contents[index].target_id + "'>" + contents[index].target_label + "</option>";
				$dlgEditContent.find("#targetId").append(html);
			}
			$dlgEditContent.find("#targetId").val(contents[index].target_id);
		}
		$dlgEditContent.find("#targetId").trigger("chosen:updated");

		$dlgEditContent.modal('show');
	});
	
	$listContent.on("click", ".deleteItemBtn", function() {
		var $parent = $(this).parents("tr");
		//contents.splice($parent.index() - 1, 1);
		$parent.remove();
		refreshOrder();
		if ($listContent.find("tr.item").size() == 0) {
			$listContent.append('<tr class="empty-row"><td class="text-center" colspan="4">没有内容！</td></tr>');
		}
	});
	
	$listContent.sortable({
		items: "tr.item",
		update: function(event, ui) {
			refreshOrder();
		}
	});
	
	

	var $editForm = $("#editContentForm");
	$editForm.validate({
		rules: {
			title: {
				required: true
			},
			url: {
				required: true
			}
		},
		submitHandler: function(form) {
			var index = $dlgEditContent.find("#index").val();
			var title = $dlgEditContent.find("#title").val();
			var targetKind = $dlgEditContent.find("#targetKind").val();
			var targetId = $dlgEditContent.find("#targetId").val();
			var targetLabel = $dlgEditContent.find("#targetId").find("option:selected").text();
			var url = $dlgEditContent.find("#url").val();
			
			if (targetKind == kindUrl) {
				targetId = 0;
				targetLabel = '';
			}
			
			if (index == -1) {
				$listContent.find("tr.empty-row").remove();
				
				var html = '<tr class="item" data-tkind="' + targetKind + '" data-tid="' + targetId + '" data-tlabel="' + targetLabel + '">';
				html += '<td>' + ($("tr.item").size() + 1) + '</td>';
				html += '<td>' + title + '</td>';
				html += '<td>' + url + '</td>';
				html += '<td>';
				html += '<a class="editItemBtn m-r" title="编辑"><i class="fa fa-edit"></i></a>';
				html += '<a class="deleteItemBtn" title="删除"><i class="fa fa-trash-o"></i></a>';
				html += '</td>';
				html += '</tr>';
				$listContent.append(html);
				
				contents.push({
					title: title,
					target_kind: targetKind,
					target_id: targetId,
					target_label: targetLabel,
					url: url
				});
			} else {
				var $tr = $("tr.item").eq(index);
				$tr.data("tkind", targetKind);
				$tr.data("tid", targetId);
				$tr.data("tlabel", targetLabel);
				$tr.children().eq(1).text(title);
				$tr.children().eq(2).text(url);
				contents[index] = {
					title: title,
					target_kind: targetKind,
					target_id: targetId,
					target_label: targetLabel,
					url: url
				};
			}
			$dlgEditContent.modal('hide');
			return false;
		}
	});
	
	
	function updateList() {
		var kind = $("#targetKind").val();
		var url = '';
		var data = {};

		url = "<?= base_url() ?>admin/content/ajax_list";
		data = {kind: kind};
		
		// item info
		$("#targetId").ajaxChosen({
			minTermLength: 1,
			type: "GET",
			url: url,
			data: data,
			dataType: 'json'
		}, function (data) {
			var results = [];
			$.each(data, function (i, val) {
			    results.push({ value: val.id, text: val.label });
			});
			return results;
		});
	}
	
	$("#targetKind").change(function() {
		var kind = $("#targetKind").val();
		$("#targetId").find("option").remove();
		$("#targetId").trigger("chosen:updated");
		$("#targetId").chosen("destory");
		$('.chosen-container').find(".search-field > input, .chosen-search > input").unbind('keyup');
		
		$("#url").val("");
		
		if (kind == kindUrl) {
			$("#url").prop("readonly", false);
			$("#targetIdWrapper").hide();
		} else {
			$("#url").prop("readonly", true);
			$("#targetIdWrapper").show();
			updateList();
		}
	});
	
	$("#targetId").chosen().change(function(data) {
		var targetId = $("#targetId").val();
		url = "<?= base_url() ?>portal/contents/" + targetId;
		$("#url").val(url);
	});
	
	$('.chosen-drop').on("click", "li.active-result", function() {
		var targetId = $("#targetId").val();
		url = "<?= base_url() ?>portal/contents/" + targetId;
		$("#url").val(url);
	});
	updateList();
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?>链接
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save" method="post" class="form-horizontal">
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>
				<input type="hidden" name="contents" value="" />

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						来&nbsp;&nbsp;源：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="source" class="form-control" value="<?= $isNew ? '' : $itemInfo['source']?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						日&nbsp;&nbsp;期：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" class="form-control Wdate Wdate-YMDhms pull-left" name="link_date" value="<?= $isNew ? '' : d2dtns($itemInfo['link_date']) ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm'});" placeholder="选择时间">
						<a role="button" class="clear-time" href="javascript:;" onclick="$(this).siblings('input').val('');">清除时间</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						封 面 图：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="image-wrapper m-r im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['image']) ?>">
							<input id="mainImageUrl" name="image" type="hidden" value="<?= $isNew ? '' : $itemInfo['image'] ?>">
							<input id="mainThumbUrl" name="thumb" type="hidden" value="<?= $isNew ? '' : $itemInfo['thumb'] ?>">
							<div class="loading">
								<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
							</div>
						</div>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						图片比例最好为<span id="ratio"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						关 键 词：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" id="keywords" name="keywords" class="form-control" value="<?= $isNew ? '' : $itemInfo['keywords'] ?>"/>
					</div>															
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						关键词用于搜索，  请用 ，号进行分割
					</div>
				</div>
				<div class="form-group m-t-lg">
					<label class="col-sm-3 col-md-2 control-label">
						分&nbsp;&nbsp;类：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="category">
							<option value="">选择分类</option>
							<?php foreach ($categories as $category): ?>
								<option value="<?= $category['id'] ?>" <?php if (!$isNew && $category['id'] == $itemInfo['category_id']): ?>selected="selected"<?php endif; ?>><?= $category['name'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>										
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						选择分类列表，用于客户端显示
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="col-sm-offset-1 control-label">
						内容
					</label>
					<div></div>
					<div id="contentWrapper" class="col-sm-offset-1 m-t-md col-sm-11 col-md-8">
						<table id="listContent" class="table list">
							<tr>
								<th>排序</th>
								<th>标题</th>
								<th>链接地址</th>
								<th>操作</th>
							</tr>
							<?php if (!$isNew && !empty($itemInfo['contents'])): ?>
								<?php foreach ($itemInfo['contents'] as $key=>$content): ?>
									<tr class="item movable" data-tkind="<?=$content['target_kind']?>" data-tid="<?=$content['target_id']?>" data-tlabel="<?=$content['target_label']?>">
										<td>
											<?= $key + 1 ?>
										</td>
										<td>
											<?= $content['title'] ?>
										</td>
										<td>
											<?= $content['url'] ?>
										</td>
										<td>
											<a class="editItemBtn m-r" data-id="<?= $content['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
											<a class="deleteItemBtn" data-id="<?= $content['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if ($isNew && empty($itemInfo['contents'])): ?>
								<tr class="empty-row"><td class="text-center" colspan="4">没有内容！</td></tr>
							<?php endif; ?>
						</table>
						<a role="button" class="addItemBtn m-t-sm"><i class="fa fa-plus"></i> 添加内容</a>
					</div>
				</div>
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button>
						<button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
					</div>
				</div>
			</form>
			<div class="modal fade" tabindex="-1" role="dialog" id="dlgEditContent">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">添加内容</h3>
						</div>
						<div class="modal-body">
						<form id="editContentForm" class="form-horizontal" method="post">
							<input type="hidden" name="index" id="index" />
							<div class="form-group">
								<label class="control-label">标题：</label>
								<input type="text" class="form-control" name="title" id="title">
							</div>
							<div class="form-group">
								<label class="control-label">类型：</label>
								<select class="form-control" name="target_kind" id="targetKind">
									<option value="<?= LINK_CONTENT_KIND_ARTICLE ?>"><?= getContentKind(LINK_CONTENT_KIND_ARTICLE) ?></option>
									<option value="<?= LINK_CONTENT_KIND_GALLERY ?>"><?= getContentKind(LINK_CONTENT_KIND_GALLERY) ?></option>
									<option value="<?= LINK_CONTENT_KIND_VIDEO ?>"><?= getContentKind(LINK_CONTENT_KIND_VIDEO) ?></option>
									<option value="<?= LINK_CONTENT_KIND_URL ?>"><?= '链接' ?></option>
								</select>
							</div>
							<div class="form-group" id="targetIdWrapper">
								<label class="control-label">输入ID：</label>
								<select name="target_id" id="targetId" class="form-control" data-placeholder="请输入ID或标题">
								</select>
							</div>
							<div class="form-group">
								<label class="control-label">链接地址：</label>
								<input type="text" class="form-control" name="url" id="url" readonly="readonly">
							</div>
						</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
							<button type="submit" class="btn btn-primary" onclick="$('#editContentForm').submit();">保&nbsp;&nbsp;存</button>
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
	</div>
</body>
</html>