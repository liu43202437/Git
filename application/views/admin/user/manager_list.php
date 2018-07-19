<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>客户经理列表</title>
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
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	<?php if (!empty($registerType) || $isEnabled !== '' || !empty($city)): ?>
		$("#moreButton").click();
	<?php endif; ?>
	
	$("#citySelect").chosen();
	
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
		<div class="title-bar">客户经理列表</div>
		<div class="list-wrapper">
			<form id="listForm" class="form-inline" action="lists" method="get">
				<div class="filter-bar">
					<div>
						<select class="form-control s-lg" name="gender">
							<option value="">性别</option>
							<option value="<?= GENDER_MALE ?>" <?php if ($gender == GENDER_MALE): ?>selected="selected"<?php endif; ?>><?= getUserGender(GENDER_MALE) ?></option>
							<option value="<?= GENDER_FEMALE ?>" <?php if ($gender == GENDER_FEMALE): ?>selected="selected"<?php endif; ?>><?= getUserGender(GENDER_FEMALE) ?></option>
						</select>
						<div class="form-group m-l-sm">
							<select class="form-control" name="time_type">
								<option>注册时间</option>
							</select>
							<input type="text" class="form-control Wdate" id="startDate" name="start_date" value="<?= $startDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', maxDate: '#F{$dp.$D(\'endDate\')}'});" placeholder="开始时间">
							<input type="text" class="form-control Wdate" id="endDate" name="end_date" value="<?= $endDate ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd', minDate: '#F{$dp.$D(\'startDate\')}', maxDate: new Date()});" placeholder="结束时间">
						</div>
						
						<input type="text" class="form-control m-l-sm" name="nickname" value="<?= $nickname ?>" placeholder="姓名">
		                <button class="btn btn-white m-l-sm" type="submit">帅 选</button>
		                <button class="btn btn-white m-l-sm" type="button" id="moreButton">更多条件 <i class="fa fa-angle-double-down"></i></button>
			        </div>
			        <div class="more-filters m-t-sm hidden">
			        	<select class="form-control s-lg" name="register_type">
							<option value="">注册方式</option>
							<option value="mobile" <?php if ($registerType == "mobile"): ?>selected="selected"<?php endif; ?>>手机</option>
							<option value="weixin" <?php if ($registerType == "weixin"): ?>selected="selected"<?php endif; ?>>微信</option>
						</select>
						<select class="form-control m-l-sm" name="is_enabled">
							<option value="">状态</option>
							<option value="1" <?php if ($isEnabled === 1): ?>selected="selected"<?php endif; ?>>正常</option>
							<option value="0" <?php if ($isEnabled === 0): ?>selected="selected"<?php endif; ?>>冻结</option>
						</select>
						<select class="form-control m-l-sm" name="city" id="citySelect">
							<option value="">城市</option>
							<?php foreach ($cities as $item): ?>
								<option value="<?= $item['name'] ?>" <?php if ($city == $item['name']): ?>selected="selected"<?php endif; ?>><?= $item['name'] ?></option>
							<?php endforeach; ?>
						</select>
			        </div>			
				</div>
				<table id="listTable" class="list table">
					<tr>
						<th class="check">&nbsp;</th>
						<th class="number">
							<a href="javascript:;" class="sort" name="id">ID</a>
						</th>
						<th class="qrcode">
							<a href="javascript:;" class="m-l-md">头像</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="phone">手机号码</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="nickname">昵称</a>
						</th>
						<th>
							<a href="javascript:;" class="sort" name="money">消费</a>
						</th>
						<<!-- th>
							<a href="javascript:;" class="sort" name="point">烟币</a>
						</th> -->
						<<!-- th>
							<a href="javascript:;" class="sort" name="app_version">版本</a>
						</th> -->
						<th class="time">
							<a href="javascript:;" class="sort" name="create_date">注册日期</a>
						</th>
						<th>
							<span>操作</span>
						</th>
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
								<a class="fancybox" href="<?= $item['avatar_url'] ?>" title="<?= $item['nickname'] ?>">
									<img src="<?= $item['avatar_url'] ?>" class="m-t-xs m-b-xs" width="60" height="60">
								</a>
							</td>
							<!-- <td>
								<a href="edit?id=<?= $item['id'] ?>"><?= $item['username'] ?></a>
							</td> -->
							<td>
								<a href="edit?id=<?= $item['id'] ?>"><?= $item['nickname'] ?></a>
							</td>
							<td>
								<a href="consume_history?id=<?= $item['id'] ?>"><?= $item['money'] ?></a>
							</td>
							<td>
								<a href="point_history?id=<?= $item['id'] ?>" class="point<?= $item['id'] ?>"><?= $item['point'] ?></a>
							</td>
							<td>
								<?= $item['app_version'] ?>
							</td>
							<td>
								<?= $item['create_date'] ?>
							</td>
							<td class="operation">
								<a href="order_list?id=<?= $item['id'] ?>" title="订单记录"><i class="fa fa-calendar-o"></i></a>
								<a role="ajax" data-url="toggle_enable" data-func="onToggleEnabled" data-reload="false" data-id="<?= $item['id'] ?>" title="<?= $item['is_enabled'] ? '冻结' : '恢复'?>"><i class="fa <?= $item['is_enabled'] ? 'fa-circle-o' : 'fa-ban'?>"></i></a>
								<a href="javascript:;" class="addPointBtn" data-id="<?= $item['id'] ?>" title="增加烟币"><i class="fa fa-database"></i></a>
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
								<input type="checkbox" class="i-check" id="selectAll" />
								<span class="m-r-sm">已选择 <span id="selectedCount">0</span>/<?=count($itemList)?>个用户</span>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="toggle_enable" data-reload="true" data-params="is_enabled=0">批量冻结</a>
								<a role="ajax" class="batch-btn btn btn-default btn-outline disabled" data-url="toggle_enable" data-reload="true" data-params="is_enabled=1">批量恢复</a>
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
</body>
</html>