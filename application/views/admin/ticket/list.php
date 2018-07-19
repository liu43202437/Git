<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>票券管理</title>
    <meta name="author" content="STSOFT Team"/>
    <meta name="copyright" content="T-One"/>
    <link href="<?= base_url() ?>resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/bootstrap.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/animate.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
    <link href="<?= base_url() ?>resources/css/style.css" rel="stylesheet">
    <script type="text/javascript" src="<?= base_url() ?>resources/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/datePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/plugins/chosen/chosen.jquery.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>
    <script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/common.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/list.js"></script>
    <script type="text/javascript">
        $().ready(function () {

            <?php if (isset($message)): ?>
            $.message("<?=$message['type']?>", "<?=$message['content']?>");
            <?php endif; ?>

            $("#areaSelect").chosen();
            $(".checkItemBtn").click(function(){

                var status = 1;
                var id = $(this).attr('data-id');
                var url = 'check';
                // var manager_id = prompt("请输入票券管理工作证号","");
                // if(manager_id == null || manager_id == ''){
                //     return;
                // }
                
                $.dialog({
                    type: "warn",
                    content: message("admin.dialog.checkConfirmOn"),
                    ok: message("admin.dialog.ok"),
                    cancel: message("admin.dialog.cancel"),
                    onOk: function () {
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: {'ids[]': id,'status':status},
                            dataType: "json",
                            cache: false,
                            success: function (message) {
                                $.message(message);
                                if (message.type == "success") {
                                    location.reload(true);
                                }
                                if (func) {
                                    window[func]($this, message);
                                }
                            }
                        });
                    }
                });
            });
            // 审核拒绝
            // $(".refuseItemBtn").click(function () {

            //     var id = $(this).attr('data-id');
            //     var $this = $(this);
            //     if ($this.hasClass("disabled")) {
            //         return false;
            //     }
            //     var url = 'refuse';
            //     if ($this.data('url')) {
            //         url = $this.data('url');
            //     }
            //     var $checkedIds = $("#listTable input[name^='ids']:enabled:checked");
            //     var data = $checkedIds.serialize();
            //     if ($this.data('params')) {
            //         var params = $this.data('params').split('&');
            //         for (var i in params) {
            //             var pair = params[i].split('=');
            //             //data[pair[0]] = pair[1];
            //             data += '&' + params[i];
            //         }
            //     }

            //     $.dialog({
            //         type: "warn",
            //         content: message("admin.dialog.refuseConfirmOn"),
            //         ok: message("admin.dialog.ok"),
            //         cancel: message("admin.dialog.cancel"),
            //         onOk: function () {
            //             $.ajax({
            //                 url: url,
            //                 type: "POST",
            //                 data: {'id':id},
            //                 dataType: "json",
            //                 cache: false,
            //                 success: function (message) {
            //                     $.message(message);
            //                     if (message.success == true) {
            //                         // $checkedIds.closest("tr").remove();
            //                         // if ($listTable.find("tr").size() <= 2) {
            //                         //     setTimeout(function () {
            //                         //         location.reload(true);
            //                         //     }, 3000);
            //                         // }
            //                         alert('已发送短信通知用户重新添加票券管理信息');
            //                         location.reload(true);
            //                     }
            //                     else{
            //                         alert('发送短信通知用户失败，请稍后再试');
            //                     }
            //                     // $batchButtons.addClass("disabled");
            //                     //$selectAll.prop("checked", false);
            //                     //$checkedIds.prop("checked", false);
            //                     // $selectAll.ifCheck("uncheck");
            //                 }
            //             });
            //         }
            //     });
            //     return false;
            // });
            var $dlgEdit = $("#dlgEdit");
            var id =0;
            $(".refuseItemBtn").click(function(){
                var $parent = $(this).parents("tr");
                $dlgEdit.find(".modal-title").text("审核拒绝原因");
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
                            alert('已发送微信消息通知用户重新填写票券管理信息');
                            location.reload(true);
                        }
                        else{
                            alert('发送微信通知用户失败，请稍后再试');
                        }
                    }
                });
            });
            $("#filtrate").click(function(){
                $.pageSkip(1);
            })

        });

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
        票券管理
            <div class="pull-right op-wrapper">
                <a href="add"><i class="fa fa-plus"></i> 添加票券</a>
            </div>
    </div>
    <div class="list-wrapper">
        <form id="listForm" class="form-inline" action="lists" method="get">
            <div class="filter-bar">
                <select class="form-control s-lg" name="province_id">
                    <?php foreach ($provinces as $key => $value):  ?>
                        <option value="<?= $key ?>" <?php if($key == $province_id):?> selected = "selected" <?php endif; ?> ><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-white m-l-sm" type="submit" id="filtrate">筛 选</button>
            </div>

            <table id="listTable" class="list table">
                <tr>
                    <th class="check">&nbsp;</th>
                    <th class="number">
                        <a href="javascript:;" class="sort" name="id">ID</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="title">名称</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="price">票价</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="count_price">价格</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="inventory">库存</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="status">状态</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="description">介绍</a>
                    </th>
                    <th>
                        <a href="javascript:;" class="sort" name="province">省份</a>
                    </th>
                    
                    <th>
                        <span>操作</span>
                    </th>
                </tr>
                <?php foreach ($itemList as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="i-check" name="ids[]" value="<?= $item['id'] ?>"/>
                        </td>
                        <td>
                            <?= $item['id'] ?>
                        </td>
                        <td>
                            <?= $item['title'] ?>
                        </td>
                        <td>
                            <?= $item['price'] ?>
                        </td>
                        <td>
                            <?= $item['count_price'] ?>
                        </td>
                        <td>
                            <?= $item['inventory'] ?>
                        </td>
                        <td>
                            <?php if($item['status'] == 0): ?>正常<?php else: ?>禁止<?php endif; ?>
                        </td>
                        <td>
                            <?= $item['description'] ?>
                        </td>
                        <td>
                            <?= $item['province'] ?>
                        </td>
                        <td class="operation">  
                                <a href="edit?id=<?= $item['id'] ?>" title="编辑"><i class="fa fa-edit"></i></a>
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
                    <th colspan="12">
							<span>
								<input type="checkbox" class="i-check" id="selectAll"
                                       />
								<span class="m-r-sm">已选择<span id="selectedCount">0</span>/<?= count($itemList) ?>
                                    票券管理</span> 
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
    </div>
</div>
</body>
</html>