<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
添加票券
</title>
<meta name="author" content="STSOFT Team" /
><meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/summernote.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/summernote/lang/summernote-zh-CN.js"></script>
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
			name: {
				required: true
			},
			view_name: {
				required: true
			},
			province_id: {
				required: true
			},
			city_id: {
				required: true
			}
		},
		submitHandler: function(form) {
			return true;
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var $galleryWrapper = $("#galleryWrapper");

	var ratioWidth = 1;
	var ratioHeight = 1;
	
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

	function addCarousel(index, url) {
		$(".carousel-indicators").append('<li data-slide-to="' + index + '" data-target="#carousel"></li>');
			
		var html = '<div class="item">';
		html += '<img alt="image" class="img-responsive" width="100%" src="' + BASE + url + '">';
		html += '<div class="carousel-caption"><p></p></div></div>';
		$(".carousel-inner").append(html);
	}
	function refreshOrder() {
		$(".carousel-indicators").empty();
		$(".carousel-inner").empty();
		$(".gallery-image").each(function(index) {
			$(this).find("span.orders").text(index + 1);
			addCarousel(index, $(this).find('input').val());
		});
	}
	$.uploader({
		formElement: "#imageIGUploadForm",
		contextElement: ".image-wrapper.ig",
		fileType: "image",
		//ratioWidth: ratioWidth,
		//ratioHeight: ratioHeight,
		done: function(result) {
			var html = '<div class="gallery-image">';
			html += '<input type="hidden" name="images[]" value="' + result.url + '">';
			html += '<img class="preview" src="' + BASE + result.url + '">';
			html += '<span class="orders">' + $galleryWrapper.children().length + '</span>';
			html += '<i role="button" class="fa fa-close"></i>';
			html += '<i role="button" class="fa fa-search"></i>';
			html += '</div>';
			$galleryWrapper.append(html);
			
			addCarousel($galleryWrapper.children().length - 2, result.url);
		}
	});
	
	$galleryWrapper.on("click", "i.fa-search", function() {
		var index = $(this).parents(".gallery-image").index() - 1;
		$(".carousel-indicators").children().eq(index).addClass("active").siblings().removeClass("active");
		$(".carousel-inner").children().eq(index).addClass("active").siblings().removeClass("active");
		$("#carousel").carousel(index);
		$("#dlgPreview").modal("show");
	});
	$galleryWrapper.on("click", "i.fa-close", function() {
		var $parent = $(this).parents(".gallery-image");
		$parent.remove();
		refreshOrder();
	});
	
	$galleryWrapper.sortable({
		items: "> .gallery-image",
		update: function(event, ui) {
			refreshOrder();
		}
	});
	
	<?php if (empty($itemInfo['thumb'])): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
	<?php endif; ?>
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);

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
					for (i in data.result) {
						var item = data.result[i];
						var html = '<option value="' + item.id + '">' + item.name + '</option>';
						$("#city").append(html);
					};
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
	$("#city").chosen().change(function() {
		var cityName = $("#city").find(":selected").text();
		$("#cityName").val(cityName);
	});
	<?php if (empty($itemInfo['area_id'])): ?>
	updateCities();
	<?php endif; ?>
	
	$("#btnShowMap").click(function() {
		$("#mapWrapper").toggle();
		$(this).find("i.fa").toggleClass("fa-map-marker").toggleClass("fa-angle-double-up");
	});
	//$("#mapWrapper").hide();
});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			添加票券
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="do_add" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						名&nbsp;&nbsp;称：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="title" class="form-control" value=""/>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						用于后台显示
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						票&nbsp;&nbsp;价：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="tel" name="price" class="form-control" value=""/>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						价&nbsp;&nbsp;格：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="tel" name="count_price" class="form-control" value=""/>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						尺&nbsp;&nbsp;寸：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="size" class="form-control" value="" placeholder="尺寸（英寸）"/>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						厚&nbsp;&nbsp;度：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="land" class="form-control" value="" placeholder="厚度（毫米）"/>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						上传图片：
					</label>
					<div class="col-sm-6 col-md-4">
						<div class="image-wrapper m-r im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="">
							<input id="mainImageUrl" name="image" type="hidden" value="">
							<div class="loading">
								<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						省&nbsp;&nbsp;份：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control s-lg" name="province_id">
		                    <?php foreach ($provinces as $key => $value):  ?>
		                        <option value="<?= $key ?>" ><?= $value ?></option>
		                    <?php endforeach; ?>
		                </select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						添加时间：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" name="create_date" class="form-control" value="<?= date("Y-m-d H:i:s")?>"/>
					</div>
				</div>				

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						票券介绍：
					</label>
					<div class="col-sm-9 col-md-6">
						<textarea class="hidden" name="description"></textarea>
						<div id="summernote"></div>
					</div>
				</div>
				<hr/>
				
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
				<form id="imageIGUploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileImageIG" multiple>
					<input type="hidden" name="watermark" id="watermark" value="0">
				</form>
				<div class="hidden">
					<form id="uploadForm" method="post" enctype="multipart/form-data">
						<input type="file" name="file" id="fileMainImg">
					</form>
				</div>
			</div>
		</div>
	</div>
</body>


<?php if (0): ?>

<script src="http://api.map.baidu.com/api?v=2.0&ak=<?=$this->config->item('baidu_map_js_appkey')?>" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.js"></script>
<link rel="stylesheet" href="http://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.css" />
<script type="text/javascript">
	// 百度地图
	var map = new BMap.Map("map");
	map.enableScrollWheelZoom();//滚轮缩放事件    
	//map.enableContinuousZoom(); // 开启连续缩放效果    
	//map.enableKeyboard(); //键盘方向键缩放事件    
	//map.enableInertialDragging();　// 开启惯性拖拽效果     
	map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
	//map.addControl(new BMap.ScaleControl());
	//map.addControl(new BMap.OverviewMapControl());
	//map.addControl(new BMap.MapTypeControl());
	map.setDefaultCursor("crosshair");

	var marker = null;
	<?php if (floatval($itemInfo['longitude']) > 0 && floatval($itemInfo['latitude']) > 0): ?>
	var point = new BMap.Point(<?= $itemInfo['longitude'] ?>, <?= $itemInfo['latitude'] ?>);  // 创建点坐标
	marker = new BMap.Marker(point);
	map.addOverlay(marker);
	map.centerAndZoom(point, 17);                 // 初始化地图，设置中心点坐标和地图级别
	<?php elseif (!empty($itemInfo['city'])): ?>
	map.centerAndZoom("<?= $itemInfo['city'] ?>", 15);
	<?php else: ?>
	map.centerAndZoom("北京", 15);
	<?php endif; ?>
	
	function getPosition(e){
		$("input[name=longitude]").val(e.point.lng);
		$("input[name=latitude]").val(e.point.lat);
		
		map.removeOverlay(marker);

		marker = new BMap.Marker(e.point);
		map.addOverlay(marker);
	}
	map.addEventListener("click", getPosition);
	
	map.panBy(305, 250);
</script>

<?php endif; ?>

</html>
