<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        <?= ($isNew) ? '添加' : '查看'?>收据
    </title>
    <meta name="author" content="STSOFT Team" />
    <meta name="copyright" content="T-One" />
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
    <script type="text/javascript" src="<?= base_url() ?>resources/wechat/js/pcas-code.js"></script>
    <style>
        /*  ''''''''''''''''''地址选择‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’ */
        .hidden-input{
            position: absolute;
            z-index: -1;
            opacity: 0;
        }

    </style>
    <script type="text/javascript">
        $().ready(function() {

            <?php if (isset($message)): ?>
            $.message("<?=$message['type']?>", "<?=$message['content']?>");
            <?php endif; ?>


            for(var i=0;i<CITY_CODE.length;i++){
                if (CITY_CODE[i]['value'] == "<?=$itemInfo['area_id']?>"){
                    console.log(CITY_CODE[i]['value']);

                    break;
                }
            }


            var $inputForm = $("#inputForm");

            // 表单验证
            $inputForm.validate({
                rules: {
                    name: {
                        required: true
                    },
                    province_id: {
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
        });


    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        查看收据
    </div>
    <div class="input-wrapper">
        <form id="inputForm" action="saves" method="post" class="form-horizontal">
            <input type="hidden" name="id" value="<?=$itemInfo['id']?>" />

            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    收据备注：
                </label>
                <div class="col-sm-6 col-md-4">
                    <textarea rows="8" cols="80" style="resize:none;outline:none;width: 100%;"><?= $itemInfo['notes']?> </textarea>
                    <!-- <input type="text" name="notes" id="notes" class="form-control" value="<?= $itemInfo['notes']?>"/> -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    收据添加时间：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="create_date" id="create_date" class="form-control" value="<?= $itemInfo['create_date']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    照&nbsp;&nbsp;片：
                </label>
                <div class="col-sm-6 col-md-4">
                    <?php foreach($itemInfo['receipt_image']['receipt_image'] as $value):  ?>
                        <img src="<?= $value ?>" height="100px" width="100px"  alt="收据照片"  />
                        &nbsp;&nbsp;
                    <?php endforeach;?>
                </div>
                <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10 value-tip">
                </div>
            </div>

            <hr/>
            <div class="form-group m-t-lg">
                <div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-10">
                    <!-- <button type="submit" class="btn btn-primary">保&nbsp;&nbsp;存</button> -->
                    <!-- <button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button> -->
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
