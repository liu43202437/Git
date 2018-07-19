<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        查看打卡
    </title>
    <meta name="author" content="STSOFT Team" />
    <meta name="copyright" content="T-One" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="//webapi.amap.com/ui/1.0/ui/overlay/SimpleInfoWindow/examples/" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
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
    <!-- 地图显示 -->
    <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
    <script src="http://cache.amap.com/lbs/static/es5.min.js"></script>
    <script src="http://webapi.amap.com/maps?v=1.4.5&key=bdbfbb94b17fd598a120c0cbbd3f0ad5"></script>
    <script src="//webapi.amap.com/ui/1.0/main.js?v=1.0.11"></script>
    <script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
    <style>
    html,
    body,
    #container {
        width: 100%;
        height: 100%;
        margin: 0px;
    }
    
    p.my-desc {
        margin: 5px 0;
        line-height: 150%;
    }
    </style>
    <!-- 地图显示 -->


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
            });
    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        查看打卡
    </div>
    <div class="input-wrapper">
        <form id="inputForm" action="saves" method="post" class="form-horizontal">
            <input type="hidden" name="id" value="<?=$itemInfo['id']?>" />

            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    店主姓名：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="name" id="name" class="form-control" value="<?= $itemInfo['name']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    店主手机：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= $itemInfo['phone']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    店铺地址：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="address" class="form-control" value="<?= $itemInfo['address']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    开始打卡时间：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="begin_time" id="begin_time" class="form-control" value="<?= $itemInfo['begin_time']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    结束打卡时间：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="end_time" id="end_time" class="form-control" value="<?= $itemInfo['end_time']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    打卡时长：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="interval" id="interval" class="form-control" value="<?= $itemInfo['interval']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    打卡状态：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="status" id="status" class="form-control" value="<?= $itemInfo['status']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    地图：
                </label>

                <div id="mapcontainer" style="width:1000px; height:600px;">
                    <div id="tip">可以缩放地图，得到缩放级别哦！<br><span id="info"></span></div>
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
<script type="text/javascript">

        var begin_longitude,
            begin_latitude,
            end_longitude,
            end_latitude;

        begin_longitude = <?= $itemInfo['begin_longitude']?>;
        begin_latitude = <?= $itemInfo['begin_latitude']?>;
        <?php
        if(!empty($itemInfo['end_longitude'])):
        ?>
         end_longitude = <?= $itemInfo['end_longitude']?>;
        <?php endif;?>
        <?php
        if(!empty($itemInfo['end_latitude'])):
         ?>
         end_latitude = <?= $itemInfo['end_latitude']?>;
        <?php endif;?>

        if(end_longitude){

        }
        var map = new AMap.Map("mapcontainer", {
            resizeEnable: true,
            zoom:18,
            center: [begin_longitude, begin_latitude]
        });
        var marker = new AMap.Marker({
            position: [begin_longitude, begin_latitude],
            draggable: true,
        });
        marker.setMap(map);
        // 设置鼠标划过点标记显示的文字提示
        marker.setTitle('开始打卡');

        // 设置label标签
        marker.setLabel({//label默认蓝框白底左上角显示，样式className为：amap-marker-label
            offset: new AMap.Pixel(20, 20),//修改label相对于maker的位置
            content: "开始打卡"
        });
        if(end_longitude){
            var marker = new AMap.Marker({
                position: [begin_longitude, begin_latitude]
            });
            marker.setMap(map);
            // 设置鼠标划过点标记显示的文字提示
            marker.setTitle('开始打卡');

            // 设置label标签
            marker.setLabel({//label默认蓝框白底左上角显示，样式className为：amap-marker-label
                offset: new AMap.Pixel(20, 20),//修改label相对于maker的位置
                content: "开始打卡"
            });
        }
         AMap.event.addListener(map,'zoomend',function(){
            document.getElementById('info').innerHTML = '当前缩放级别：' + map.getZoom();
        });
</script>

</script>
</html>

