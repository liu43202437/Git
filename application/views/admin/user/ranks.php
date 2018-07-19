<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>用户等级</title>
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
		$dlgEdit.find("#rankId").val(0);
		$dlgEdit.find("#nameMale").val("");
		$dlgEdit.find("#nameFemale").val("");
		$dlgEdit.find("#rank").val("1");
		$dlgEdit.find("#minExp").val("1");
		$dlgEdit.modal('show');
	});
	
	$(".editItemBtn").click(function() {
		var $parent = $(this).parents("tr");
		$dlgEdit.find(".modal-title").text("编辑等级");
		$dlgEdit.find("#rankId").val($(this).data("id"));
		$dlgEdit.find("#nameMale").val($.trim($parent.children().eq(1).text()));
		$dlgEdit.find("#nameFemale").val($.trim($parent.children().eq(2).text()));
		$dlgEdit.find("#rank").val($.trim($parent.children().eq(3).text()));
		$dlgEdit.find("#minExp").val($.trim($parent.children().eq(4).text()));
		$dlgEdit.modal('show');
	});
	
	var $editForm = $("#editRankForm");
	$editForm.validate({
		rules: {
			name_male: {
				required: true,
				minlength: 1,
				maxlength: 20
			},
			name_female: {
				required: true,
				minlength: 1,
				maxlength: 20
			},
			rank: {
				required: true,
				min: 1,
				max: 50
			},
			min_exp: {
				required: true,
				min: 1
			}
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">用户等级</div>
		<div class="list-wrapper">
			<form id="listForm" class="col-sm-10 col-md-7 form-inline" action="ranks" method="get">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">ID</a>
						</th>
						<th>
							<a href="javascript:;">头衔（男）</a>
						</th>
						<th>
							<a href="javascript:;">头衔（女）</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="rank">等级</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="min_exp">经验下限</a>
						</th>
						<th>
							<a href="javascript:;">操作</a>
						</th>
					</tr>
					<?php foreach ($itemList as $item): ?>
						<tr>
							<td>
								<?= $item['id'] ?>
							</td>
							<td>
								<?= $item['name_male'] ?>
							</td>
							<td>
								<?= $item['name_female'] ?>
							</td>
							<td>
								<?= $item['rank'] ?>
							</td>
							<td>
								<?= $item['min_exp'] ?>
							</td>
							<td class="operation">
								<a class="editItemBtn" href="javascript:;" data-id="<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete_rank" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="6">
							<div class="p-lg">没有用户等级！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="6">							
							<button type="button" class="btn btn-white" id="addButton"><i class="fa fa-plus"></i> 添加等级</button>
							
							<span class="hidden"><?php $this->load->view('admin/pagination'); ?></span>
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
						<h3 class="modal-title">添加等级</h3>
					</div>
					<div class="modal-body">
					<form id="editRankForm" action="edit_rank" class="form-horizontal" method="post">
						<input type="hidden" name="id" id="rankId" />
						<div class="form-group">
							<label for="name_male" class="control-label">头衔（男）：</label>
							<input type="text" class="form-control" name="name_male" id="nameMale">
						</div>
						<div class="form-group">
							<label for="name_female" class="control-label">头衔（女）：</label>
							<input type="text" class="form-control" name="name_female" id="nameFemale">
						</div>
						<div class="form-group">
							<label for="rank" class="control-label">等级：</label>
							<input type="number" class="form-control" name="rank" id="rank" min="1">
						</div>
						<div class="form-group">
							<label for="rank" class="control-label">经验下限：</label>
							<input type="number" class="form-control" name="min_exp" id="minExp" min="1">
						</div>
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
						<button type="submit" class="btn btn-primary" onclick="$('#editRankForm').submit();">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>