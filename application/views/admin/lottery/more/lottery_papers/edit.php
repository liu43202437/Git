<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        <?= ($isNew) ? '添加' : '查看'?>代销证
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

            var area_id, cities = [], counties = [], street = [], area_code, cityName='',countyName = '',streetname='';
            function initSelected() {
                var area_code = '<?= $itemInfo['area_code']?>';
                var area_id = '<?= $itemInfo['area_id'] ?>' || '';
                var initCity = '',
                    initCounty = '',
                    initStreet = '';
                if(area_code) {
                    initCity = area_code.substr(0,4),
                        initCounty = area_code.substr(0,6),
                        initStreet =  area_code.substr(0,9);
                }
                var initprovince = area_id;
                if(initprovince) {
                    updatelist('#selectprovince',CITY_CODE,initprovince);
                } else {
                    updatelist('#selectprovince',CITY_CODE);
                    return false;
                }

                if (initCity) {
                    cities = findChilds(initprovince,CITY_CODE);
                    updatelist('#selectcity',cities, initCity);
                    cityName = $("#selectcity  option:selected").text()
                    // $('#address').val(cityName+countyName+streetname);
                    $('#selectcityname').val(cityName);
                } else {
                    return false;
                }
                if (initCounty) {
                    counties = findChilds(initCity,cities);
                    updatelist('#selectcounty',counties , initCounty);
                    countyName = $("#selectcounty  option:selected").text();
                    // $('#address').val(cityName+countyName+streetname);
                }else {
                    return false;
                }

                if (initStreet) {
                    street = findChilds(initCounty,counties);
                    updatelist('#selectstreet',street, initStreet);
                    streetname = $("#selectstreet  option:selected").text();
                    // $('#address').val(cityName+countyName+streetname);
                } else {
                    return fasle;
                }
            }
            initSelected()
            $('#selectprovince').change(function(){
                $('#selectcity option').remove();
                $('#selectcounty option').remove();
                $('#selectstreet option').remove();
                area_code = $("#selectprovince").val();
                area_id = area_code;
                cities = findChilds(area_code,CITY_CODE);
                clearNextOption(3);
                if (cities) {
                    updatelist('#selectcity',cities);
                }
            });
            $('#selectcity').change(function(){
                $('#selectcounty option').remove();
                $('#selectstreet option').remove();
                area_code = $("#selectcity").val();
                cityName = $("#selectcity  option:selected").text();
                $('#selectcityname').val(cityName);
                countyName = streetname = '';
                // $('#address').val(cityName+countyName+streetname);
                counties = findChilds(area_code,cities);
                clearNextOption(2);
                if (counties) {
                    updatelist('#selectcounty',counties);
                }
            });
            $('#selectcounty').change(function(){
                $('#selectstreet option').remove();
                area_code = $("#selectcounty").val();
                countyName = $("#selectcounty  option:selected").text();
                streetname = '';
                // $('#address').val(cityName+countyName+streetname);
                street = findChilds(area_code,counties);
                clearNextOption(1);
                if (street) {
                    updatelist('#selectstreet',street);
                }
            });
            $('#selectstreet').change(function(){
                area_code = $("#selectstreet").val();
                streetname = $("#selectstreet  option:selected").text();
                // $('#address').val('');
                // $('#address').val(cityName+countyName+streetname);
            });
            //$("#mapWrapper").hide();
        });


        function clearNextOption(number){
            var eleArr = ['#selectstreet','#selectcounty','#selectcity',]
            for ( var i = 0; i<number; i++) {
                $(eleArr[i]).html('<option value=0 > 全部 </option>');
            }
        }

        function findChilds(id,arr){
            var childs = null;
            if(arr){
                for(var i=0; i<arr.length; i++){
                    if(arr[i].childs && id === arr[i].id){
                        console.log('0');
                        return arr[i].childs
                    }
                }
                console.log('1');
                return null
            } else {
                console.log('2');
                return null;
            }
        }

        function updatelist(eleId,arr,initCode){
            $(eleId).html('');
            var html = '<option value=0 > 全部 </option>';
            if (arr) {
                for( let item of arr) {
                    var options = '<option value="' + item.id + '">' + item.value + '</option>';
                    if (initCode && item.id === initCode) {
                        options = '<option  selected value="' + item.id + '">' + item.value + '</option>';
                    }
                    html += options;
                }
            }
            $(eleId).append(html);
        }

    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        查看代销证
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
                    代销证号：
                </label>
                <div class="col-sm-6 col-md-4">
                    <input type="text" name="lottery_papers" class="form-control" value="<?= $itemInfo['lottery_papers']?>"/>
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
                    照&nbsp;&nbsp;片：
                </label>
                <div class="col-sm-6 col-md-4">
                    <img src="<?= $itemInfo['lottery_papers_image'] ?>" height="520px" width="520px"  alt="代销证照片"  />
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