<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?><?= getEventKind($kind) ?>
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/switchery/switchery.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/ajax-chosen.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/summernote.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/lang/summernote-zh-CN.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var counterparts = [
		<?php if (!$isNew && !empty($itemInfo['counterparts'])): ?>
			<?php foreach ($itemInfo['counterparts'] as $key=>$part): ?>
				{
					id: <?= $part['id'] ?>,
					a_player_id: '<?= $part['a_player_id'] ?>',
					b_player_id: '<?= $part['b_player_id'] ?>',
					winner: '<?= $part['winner'] ?>',
					description: '<?= $part['description'] ?>',
					a_player: '<?= $part['a_player'] ?>',
					b_player: '<?= $part['b_player'] ?>',
					winner_name: '<?= $part['winner_name'] ?>'
				},
			<?php endforeach; ?>
		<?php endif; ?>
	];
	
	var prices = [
		<?php if (!$isNew && !empty($itemInfo['ticket_prices'])): ?>
			<?php foreach ($itemInfo['ticket_prices'] as $key=>$price): ?>
				{
					id: <?= $price['id'] ?>,
					name: '<?= $price['name'] ?>',
					price: '<?= $price['price'] ?>',
					count: '<?= $price['count'] ?>',
					color: '<?= $price['color'] ?>'
				},
			<?php endforeach; ?>
		<?php endif; ?>
	];
	
	var $inputForm = $("#inputForm");
	var $priceChanged = $("input[name=price_changed]");
	
	var hasTicket = document.querySelector('#hasTicket');
	var switchery = new Switchery(hasTicket, { size: 'small', color: '#18a689' });
	hasTicket.onchange = function() {
		if (hasTicket.checked) {
			$("#ticketWrapper").show();
		} else {
			$("#ticketWrapper").hide();
		}
	};
	
	// 表单验证
	$inputForm.validate({
		rules: {
			title: {
				required: true
			},
			image: {
				required: true
			}
			<?php if ($kind == EVENT_KIND_COMPETITION): ?>
			,subtitle: {
				required: true,
			}
			<?php endif; ?>
		},
		submitHandler: function(form) {
			if (counterparts == null || counterparts.length == 0) {
				$.message("error", "对阵图不能为空！");
				return false;
			}
			if (hasTicket.checked) {
				if ($.trim($("input[name=ticket_title]").val()).length == 0) {
					$.message("error", "门票标题不能为空！");
					return false;
				}
				if ($.trim($("input[name=ticket_image]").val()).length == 0) {
					$.message("error", "门票图片要上传！");
					return false;
				}
				if (prices == null || prices.length == 0) {
					$.message("error", "请您添加大于1个门票价格");
					return false;
				}
			}
			$("input[name=counterparts]").val(JSON.stringify(counterparts));
			$("input[name=ticket_prices]").val(JSON.stringify(prices));
			
			return true;
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var ratioWidth = 16;
	var ratioHeight = 11;
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#mainImage",
		resultElement: "#mainImageUrl",
		fileType: "image",
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight
	});
	$.uploader({
		formElement: "#videoUploadForm",
		contextElement: "#uploadStatus",
		resultElement: "#videoUrl",
		fileType: "video",
		before: function() {
			$("#videoUploadBtn").prop("disabled", true);
			$("input[name=link]").prop("disabled", true);
		},
		done: function() {
			$("#videoUploadBtn").prop("disabled", false);
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
	
	// item info
	$("#a_player_id").ajaxChosen({
		minTermLength: 1,
		type: "GET",
		url: "<?= base_url() ?>admin/member/ajax_list",
		data: {kind: '<?= MEMBER_KIND_PLAYER ?>'},
		dataType: 'json'
	}, function (data) {
		var results = [];
		$.each(data, function (i, val) {
			results.push({ value: val.id, text: val.label });
		});
		return results;
	});
	$("#b_player_id").ajaxChosen({
		minTermLength: 1,
		type: "GET",
		url: "<?= base_url() ?>admin/member/ajax_list",
		data: {kind: '<?= MEMBER_KIND_PLAYER ?>'},
		dataType: 'json'
	}, function (data) {
		var results = [];
		$.each(data, function (i, val) {
			results.push({ value: val.id, text: val.label });
		});
		return results;
	});

	function refreshCounterpartOrder() {
		counterparts = new Array();
		$("#listCounterparts").find("tr.item").each(function(index) {
			$(this).children().eq(0).text(index + 1);
			counterparts.push({
				a_player_id: $.trim($(this).children().eq(1).data('id')),
				a_player: $.trim($(this).children().eq(1).text()),
				b_player_id: $.trim($(this).children().eq(2).data('id')),
				b_player: $.trim($(this).children().eq(2).text()),
				winner: $.trim($(this).children().eq(4).data('id')),
				winner_name: $.trim($(this).children().eq(4).text()),
				description: $.trim($(this).children().eq(3).text()) 
			});
		});
	}
	
	var $dlgEditCounterpart = $("#dlgEditCounterpart");
	
	$("#listCounterparts + .addItemBtn").click(function() {
		$dlgEditCounterpart.find(".modal-title").text("添加对阵");
		$dlgEditCounterpart.find("#index").val(-1);
		$dlgEditCounterpart.find("#description").val("");
		
		$dlgEditCounterpart.find("#a_player_id").val("");
		$dlgEditCounterpart.find("#a_player_id").trigger("chosen:updated");
		
		$dlgEditCounterpart.find("#b_player_id").val("");
		$dlgEditCounterpart.find("#b_player_id").trigger("chosen:updated");
		
		$dlgEditCounterpart.find("input[name=winner][value='']").iCheck("check");
		
		$dlgEditCounterpart.modal('show');
	});
	
	$("#listCounterparts").on("click", ".editItemBtn", function() {
		var $parent = $(this).parents("tr");
		var index = $parent.index() - 1;
		$dlgEditCounterpart.find(".modal-title").text("编辑对阵");
		$dlgEditCounterpart.find("#index").val(index);
		$dlgEditCounterpart.find("#description").val(counterparts[index].description);
		
		if ($dlgEditCounterpart.find("#a_player_id").find("option[value=" + counterparts[index].a_player_id + "]").length == 0) {
			var html = "<option value='" + counterparts[index].a_player_id + "'>" + counterparts[index].a_player + "</option>";
			$dlgEditCounterpart.find("#a_player_id").append(html);
		}
		$dlgEditCounterpart.find("#a_player_id").val(counterparts[index].a_player_id);
		$dlgEditCounterpart.find("#a_player_id").trigger("chosen:updated");
		
		if ($dlgEditCounterpart.find("#b_player_id").find("option[value=" + counterparts[index].b_player_id + "]").length == 0) {
			var html = "<option value='" + counterparts[index].b_player_id + "'>" + counterparts[index].b_player + "</option>";
			$dlgEditCounterpart.find("#b_player_id").append(html);
		}
		$dlgEditCounterpart.find("#b_player_id").val(counterparts[index].b_player_id);
		$dlgEditCounterpart.find("#b_player_id").trigger("chosen:updated");
		
		$dlgEditCounterpart.find("input[name=winner][value='" + counterparts[index].winner + "']").iCheck("check");
		
		$dlgEditCounterpart.modal('show');
	});
	
	$("#listCounterparts").on("click", ".deleteItemBtn", function() {
		var $parent = $(this).parents("tr");
		//counterparts.splice($parent.index() - 1, 1);
		$parent.remove();
		refreshCounterpartOrder();
		if ($("#listCounterparts").find("tr.item").size() == 0) {
			$("#listCounterparts").append('<tr class="empty-row"><td class="text-center" colspan="6">没有对阵图</td></tr>');
		}
	});
	
	$("#listCounterparts").sortable({
		items: "tr.item",
		update: function(event, ui) {
			refreshCounterpartOrder();
		}
	});
	
	var $editCounterpartForm = $("#editCounterpartForm");
	$editCounterpartForm.validate({
		rules: {
			a_player_id: {
				required: true,
				min: 0
			},
			b_player_id: {
				required: true,
				min: 0
			},
			winner: {
				//required: true
			}
		},
		submitHandler: function(form) {
			var index = $dlgEditCounterpart.find("#index").val();
			var a_player_id = $dlgEditCounterpart.find("#a_player_id").val();
			var a_player = $dlgEditCounterpart.find("#a_player_id").find("option:selected").text();
			var b_player_id = $dlgEditCounterpart.find("#b_player_id").val();
			var b_player = $dlgEditCounterpart.find("#b_player_id").find("option:selected").text();
			var winner = $dlgEditCounterpart.find("input[name=winner]:checked").val();
			var winner_name = (winner == 'a') ? a_player : ( (winner == 'b') ? b_player : '' );
			var description = $dlgEditCounterpart.find("#description").val();
			if (a_player_id == b_player_id) {
				$.message('warn', '选手A和选手B不能一样的！');
				return false;
			}
			
			if (index == -1) {
				$("#listCounterparts").find("tr.empty-row").remove();
				
				var html = '<tr class="item movable">';
				html += '<td>' + ($("#listCounterparts").find("tr.item").size() + 1) + '</td>';
				html += '<td data-id="' + a_player_id + '">' + a_player + '</td>';
				html += '<td data-id="' + b_player_id + '">' + b_player + '</td>';
				html += '<td>' + description + '</td>';
				html += '<td data-id="' + winner + '">' + winner_name + '</td>';
				html += '<td>';
				html += '<a class="editItemBtn m-r" title="编辑"><i class="fa fa-edit"></i></a>';
				html += '<a class="deleteItemBtn" title="删除"><i class="fa fa-trash-o"></i></a>';
				html += '</td>';
				html += '</tr>';
				$("#listCounterparts").append(html);
				
				counterparts.push({
					a_player_id: a_player_id,
					a_player: a_player,
					b_player_id: b_player_id,
					b_player: b_player,
					description: description,
					winner: winner,
					winner_name: winner_name
				});
			} else {
				var $tr = $("#listCounterparts").find("tr.item").eq(index);
				$tr.children().eq(1).text(a_player);
				$tr.children().eq(1).data("id", a_player_id);
				$tr.children().eq(2).text(b_player);
				$tr.children().eq(2).data("id", b_player_id);
				$tr.children().eq(3).text(description);
				$tr.children().eq(4).text(winner_name);
				$tr.children().eq(4).data("id", winner);

				counterparts[index] = {
					a_player_id: a_player_id,
					a_player: a_player,
					b_player_id: b_player_id,
					b_player: b_player,
					description: description,
					winner: winner,
					winner_name: winner_name
				};
			}
			$dlgEditCounterpart.modal('hide');
			return false;
		}
	});
	
	<?php if ($isNew): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	<?php endif; ?>
	$("#ratio").text(ratioWidth + "：" + ratioHeight);
	$(".image-wrapper.im").addClass('ratio-' + ratioWidth + "-" + ratioHeight);

	<?php if (empty($itemInfo['ticket_image'])): ?>
	$("#ticketImage").attr("src", "<?=base_url()?>resources/images/add_3_4.png");
    <?php endif; ?>
    <?php if (empty($itemInfo['ticket_pos_image'])): ?>
	$("#ticketPosImage").attr("src", "<?=base_url()?>resources/images/add_16_11.png");
    <?php endif; ?>
    
	$.uploader({
		formElement: "#ticketImageUploadForm",
		contextElement: ".image-wrapper.ti",
		previewElement: "#ticketImage",
		resultElement: "#ticketImageUrl",
		fileType: "image",
		ratioWidth: 3,
		ratioHeight: 4
	});
	$.uploader({
		formElement: "#ticketPosImageUploadForm",
		contextElement: ".image-wrapper.tpi",
		previewElement: "#ticketPosImage",
		resultElement: "#ticketPosImageUrl",
		fileType: "image"
	});
	
	function refreshPriceOrder() {
		prices = new Array();
		$("#listPrices").find("tr.item").each(function(index) {
			$(this).children().eq(0).text(index + 1);
			prices.push({
				name: $.trim($(this).children().eq(1).text()),
				price: $.trim($(this).children().eq(2).text()), 
				count: $.trim($(this).children().eq(3).text()), 
				color: $.trim($(this).children().eq(1).data("color")) 
			});
		});
		
		$priceChanged.val("1");
	}
	
	var $dlgEditPrice = $("#dlgEditPrice");
	
	$("#listPrices + .addItemBtn").click(function() {
		$dlgEditPrice.find(".modal-title").text("添加价格");
		$dlgEditPrice.find("#index").val(-1);
		$dlgEditPrice.find("#name").val("");
		$dlgEditPrice.find("#price").val("");		
		$dlgEditPrice.find("#count").val("1");
		if ($dlgEditPrice.find("#color").length > 0) {
			$dlgEditPrice.find("#color").val("#000000");
			$dlgEditPrice.find(".pos-colorpicker i").css("background-color", "#000000");
		}		
		$dlgEditPrice.modal('show');
	});
	
	$("#listPrices").on("click", ".editItemBtn", function() {
		var $parent = $(this).parents("tr");
		var index = $parent.index() - 1;
		$dlgEditPrice.find(".modal-title").text("编辑价格");
		$dlgEditPrice.find("#index").val(index);
		$dlgEditPrice.find("#name").val(prices[index].name);
		$dlgEditPrice.find("#price").val(prices[index].price);
		$dlgEditPrice.find("#count").val(prices[index].count);
		if ($dlgEditPrice.find("#color").length > 0) {
			$dlgEditPrice.find("#color").val(prices[index].color);
			$dlgEditPrice.find(".pos-colorpicker i").css("background-color", prices[index].color);
		}
		$dlgEditPrice.modal('show');
	});
	
	$("#listPrices").on("click", ".deleteItemBtn", function() {
		var $parent = $(this).parents("tr");
		//price.splice($parent.index() - 1, 1);
		$parent.remove();
		refreshPriceOrder();
		
		if ($("#listPrices").find("tr.item").size() == 0) {
			$("#listPrices").append('<tr class="empty-row"><td class="text-center" colspan="4">没有门票设置</td></tr>');
		}
	});
	
	$("#listPrices").sortable({
		items: "tr.item",
		update: function(event, ui) {
			refreshPriceOrder();
		}
	});
	
	var $editPriceForm = $("#editPriceForm");
	$editPriceForm.validate({
		rules: {
			name: {
				required: true
			},
			price: {
				required: true,
				min: 0.01
			},
			count: {
				required: true,
				min: 1
			}
		},
		submitHandler: function(form) {
			var index = $dlgEditPrice.find("#index").val();
			var name = $dlgEditPrice.find("#name").val();
			var price = $dlgEditPrice.find("#price").val();
			var count = $dlgEditPrice.find("#count").val();
			var color = $dlgEditPrice.find("#color").val();
			
			if (index == -1) {
				$("#listPrices").find("tr.empty-row").remove();
				
				var html = '<tr class="item movable">';
				html += '<td>' + ($("#listPrices").find("tr.item").size() + 1) + '</td>';
				if ($dlgEditPrice.find("#color").length > 0) {
					html += '<td data-color="' + color + '"><i class="pos-color" style="background-color:' + color + '"></i> ' + name + '</td>';
				} else {
					html += '<td>' + name + '</td>';
				}
				html += '<td>' + price + '</td>';
				html += '<td>' + count + '</td>';
				html += '<td>';
				html += '<a class="editItemBtn m-r" title="编辑"><i class="fa fa-edit"></i></a>';
				html += '<a class="deleteItemBtn" title="删除"><i class="fa fa-trash-o"></i></a>';
				html += '</td>';
				html += '</tr>';
				$("#listPrices").append(html);
				
				prices.push({
					name: name,
					price: price,
					count: count,
					color: color
				});
			} else {
				var $tr = $("#listPrices").find("tr.item").eq(index);
				if ($dlgEditPrice.find("#color").length > 0) {
					$tr.children().eq(1).html('<i class="pos-color" style="background-color:' + color + '"></i> ' + name);
					$tr.children().eq(1).data("color", color);
				} else {
					$tr.children().eq(1).text(name);
				}				
				$tr.children().eq(2).text(price);
				$tr.children().eq(3).text(count);

				prices[index] = {
					name: name,
					price: price,
					count: count,
					color: color
				};
			}
			$dlgEditPrice.modal('hide');
			$priceChanged.val("1");
			return false;
		}
	});
	
	$(".pos-colorpicker").colorpicker();

	<?php if ($kind == EVENT_KIND_MATCH): ?>
	
	<?php if (0): ?>
	$("#btnShowMap").click(function() {
		$("#mapWrapper").toggle();
		$(this).find("i.fa").toggleClass("fa-map-marker").toggleClass("fa-angle-double-up");
	});
	<?php endif; ?>
	
	<?php if (!empty($itemInfo['link'])): ?>
	$("#videoUploadBtn").prop("disabled", true);
	<?php elseif (!empty($itemInfo['video'])): ?>
	$("input[name=link]").prop("disabled", true);
	<?php endif; ?>
	$("input[name=link]").on("input", function() {
		if ( $.trim($(this).val()).length == 0 ) {
			$("#videoUploadBtn").prop("disabled", false);
		} else {
			$("#videoUploadBtn").prop("disabled", true);
		}
	});
	
	<?php endif; ?>
});
</script>
<style type="text/css">
.pos-color {
	display: inline-block;
	width: 16px;
	height: 16px;
	vertical-align: text-top;
	cursor: pointer;
	margin-right: 5px;
}
</style>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?><?= getEventKind($kind) ?>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="../save" method="post" class="form-horizontal">
				<input type="hidden" name="kind" value="<?=$kind?>" />
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>
				<input type="hidden" name="counterparts" value="" />
				<input type="hidden" name="ticket_prices" value="" />
				<input type="hidden" name="price_changed" value="0" />
				<?php if ($kind == EVENT_KIND_MATCH): ?>
				<input type="hidden" name="longitude" value="<?= $isNew ? '' : $itemInfo['longitude']?>">
				<input type="hidden" name="latitude" value="<?= $isNew ? '' : $itemInfo['longitude']?>">
				<?php endif; ?>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						标&nbsp;&nbsp;题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="title" class="form-control" value="<?= $isNew ? '' : $itemInfo['title']?>"/>
					</div>
				</div>
				<?php if ($kind == EVENT_KIND_COMPETITION): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						副 标 题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="subtitle" class="form-control" value="<?= $isNew ? '' : $itemInfo['subtitle'] ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						类&nbsp;&nbsp;型：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="type">
							<option value="<?= EVENT_COMPETITION_TYPE1 ?>" <?php if ($itemInfo['type'] == EVENT_COMPETITION_TYPE1): ?>selected="selected"<?php endif; ?>>栏目1</option>
							<option value="<?= EVENT_COMPETITION_TYPE2 ?>" <?php if ($itemInfo['type'] == EVENT_COMPETITION_TYPE2): ?>selected="selected"<?php endif; ?>>栏目2</option>
						</select>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						日&nbsp;&nbsp;期：
					</label>
					<div class="col-sm-8 col-md-9">
						<input type="text" class="form-control Wdate Wdate-YMDhms pull-left" name="event_date" value="<?= $isNew ? '' : d2dtns($itemInfo['event_date']) ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm'});" placeholder="选择时间">
						<a role="button" class="clear-time" href="javascript:;" onclick="$(this).siblings('input').val('');">清除时间</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						地&nbsp;&nbsp;点：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="input-group" style="width:100%;">
							<input type="text" name="location" class="form-control" value="<?= $isNew ? '' : $itemInfo['location']?>"/>
							<?php if ($kind == EVENT_KIND_MATCH && 0): ?>
							<a href="javascript:;" id="btnShowMap" class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php if ($kind == EVENT_KIND_MATCH && 0): ?>
				<div id="mapWrapper" class="form-group" style="display:none;">
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-8 col-md-7">
						<div id="map" style="height:500px; border:1px solid #ddd; padding:10px;"></div>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						缩 略 图：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="image-wrapper im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['image']) ?>">
							<input id="mainImageUrl" name="image" type="hidden" value="<?= $isNew ? '' : $itemInfo['image'] ?>">
							<div class="loading">
								<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
							</div>
						</div>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						建议比例<span id="ratio"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						上传视频：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="hidden" name="video" id="videoUrl" value="<?= $isNew ? '' : $itemInfo['video'] ?>">
						<button id="videoUploadBtn" type="button" class="btn btn-white" onclick="$('#fileVideo').click()">选择视频 ...</button>
						<span id="uploadStatus" <?php if (!$isNew && !empty($itemInfo['video'])): ?>class="uploaded"<?php endif; ?>>
							<i class="fa fa-spinner fa-pulse fa-fw gray"></i>
							<i class="fa fa-check green"></i>
						</span>
						<span id="uploadProgress"></span>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						支持：mp4、flv、f4v等视频通用格式
					</div>
				</div>
				<?php if ($kind == EVENT_KIND_MATCH): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						链接地址：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="link" class="form-control" value="<?= $isNew ? '' : $itemInfo['link'] ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						主板方ID：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="organization" class="form-control" value="<?= $isNew ? '' : $itemInfo['organization_id'] ?>"/>
					</div>
				</div>
				<?php endif; ?>
				<hr/>
				<div class="form-group">
					<h3 class="col-sm-offset-1">
						对阵图
					</h3>
					<div></div>
					<div id="counterpartWrapper" class="col-sm-offset-1 m-t-md col-sm-11 col-md-8">
						<table id="listCounterparts" class="table list">
							<tr>
								<th>排序</th>
								<th>选手A</th>
								<th>选手B</th>
								<th>备注</th>
								<th>胜利者</th>
								<th>操作</th>
							</tr>
							<?php if (!$isNew && !empty($itemInfo['counterparts'])): ?>
								<?php foreach ($itemInfo['counterparts'] as $key=>$part): ?>
									<tr class="item movable">
										<td>
											<?= $key + 1 ?>
										</td>
										<td data-id="<?= $part['a_player_id'] ?>">
											<?= $part['a_player'] ?>
										</td>
										<td data-id="<?= $part['b_player_id'] ?>">
											<?= $part['b_player'] ?>
										</td>
										<td>
											<?= $part['description'] ?>
										</td>
										<td data-id="<?= $part['winner'] ?>">
											<?= $part['winner_name'] ?>
										</td>
										<td>
											<a class="editItemBtn m-r" data-id="<?= $part['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
											<a class="deleteItemBtn" data-id="<?= $part['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if ($isNew && empty($itemInfo['ticket_prices'])): ?>
								<tr class="empty-row"><td class="text-center" colspan="6">没有对阵图</td></tr>
							<?php endif; ?>
						</table>
						<a role="button" class="addItemBtn m-t-sm"><i class="fa fa-plus"></i> 添加对阵</a>
					</div>
				</div>
				
				<div class="form-group m-t-lg p-t-md">
					<label class="col-sm-3 col-md-2 control-label">
						售票开启：
					</label>
					<div class="col-sm-8 col-md-9 p-t-xxs">
						<input type="checkbox" name="has_ticket" id="hasTicket" <?php if ($itemInfo['has_ticket']): ?>checked="checked"<?php endif; ?>>
						<span class="value-tip m-l-sm">您要设置门票信息，<?= getEventKind($kind) ?>日期已过去时在客服端只显示。</span>
					</div>
				</div>
				<div id="ticketWrapper" <?php if (!$itemInfo['has_ticket']): ?>style="display:none;"<?php endif; ?>>
					<hr/>
					<div class="form-group">
						<h3 class="col-sm-offset-1">
							门票设置
						</h3>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							缩 略 图：
						</label>
						<div class="pull-left m-l">
							<div class="image-wrapper ti ratio-3-4" role="button" onclick="$('#fileTicketImage').click()">
								<img class="preview" id="ticketImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['ticket_image']) ?>">
								<input id="ticketImageUrl" name="ticket_image" type="hidden" value="<?= $isNew ? '' : $itemInfo['ticket_image'] ?>">
								<div class="loading">
									<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
								</div>
							</div>
						</div>
						<?php if ($kind == EVENT_KIND_COMPETITION): ?>
						<label class="pull-left control-label m-l-lg p-l-m">
							坐 席 图：
						</label>
						<div class="pull-left m-l">
							<div class="image-wrapper tpi ratio-16-11" role="button" onclick="$('#fileTicketPosImage').click()">
								<img class="preview" id="ticketPosImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['ticket_pos_image']) ?>">
								<input id="ticketPosImageUrl" name="ticket_pos_image" type="hidden" value="<?= $isNew ? '' : $itemInfo['ticket_pos_image'] ?>">
								<div class="loading">
									<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label required">
							标&nbsp;&nbsp;题：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="text" name="ticket_title" class="form-control" value="<?= $isNew ? '' : $itemInfo['ticket_title']?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							介&nbsp;&nbsp;绍：
						</label>
						<div class="col-sm-9 col-md-7">
							<textarea class="hidden" name="ticket_note"><?= $isNew ? '' : $itemInfo['ticket_note'] ?></textarea>
							<div id="summernote"><?= $isNew ? '' : $itemInfo['ticket_note'] ?></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							自提方式：
						</label>
						<div class="col-sm-6 col-md-4">
							<textarea class="form-control" name="ticket_take_desc" style="height:100px"><?= $isNew ? '' : $itemInfo['ticket_take_desc'] ?></textarea>
						</div>
					</div>
                    <div class="form-group m-t-lg">
						<label class="col-sm-offset-1 control-label">
							门票价格
						</label>
						<div></div>
						<div id="priceWrapper" class="col-sm-offset-1 m-t-md col-sm-11 col-md-8">
							<table id="listPrices" class="table list">
								<tr>
									<th>排序</th>
									<th>名称</th>
									<th>价格</th>
									<th>票数</th>
									<th>操作</th>
								</tr>
								<?php if (!$isNew && !empty($itemInfo['ticket_prices'])): ?>
									<?php foreach ($itemInfo['ticket_prices'] as $key=>$price): ?>
										<tr class="item movable">
											<td>
												<?= $key + 1 ?>
											</td>
											<td data-color="<?= $price['color'] ?>">
												<?php if ($price['color']): ?>
													<i class="pos-color" style="background-color: <?= $price['color'] ?>"></i> 
												<?php endif; ?>
												<?= $price['name'] ?>
											</td>
											<td>
												<?= $price['price'] ?>
											</td>
											<td>
												<?= $price['count'] ?>
											</td>
											<td>
												<a class="editItemBtn m-r" data-id="<?= $price['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
												<a class="deleteItemBtn" data-id="<?= $price['id'] ?>" title="删除"><i class="fa fa-trash-o"></i></a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
								<?php if ($isNew && empty($itemInfo['ticket_prices'])): ?>
									<tr class="empty-row"><td class="text-center" colspan="4">没有门票设置</td></tr>
								<?php endif; ?>
							</table>
							<a role="button" class="addItemBtn m-t-sm"><i class="fa fa-plus"></i> 添加价格</a>
						</div>
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
				<form id="videoUploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileVideo">
				</form>
				<form id="ticketImageUploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileTicketImage">
				</form>
				<form id="ticketPosImageUploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileTicketPosImage">
				</form>
			</div>
			
			<div class="modal fade" tabindex="-1" role="dialog" id="dlgEditCounterpart">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">添加对阵</h3>
						</div>
						<div class="modal-body">
						<form id="editCounterpartForm" class="form-horizontal" method="post">
							<input type="hidden" name="index" id="index" />
							<div class="form-group">
								<label class="control-label">选手A：</label>
								<select name="a_player_id" id="a_player_id" class="form-control" data-placeholder="请输入ID或姓名">
								</select>
							</div>
							<div class="form-group">
								<label class="control-label">选手B：</label>
								<select name="b_player_id" id="b_player_id" class="form-control" data-placeholder="请输入ID或姓名">
								</select>
							</div>
							<div class="form-group">
								<label class="control-label">胜利者：</label>
								<div class="p-t-xxs">
									<label role="button" class="m-l">
										<input type="radio" class="i-check" name="winner" value="" /> 未定
									</label>
									<label role="button" class="m-l">
										<input type="radio" class="i-check" name="winner" value="a" /> 选手A
									</label>
									<label role="button" class="m-l">
										<input type="radio" class="i-check" name="winner" value="b" /> 选手B
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label">备注：</label>
								<input type="text" class="form-control" name="description" id="description">
							</div>
						</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
							<button type="submit" class="btn btn-primary" onclick="$('#editCounterpartForm').submit();">保&nbsp;&nbsp;存</button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" tabindex="-1" role="dialog" id="dlgEditPrice">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">添加价格</h3>
						</div>
						<div class="modal-body">
						<form id="editPriceForm" class="form-horizontal" method="post">
							<input type="hidden" name="index" id="index" />
							<div class="form-group">
								<label class="control-label">名称：</label>
								<input type="text" class="form-control" name="name" id="name">
							</div>
							<?php if ($kind == EVENT_KIND_COMPETITION): ?>
							<div class="form-group">
								<label class="control-label">坐席颜色：</label>
								<div class="input-group pos-colorpicker">
                                    <input type="text" value="" class="form-control" name="color" id="color" />
                                    <span class="input-group-addon"><i></i></span>
                                </div>
							</div>
							<?php endif; ?>
							<div class="form-group">
								<label class="control-label">价格：</label>
								<input type="text" class="form-control" name="price" id="price">
							</div>
							<div class="form-group">
								<label class="control-label">票数：</label>
								<input type="text" class="form-control" name="count" id="count">
							</div>
						</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
							<button type="submit" class="btn btn-primary" onclick="$('#editPriceForm').submit();">保&nbsp;&nbsp;存</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<?php if ($kind == EVENT_KIND_MATCH && 0): ?>
<script src="http://api.map.baidu.com/api?v=2.0&ak=<?=$this->config->item('baidu_map_js_appkey')?>" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.js"></script>
<link rel="stylesheet" href="http://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.css" />
<script type="text/javascript">
	// 百度地图
	var map = new BMap.Map("map");
	map.enableScrollWheelZoom();//滚轮缩放事件    
	map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
	map.setDefaultCursor("crosshair");

	var marker = null;
	<?php if (floatval($itemInfo['longitude']) > 0 && floatval($itemInfo['latitude']) > 0): ?>
	var point = new BMap.Point(<?= $itemInfo['longitude'] ?>, <?= $itemInfo['latitude'] ?>);  // 创建点坐标
	marker = new BMap.Marker(point);
	map.addOverlay(marker);
	map.centerAndZoom(point, 16);                 // 初始化地图，设置中心点坐标和地图级别
	<?php elseif (!empty($itemInfo['location'])): ?>
	var myGeo = new BMap.Geocoder();
	myGeo.getPoint("<?= $itemInfo['location'] ?>", function(point){
		if (point) {
			marker = new BMap.Marker(point);
			map.centerAndZoom(point, 16);
			map.addOverlay(marker);
		} else{
			map.centerAndZoom("北京", 15);
		}
	}, "");
	<?php else: ?>
	map.centerAndZoom("北京", 15);
	<?php endif; ?>
	
	var geoc = new BMap.Geocoder();
	function getPosition(e){
		$("input[name=longitude]").val(e.point.lng);
		$("input[name=latitude]").val(e.point.lat);
		
		map.removeOverlay(marker);

		marker = new BMap.Marker(e.point);
		map.addOverlay(marker);
		
		geoc.getLocation(e.point, function(rs){
			var addComp = rs.addressComponents;
			$("input[name=location]").val(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
		});
	}
	map.addEventListener("click", getPosition);
	
	map.panBy(305, 250);
</script>
<?php endif; ?>
</html>