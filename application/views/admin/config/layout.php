<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>页面内容</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
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
	
	var $inputForm = $("#inputForm");
	$("#listedItems").sortable({
		items: "tr.movable",
		connectWith: "#unlistedItems",
		/*start: function(event, ui) {
			$( "#unlistedItems" ).sortable( "enable" );
		},
		stop: function(event, ui) {
			if ($("#listedItems").find("tr.movable").length >= 6) {
				$( "#unlistedItems" ).sortable( "disable" );
			} else {
				$( "#unlistedItems" ).sortable( "enable" );
			}
		},*/
		update: function(event, ui) {
			if ($("#listedItems").find("tr.movable").length >= 7) {
				$.message('warn', '首页最多仅展示6个Banner');
				$( "#listedItems" ).sortable( "cancel" );
				$( "#unlistedItems" ).sortable( "cancel" );
				
				return false;
			}
			/*if ($("#listedItems").find("tr.movable").length < 2) {
				$.message('warn', 'Banner数不能小于2个');
				$( "#listedItems" ).sortable( "cancel" );
				$( "#unlistedItems" ).sortable( "cancel" );
				
				return false;
			}*/
			
			$.ajax({
				url: "save_layout",
				type: "post",
				data: $("#listedItems input[name^='ids']").serialize(),
				dataType: "json",
				cache: false,
				success: function(message) {
					$.message(message);
					if (message.type == "success") {
						var img = $("#listedItems").find("tr.movable:first-child img").attr("src");
						$("#layoutView td.banner img").attr("src", img);
						
						$("#layoutView td.banner .indicator").empty();
						for (var i = 0; i < $("#listedItems").find("tr.movable").length; i++) {
							$("#layoutView td.banner .indicator").append("<i></i>");
						}
					} else {
						setTimeout(function() {
							location.reload(true);
						}, 1500);
					}
				}
			});
		}
	});
	
	$("#unlistedItems").sortable({
		items: "tr.movable",
		connectWith: "#listedItems",
	});
	
	$("#layoutView td.top-item a").click(function() {
		$("#dlgEdit").modal("show");
	});
	
	$("#editForm").validate({
		rules: {
		},
		submitHandler: function() {
			$.ajax({
				url: "save_top_item",
				type: "post",
				data: $("#editForm").serialize(),
				dataType: "json",
				cache: false,
				success: function(message) {
					$.message(message);
					if (message.type == "success") {
						$("#dlgEdit").modal("hide");						
					} else {
						
					}
				}
			});
			return false;
		}
	});
	
	function updateList() {
		var kind = $("#itemKind").val();
		var url = '';
		var data = {};
		
		if (kind == '<?= BANNER_KIND_ARTICLE ?>' || kind == '<?= BANNER_KIND_GALLERY ?>' || kind == '<?= BANNER_KIND_VIDEO ?>' || kind == '<?= BANNER_KIND_LIVE ?>') {
			url = "<?= base_url() ?>admin/content/ajax_list";
			data = {kind: kind};
		} else if (kind == '<?= BANNER_KIND_MEMBER ?>') {
			url = "<?= base_url() ?>admin/member/ajax_list";
			data = {kind: '<?= MEMBER_KIND_PLAYER ?>'};
		} else if (kind == '<?= BANNER_KIND_EVENT ?>') {
			url = "<?= base_url() ?>admin/events/ajax_list";
			data = {kind: '<?= EVENT_KIND_COMPETITION ?>'};
		}
		
		// item info
		$("#itemId").ajaxChosen({
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
	
	$("#itemKind").change(function() {
		var kind = $("#itemKind").val();
		$("#itemId").find("option").remove();
		$("#itemId").trigger("chosen:updated");
		$("#itemId").chosen("destory");
		$('.chosen-container').find(".search-field > input, .chosen-search > input").unbind('keyup');
		
		if (kind == '<?= BANNER_KIND_EVENT ?>') {
			$("#isTicketWrapper").show();
		} else {
			$("#isTicketWrapper").hide();
		}
		
		updateList();
	});
	
	$("#clearBtn").click(function() {
		$("#itemKind").val('');
		$("#itemKind").change();
	});
	
	<?php if (!empty($topItem['item_kind'])): ?>
	updateList();
	<?php endif; ?>
});
</script>
<style type="text/css">
#layoutView td {
	vertical-align: middle;
	text-align: center;
}
#layoutView td.banner {
	height: 150px;
	position: relative;
	vertical-align: middle;
	padding: 0;
	font-size: 16px;
}
#layoutView td.banner span.title {
	position: absolute; 
	left: 50%; 
	top: 50%;
	margin-top: -10px;
	margin-left: -30px;
	text-shadow: 0 1px 2px #222;
	color: #FFF;
	font-weight: bold;
}
#layoutView td.banner span.indicator {
	display: block;
	position: absolute;
	width: 100px;
	height: 7px; 
	right: 10px; 
	bottom: 10px;
	margin-left: -25px;
	text-align: right;
}
#layoutView td.banner span.indicator i {
	display: inline-block;
	width: 6px;
	height: 6px;
	border-radius: 50%;
	border: 1px solid #fff;
	margin-right: 5px;
	float: right;
}
#layoutView td.banner span.indicator i:last-child {
	background: #fff;
}
#layoutView td.top-item {
	height: 60px;
	font-size: 14px;
}
</style>
</head>
<body>
	<div class="content-wrapper config layout">
		<div class="title-bar">页面内容</div>
		<div class="row m-t-lg m-b-md">
			<ul class="nav nav-pills col-xs-offset-1">
				<li class="active"><a href="javascript:;">头条</a></li>
				<li><a href="layout_discover">发现</a></li>
			</ul>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save_layout" method="post" class="form-horizontal">
				<div class="panel panel-default pull-left">
					<div class="panel-heading">
						<span class="font-bold">页面</span>
					</div>
					<div class="panel-body">
						<table class="table" id="layoutView">
							<tr>
								<td class="banner">
									<img src="<?= empty($listedItems) ? '' : getFullUrl($listedItems[0]['image']) ?>" width="100%" height="100%">
									<span class="title">Banner区</span>
									<span class="indicator">
										<?php foreach ($listedItems as $item): ?>
											<i></i>
										<?php endforeach; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="top-item">
									<a href="javascript:;">头条区编辑<i role="button" class="fa fa-edit m-l-xs green"></i></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="pull-left m-l-sm m-r-sm" style="padding-top: 250px;">
					<i class="fa fa-long-arrow-left fa-2x"></i>
				</div>
				<div class="panel panel-default pull-left">
					<div class="panel-heading">
						<span class="font-bold">展示再首页的BANNER</span>
					</div>
					<div class="panel-body">
						<table class="table" id="listedItems">
							<tbody>
							<?php foreach ($listedItems as $item): ?>
								<tr class="movable">
									<td class="text-center" width="65">
										<img src="<?= getFullUrl($item['image']) ?>" width="120" height="60">
									</td>
									<td class="text-left">
										<input type="hidden" name="ids[]" value="<?= $item['id'] ?>">																		
										<?= $item['title'] ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="pull-left m-l-sm m-r-sm" style="padding-top: 250px;">
					<i class="fa fa-exchange fa-2x"></i>
				</div>
				<div class="panel panel-default pull-left">
					<div class="panel-heading">
						<span class="font-bold">可选择的BANNER</span>
					</div>
					<div class="panel-body">
						<table class="table" id="unlistedItems">
							<tr>
								<td colspan="2" class="text-left separater">首页最多仅展示6个Banner</td>
							</tr>
							<?php foreach ($unlistedItems as $item): ?>
								<tr class="movable">
									<td class="text-center" width="65">
										<img src="<?= getFullUrl($item['image']) ?>" width="120" height="60">
									</td>
									<td class="text-left">
										<input type="hidden" name="ids[]" value="<?= $item['id'] ?>">																		
										<?= $item['title'] ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
			</form>
		</div>
		
		<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title">头条区编辑</h3>
					</div>
					<div class="modal-body">
					<form id="editForm" class="form-horizontal" method="post" action="save_top_item">
						<div class="form-group">
							<label class="control-label">类型：</label>
							<select class="form-control" name="item_kind" id="itemKind">
								<option value="" <?php if (empty($topItem['item_kind'])): ?>selected="selected"<?php endif; ?>></option>
								<option value="<?= BANNER_KIND_ARTICLE ?>" <?php if (BANNER_KIND_ARTICLE == $topItem['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_ARTICLE) ?></option>
								<option value="<?= BANNER_KIND_GALLERY ?>" <?php if (BANNER_KIND_GALLERY == $topItem['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_GALLERY) ?></option>
								<option value="<?= BANNER_KIND_VIDEO ?>" <?php if (BANNER_KIND_VIDEO == $topItem['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_VIDEO) ?></option>
								<option value="<?= BANNER_KIND_LIVE ?>" <?php if (BANNER_KIND_LIVE == $topItem['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_LIVE) ?></option>
								<option value="<?= BANNER_KIND_MEMBER ?>" <?php if (BANNER_KIND_MEMBER == $topItem['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_MEMBER) ?></option>
								<option value="<?= BANNER_KIND_EVENT ?>" <?php if (BANNER_KIND_EVENT == $topItem['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_EVENT) ?></option>
							</select>
						</div>
						<div class="form-group">
							<label class="control-label">输入ID：</label>
							<select name="item_id" id="itemId" class="form-control" data-placeholder="请输入ID或标题">
								<?php if (!empty($topItem['item_id'])): ?>
								<option value="<?= $topItem['item_id'] ?>" selected="selected"><?= $topItem['item_label'] ?></option>
								<?php endif; ?>
							</select>
						</div>
						<div class="form-group" id="isTicketWrapper" <?php if (BANNER_KIND_EVENT != $topItem['item_kind']): ?>style="display:none;"<?php endif; ?>>
							<label class="control-label">样式：</label>
							<div class="p-t-xxs">
								<label role="button" class="m-l">
									<input type="radio" class="i-check" name="is_ticket" value="" <?php if (BANNER_KIND_EVENT != $topItem['item_kind'] || !$topItem['is_ticket']): ?>checked="checked"<?php endif; ?>/> 内容
								</label>
								<label role="button" class="m-l-lg">
									<input type="radio" class="i-check" name="is_ticket" value="1" <?php if (BANNER_KIND_EVENT == $topItem['item_kind'] && $topItem['is_ticket']): ?>checked="checked"<?php endif; ?>/> 售票
								</label>
							</div>
						</div>
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
						<button type="button" class="btn btn-default" id="clearBtn">清&nbsp;&nbsp;除</button>
						<button type="submit" class="btn btn-primary" onclick="$('#editForm').submit();">保&nbsp;&nbsp;存</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>