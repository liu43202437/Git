<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>访销经理管理</title>
    <meta name="author" content="STSOFT Team"/>
    <meta name="copyright" content="T-One"/>
    <link href="<?= base_url() ?>resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/bootstrap.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/animate.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/style.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/admin.style.css" rel="stylesheet">
    <script type="text/javascript" src="<?= base_url() ?>resources/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/datePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/chosen/chosen.jquery.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
    <script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/common.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/list.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/wechat/js/pcas-code.js"></script>

    <style>
        /*  ''''''''''''''''''地址选择‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’‘’ */
        .hidden-input{
            position: absolute;
            z-index: 0;
            opacity: 0;
        }

    </style>
    <script type="text/javascript">
        $().ready(function () {
            var $dlgEdit = $("#dlgEdit");
            var $dlgEdit2 = $("#dlgEdit2");

            <?php if (isset($message)): ?>
            $.message("<?=$message['type']?>", "<?=$message['content']?>");
            <?php endif; ?>

            $("#areaSelect").chosen();
            var id = 0;
            var content = '';
            $(".refuseItemBtn").click(function() {
                var $parent = $(this).parents("tr");
                $dlgEdit.find(".modal-title").text("请填入审核不通过原因");
                // $dlgEdit.find("#rankId").val($(this).data("id"));
                // $dlgEdit.find("#nameMale").val($.trim($parent.children().eq(1).text()));
                $dlgEdit.find("#nameFemale").val($.trim($parent.children().eq(2).text()));
                $dlgEdit.find("#rank").val($.trim($parent.children().eq(3).text()));
                $dlgEdit.find("#minExp").val($.trim($parent.children().eq(4).text()));
                $dlgEdit.modal('show');
                id = $(this).attr('data-id');
            });
            $("#refuse").click(function(){
                var url = 'refuse';
                content = $dlgEdit.find("#nameMale").val();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {'id':id,'content':content},
                    dataType: "json",
                    cache: false,
                    success: function (message) {
                        $.message(message);
                        if (message.success == true) {
                            alert('已发送短信消息通知用户重新申请');
                            location.reload(true);
                        }
                        else{
                            alert('发送短信通知用户失败，请稍后再试');
                        }
                    }
                });
            });

            $(".checkItemBtn").click(function() {
                var $parent = $(this).parents("tr");
                $dlgEdit2.find(".modal-title").text("完善信息");
                // $dlgEdit.find("#rankId").val($(this).data("id"));
                // $dlgEdit.find("#nameMale").val($.trim($parent.children().eq(1).text()));
                $dlgEdit2.find("#nameFemale").val($.trim($parent.children().eq(2).text()));
                $dlgEdit2.find("#rank").val($.trim($parent.children().eq(3).text()));
                $dlgEdit2.find("#minExp").val($.trim($parent.children().eq(4).text()));
                $dlgEdit2.modal('show');
                id = $(this).attr('data-id');
            });
            $("#pass").click(function(){
                var url = 'check';
                lottery_license = $dlgEdit2.find("#nameMale").val();
                stationId = $dlgEdit2.find("#stationId").val();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {'ids[]':id,'status':1,'stationId':45898888},
                    dataType: "json",
                    cache: false,
                    success: function (message) {
                        $.message(message);
                        if (message.type == 'success') {
                            location.reload(true);
                        }
                    }
                });
            });
            $("#filtrate").click(function(){
                $.pageSkip(1);
            });
            $("#exportClub").click(function() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var search_key = $('#search_key').val();
                var type = $('#type').val();

                var selectprovince = $('#selectprovince').val();
                var selectprovincename = $('#selectprovincename').val();
                var selectcity = $('#selectcity').val();
                var selectcityname = $('#selectcityname').val();
                var selectcounty = $('#selectcounty').val();
                var selectstreet = $('#selectstreet').val();
                if(startDate == '' || endDate == ''){
                    alert('清输入起止时间');
                    return;
                }
                //赋值
                $("#editRankForm3 input[name='startDate']").val(startDate);
                $("#editRankForm3 input[name='endDate']").val(endDate);
                $("#editRankForm3 input[name='search_key']").val(search_key);
                $("#editRankForm3 input[name='type']").val(type);
                $("#editRankForm3 input[name='selectprovince']").val(selectprovince);
                $("#editRankForm3 input[name='selectprovincename']").val(selectprovincename);
                $("#editRankForm3 input[name='selectcity']").val(selectcity);
                $("#editRankForm3 input[name='selectcityname']").val(selectcityname);
                $("#editRankForm3 input[name='selectcounty']").val(selectcounty);
                $("#editRankForm3 input[name='selectstreet']").val(selectstreet);
                var $dlgEdit3 = $("#dlgEdit3");
                $dlgEdit3.find(".modal-title").text("导出店铺");
                $dlgEdit3.modal('show');

            });


            // 地址选择............................................................................................................
            var area_id, cities = [], counties = [], street = [], area_code, cityName;
            function initSelected() {
                var initCounty = '<?= $selectcounty ?>',initCity = '<?= $selectcity ?>',initStreet = '<?= $selectstreet?>';
                var initprovince = '<?= $selectprovince ?>';
                if(initprovince) {
                    updatelist('#selectprovince',CITY_CODE,initprovince);
                } else {
                    updatelist('#selectprovince',CITY_CODE);
                    return false;
                }

                if (initCity) {
                    var cities = findChilds(initprovince,CITY_CODE);
                    updatelist('#selectcity',cities, initCity);
                    cityName = $("#selectcity  option:selected").text()
                    $('#selectcityname').val(cityName);
                } else {
                    return false;
                }

                if (initCounty) {
                    var counties = findChilds(initCity,cities);
                    updatelist('#selectcounty',counties , initCounty);
                }else {
                    return false;
                }

                if (initStreet) {
                    var street = findChilds(initCounty,counties);
                    updatelist('#selectstreet',street, initStreet);
                } else {
                    return fasle;
                }
            }
            initSelected()
            $('#selectprovince').change(function(){
                $('#selectcity option').remove();
                area_code = $("#selectprovince").val();
                area_id = area_code;
                cities = findChilds(area_code,CITY_CODE);
                clearNextOption(3);
                if (cities) {
                    updatelist('#selectcity',cities);
                }
                $('#selectprovincename').val(selectprovincename);
            });
            $('#selectcity').change(function(){
                $('#selectcounty option').remove();
                area_code = $("#selectcity").val();
                cityName = $("#selectcity  option:selected").text()
                $('#selectcityname').val(cityName);
                counties = findChilds(area_code,cities);
                clearNextOption(2);
                if (counties) {
                    updatelist('#selectcounty',counties);
                }
            });
            $('#selectcounty').change(function(){
                $('#selectstreet option').remove();
                area_code = $("#selectcounty").val();
                street = findChilds(area_code,counties);
                clearNextOption(1);
                if (street) {
                    updatelist('#selectstreet',street);
                }
            });
            $('#selectstreet').change(function(){
                area_code = $("#selectstreet").val();
            });


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
                        return arr[i].childs
                    }
                }
                return null
            } else {
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
        // 地址选择............................................................................................................







        function onToggleShow(obj, message) {
            var $obj = $(obj);
            var $iChild = $(obj).children("i.fa");
            if ($iChild.hasClass("fa-eye")) {
                $iChild.removeClass("fa-eye").addClass("fa-eye-slash");
                $obj.attr("title", "显示");
            } else {
                $iChild.removeClass("fa-eye-slash").addClass("fa-eye");
                $obj.attr("title", "隐藏");
            }
        }
    </script>
    <style type="text/css">
        #areaSelect_chosen {
            width: 140px !important;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        访销经理
        <?php if ($isEditable): ?>
            <div class="pull-right op-wrapper">
                <a href="#">管理操作</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="list-wrapper">
        <form id="listForm" class="form-inline" action="lists" method="get">
            <div class="filter-bar">
                <!-- <select class="form-control s-lg" name="area_id" id="areaSelect">
                    <option value="">地点</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= $city['id'] ?>"
                                <?php if ($areaId == $city['id']): ?>selected="selected"<?php endif; ?>><?= $city['name'] ?></option>
                    <?php endforeach; ?>
                </select> -->
                <!--<select class="form-control s-lg" name="area_id">
                        <option value="">国家</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= $city['id'] ?>" <?php if ($areaId == $city['id']): ?>selected="selected"<?php endif; ?>><?= $city['name'] ?></option>
                        <?php endforeach; ?>
                    </select>-->

                <div class="form-group m-l-sm">
                    <select class="form-control" name="time_type">
                        <option>注册时间</option>
                    </select>
                    <input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
                    <input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">
                </div>

                <input type="text" class="form-control m-l-sm" name="search_key" id="search_key" value="<?= $searchKey ?>"
                       placeholder="名称、电话、身份证">

                <!-- 地址选择 -->
                <div class="form-group m-l-sm">
                    区域:&nbsp;
                    <select class="form-control" name="selectprovince" id="selectprovince" value="<?= $selectcity ?>">
                        <option value=0 > 全部 </option>
                    </select>
                    <select class="form-control" name="selectcity" id='selectcity'>
                        <option value=0 > 全部 </option>
                    </select>
                    <input class="hidden-input" name="selectcityname" id='selectcityname' type="text" value="">
                    <input class="hidden-input" name="selectprovincename" id='selectprovicename' type="text" value="<?= $selectprovincename?>">
                    <select class="form-control" name="selectcounty" id='selectcounty'>
                        <option value=0 > 全部 </option>
                    </select>
                    <select class="form-control" name="selectstreet" id='selectstreet'>
                        <option value=0 > 全部 </option>
                    </select>

                </div >
                <!-- 地址选择 -->

                <div class="form-group m-l-sm">
                    类型:&nbsp;
                    <select class="form-control s-lg" name="type" id="type">
                        <option value="4" <?php if($type == 4):?> selected = "selected" <?php endif; ?> >全部</option>
                        <option value="0" <?php if($type == 0):?> selected = "selected" <?php endif; ?> >未审核</option>
                        <option value="1" <?php if($type == 1):?> selected = "selected" <?php endif; ?> >通过审核</option>
                        <option value="3" <?php if($type == 3):?> selected = "selected" <?php endif; ?> >审核未通过</option>
                    </select>
                </div>
                <button class="btn btn-white m-l-sm" type="submit" id="filtrate">筛 选</button>
                <button class="btn btn-white m-l-sm" type="button" id="exportClub">导出店铺</button>
            </div>

            <table id="listTable" class="list table">
                <tr>
                    <th class="check">&nbsp;</th>
                    <th class="number">
                        <a href="javascript:;" class="sort" name="id">ID</a>
                    </th>
                    <!-- <th class="qrcode">
                        <a href="javascript:;">二维码</a>
                    </th> -->

                    <th class="name">
                        <a href="javascript:;" name="view_name">经理姓名</a>
                    </th>
                    <th class="phone">
                        <a href="javascript:;">经理手机</a>
                    </th>
                    <th class="address">
                        <a href="javascript:;">地址</a>
                    </th>
                    <th class="time">
                        <a href="javascript:;" class="sort" name="create_date">添加时间</a>
                    </th>
                    <th>
                        <span>操作</span>
                    </th>
                </tr>
                <?php foreach ($itemList as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>"
                                   <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?>/>
                        </td>
                        <td>
                            <?= $item['id'] ?>
                        </td>
                        <!-- <td>
                            <a class="fancybox"
                               href="<?= base_url() . 'common/qrcode?w=300&t=' . getPortalUrl($item['id'], PORTAL_KIND_CLUB) ?>&m=1&ext=.png">
                                <img
                                    src="<?= base_url() . 'common/qrcode?w=100&t=' . getPortalUrl($item['id'], PORTAL_KIND_CLUB) ?>">
                            </a>
                        </td> -->
                        <td>
                            <?= $item['name'] ?>
                        </td>
                        <td>
                            <?= $item['phone'] ?>
                        </td>
                        <td>
                            <?= $item['address'] ?>
                        </td>

                        <td>
                            <?= $item['create_date'] ?>
                        </td>
                        <td class="operation">
                            <?php if ($isEditable): ?>
                                <!-- <a role="ajax" data-url="toggle_show" data-func="onToggleShow" data-reload="false"
                                   data-id="<?= $item['id'] ?>" title="<?= $item['is_show'] ? '隐藏' : '显示' ?>"><i
                                        class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash' ?>"></i></a> -->
                                <a href="edit?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
                                <a href="more?user_id=<?= $item['user_id'] ?>" title="更多"><i class="fa fa-th-large"></i></a>
                                <!--  <a href="<?= getPortalUrl($item['id'], PORTAL_KIND_CLUB) ?>" target="_blank" title="链接"><i
                                        class="fa fa-link"></i></a> -->
                                <!-- <a class="deleteItemBtn" data-url="delete" data-id="<?= $item['id'] ?>" title="删除"><i
                                        class="fa fa-trash-o"></i></a> -->
                                <?php if ($item['status'] != 1): ?>
                                    <span>
                                    <?php if($item['refuse'] == 0): ?>
                                        <a class="checkItemBtn" data-url="check" data-id="<?= $item['id'] ?>" shop="1"  title="审核通过" data-status="1"><i
                                                class="fa fa-circle-o"></i></a>
                                        <a class="refuseItemBtn" data-url="refuse" data-id="<?= $item['id'] ?>" title="审核拒绝"><i
                                                class="fa fa-remove"></i></a>

                                    <?php endif; ?>
                                        </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <a class="disabled" href="javascript:;" title="<?= $item['is_show'] ? '隐藏' : '显示' ?>"><i
                                        class="fa <?= $item['is_show'] ? 'fa-eye' : 'fa-eye-slash' ?>"></i></a>
                                <a class="disabled" href="javascript:;" title="编辑"><i class="fa fa-edit"></i></a>
                                <a class="disabled" href="javascript:;" title="链接"><i class="fa fa-link"></i></a>
                                <a class="disabled" href="javascript:;" title="删除"><i class="fa fa-trash-o"></i></a>
                                <a class="disabled" href="javascript:;" title="审核通过"><i class="fa fa-trash-o"></i></a>
                                <a class="disabled" href="javascript:;" title="审核拒绝"><i class="fa fa-trash-o"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($itemList) == 0): ?>
                    <tr>
                        <td class="text-center" colspan="9">
                            <div class="p-lg">没有符合条件的记录！</div>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr class="bottom-bar">
                    <th colspan="9">
                            <span>
                                <input type="checkbox" class="i-check" id="selectAll"
                                       <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?> />
                                <span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?= count($itemList) ?>
                                    零售店</span>
                                <!-- <a role="ajax" class="batch-btn btn btn-default btn-outline disabled"
                                   data-url="toggle_show" data-reload="true" data-params="is_show=0">批量隐藏</a>
                                <a class="batch-btn btn btn-default btn-outline disabled" id="deleteButton"
                                   data-url="delete">批量删除</a> -->
                            </span>

                        <?php $this->load->view('admin/pagination'); ?>
                    </th>
                </tr>
            </table>
        </form>

        <div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit" style="width:500px; margin-left: 35% ">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">添加等级</h3>
                    </div>
                    <div class="modal-body">
                        <form id="editRankForm" action="edit_rank" class="form-horizontal" method="post">
                            <input type="hidden" name="id" id="rankId" />
                            <div class="form-group">
                                <textarea class="form-control" name="name_male" id="nameMale" style="width: 250px; height: 120px " ></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
                        <!-- <button type="button" class="btn btn-primary" onclick="$('#editRankForm')">保&nbsp;&nbsp;存</button> -->
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="refuse">确&nbsp;&nbsp;定</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit2" style="width:500px; margin-left: 35% ">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">添加等级</h3>
                    </div>
                    <div class="modal-body">
                        <form id="editRankForm" action="edit_rank" class="form-horizontal" method="post">
                            <input type="hidden" name="id" id="rankId" />
                            <div class="form-group">确认通过？
<!--                                <input type="text" class="form-control" name="lottery_license" id="nameMale">-->
                            </div>
                            <!-- <div class="form-group">站点号:
                                <input type="text" class="form-control" name="stationId" id="stationId">
                            </div> -->
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
                        <!-- <button type="button" class="btn btn-primary" onclick="$('#editRankForm')">保&nbsp;&nbsp;存</button> -->
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="pass">确&nbsp;&nbsp;定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit3">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">添加等级</h3>
                </div>
                <div class="modal-body">
                    <form id="editRankForm3" action="exportClub" class="form-horizontal" method="post">
                        <div>
                            <h4>导出需要一些时间,确认导出？</h4>
                        </div>
                        <input type="hidden" value="" name="startDate"></input>
                        <input type="hidden" value="" name="endDate"></input>
                        <input type="hidden" value="" name="search_key"></input>
                        <input type="hidden" value="" name="type"></input>
                        <input type="hidden" value="" name="selectprovince"></input>
                        <input type="hidden" value="" name="selectprovincename"></input>
                        <input type="hidden" value="" name="selectcity"></input>
                        <input type="hidden" value="" name="selectcounty"></input>
                        <input type="hidden" value="" name="selectstreet"></input>
                        <input type="hidden" value="" name="selectcityname"></input>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
                    <button type="submit" class="btn btn-primary" onclick="$('#editRankForm3').submit();"  data-dismiss="modal">确&nbsp;&nbsp;定</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>