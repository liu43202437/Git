<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?= ($isNew) ? '添加' : '编辑'?><?= getContentKind($kind) ?>
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
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
<script type="text/javascript" src="<?=base_url()?>resources/plugins/kindeditor/kindeditor.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/ajax-chosen.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
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
			title: {
				required: true
			},
			image: {
				required: true
			},
			hits: {
				min: 0
			}
			<?php if ($kind == CONTENT_KIND_ARTICLE): ?>
			,content: {
				required: {
					depends: function(element) {
						return $("#articleType").val() == 0;
					}
				}
			}
			,link: {
				required: {
					depends: function(element) {
						return $("#articleType").val() == 1;
					}
				}
			}
			<?php elseif ($kind == CONTENT_KIND_GALLERY): ?>
			,image1: {
				required: true
			}
			,image2: {
				required: true
			}
			<?php elseif ($kind == CONTENT_KIND_VIDEO): ?>
			,video: {
				required: true
			}
			<?php elseif ($kind == CONTENT_KIND_LIVE): ?>
			,link: {
				required: true
			}
			<?php elseif ($kind == CONTENT_KIND_ADVERT): ?>
			,sub_title: {
				required: true
			}
			<?php endif; ?>
		},
		messages: {
			
		}
	});

	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});

	var ratioWidth = 0;
	var ratioHeight = 0;
	<?php if ($kind == CONTENT_KIND_ARTICLE): ?>
	ratioWidth = 16; ratioHeight = 11;	
	<?php elseif ($kind == CONTENT_KIND_GALLERY): ?>
	ratioWidth = 3; ratioHeight = 2;
	<?php elseif ($kind == CONTENT_KIND_VIDEO): ?>
	ratioWidth = 10; ratioHeight = 6;
	<?php elseif ($kind == CONTENT_KIND_LIVE): ?>
	ratioWidth = 10; ratioHeight = 6;
	<?php elseif ($kind == CONTENT_KIND_ADVERT): ?>
	ratioWidth = 16; ratioHeight = 11;
	<?php endif; ?>
	
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

	<?php if ($kind == CONTENT_KIND_GALLERY): ?>
	var $galleryWrapper = $("#galleryWrapper");
	$.uploader({
		formElement: "#image1UploadForm",
		contextElement: ".image-wrapper.i1",
		previewElement: "#image1",
		resultElement: "#image1Url",
		fileType: "image",
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight
	});
	$.uploader({
		formElement: "#image2UploadForm",
		contextElement: ".image-wrapper.i2",
		previewElement: "#image2",
		resultElement: "#image2Url",
		fileType: "image",
		ratioWidth: ratioWidth,
		ratioHeight: ratioHeight
	});
	
	function addCarousel(index, url, description) {
		$(".carousel-indicators").append('<li data-slide-to="' + index + '" data-target="#carousel"></li>');
			
		var html = '<div class="item">';
		html += '<img alt="image" class="img-responsive" width="100%" src="' + BASE + url + '">';
		html += '<div class="carousel-caption"><p>' + description + '</p></div></div>';
		$(".carousel-inner").append(html);
	}
	function refreshOrder() {
		$(".carousel-indicators").empty();
		$(".carousel-inner").empty();
		$(".gallery-image").each(function(index) {
			$(this).find("span.orders").text(index + 1);
			addCarousel(index, $(this).find('input').eq(0).val(), $(this).find('input').eq(1).val());
		});
	}
	$.uploader({
		formElement: "#imageIGUploadForm",
		contextElement: ".image-wrapper.ig",
		fileType: "image",
		//ratioWidth: ratioWidth,
		//ratioHeight: ratioHeight,
		//before: function() {
		//	$("#dlgAddImage").modal("hide");
		//},
		done: function(result) {
			var html = '<div class="gallery-image">';
			html += '<input type="hidden" name="images[]" value="' + result.url + '">';
			html += '<input type="hidden" name="descriptions[]" value="">';
			html += '<img class="preview" src="' + BASE + result.url + '">';
			html += '<span class="orders">' + $galleryWrapper.children().length + '</span>';
			html += '<i role="button" class="fa fa-close"></i>';
			html += '<i role="button" class="fa fa-search"></i>';
			html += '<i role="button" class="fa fa-newspaper-o"></i>';
			html += '</div>';
			$element = $galleryWrapper.append(html);
			
			//$element.find(".fa-newspaper-o").click();
			
			addCarousel($galleryWrapper.children().length - 2, result.url, '');
		}
	});
	$("#imageIGUploadForm").bind("fileuploadsubmit", function(e, data) {
		data.formData = {
			file_type: "image",
			watermark: $("#watermark").val()
		}
	});
	
	$galleryWrapper.on("click", "i.fa-close", function() {
		var $parent = $(this).parents(".gallery-image");
		$parent.remove();
		refreshOrder();
	});
	$galleryWrapper.on("click", "i.fa-search", function() {
		var index = $(this).parents(".gallery-image").index() - 1;
		$(".carousel-indicators").children().eq(index).addClass("active").siblings().removeClass("active");
		$(".carousel-inner").children().eq(index).addClass("active").siblings().removeClass("active");
		$("#carousel").carousel(index);
		$("#dlgPreview").modal("show");
	});
	$galleryWrapper.on("click", "i.fa-newspaper-o", function() {
		var $parent = $(this).parents(".gallery-image");
		$("#txtDescription").data("index", $parent.index());
		$("#txtDescription").val($parent.find("input").eq(1).val());
		$("#dlgDescription").modal("show");
	});
	$("#dlgDescription").find(".btn-primary").click(function() {
		var index = $("#txtDescription").data("index");
		var value = $("#txtDescription").val();
		if (index > 0) {
			index--;
			$(".gallery-image").eq(index).find("input").eq(1).val(value);
		}
		$("#dlgDescription").modal("hide");
	});
	
	$galleryWrapper.sortable({
		items: "> .gallery-image",
		update: function(event, ui) {
			refreshOrder();
		}
	});
	
	<?php elseif ($kind == CONTENT_KIND_VIDEO): ?>
	$.uploader({
		formElement: "#videoUploadForm",
		contextElement: "#uploadStatus",
		resultElement: "#videoUrl",
		fileType: "video",
		before: function() {
			$("#videoUploadBtn").prop("disabled", true);
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
	<?php endif; ?>
	
	<?php if ($isNew): ?>
	$("#mainImage").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
		<?php if ($kind == CONTENT_KIND_GALLERY): ?>
		$("#image1").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
		$("#image2").attr("src", "<?=base_url()?>resources/images/add_" + ratioWidth + "_" + ratioHeight + ".png");
		<?php endif; ?>
	<?php endif; ?>
	$("#ratio").text(ratioWidth + "：" + ratioHeight);
	$(".image-wrapper").addClass('ratio-' + ratioWidth + "-" + ratioHeight);
	
	// event ids
	$("#eventIds").ajaxChosen({
		minTermLength: 1,
	    type: "GET",
	    url: "<?= base_url() ?>admin/events/ajax_list",
	    dataType: 'json'
	}, function (data) {
	    var results = [];
	    $.each(data, function (i, val) {
	        results.push({ value: val.id, text: val.label });
	    });
	    return results;
	});
	
	// member ids
	$("#memberIds").ajaxChosen({
		minTermLength: 1,
	    type: 'GET',
	    url: "<?= base_url() ?>admin/member/ajax_list",
	    dataType: 'json'
	}, function (data) {
	    var results = [];
	    $.each(data, function (i, val) {
	        var group = {
	            group: true,
	            text: val.label,
	            items: []
	        };
	        $.each(val.items, function (i1, val1) {
	            group.items.push({value: val1.id, text: val1.label});
	        });
	        results.push(group);
	    });

	    return results;
	});
	
	<?php if ($kind == CONTENT_KIND_ARTICLE): ?>
	$("#articleType").change(function() {
		if ($(this).val() == 0) {
			$("#articleLink").hide();
			$("#articleContent").show();
		} else {
			$("#articleLink").show();
			$("#articleContent").hide();
		}
	});
	<?php endif; ?>

	<?php if ($kind == CONTENT_KIND_LIVE): ?>
	$("input[name=platform]").eq(0).iCheck("check");
	<?php endif; ?>});
</script>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">
			<?= ($isNew) ? '添加' : '编辑'?><?= getContentKind($kind) ?>
		</div>
		<div class="input-wrapper">
			<form id="inputForm" action="../save" method="post" class="form-horizontal">
				<input type="hidden" name="kind" value="<?=$kind?>" />
				<?php if (!$isNew): ?>
				<input type="hidden" name="id" value="<?=$itemInfo['id']?>" />
				<?php endif; ?>

				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						标&nbsp;&nbsp;题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" id="title" name="title" class="form-control" value="<?= $isNew ? '' : $itemInfo['title']?>"/>
					</div>
				</div>
				<?php if ($kind == CONTENT_KIND_ADVERT): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						副标题：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" id="sub_title" name="sub_title" class="form-control" value="<?= $isNew ? '' : $itemInfo['sub_title'] ?>"/>
					</div>
				</div>
				<?php endif; ?>
				<?php if ($kind == CONTENT_KIND_ARTICLE): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						作&nbsp;&nbsp;者：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" id="author" name="author" class="form-control" value="<?= $isNew ? '' : $itemInfo['author'] ?>"/>
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						日&nbsp;&nbsp;期：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="text" class="form-control Wdate Wdate-YMDhms pull-left" id="contentDate" name="content_date" value="<?= $isNew ? '' : d2dtns($itemInfo['content_date']) ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm'});" placeholder="选择时间">
						<a role="button" class="clear-time" href="javascript:;" onclick="$(this).siblings('input').val('');">清除时间</a>
					</div>
				</div>
				<?php if ($kind == CONTENT_KIND_GALLERY): ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						样&nbsp;&nbsp;式：
					</label>
					<div class="col-sm-6 col-md-4">
						<select class="form-control" name="type">
							<option value="1" <?php if (!$isNew && 1 == $itemInfo['type']): ?>selected="selected"<?php endif; ?>>样式1</option>
							<option value="2" <?php if (!$isNew && 2 == $itemInfo['type']): ?>selected="selected"<?php endif; ?>>样式2</option>
							<option value="3" <?php if (!$isNew && 3 == $itemInfo['type']): ?>selected="selected"<?php endif; ?>>样式3</option>
						</select>
					</div>										
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						选择类型列表，用于客户端显示
					</div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label required">
						封 面 图：
					</label>
					<div class="<?= ($kind == CONTENT_KIND_GALLERY) ? 'col-sm-9 col-md-10' : 'col-sm-6 col-md-4' ?>">
						<div class="image-wrapper m-r im" role="button" onclick="$('#fileMainImg').click()">
							<img class="preview" id="mainImage" src="<?= $isNew ? '' : getFullUrl($itemInfo['image']) ?>">
							<input id="mainImageUrl" name="image" type="hidden" value="<?= $isNew ? '' : $itemInfo['image'] ?>">
							<input id="mainThumbUrl" name="thumb" type="hidden" value="<?= $isNew ? '' : $itemInfo['thumb'] ?>">
							<div class="loading">
								<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
							</div>
						</div>
						<?php if ($kind == CONTENT_KIND_GALLERY): ?>
							<div class="image-wrapper m-r i1" role="button" onclick="$('#fileImage1').click()">
								<img class="preview" id="image1" src="<?= $isNew ? '' : getFullUrl($itemInfo['image1']) ?>">
								<input id="image1Url" name="image1" type="hidden" value="<?= $isNew ? '' : $itemInfo['image1'] ?>">
								<div class="loading">
									<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
								</div>
							</div>
							<div class="image-wrapper m-r i2" role="button" onclick="$('#fileImage2').click()">
								<img class="preview" id="image2" src="<?= $isNew ? '' : getFullUrl($itemInfo['image2']) ?>">
								<input id="image2Url" name="image2" type="hidden" value="<?= $isNew ? '' : $itemInfo['image2'] ?>">
								<div class="loading">
									<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						建议比例<span id="ratio"></span>
					</div>
				</div>
				
				<?php if ($kind != CONTENT_KIND_ADVERT): ?>
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
				<?php endif; ?>

				<?php if ($kind == CONTENT_KIND_ARTICLE): ?>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							类&nbsp;&nbsp;型：
						</label>
						<div class="col-sm-6 col-md-4">
							<select class="form-control" name="type" id="articleType">
								<option value="0" <?php if ($itemInfo['type'] == 0): ?>selected="selected"<?php endif; ?>>文本</option>
								<option value="1" <?php if ($itemInfo['type'] == 1): ?>selected="selected"<?php endif; ?>>链接</option>
							</select>
						</div>
					</div>
					<div id="articleLink" class="form-group" <?php if ($itemInfo['type'] == 0): ?>style="display:none;"<? endif; ?>>
						<label class="col-sm-3 col-md-2 control-label required">
							链&nbsp;&nbsp;接：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="text" name="link" class="form-control" value="<?= $isNew ? '' : $itemInfo['link'] ?>"/>
						</div>
					</div>
					<div id="articleContent" class="form-group" <?php if ($itemInfo['type'] == 1): ?>style="display:none;"<? endif; ?>>
						<label class="col-sm-3 col-md-2 control-label required">
							内&nbsp;&nbsp;容：
						</label>
						<div class="col-sm-9 col-md-8">
							<textarea id="editor" name="content" class="editor" style="width:100%;"><?= $isNew ? '' : $itemInfo['content'] ?></textarea>
						</div>
					</div>
				<?php elseif ($kind == CONTENT_KIND_VIDEO): ?>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label required">
							上传视频：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="hidden" name="video" id="videoUrl" value="<?= $isNew ? '' : $itemInfo['video'] ?>">
							<button type="button" id="videoUploadBtn" class="btn btn-white" onclick="$('#fileVideo').click()">选择视频 ...</button>
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
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							简&nbsp;&nbsp;介：
						</label>
						<div class="col-sm-6 col-md-4">
							<textarea class="form-control" name="introduction" style="height:150px"><?= $isNew ? '' : $itemInfo['introduction'] ?></textarea>
						</div>
					</div>
				<?php elseif ($kind == CONTENT_KIND_LIVE): ?>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							来&nbsp;&nbsp;源：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="text" name="source" class="form-control" value="<?= $isNew ? '' : $itemInfo['source'] ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							状&nbsp;&nbsp;态：
						</label>
						<div class="col-sm-6 col-md-4 p-t-xxs">
							<input type="radio" class="i-check" name="status" value="<?= LIVE_STATUS_COUNTDOWN ?>" <?php if (!$isNew && $itemInfo['status'] == LIVE_STATUS_COUNTDOWN): ?>checked="checked"<?php endif; ?>/> 倒计时
							<span class="m-r"></span>
							<input type="radio" class="i-check" name="status" value="<?= LIVE_STATUS_LIVE ?>" <?php if (!$isNew && $itemInfo['status'] == LIVE_STATUS_LIVE): ?>checked="checked"<?php endif; ?>/> 直播中
							<span class="m-r"></span>
							<input type="radio" class="i-check" name="status" value="<?= LIVE_STATUS_RETURN ?>" <?php if (!$isNew && $itemInfo['status'] == LIVE_STATUS_RETURN): ?>checked="checked"<?php endif; ?>/> 回放
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 col-md-2 control-label required">
							链接地址：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="text" name="link" class="form-control" value="<?= $isNew ? '' : $itemInfo['link'] ?>"/>
						</div>
					</div>
				<?php elseif ($kind == CONTENT_KIND_GALLERY): ?>
					<hr/>
					<div class="form-group">
						<h3 class="col-sm-offset-1">
							编辑图集
						</h3>
						<div id="galleryWrapper" class="col-sm-offset-1 m-t-md">
							<div class="image-wrapper m-r-sm m-b ig" role="button" onclick="$('#fileImageIG').click()">
								<img class="preview" id="imageIG" src="<?=base_url()?>resources/images/add_3_2.png">
								<div class="loading">
									<i class="fa fa-spinner fa-pulse fa-3x fa-fw white"></i>
								</div>
							</div>
							<?php if (!$isNew && !empty($itemInfo['images'])): ?>
								<?php foreach ($itemInfo['images'] as $key=>$image): ?>
									<div class="gallery-image">
										<input type="hidden" name="images[]" value="<?= $image['image'] ?>">
										<input type="hidden" name="descriptions[]" value="<?= $image['description'] ?>">
										<img class="preview" src="<?= getFullUrl($image['image']) ?>">
										<span class="orders"><?= $key + 1 ?></span>
										<i role="button" class="fa fa-close"></i>
										<i role="button" class="fa fa-search"></i>
										<i role="button" class="fa fa-newspaper-o"></i>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="modal fade" tabindex="-1" role="dialog" id="dlgAddImage">
						<div class="modal-dialog add-modal">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">添加图片</h3>
								</div>
								<div class="modal-body">
									<div class="row text-center">
										<div class="col-xs-6">
											<a role="button" class="add-item" onclick="$('#watermark').val(1);$('#fileImageIG').click()">
											<i class="fa-stack fa-2x">
												<i class="fa fa-square-o fa-stack-2x"></i>
  												<i class="fa fa-upload fa-stack-1x"></i>
											</i>
											<span>上传文件</span>
											</a>
										</div>
										<div class="col-xs-6">
											<a role="button" class="add-item"  onclick="$('#watermark').val(0);$('#fileImageIG').click()">
											<i class="fa-stack fa-2x">
												<i class="fa fa-circle-thin fa-stack-2x"></i>
  												<i class="fa fa-upload fa-stack-1x"></i>
											</i>
											<span>无水印上传</span>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal fade" tabindex="-1" role="dialog" id="dlgDescription">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">输入图片说明</h3>
								</div>
								<div class="modal-body">
									<textarea class="form-control" id="txtDescription" data-index="" style="height: 160px;"></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary">确&nbsp;&nbsp;定</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="modal fade" tabindex="-1" role="dialog" id="dlgPreview">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">图片预览</h3>
								</div>
								<div class="modal-body">
									<div class="carousel slide" id="carousel" data-interval="false">
			                            <ol class="carousel-indicators">
			                            	<?php if (!$isNew && !empty($itemInfo['images'])): ?>
												<?php foreach ($itemInfo['images'] as $key=>$image): ?>
													<li data-slide-to="<?= $key ?>" data-target="#carousel"></li>
												<?php endforeach; ?>
											<?php endif; ?>
			                            </ol>
			                            <div class="carousel-inner">
			                            	<?php if (!$isNew && !empty($itemInfo['images'])): ?>
												<?php foreach ($itemInfo['images'] as $key=>$image): ?>
													<div class="item">
					                                    <img alt="image" class="img-responsive" width="100%" src="<?= getFullUrl($image['image']) ?>">
					                                    <div class="carousel-caption">
					                                        <p><?= $image['description'] ?></p>
					                                    </div>
					                                </div>
												<?php endforeach; ?>
											<?php endif; ?>
			                            </div>
			                            <a data-slide="prev" href="#carousel" class="left carousel-control">
			                            	<i class="fa fa-angle-left fa-2x"></i>
			                            </a>
			                            <a data-slide="next" href="#carousel" class="right carousel-control">
			                                <i class="fa fa-angle-right fa-2x"></i>
			                            </a>
			                        </div>
								</div>
							</div>
						</div>
					</div>
				<?php elseif ($kind == CONTENT_KIND_ADVERT): ?>
					<div id="articleLink" class="form-group">
						<label class="col-sm-3 col-md-2 control-label">
							链&nbsp;&nbsp;接：
						</label>
						<div class="col-sm-6 col-md-4">
							<input type="text" name="link" class="form-control" value="<?= $isNew ? '' : $itemInfo['link'] ?>"/>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($kind != CONTENT_KIND_ADVERT) : ?>
				<hr/>
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
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						点击次数：
					</label>
					<div class="col-sm-6 col-md-4">
						<input type="number" name="hits" class="form-control" value="<?= $isNew ? '' : $itemInfo['hits'] ?>" min="0"/>
					</div>					
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						额外增加的点击次数
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						赛事或比赛：
					</label>
					<div class="col-sm-6 col-md-4">
						<select name="event_ids[]" id="eventIds" class="form-control" data-placeholder="请输入赛事ID或标题" multiple="multiple">
							<?php if (!$isNew && !empty($itemInfo['events'])): ?>
							<?php foreach ($itemInfo['events'] as $event): ?>
							<option value="<?= $event['id'] ?>" selected="selected">[<?= $event['id'] ?>] <?= ellipseStr($event['title'], 20) ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						输入赛事ID，用于显示在赛事动态。多个赛事用，号分割
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 col-md-2 control-label">
						选手、教练、裁判：
					</label>
					<div class="col-sm-6 col-md-4">
						<select name="member_ids[]" id="memberIds" class="form-control" data-placeholder="请输入姓名或认证编号" multiple="multiple">
							<?php if (!$isNew && !empty($itemInfo['members'])): ?>
							<?php foreach ($itemInfo['members'] as $member): ?>
							<option value="<?= $member['id'] ?>" selected="selected">[<?= $member['id'] ?>] <?= $member['name'] ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
						输入选手、教练、裁判ID，用于显示在人物动态。多个人物，号分割
					</div>
				</div>
				<?php endif; ?>

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
				<?php if ($kind == CONTENT_KIND_VIDEO): ?>
				<form id="videoUploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileVideo">
				</form>
				<?php elseif ($kind == CONTENT_KIND_GALLERY): ?>
				<form id="image1UploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileImage1">
				</form>
				<form id="image2UploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileImage2">
				</form>
				<form id="imageIGUploadForm" method="post" enctype="multipart/form-data">
					<input type="file" name="file" id="fileImageIG" multiple>
					<input type="hidden" name="watermark" id="watermark" value="0">
				</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
</body>
</html>
