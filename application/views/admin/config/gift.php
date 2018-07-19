<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>礼物管理</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/switchery/switchery.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#imagePreview",
		resultElement: "#imageUrl",
		fileType: "image",
		ratioWidth: 1,
		ratioHeight: 1
	});
		
	var $dlgEdit = $("#dlgEdit");
	
	$("#addButton").click(function() {
		$dlgEdit.find(".modal-title").text("添加礼物");
		$dlgEdit.find("#giftId").val(0);
		$dlgEdit.find("#name").val("");
		$dlgEdit.find("#price").val("");
		$dlgEdit.find("#exp").val("");
		$dlgEdit.find("#imageUrl").val("");
		$dlgEdit.find("#imagePreview").attr("src", "<?= base_url() ?>resources/images/add_1_1.png");
		$dlgEdit.modal('show');
	});
	
	$(".editItemBtn").click(function() {
		var $parent = $(this).parents("tr");
		$dlgEdit.find(".modal-title").text("编辑礼物");
		$dlgEdit.find("#giftId").val($(this).data("id"));
		$dlgEdit.find("#name").val($.trim($parent.children().eq(0).text()));
		$dlgEdit.find("#price").val($.trim($parent.children().eq(2).text()));
		$dlgEdit.find("#exp").val($.trim($parent.children().eq(3).text()));
		var imgUrl = $parent.children().eq(1).children('img').attr("src");
		$dlgEdit.find("#imageUrl").val(imgUrl);
		$dlgEdit.find("#imagePreview").attr("src", imgUrl);
		$dlgEdit.modal('show');
	});
	
	var $editForm = $("#editForm");
	$editForm.validate({
		rules: {
			name: {
				required: true,
				minlength: 1,
				maxlength: 50
			},
			image: {
				required: true,
			},
			price: {
				min: 0
			},
			exp: {
				min: 0
			}
		}
	});
	
	var isOpenGift = document.querySelector('#isOpenGift');
	var switchery = new Switchery(isOpenGift, { size: 'small', color: '#18a689' });
	isOpenGift.onchange = function() {
		$.ajax({
			url: 'toggle_open_gift',
			type: 'post',
			dataType: 'json',
			data: { is_open_gift: isOpenGift.checked },
			cache: false,
			success: function(data) {
				$.message(data.message);
			}
		});
	};
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">礼物管理</div>
		
		<div class="list-wrapper col-xs-12 col-md-8">
			<form id="listForm" class="form-inline" action="gift" method="get">
				<div class="filter-bar">					
					<input type="text" class="form-control m-l-sm" name="search_name" value="<?= $searchName ?>" placeholder="名称">
		            <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
		            <div class="pull-right p-t-xs">
		            	<input type="checkbox" name="is_open_gift" id="isOpenGift" <?php if ($isOpenGift): ?>checked="checked"<?php endif; ?>>
						<span class="value-tip m-l-xs">是否开启</span>
		            </div>
				</div>
				<table id="listTable" class="list table">
					<tr>
						<th>
							<a href="javascript:;" class="sort" name="name">名称</a>
						</th>
						<th class="qrcode">
							<a href="javascript:;">图片</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="price">价格</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="exp">经验</a>
						</th>
						<th>
							<span>操作</span>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr>
							<td>
								<?= $item['name'] ?>
							</td>
							<td>
								<img src="<?= getFullUrl($item['image']) ?>" width="60" height="60">
							</td>
							<td>
								<?= $item['price'] ?>
							</td>
							<td>
								<?= $item['exp'] ?>
							</td>
							<td class="operation">
								<a class="editItemBtn" href="javascript:;" data-id="<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
								<a class="deleteItemBtn" data-url="delete_gift" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="5">
							<div class="p-lg">没有礼物！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="5">							
							<button type="button" class="btn btn-white" id="addButton"><i class="fa fa-plus"></i> 添加礼物</button>
							<?php $this->load->view('admin/pagination'); ?>
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
						<h3 class="modal-title">添加礼物</h3>
					</div>
					<div class="modal-body">
					<form id="editForm" action="save_gift" class="form-horizontal" method="post">
						<input type="hidden" name="id" id="giftId" />
						<div class="form-group">
							<label class="control-label col-xs-4">名称：</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="name" id="name">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-4">图片：</label>
							<div class="input-wrapper col-xs-8" style="padding: 0 15px;">
								<div class="image-wrapper im" role="button" onclick="$('#fileImage').click()" style="width:70px; height:70px">
									<img class="preview" id="imagePreview" src="">
									<input id="imageUrl" name="image" type="hidden">
									<div class="loading">
										<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-4">价格：</label>
							<div class="col-xs-8">
								<input type="number" class="form-control" name="price" id="price">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-4">经验：</label>
							<div class="col-xs-8">
								<input type="number" class="form-control" name="exp" id="exp">
							</div>
						</div>
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">取&nbsp;&nbsp;消</button>
						<button type="submit" class="btn btn-primary" onclick="$('#editForm').submit();">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</div>
		</div>
		<div class="hidden">
			<form id="uploadForm" method="post" enctype="multipart/form-data">
				<input type="file" name="file" id="fileImage">
			</form>
		</div>
	</div>
</body>
</html>