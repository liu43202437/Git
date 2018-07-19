<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        查看打卡照片
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
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        查看打卡照片
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
                    添加时间：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="create_date" id="create_date" class="form-control" value="<?= $itemInfo['create_date']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label">
                    打卡照片：
                </label>
                <div class="col-sm-6 col-md-4">
                    <?php foreach($itemInfo['images'] as $value):  ?>
                        <img src="<?= $value ?>" height="500px" width="500px"  alt="打卡照片"  />
                        &nbsp;&nbsp;<br><br>
                    <?php endforeach;?>
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
