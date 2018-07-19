<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>分类管理</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $dlgEdit = $("#dlgEdit");
	
	$("#addButton").click(function() {
		$dlgEdit.find(".modal-title").text("添加等级");
		$dlgEdit.find("#categoryId").val(0);
		$dlgEdit.find("#name").val("");
		$dlgEdit.modal('show');
	});
	
	$(".editItemBtn").click(function() {
		var $parent = $(this).parents("tr");
		$dlgEdit.find(".modal-title").text("编辑等级");
		$dlgEdit.find("#categoryId").val($(this).data("id"));
		$dlgEdit.find("#name").val($.trim($parent.children().eq(2).text()));
		$dlgEdit.modal('show');
	});
	
	var $editForm = $("#editCategoryForm");
	$editForm.validate({
		rules: {
			name: {
				required: true,
				minlength: 1,
				maxlength: 20
			}
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">分类管理</div>
		<div class="list-wrapper col-xs-9 col-md-6 m-t-md">
			<form id="listForm" class="form-inline" action="category" method="get">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">ID</a>
						</th>
						<th class="number">
							<a href="javascript:;">排序</a>
						</th>
						<th>
							<a href="javascript:;">名称</a>
						</th>
						<th>
							<a href="javascript:;">操作</a>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr>
							<td>
								<?= $item['id'] ?>
							</td>
							<td>
								<?= $key + 1 ?>
							</td>
							<td>
								<?= $item['name'] ?>
							</td>
							<td class="operation">
								<a class="editItemBtn" href="javascript:;" data-id="<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete_category" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="4">
							<div class="p-lg">没有分类！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="4">							
							<button type="button" class="btn btn-white" id="addButton"><i class="fa fa-plus"></i> 添加分类</button>
						</th>
					</tr>
				</table>
			</form>
		</div>
		
		<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title">添加分类</h3>
					</div>
					<div class="modal-body">
					<form id="editCategoryForm" action="save_category" class="form-horizontal" method="post">
						<input type="hidden" name="id" id="categoryId" />
						<div class="form-group">
							<label class="control-label">分类名称：</label>
							<input type="text" class="form-control" name="name" id="name">
						</div>
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
						<button type="submit" class="btn btn-primary" onclick="$('#editCategoryForm').submit();">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>