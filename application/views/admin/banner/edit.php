<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?>Banner
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/switchery/switchery.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/ajax-chosen.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
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
		submitHandler: function() {
			var kind = $("#itemKind").val();

			if (kind == '<?= BANNER_KIND_URL ?>') {
				if ($.trim($("#itemInfoUrl").val()).length == 0) {
					alert("请您输入链接地址！");
					return false;
				}
			} else if ($("#itemInfoId").val() == null) {
				alert("请您选择ID！");
				return false;
			}
			
			if (isShowLimit.checked) {
				var sh = $("#startHour").val();
				var sm = $("#startMinute").val();
				var eh = $("#endHour").val();
				var em = $("#endMinute").val();
				if (sh != '' && sm != '' && eh != '' && em != '') {
					if ( (parseInt(sh) > parseInt(eh)) || 
						(parseInt(sh) == parseInt(eh) && parseInt(sm) >= parseInt(em))) 
					{
						alert("起始时间不能大于结束时间！");
						return false;
					}
				}
			}
			return true;
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var ratioWidth = 16;
	var ratioHeight = 8;
	
	$.uploader({
		formElement: "#uploadForm",
		contextElement: ".image-wrapper.im",
		previewElement: "#mainImage",
		resultElement: "#mainImageUrl",
		fileType: "image",
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight
	});
	
	<?php if ($isNew): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	$("input[name=platform]").eq(0).iCheck("check");
	<?php endif; ?>
	$("#ratio").text(ratioWidth + "：" + ratioHeight);
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);
	
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
		$("#itemInfoId").ajaxChosen({
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
		if (kind == '<?= BANNER_KIND_URL ?>') {
			$("#itemUrlWrapper").show();
			$("#itemIdWrapper").hide();
		} else {
			$("#itemUrlWrapper").hide();
			$("#itemIdWrapper").show();
			
			$("#itemInfoId").find("option").remove();
			$("#itemInfoId").trigger("chosen:updated");
			$("#itemInfoId").chosen("destory");
			$('.chosen-container').find(".search-field > input, .chosen-search > input").unbind('keyup');
			
			updateList();
		}
	});
	
	<?php if ($isNew || $itemInfo['item_kind'] != BANNER_KIND_URL): ?>
	updateList();
	<?php endif; ?>
	
	
	// limit options
	var isShowLimit = document.querySelector('#isShowLimit');
	var switchery = new Switchery(isShowLimit, { size: 'small', color: '#18a689' });
	isShowLimit.onchange = function() {
		if (isShowLimit.checked) {
			$("#limitWrapper").show();
		} else {
			$("#limitWrapper").hide();
		}
	};

	var isAreaLimit = document.querySelector('#isAreaLimit');
	var switchery2 = new Switchery(isAreaLimit, { size: 'small', color: '#18a689' });
	isAreaLimit.onchange = function() {
		if (isAreaLimit.checked) {
			$("#areaLimitWrapper").show();
		} else {
			$("#areaLimitWrapper").hide();
		}
	};
	
	var isOpenDate = document.querySelector('#isOpenDate');
	var switchery3 = new Switchery(isOpenDate, { size: 'small', color: '#18a689' });
	isOpenDate.onchange = function() {
		if (isOpenDate.checked) {
			$("#openDateWrapper").show();
		} else {
			$("#openDateWrapper").hide();
		}
	};
	
	function updateCities() {
		$("#city").empty();
		$.ajax({
			url: "<?=base_url()?>common/city_list",
			type: "get",
			data: {province_id: $("#province").val()},
			dataType: "json",
			cache: false,
			success: function(data) {
				if (data.error == 0) {
					var label = $("#province").find("option:selected").text() + '全地域';
					$("#city").append('<option value="' + $("#province").val() + '">' + label + '</option>');
					
					$.each(data.result, function(i, item) {
						var html = '<option data-province="' + $("#province").val() + '" value="' + item.id + '">' + item.name + '</option>';
						$("#city").append(html);
					});
					$("#city").trigger("chosen:updated");
					
					var cityName = $("#city").find(":selected").text();
					$("#cityName").val(cityName);
				}
			}
		});
	}	
	$("#province").chosen().change(function() {
		updateCities();
	});
	$("#city").chosen();
	updateCities();
	
	$("#areaIds").chosen({height: "100px"});
	$("#areaIds_chosen").find("input").remove();
	
	$("#addAreaBtn").click(function() {
		var $item = $("#city").find("option:selected");
		var id = $("#city").val();
		var html = null;
		$options = $("#areaIds").find("option:selected");
		if ($options.filter("[value=" + id + "]").length > 0) {
			return false;
		}
		if ($item.data("province")) {
			var provinceId = $item.data("province");
			if ($options.filter("[value=" + provinceId + "]").length > 0) {
				return false;
			}
			html = '<option data-province="' + provinceId + '" value="' + id + '" selected="selected">' + $item.text() + '</option>';
		} else {
			$options.filter("[data-province=" + id + "]").each(function() {
				$(this).remove();
			});
			html = '<option value="' + id + '" selected="selected">' + $item.text() + '</option>';
		}
		$("#areaIds").append(html);
		$("#areaIds").find("option:not(:selected)").remove();
		$("#areaIds").trigger("chosen:updated");
	});

});
</script>
<style type="text/css">
#areaIds_chosen .chosen-choices {
	min-height: 80px;
	cursor: default !important;
	border: 1px solid #ddd !important;
	box-shadow: 0 0 0 #ddd !important;
}
#areaIds_chosen .chosen-drop {
	display: none !important;
} 
</style>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?>Banner
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="save" method="post" class="form-horizontal">
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>
				<input type="hidden" name="banner_kind" value="<?=BANNER_MAIN?>" />

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						标&nbsp;&nbsp;题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" id="title" name="title" class="form-control" value="<?= $isNew ? '' : $itemInfo['title']?>"/>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						客户端展示的标题
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						类&nbsp;&nbsp;型：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="item_kind" id="itemKind">
							<option value="<?= BANNER_KIND_ARTICLE ?>" <?php if (!$isNew && BANNER_KIND_ARTICLE == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_ARTICLE) ?></option>
							<option value="<?= BANNER_KIND_GALLERY ?>" <?php if (!$isNew && BANNER_KIND_GALLERY == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_GALLERY) ?></option>
							<option value="<?= BANNER_KIND_VIDEO ?>" <?php if (!$isNew && BANNER_KIND_VIDEO == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_VIDEO) ?></option>
							<option value="<?= BANNER_KIND_LIVE ?>" <?php if (!$isNew && BANNER_KIND_LIVE == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_LIVE) ?></option>
							<option value="<?= BANNER_KIND_MEMBER ?>" <?php if (!$isNew && BANNER_KIND_MEMBER == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_MEMBER) ?></option>
							<option value="<?= BANNER_KIND_EVENT ?>" <?php if (!$isNew && BANNER_KIND_EVENT == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_EVENT) ?></option>
							<option value="<?= BANNER_KIND_URL ?>" <?php if (!$isNew && BANNER_KIND_URL == $itemInfo['item_kind']): ?>selected="selected"<?php endif; ?>><?= getBannerKind(BANNER_KIND_URL) ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						输入地址或ID：
					</label>
					<div class="col-sm-6 col-md-4" id="itemUrlWrapper" <?php if ($itemInfo['item_kind'] != BANNER_KIND_URL): ?>style="display:none;"<?php endif; ?>>
						<input type="text" name="item_info_url" id="itemInfoUrl" class="form-control" <?php if ($itemInfo['item_kind'] == BANNER_KIND_URL): ?>value="<?= $itemInfo['item_info'] ?>"<?php endif; ?> >						
					</div>
					<div class="col-sm-6 col-md-4" id="itemIdWrapper" <?php if ($itemInfo['item_kind'] == BANNER_KIND_URL): ?>style="display:none;"<?php endif; ?>>
						<select name="item_info_id" id="itemInfoId" class="form-control" data-placeholder="请输入标题或姓名">
							<?php if ($itemInfo['item_kind'] != BANNER_KIND_URL && !empty($itemInfo['item_info'])): ?>
							<option value="<?= $itemInfo['item_info'] ?>" selected="selected"><?= $itemInfo['item_info_label'] ?></option>
							<?php endif; ?>
						</select>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						选择新闻、图集、视频、比赛、选手输入ID即可、链接支持站外url或直播url
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						上传图片：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="image-wrapper m-r im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['image']) ?>">
							<input id="mainImageUrl" name="image" type="hidden" value="<?= $isNew ? '' : $itemInfo['image'] ?>">
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
						平&nbsp;&nbsp;台：
					</label>
					<div class="col-sm-6 col-md-4 p-t-xxs">
						<input type="radio" class="i-check" name="platform" value="<?= DEVICE_TYPE_ALL ?>" <?php if (!$isNew && $itemInfo['platform'] == DEVICE_TYPE_ALL): ?>checked="checked"<?php endif; ?>/> 全部
						<span class="m-r"></span>
						<input type="radio" class="i-check" name="platform" value="<?= DEVICE_TYPE_IPHONE ?>" <?php if (!$isNew && $itemInfo['platform'] == DEVICE_TYPE_IPHONE): ?>checked="checked"<?php endif; ?>/> 苹果
						<span class="m-r"></span>
						<input type="radio" class="i-check" name="platform" value="<?= DEVICE_TYPE_ANDROID ?>" <?php if (!$isNew && $itemInfo['platform'] == DEVICE_TYPE_ANDROID): ?>checked="checked"<?php endif; ?>/> 安卓
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						显示规则：
					</label>
					<div class="col-sm-6 col-md-4 p-t-xxs">
						<input type="checkbox" name="is_show_limit" id="isShowLimit" <?php if ($itemInfo['is_show_limit']): ?>checked="checked"<?php endif; ?>>
						<span class="value-tip m-l-sm">自动显示时间范围</span>
					</div>
				</div>
				<div id="limitWrapper" <?php if (!$itemInfo['is_show_limit']): ?>style="display:none;"<?php endif; ?>>
					<div class="form-group m-t-md">
						<label class="col-sm-4 col-md-3 control-label">
							显示日期：
						</label>
						<div class="col-sm-8 col-md-9">
							<div class="form-inline">
								<input type="text" class="form-control Wdate Wdate-YMD" name="start_date" id="startDate" value="<?= $itemInfo['start_date'] ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="起始日期">
								<span>-</span>
								<input type="text" class="form-control Wdate Wdate-YMD" name="end_date" id="endDate" value="<?= $itemInfo['end_date'] ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}'});" placeholder="结束日期">
							</div>
						</div>
						<div class="col-sm-offset-4 col-md-offset-3 col-sm-8 col-md-9 value-tip">
							配置之后，在日期区间内显示，不填则为每天显示
						</div>
					</div>
					<div class="form-group m-t-md">
						<label class="col-sm-4 col-md-3 control-label">
							显示时间：
						</label>
						<div class="col-sm-8 col-md-9">
							<div class="form-inline">
								<select name="start_hour" class="form-control" id="startHour">
									<option value="">时</option>
									<?php for ($i = 0; $i < 24; $i++) {
										$selected = ($itemInfo['start_hour'] !== null && intval($itemInfo['start_hour']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
								<span>：</span>
								<select name="start_minute" class="form-control" id="startMinute">
									<option value="">分</option>
									<?php for ($i = 0; $i < 60; $i++) {
										$selected = ($itemInfo['start_minute'] !== null && intval($itemInfo['start_minute']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
								<span class="p-l-xxs p-r-xxs">至</span>
								<select name="end_hour" class="form-control" id="endHour">
									<option value="">时</option>
									<?php for ($i = 0; $i < 24; $i++) {
										$selected = ($itemInfo['end_hour'] !== null && intval($itemInfo['end_hour']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
								<span>：</span>
								<select name="end_minute" class="form-control" id="endMinute">
									<option value="">分</option>
									<?php for ($i = 0; $i < 60; $i++) {
										$selected = ($itemInfo['end_minute'] !== null && intval($itemInfo['end_minute']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
							</div>
						</div>
						<div class="col-sm-offset-4 col-md-offset-3 col-sm-8 col-md-9 value-tip">
							配置之后，在时间区间内显示，不填则为每天显示
						</div>
					</div>
					<div class="form-group m-t-md">
						<label class="col-sm-4 col-md-3 control-label">
							显示地区：
						</label>
						<div class="col-sm-6 col-md-4 p-t-xxs">
							<input type="checkbox" name="is_area_limit" id="isAreaLimit" <?php if ($itemInfo['is_area_limit']): ?>checked="checked"<?php endif; ?>>
							<span class="value-tip m-l-sm">您可以设置显示的地点</span>
						</div>
					</div>
					<div class="form-group m-t-md" id="areaLimitWrapper" <?php if (!$itemInfo['is_area_limit']): ?>style="display:none;"<?php endif; ?>>
						<div class="col-sm-offset-4 col-md-offset-3 col-sm-8 col-md-9 p-t-xxs">
							<input type="radio" class="i-check" name="area_limit_type" value="<?= AREA_LIMIT_WHITELIST ?>" id="whitelist" <?php if ($itemInfo['area_limit_type'] == AREA_LIMIT_WHITELIST): ?>checked="checked"<?php endif; ?>/> <label for="whitelist" role="button">白名单，以下地域可以显示</label>
							<span class="m-r"></span>
							<input type="radio" class="i-check" name="area_limit_type" value="<?= AREA_LIMIT_BLACKLIST ?>" id="blacklist"  <?php if ($itemInfo['area_limit_type'] == AREA_LIMIT_BLACKLIST): ?>checked="checked"<?php endif; ?>/> <label for="blacklist" role="button">黑名单，以下地域不可显示</label>
						</div>
						<div class="col-sm-offset-4 col-md-offset-3 col-sm-8 col-md-9 m-t-md">
							<div class="form-group">
								<div class="pull-left" style="width: 160px;">
                        			<select name="province_id" id="province" data-placeholder="请选择省份..." class="chosen-select form-control">
                        				<?php foreach ($provinces as $province): ?>
                        					<option value="<?= $province['id'] ?>"><?= $province['name'] ?></option>
                        				<?php endforeach; ?>
                        			</select>
                        		</div>
                        		<div class="pull-left p-l-xs" style="width: 180px;">
                        			<select name="city_id" id="city" data-placeholder="请选择城市..." class="chosen-select form-control">
                        			</select>
                        		</div>
                        		<button type="button" class="btn btn-white m-l-xs" id="addAreaBtn">添加</button>
                        	</div>
                        	<div class="form-group" style="width: 400px;">
                        		<select name="area_ids[]" id="areaIds" data-placeholder="请添加地域。" readonly="readonly" class="chosen-select form-control" multiple="multiple">
                        			<?php if (isset($itemInfo['areaLimits']) && !empty($itemInfo['areaLimits'])): ?>
                        			<?php foreach ($itemInfo['areaLimits'] as $aItem) {
                        				if ($aItem['type'] == AREA_TYPE_PROVINCE) {
											echo "<option selected='selected' value='" . $aItem['id'] . "'>" . $aItem['name'] . "全地域</option>";
                        				} else {
											echo "<option selected='selected' data-province='" . $aItem['parent_id'] . "' value='" . $aItem['id'] . "'>" . $aItem['name'] . "</option>";
                        				}
                        			} ?>
                        			<?php endif; ?>
                        		</select>
                        	</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						显示倒计时：
					</label>
					<div class="col-sm-6 col-md-4 p-t-xxs">
						<input type="checkbox" name="is_open_date" id="isOpenDate" <?php if ($itemInfo['is_open_date']): ?>checked="checked"<?php endif; ?>>
						<span class="value-tip m-l-sm">自动显示时间范围</span>
					</div>
				</div>

				<div id="openDateWrapper" <?php if (!$itemInfo['is_open_date']): ?>style="display:none;"<?php endif; ?>>
					<div class="form-group m-t-md">
						<label class="col-sm-4 col-md-3 control-label">
							倒计时：
						</label>
						<div class="col-sm-8 col-md-9">
							<div class="form-inline">
								<input type="text" class="form-control Wdate Wdate-YMD" name="open_date" id="openDate" value="<?= $itemInfo['open_date'] ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd'});" placeholder="起始日期">
								<span>&nbsp;&nbsp;&nbsp;</span>
								<select name="open_hour" class="form-control" id="openHour">
									<option value="">时</option>
									<?php for ($i = 0; $i < 24; $i++) {
										$selected = ($itemInfo['open_hour'] !== null && intval($itemInfo['open_hour']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
								<span>：</span>
								<select name="open_minute" class="form-control" id="openMinute">
									<option value="">分</option>
									<?php for ($i = 0; $i < 60; $i++) {
										$selected = ($itemInfo['open_minute'] !== null && intval($itemInfo['open_minute']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
								<span>：</span>
								<select name="open_second" class="form-control" id="openSecond">
									<option value="">Sec</option>
									<?php for ($i = 0; $i < 60; $i++) {
										$selected = ($itemInfo['open_second'] !== null && intval($itemInfo['open_second']) == $i) ? "selected='selected'" : "";
										echo "<option value='$i' $selected>$i</option>";
									} ?>
								</select>
							</div>
						</div>
						<div class="col-sm-offset-4 col-md-offset-3 col-sm-8 col-md-9 value-tip">
							请输入倒计时
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
			</div>
		</div>
	</div>
</body>
</html>