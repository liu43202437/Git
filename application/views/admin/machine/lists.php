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
            $("#select").click(function(){
                $('#reload').val(1);
                $('#listForm').submit();
            })

            $("#exportOrders").click(function() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var selectType = $('#selectType').val();
                var exportStatus = $("select[name='exportStatus']:checked").val();
                var selectKeyword = $('#selectKeyword').val();
                //判断起止时间
                if(startDate == '' || endDate == ''){
                    alert('请输入起止时间');
                    return;
                }
                //赋值
                $("#editRankForm2 input[name='startDate']").val(startDate);
                $("#editRankForm2 input[name='endDate']").val(endDate);
                $("#editRankForm2 input[name='selectType']").val(selectType);
                $("#editRankForm2 input[name='exportStatus']").val(exportStatus);
                $("#editRankForm3 input[name='selectKeyword']").val(selectKeyword);
                var $dlgEdit = $("#dlgEdit2");
                $dlgEdit.find(".modal-title").text("导出订单");
                $dlgEdit.modal('show');
            });

        });
        
        

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
    <div class="title-bar">彩票机列表</div>
    <div class="list-wrapper">
        <form id="listForm" class="form-inline" action="lists" method="get">
            <div class="filter-bar">
                <div>
                    <div class="form-group m-l-sm">
                        <input type="text" class="form-control m-l-sm" name="selectKeyword" id="selectKeyword" value="<?= $selectKeyword?>" placeholder="机器号">
                    </div>
                    <input type="hidden" name="reload" value="0" id="reload"></input>
                    <button class="btn btn-white m-l-sm" type="button" id="select">筛 选</button>
                    <!-- 		                <button class="btn btn-white m-l-sm" type="button" id="moreButton">更多条件 <i class="fa fa-angle-double-down"></i></button> -->
<!--                    <button class="btn btn-white m-l-sm" type="button" id="exportOrders">导出订单</a></button>-->

                </div>
            </div>
            <table id="listTable" class="list table">
                <tr>
                    <th class="check">&nbsp;</th>
                    <th class="">
                        <a href="javascript:;"  name="machine_id">机器编号</a>
                    </th>
                    <th>
                        <a href="javascript:;"  name="name">店主</a>
                    </th>
                    <th>
                        <a href="javascript:;"  name="name">状态</a>
                    </th>
                    <th>
                        <a href="javascript:;"  name="name">票种</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="ticket_num">库存</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="total_ticket_num">总销售票数</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="total_ticket_amount">总销售额</a>
                    </th>
                    <th>
                        <a href="javascript:;" name="title">机头状态</a>
                    </th>
                    <th>
                        <a href="javascript:;" name="nickname">上次心跳时间</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="create_date">激活时间</a>
                    </th>
                    <th>
                        <span>操作</span>
                    </th>
                </tr>
                <?php foreach ($itemList as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="i-check" name="ids[]" value="<?= $item['machine_id'] ?>" />
                        </td>
                        <td>
                            <?= $item['machine_code']?>
                        </td>
                        <td>
                            <?= $item['name']?>
                        </td>
                        <td>
                            <?= $item['status'] ?>
                        </td>
                        <td>
                            <?= $item['ticket_name'] ?>
                        </td>
                        <td>
                            <?= $item['ticket_num'] ?>
                        </td>
                        <td>
                            <?= $item['total_ticket_num'] ?>
                        </td>
                        <td>
                            <?= $item['total_ticket_amount'] ?>
                        </td>
                        <td>
                            <?= $item['header_status'] ?>
                        </td>
                        <td>
                            <?php
                                echo date('Y-m-d H:i:s',$item['update_date']);
                            ?>
                        </td>
                        <td>
                            <?= $item['create_date'] ?>
                        </td>
                        <td class="operation">
                            <a class="remove" data-id="<?=$item['id']?>" href="del?id=<?=$item['id']?>" title="初始化"><i class="fa fa-trash-o"></i></a>
                            <a href="change_club?id=<?=$item['id']?>" title="编辑"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>
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

<div class="modal fade" tabindex="-1" role="dialog" id="dlgEdit2">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">添加等级</h3>
            </div>
            <div class="modal-body">
                <form id="editRankForm2" action="exportorder" class="form-horizontal" method="post">
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