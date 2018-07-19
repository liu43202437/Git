<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>管理中心首页</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
<link href="<?=base_url()?>resources/plugins/iCheck/custom.css" rel="stylesheet">
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/echarts.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/plugins/datePicker/WdatePicker.js"></script>
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>

<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var chartData = null;
	
	function loadMainChart(url) {
		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			cache: false,
			success: function(data) {
				if (data.message.type == "success") {
					var xAxisData = new Array();
					var seriesData = new Array();
					for (var i = 0; i < data.labels.length; i++) {
						seriesData.push({
							name: data.labels[i],
						    type: 'line',
						    data: []
						});
					}
					
					$.each(data.day_report, function(iIndex, item) {
						var aIndex = 0;
						for (var name in item) {
							if (name == 'label') {
								xAxisData.push(item.label);
							} else {
								seriesData[aIndex++].data.push(item[name]);
							}
						}
					});
						
					var option = {
						tooltip : {
						    trigger: 'axis'
						},
						legend : {
	    					y: 'bottom',
						    data: data.labels
						},
						grid: {
							x: '40px',
							y: '20px',
							x2: '40px'
						},
						xAxis : [
						    {
						        type : 'category',
						        boundaryGap : true,
						        data : xAxisData
						    }
						],
						yAxis : [
						    {
						        type : 'value',
						        axisLabel : {
						            formatter: '{value}'
						        }
						    }
						],
						series : seriesData,
						color: [ '#ff7f50', '#87cefa', '#da70d6', '#32cd32', '#6495ed']
					};
					echarts.init(document.getElementById('mainChart')).setOption(option);
					
					chartData = data;
					$("#subNav li:first-child a").click();
					
				} else {
					$.message(data.message);
				}
			},
			fail: function() {
				$.message('error', '网路错误！');
			}
		});
	}
	
	function loadMainPieChart(kind) {
		if (chartData == null) {
			return false;
		}
		var kindData = chartData[kind];
		
		seriesData = new Array();
		var index = 0;
		for (name in kindData) {
			seriesData.push({
				value: kindData[name],
				name: chartData.labels[index++]
			});
		}
		
		option = {
			tooltip : {
				formatter: "{b} : {c} ({d}%)"
			},
			series : [
				{
					type: 'pie',
					radius: ['40%', '60%'], 
					center: ['50%', '50%'],
					itemStyle : {
		                emphasis : {
		                    label : {
		                        show : true,
		                        position : 'center',
		                        textStyle : {
		                            fontSize : '20',
		                            fontWeight : 'bold'
		                        }
		                    }
		                }
		            },
					data: seriesData
				}
			],
			color: ['#ff7f50', '#87cefa', '#da70d6', '#32cd32', '#6495ed']
		};
		echarts.init(document.getElementById('mainPieChart')).setOption(option);
	}
	
	$("#mainNav a").click(function(e) {
		e.preventDefault();
		$(this).parent().addClass("active").siblings().removeClass("active");
		var url = $(this).attr("href");
		loadMainChart('<?=base_url()?>admin/home/' + url);
	});
	$("#mainNav li:first-child a").click();
	
	$("#subNav a").click(function(e) {
		e.preventDefault();
		$(this).parent().addClass("active").siblings().removeClass("active");
		var kind = $(this).attr("href");
		loadMainPieChart(kind);
	});
	
	option = {
	    tooltip : {
	        formatter: "{b} : {c} ({d}%)"
	    },
	    legend: {
	        orient : 'horizontal',
	        x : 'center',
	        y: 'bottom',
	        data:['文章','图集','视频','链接','图文']
	    },
	    series : [
	        {
	            type:'pie',
	            radius : '60%', 
	            center: ['50%', '40%'],
	            itemStyle : {
	            	normal: {
						label: { show: false },
						labelLine: { show: false }	            	
					},
		            emphasis : {
		                label : { show : true },
		                labelLine: { show: true }
		            }
		        },
	            data:[
	                {value:<?= $article ?>, name:'文章'},
	                {value:<?= $gallery ?>, name:'图集'},
	                {value:<?= $video ?>, name:'视频'},
	                {value:<?= $link ?>, name:'链接'},
	                {value:<?= $baby ?>, name:'图文'}
	            ]
	        }
	    ],
	    color: [ '#ff7f50', '#87cefa', '#da70d6', '#32cd32', '#6495ed']
	};
	echarts.init(document.getElementById('contentChart')).setOption(option);
	
	option = {
	    tooltip : {
	        formatter: "{b} : {c} ({d}%)"
	    },
	    legend: {
	        orient : 'horizontal',
	        x : 'center',
	        y: 'bottom',
	        data:['零售店','公益彩票','联盟报名','教练报名', '道馆报名']
	    },
	    series : [
	        {
	            type:'pie',
	            radius : '60%', 
	            center: ['50%', '40%'],
	            itemStyle : {
	            	normal: {
						label: { show: false },
						labelLine: { show: false }	            	
					},
		            emphasis : {
		                label : { show : true },
		                labelLine: { show: true }
		            }
		        },
	            data:[
	                {value:<?= $player ?>, name:'零售店'},
	                {value:<?= $referee ?>, name:'公益彩票'},
					//{value:<?= $challenge ?>, name:'联盟报名'},
	              //  {value:<?= $coach ?>, name:'教练报名'},
	             //   {value:<?= $club ?>, name:'道馆报名'}
	            ]
	        }
	    ],
	    color: [ '#87cefa', '#da70d6', '#32cd32', '#6495ed', '#ff7f50']
	};
	echarts.init(document.getElementById('auditChart')).setOption(option);
	
	option = {
	    tooltip : {
	        formatter: "{b} : {c} ({d}%)"
	    },
	    legend: {
	        orient : 'horizontal',
	        x : 'center',
	        y: 'bottom',
	        data:['意见反馈','未读反馈','评论']
	    },
	    series : [
	        {
	            type:'pie',
	            radius : '60%', 
	            center: ['50%', '40%'],
	            itemStyle : {
	            	normal: {
						label: { show: false },
						labelLine: { show: false }	            	
					},
		            emphasis : {
		                label : { show : true },
		                labelLine: { show: true }
		            }
		        },
	            data:[
	                {value:<?= $feedback ?>, name:'意见反馈'},
	                {value:<?= $req_feedback ?>, name:'未读反馈'},
	                {value:<?= $comment ?>, name:'评论'}
	            ]
	        }
	    ],
	    labelLine: {
			show: false
	    },
	    color: [ '#da70d6', '#32cd32', '#6495ed']
	};
	echarts.init(document.getElementById('auditChart2')).setOption(option);
});
</script>
<style type="text/css">
body {
	font-size: 14px;
}
</style>
</head>
<body>
	<div class="content-wrapper home">
		<div class="title-bar">
		</div>
		<div class="list-wrapper">
			<div class="top">
				<div class="row">
					<ul id="mainNav" class="nav nav-pills col-xs-offset-1">
						<li><a href="visit_info">访问数据</a></li>
						<li><a href="new_users_info">新增用户</a></li>
						<li><a href="users_info">用户总数</a></li>
						<li><a href="money_info">充值资金</a></li>
						<li><a href="new_member_info">新认证</a></li>
						<li><a href="member_info">总认证</a></li>
					</ul>
				</div>
				<div class="m-t-lg">
					<div class="main-chart-wrapper pull-left">
						<div id="mainChart" style="width:960px; height:330px;"></div>
					</div>
					<div class="main-pie-chart-wrapper pull-left b-l p-l-md">
						<ul id="subNav" class="nav nav-pills m-l-lg">
							<li><a href="yesterday">昨天</a></li>
							<li><a href="week">周</a></li>
							<li><a href="month">月</a></li>
						</ul>
						<div id="mainPieChart" style="width:300px; height:300px;"></div>
					</div>
					<div class="clearfix"></div>
				</div>				
			</div>
			<hr/>
			<div class="bottom m-t-lg">
				<div class="sub-wrapper">
					<div class="chart-wrapper pull-left">
						<div id="contentChart" class="pie-chart" style="width:300px;"></div>
					</div>
					<div class="panel panel-default pull-right">
						<div class="panel-heading">
							<span class="font-bold">内容数据</span>
						</div>
						<div class="panel-body">
							<table class="table">
								<tr>
									<td>文&nbsp;&nbsp;章：</td><td><?= $article ?></td>
								</tr>
								<tr>
									<td>图&nbsp;&nbsp;集：</td><td><?= $gallery ?></td>
								</tr>
								<tr>
									<td>视&nbsp;&nbsp;频：</td><td><?= $video ?></td>
								</tr>
								<tr>
									<td>链&nbsp;&nbsp;接：</td><td><?= $link ?></td>
								</tr>
								<tr>
									<td>图&nbsp;&nbsp;文：</td><td><?= $baby ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="sub-wrapper">
					<div class="chart-wrapper pull-left">
						<div id="auditChart" class="pie-chart" style="width:280px;"></div>
					</div>
					<div class="panel panel-default pull-right">
						<div class="panel-heading">
							<span class="font-bold">审核</span>
						</div>
						<div class="panel-body">
							<table class="table">
								<tr>
									<td>零售店：</td><td><?= $player ?></td>
								</tr>
								<tr>
									<td>公益彩票：</td><td><?= $referee ?></td>
								</tr>
								<!--
								<tr>

									<td>联盟报名：</td><td><?= $challenge ?></td>
								</tr>
								<tr>
									<td>教练报名：</td><td><?= $coach ?></td>
								</tr>
								<tr>
									<td>道馆报名：</td><td><?= $club ?></td>
								</tr>
								-->
							</table>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="sub-wrapper">
					<div class="chart-wrapper pull-left">
						<div id="auditChart2" class="pie-chart" style="width:280px;"></div>
					</div>
					<div class="panel panel-default pull-right">
						<div class="panel-heading">
							<span class="font-bold">内容数据</span>
						</div>
						<div class="panel-body">
							<table class="table">
								<tr>
									<td>意见反馈：</td><td><?= $feedback ?></td>
								</tr>
								<tr>
									<td>未读反馈：</td><td><?= $req_feedback ?></td>
								</tr>
								<tr>
									<td>评&nbsp;&nbsp;论：</td><td><?= $comment ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
