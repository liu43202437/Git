<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>版本更新</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/summernote.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/lang/summernote-zh-CN.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	$.uploader({
		formElement: "#appUploadForm",
		contextElement: "#uploadStatus",
		resultElement: "#fileUrl",
		fileType: "binary",
		before: function() {
			$("#uploadBtn").prop("disabled", true);
		},
		done: function(result) {
			$("#filename").val(result.filename);
			$("#filesize").val(result.filesize);
			$("#uploadBtn").prop("disabled", false);
		},
		progress: function(data) {
			var percent = (data.loaded / data.total * 100).toFixed(2);
			$("#uploadProgress").text(percent + " %");
			$("#uploadProgress").show();
		},
		always: function(data) {
			$("#uploadProgress").hide();
		}
	});
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">版本更新</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_version" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-2 col-md-1 control-label">
						安卓客户端：
					</label>
					<div class="col-sm-4 col-md-4">
						<input type="hidden" name="url" id="fileUrl">
						<input type="hidden" name="filename" id="filename">
						<input type="hidden" name="filesize" id="filesize">
						<button type="button" id="uploadBtn" class="btn btn-white" onclick="$('#fileApp').click()">上传客户端 ...</button>
						<span id="uploadStatus">
							<i class="fa fa-spinner fa-pulse fa-fw gray"></i>
							<i class="fa fa-check green"></i>
						</span>
						<span id="uploadProgress"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 col-md-1 control-label">
						版&nbsp;&nbsp;本：
					</label>
					<div class="col-sm-8 col-md-4">
						<div class="col-xs-3" style="padding:0 8px 0 0;">
							<div class="input-group">
								<input type="text" class="form-control text-center" name="version_1" value="1" style="z-index:inherit;">
								<div class="input-group-addon">-</div>
							</div>
						</div>
						<div class="col-xs-3" style="padding:0 8px 0 0;">
							<div class="input-group">
								<input type="text" class="form-control text-center" name="version_2" value="0" style="z-index:inherit;">
								<div class="input-group-addon">-</div>
							</div>
						</div>
						<div class="col-xs-3" style="padding:0 8px 0 0;">
							<div class="input-group">
								<input type="text" class="form-control text-center" name="version_3" value="0" style="z-index:inherit;">
								<div class="input-group-addon"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 col-md-1 control-label">
						更新内容：
					</label>
					<div class="col-sm-8 col-md-6">
						<textarea class="hidden" name="description"></textarea>
						<div id="summernote" class="simple"></div>
					</div>
				</div>
				<div class="form-group m-t-lg">
					<div class="col-sm-offset-2 col-md-offset-1 col-sm-9 col-md-10">
						<button type="submit" class="btn btn-primary">更&nbsp;&nbsp;新</button>
					</div>
				</div>
			</form>
		</div>
		<div class="hidden">
			<form id="appUploadForm" method="post" enctype="multipart/form-data">
				<input type="file" name="file" id="fileApp">
			</form>
		</div>
		
		<div class="list-wrapper col-sm-offset-1 col-sm-10 col-md-7 m-t-lg">
			<form id="listForm" class="form-inline" action="category" method="get">
				<table id="listTable" class="list table">
					<tr>
						<th class="number_f">
							<a href="javascript:;">编号</a>
						</th>
						<th>
							<a href="javascript:;">文件名称</a>
						</th>
						<th>
							<a href="javascript:;">版本</a>
						</th>
						<th>
							<a href="javascript:;">文件大小</a>
						</th>
						<th class="time">
							<a href="javascript:;">更新时间</a>
						</th>
						<th>
							<a href="javascript:;">创建人</a>
						</th>
						<th>
							<a href="javascript:;">操作</a>
						</th>
					</tr>
					<?php foreach ($itemList as $key=>$item): ?>
						<tr>
							<td>
								<?= $key + 1 + ($pager['pageSize'] * ($pager['pageNumber'] - 1)) ?>
							</td>
							<td>
								<a href="<?= base_url().$item['url'] ?>"><?= $item['filename'] ?></a>
							</td>
							<td>
								<?= $item['version'] ?>
							</td>
							<td>
								<?= $item['filesize'] ?>
							</td>
							<td>
								<?= $item['create_date'] ?>
							</td>
							<td>
								<?= $item['admin_name'] ?>
							</td>
							<td class="operation" style="width:50px;">
								<a class="deleteItemBtn" data-url="delete_version" data-id="<?= $item['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($itemList) == 0): ?>
					<tr>
						<td class="text-center" colspan="7">
							<div class="p-lg">没有更新记录！</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr class="bottom-bar">
						<th colspan="7">							
							<?php $this->load->view('admin/pagination'); ?>
						</th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>