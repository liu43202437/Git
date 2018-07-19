<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>订单列表</title>
    <meta name="author" content="STSOFT Team" />
    <meta name="copyright" content="T-One" />
    <link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
    <script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/plugins/fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/plugins/chosen/chosen.jquery.js"></script>
    <script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
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
        $().ready(function() {
            <?php if (isset($message)): ?>
            $.message("<?=$message['type']?>", "<?=$message['content']?>");
            <?php endif; ?>

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
                    cityName = $("#selectcity  option:selected").text();
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
            initSelected();
            $('#selectprovince').change(function(){
                $('#selectcity option').remove();
                area_code = $("#selectprovince").val();
                if (area_code != 0){
                    for (var i=0; i<CITY_CODE.length; i++){
                        if (CITY_CODE[i].id == area_code){
                            var selectprovincename= CITY_CODE[i].value;
                        }
                    }
                }
                $('#selectprovincename').val(selectprovincename);

                area_id = area_code;
                cities = findChilds(area_code,CITY_CODE);
                clearNextOption(3);
                if (cities) {
                    updatelist('#selectcity',cities);
                }
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



            $("#exportOrders").click(function() {
                var selectprovince = $('#selectprovince').val();
                var selectprovincename = $('#selectprovincename').val();
                var selectcity = $('#selectcity').val();
                var selectcityname = $('#selectcityname').val();
                var selectcounty = $('#selectcounty').val();
                var selectstreet = $('#selectstreet').val();
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var exportStatus = $("input[name='exportStatus']:checked").val();
                //赋值
                $("#editRankForm3 input[name='selectprovince']").val(selectprovince);
                $("#editRankForm3 input[name='selectprovincename']").val(selectprovincename);
                $("#editRankForm3 input[name='selectcity']").val(selectcity);
                $("#editRankForm3 input[name='selectcityname']").val(selectcityname);
                $("#editRankForm3 input[name='selectcounty']").val(selectcounty);
                $("#editRankForm3 input[name='selectstreet']").val(selectstreet);
                $("#editRankForm3 input[name='start_date']").val(startDate);
                $("#editRankForm3 input[name='end_date']").val(endDate);
                $("#editRankForm3 input[name='exportStatus']").val(exportStatus);
                var $dlgEdit3 = $("#dlgEdit3");
                $dlgEdit3.find(".modal-title").text("导出店铺");
                $dlgEdit3.modal('show');

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
        // 地址选择..

    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">报表导出</div>
    <div class="list-wrapper">
        <form id="listForm" class="form-inline" action="area" method="get">
            <div class="filter-bar">
                <div>
                    <div class="form-group m-l-sm">
                        <input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
                        <input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">
                        <div class="form-group m-l-sm">
                            区域:&nbsp;
                            <select class="form-control" name="selectprovince" id="selectprovince" value="<?= $selectcity ?>">
                                <option value=0 > 全部 </option>
                            </select>
                            <input class="hidden-input" name="selectprovincename" id='selectprovincename' type="text" value="<?= $selectprovincename ?>">
                            <select class="form-control" name="selectcity" id='selectcity'>
                                <option value=0 > 全部 </option>
                            </select>
                            <input class="hidden-input" name="selectcityname" id='selectcityname' type="text" value="">
                            <select class="form-control" name="selectcounty" id='selectcounty'>
                                <option value=0 > 全部 </option>
                            </select>
                            <select class="form-control" name="selectstreet" id='selectstreet'>
                                <option value=0 > 全部 </option>
                            </select>
                        </div >
                        <div class="form-group m-l-sm">
                            导出状态:&nbsp;
                            <label><input name="exportStatus" type="radio" value="2" <?php  if(empty($exportStatus) || $exportStatus == '2'):?>  checked="checked" <?php endif;?> />全部 </label>
                            <label><input name="exportStatus" type="radio" value="0"  <?php  if($exportStatus == '0'):?>  checked="checked" <?php endif;?> />已支付 </label>
                            <label><input name="exportStatus" type="radio" value="1"  <?php  if($exportStatus == '1'):?>  checked="checked" <?php endif;?> />已完成 </label>
                            <label><input name="exportStatus" type="radio" value="3"  <?php  if($exportStatus == '3'):?>  checked="checked" <?php endif;?> />已取消 </label>
                        </div>
                    </div>
                    <input type="hidden" name="reload" value="0" id="reload"></input>
                    <button class="btn btn-white m-l-sm" type="submit" id="select">筛 选</button>
                    <!-- 		                <button class="btn btn-white m-l-sm" type="button" id="moreButton">更多条件 <i class="fa fa-angle-double-down"></i></button> -->
                    <button class="btn btn-white m-l-sm" type="button" id="exportOrders">导出订单</a></button>

                </div>
            </div>
            <table id="listTable" class="list table">
                <tr>
                    <th class="check">&nbsp;</th>
                    <th class="">
                        <a href="javascript:;" class="sort" name="area">省</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="city">市</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="address">区域</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="number">订单数</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="total_money">总金额</a>
                    </th>

                    <!-- <th>
                        <span>操作</span>
                    </th> -->
                </tr>
                <?php foreach ($itemList as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>" />
                        </td>

                        <td>
                            <?= $item['area']?>
                        </td>
                        <td>
                            <?= $item['city']?>
                        </td>
                        <td>
                            <?= $item['names'] ?>
                        </td>
                        <td>
                            <?= $item['number'] ?>
                        </td>

                        <td>
                            <?= $item['total_money'] ?>
                        </td>

                        <!-- <td class="operation">
                                <a class="deleteOrder" data-url="delete" data-id="<?= $item['id'] ?>" title="删除"><i
                                            class="fa fa-trash-o"></i></a>
							</td> -->
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
                    <th colspan="13">
							<span>
								<input type="checkbox" class="i-check" id="selectAll" />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?>个订单</span>
                                <!-- <a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="toggle_enable" data-reload="true" data-params="is_enabled=0">批量导出</a> -->
							</span>

                        <?php $this->load->view('admin/pagination'); ?>
                    </th>
                </tr>
            </table>
        </form>
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
                <form id="editRankForm3" action="exportarea" class="form-horizontal" method="post">
                    <div>
                        <h4>导出需要一些时间,确认导出？</h4>
                    </div>
                    <input type="hidden" value="" name="selectprovince"></input>
                    <input type="hidden" value="" name="selectprovincename"></input>
                    <input type="hidden" value="" name="selectcity"></input>
                    <input type="hidden" value="" name="selectcounty"></input>
                    <input type="hidden" value="" name="selectstreet"></input>
                    <input type="hidden" value="" name="selectcityname"></input>
                    <input type="hidden" value="" name="start_date"></input>
                    <input type="hidden" value="" name="end_date"></input>
                    <input type="hidden" value="" name="exportStatus"></input>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
                <button type="submit" class="btn btn-primary" onclick="$('#editRankForm3').submit();"  data-dismiss="modal">确&nbsp;&nbsp;定</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>