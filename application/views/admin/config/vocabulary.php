<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>基本设置</title>
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
		$dlgEdit.find(".modal-title").text("添加词汇");
		$dlgEdit.find("#vocabId").val(0);
		$dlgEdit.find("#vocab").val("");
		$dlgEdit.find("#vocab").focus();
		$dlgEdit.modal('show');
	});
	
	$(".editItemBtn").click(function() {
		var $parent = $(this).parents("tr");
		$dlgEdit.find(".modal-title").text("编辑词汇");
		$dlgEdit.find("#vocabId").val($(this).data("id"));
		$dlgEdit.find("#vocab").val($.trim($parent.children().eq(1).text()));
		$dlgEdit.find("#vocab").focus();
		$dlgEdit.modal('show');
	});
	
	var $editForm = $("#editForm");
	$editForm.validate({
		rules: {
			vocab: {
				required: true,
				minlength: 1,
				maxlength: 50
			}
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper config">
		<div class="title-bar">基本设置</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li><a href="basis">烟币，经验</a></li>
				<li><a href="about">关于</a></li>
				<li class="active"><a href="javascript:;">屏蔽词汇</a></li>
				<li><a href="hits">点击量</a></li>
				<li><a href="search_word">搜索文字</a></li>
			</ul>
		</div>
		<div class="input-wrapper">
		<div class="list-wrapper col-xs-12 col-sm-8 col-md-6">
			<form id="listForm" class="form-inline" action="vocabulary" method="get">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">ID</a>
						</th>
						<th width="50%">
							<a href="javascript:;">词汇</a>
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
								<?= $item['vocab'] ?>
							</td>
							<td class="operation">
								<a class="editItemBtn" href="javascript:;" data-id="<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete_vocab" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="3">
							<div class="p-lg">没有词汇！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="3">							
							<button type="button" class="btn btn-white" id="addButton"><i class="fa fa-plus"></i> 添加词汇</button>
							<?php $this->load->view('admin/pagination'); ?>
						</th>
					</tr>
				</table>
			</form>
		</div>
		</div>
		
		<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title">添加词汇</h3>
					</div>
					<div class="modal-body">
					<form id="editForm" action="save_vocab" class="form-horizontal" method="post">
						<input type="hidden" name="id" id="vocabId" />
						<div class="form-group">
							<label class="control-label p-b-xxs">词汇：</label>
							<input type="text" class="form-control" name="vocab" id="vocab">
						</div>
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
						<button type="submit" class="btn btn-primary" onclick="$('#editForm').submit();">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>