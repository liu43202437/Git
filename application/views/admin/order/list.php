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
	
	$(".addPointBtn").click(function() {
		var $td = $(this).parent("td");
		var id = $(this).data("id");
		$("#userId").val(id);
		$("#userNickname").text($td.siblings().eq(3).text());
		$("#currentPoint").text($td.siblings().eq(4).text());
		$("#dlgAddPoint").modal("show");
	});
	
	$("#addPointForm").validate({
		rules: {
			point: {
				required: true,
				min: 1
			}
		},
		submitHandler: function(form) {
			$.ajax({
				url: 'add_point',
				type: 'post',
				data: $("#addPointForm").serialize(),
				dataType: 'json',
				cache: false,
				success: function(data) {
					$.message(data.message);
					if (data.message.type == "success") {
						var userId = $("#userId").val();
						$(".point" + userId).text(data.point);
						$("#dlgAddPoint").modal("hide");
					}
				},
				fail: function() {
					$.message('error', '网路错误！');
				}
			});
			return false;
		}
	});
	$('.selectprovince').change(function(){
		$('#selectcity option').remove();
		updateCities();
	});
	$("#select").click(function(){
		$('#reload').val(1);
		$('#listForm').submit();
	})
	function updateCities() {
		$("#city").empty();
		$.ajax({
			url: "<?=base_url()?>common/city_list",
			type: "get",
			data: {province_id: $(".selectprovince").val()},
			dataType: "json",
			cache: false,
			success: function(data) {
				if (data.error == 0) {
					var html = "<option value=1 > 全部 </option>";
					for (i in data.result) {
						var item = data.result[i];
						html += '<option value="' + item.name + '">' + item.name + '</option>';
						
					};
					$("#selectcity").append(html);
					
					var cityName = $("#city").find(":selected").text();
					$("#cityName").val(cityName);
				}
			}
		});
	}

	$("#dumpexcel").click(function() {
		var $dlgEdit = $("#dlgEdit");
		$dlgEdit.find(".modal-title").text("配送");
		$dlgEdit.modal('show');
	});
	$("#exportOrders").click(function() {
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var selectType = $('#selectType').val();
		var selectKeyword = $('#selectKeyword').val();
		var selectprovince = $('#selectprovince').val();
		var selectprovincename = $('#selectprovincename').val();
		var selectcity = $('#selectcity').val();
		var selectcityname = $('#selectcityname').val();
		var selectcounty = $('#selectcounty').val();
		var selectstreet = $('#selectstreet').val();
		var exportStatus = $("select[name='exportStatus']:checked").val();
		//判断起止时间
		if(startDate == '' || endDate == ''){
			alert('请输入起止时间');
			return;
		}
		//赋值
		$("#editRankForm2 input[name='startDate']").val(startDate);
		$("#editRankForm2 input[name='endDate']").val(endDate);
		$("#editRankForm2 input[name='selectType']").val(selectType);
		$("#editRankForm2 input[name='selectKeyword']").val(selectKeyword);
		$("#editRankForm2 input[name='selectprovince']").val(selectprovince);
		$("#editRankForm2 input[name='selectprovincename']").val(selectprovincename);
		$("#editRankForm2 input[name='selectcity']").val(selectcity);
		$("#editRankForm2 input[name='selectcityname']").val(selectcityname);
		$("#editRankForm2 input[name='selectcounty']").val(selectcounty);
		$("#editRankForm2 input[name='selectstreet']").val(selectstreet);
		$("#editRankForm2 input[name='exportStatus']").val(exportStatus);
		var $dlgEdit = $("#dlgEdit2");
		$dlgEdit.find(".modal-title").text("导出订单");
		$dlgEdit.modal('show');
	});

});

function onToggleEnabled(obj, message) {
	var $obj = $(obj);
	var $iChild = $(obj).children("i.fa");
	if ($iChild.hasClass("fa-ban")) {
		$iChild.removeClass("fa-ban").addClass("fa-circle-o");
		$obj.attr("title", "冻结");
	} else {
		$iChild.removeClass("fa-circle-o").addClass("fa-ban");
		$obj.attr("title", "恢复");
	}
}


</script>
<style type="text/css">
#citySelect_chosen {
	width: 140px !important;
	margin-left: 10px;
}
</style>
</head>
<body>
	<div class="content-wrapper">
		<div class="title-bar">订单列表</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="lists" method="get">
				<div class="filter-bar">
					<div>
						<div class="form-group m-l-sm">
							<input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
							<input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">

							<select class="form-control" name="selectType" id="selectType">
								<option value="name" <?php  if($selectType == 'name'):?> selected='selected' <?php endif;?> >用户名</option>
								<option value="trade_no" <?php  if($selectType == 'trade_no'):?> selected='selected' <?php endif;?> >订单号</option>
								<option value="phone"  <?php  if($selectType == 'phone'):?> selected='selected' <?php endif;?> >手机</option>
							</select>
							<input type="text" class="form-control m-l-sm" name="selectKeyword" id="selectKeyword" value="<?= $selectKeyword?>" placeholder="关键字">

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
								<select class="form-control s-lg" name="exportStatus" id="type">
									<option value="0" <?php if(empty($exportStatus) || $exportStatus == 5):?> selected = "selected" <?php endif; ?> >全部</option>
									<option value="1" <?php if($exportStatus == 1):?> selected = "selected" <?php endif; ?> >已配送</option>
									<option value="2" <?php if($exportStatus == 2):?> selected = "selected" <?php endif; ?> >已付款</option>
									<option value="3" <?php if($exportStatus == 3):?> selected = "selected" <?php endif; ?> >已完成</option>
									<option value="4" <?php if($exportStatus == 4):?> selected = "selected" <?php endif; ?> >已取消</option>
								</select>
							</div >
							
						</div>
						<input type="hidden" name="reload" value="0" id="reload"></input>
                        <button class="btn btn-white m-l-sm" type="button" id="select">筛 选</button>
<!-- 		                <button class="btn btn-white m-l-sm" type="button" id="moreButton">更多条件 <i class="fa fa-angle-double-down"></i></button> -->
						<button class="btn btn-white m-l-sm" type="button" id="exportOrders">导出订单</a></button>
                        <button class="btn btn-white m-l-sm" type="button" id="dumpexcel">配送</a></button>

			        </div>		
				</div>
				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="number">
							<a href="javascript:;" class="sort" name="id">ID</a>
						</th>
                        <th class="">
                            <a href="javascript:;" class="sort" name="trade_no">订单号</a>
                        </th>
						 <th>
							<a href="javascript:;" class="sort" name="phone">手机号码</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="nickname">用户昵称</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="total_money">总金额</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="description">订单详情描述</a>
						</th>
                        <th>
                            <a href="javascript:;" class="sort" name="order_status">订单状态</a>
                        </th>
                        <th>
                            <a href="javascript:;" class="sort" name="address">用户地址</a>
                        </th>
						<th>
							<a href="javascript:;" class="sort" name="create_date">下单时间</a>
						</th>
                        <th>
                            <a href="javascript:;" class="sort" name="dump_status">配送状态</a>
                        </th>
						<th class="time">
							<a href="javascript:;" class="sort" name="update_date">配送时间</a>
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
								<?= $item['id'] ?>
							</td>
                            <td>
                                <?= $item['trade_no']?>
                            </td>
							 <td>
                               <?= $item['phone']?>
                            </td>
                            <td>
								<?= $item['nickname'] ?>
							</td>
							<td>
								<?= $item['money'] ?>
							</td>
							<td>
                                <?= $item['description'] ?>
							</td>
                            <td>
                                <?= $item['status'] ?>
                            </td>
                            <td>
                                <?= $item['address'] ?>
                            </td>
							<td>
								<?= $item['create_date'] ?>
							</td>
                            <td>
                                <?= $item['dump_status'] ?>
                            </td>
							<td>
								<?= $item['dump_time'] ?>
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
		
		<div class="modal fade" id="dlgAddPoint">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">增加烟币</h4>
					</div>
					<div class="modal-body" style="padding:15px;">
					<form id="addPointForm" action="add_point" class="form-horizontal" method="post">
						<input type="hidden" name="user_id" id="userId" value="" />
						<div class="form-group">
							<label class="control-label col-xs-5">用&nbsp;&nbsp;户：</label>
							<div class="col-xs-7 font-bold" id="userNickname" style="padding-top:7px;"></div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-5">当前烟币：</label>
							<div class="col-xs-7 font-bold" id="currentPoint" style="padding-top:7px;"></div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-5">增加烟币：</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" name="point" id="addPoint">
							</div>
						</div>
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">取&nbsp;消</button>
						<button type="submit" class="btn btn-primary" onclick="$('#addPointForm').submit();">确&nbsp;&nbsp;定</button>
					</div>
				</div>
			</div>
		</div>
		
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">添加等级</h3>
                    </div>
                    <div class="modal-body">
                    <form id="editRankForm" action="ajax_dump" class="form-horizontal" method="post">
                        <div>
                        <h4>确认配送？</h4>
                        </div>
                    </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
                        <button type="submit" class="btn btn-primary" onclick="$('#editRankForm').submit();"  data-dismiss="modal">确&nbsp;&nbsp;定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit2">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">添加等级</h3>
                    </div>
                    <div class="modal-body">
                    <form id="editRankForm2" action="exportOrders" class="form-horizontal" method="post">
                        <div>
                        <h4>导出需要一些时间,确认导出？</h4>
                        </div>
                        <input type="hidden" value="" name="startDate"></input>
                        <input type="hidden" value="" name="endDate"></input>
                        <input type="hidden" value="" name="selectType"></input>
                        <input type="hidden" value="" name="selectKeyword"></input>
						<input type="hidden" value="" name="selectprovince"></input>
						<input type="hidden" value="" name="selectprovincename"></input>
						<input type="hidden" value="" name="selectcity"></input>
						<input type="hidden" value="" name="selectcounty"></input>
						<input type="hidden" value="" name="selectstreet"></input>
						<input type="hidden" value="" name="selectcityname"></input>
                        <input type="hidden" value="" name="exportStatus"></input>
                    </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关&nbsp;&nbsp;闭</button>
                        <button type="submit" class="btn btn-primary" onclick="$('#editRankForm2').submit();"  data-dismiss="modal">确&nbsp;&nbsp;定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>