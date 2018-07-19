<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        退款
    </title>
    <meta name="author" content="STSOFT Team" />
    <meta name="copyright" content="T-One" />
    <link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
    <link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">
    <script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
    <script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
    <script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
    <script type="text/javascript">
        $().ready(function() {

            <?php if (isset($message)): ?>
            $.message("<?=$message['type']?>", "<?=$message['content']?>");
            <?php endif; ?>

            var price;
            var ticket_id=$("input[name='refuse_num']").data("id");
            $.post("get_price",{ticket_id:ticket_id},function (res) {
                if (res != "fail"){
                    price=res;
                }
            },'json');

//            alert($("input[name='refuse_num']").data('id'));
            $("input[name='refuse_num']").on("input",function () {
                var ticket_id=$("input[name='refuse_num']").data("id");
                $.post("get_price",{ticket_id:ticket_id},function (res) {
                    if (res != "fail"){
                        price=res;
                    }
                },'json');
                var refuse_num=$(this).val();
                var ticket_num=$("input[name='ticket_num']").val();
                var cha=refuse_num-ticket_num;
                if (cha > 0){
                    alert("票数超标");
                    $(this).val(0);
                    refuse_num=0;
                }
                var refuse_fee=refuse_num*price;
                $("input[name='refuse_fee']").val(refuse_fee);

            })
        });
    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        订单退款
    </div>
    <div class="input-wrapper">
        <form id="inputForm" action="on_refuse" method="post" class="form-horizontal">
            <?php if (!empty($itemInfo)): ?>
                <input type="hidden" name="ticket_id" value="<?=$itemInfo['ticket_id']?>" />
            <?php endif; ?>

            <div class="form-group">
                <label for="oid" class="col-sm-2 control-label">
                    订单号：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="oid" name="oid" class="form-control" readonly maxlength="50" value="<?= empty($itemInfo)?'':$itemInfo['oid']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">
                    票种：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="title" name="title" class="form-control" readonly value="<?= empty($itemInfo)?'':$itemInfo['title']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="ticket_num" class="col-sm-2 control-label">
                    票数：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="ticket_num" name="ticket_num" class="form-control" readonly value="<?= empty($itemInfo)?'':$itemInfo['ticket_num']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="real_ticket_num" class="col-sm-2 control-label">
                    实际出票数：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="real_ticket_num" name="real_ticket_num" class="form-control" readonly value="<?= empty($itemInfo)?'':$itemInfo['real_ticket_num']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="total_fee" class="col-sm-2 control-label">
                    花费金额：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="total_fee" name="total_fee" class="form-control" readonly value="<?= empty($itemInfo)?'':$itemInfo['total_fee']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="create_date" class="col-sm-2 control-label">
                    下单时间：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="create_date" name="create_date" class="form-control" readonly value="<?= empty($itemInfo)?'':$itemInfo['create_date']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="refuse_num" class="col-sm-2 control-label required">
                    申请退款彩票个数：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="refuse_num" name="refuse_num" data-id="<?=$itemInfo['ticket_id']?>" class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <label for="refuse_fee" class="col-sm-2 control-label required">
                    退款金额：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="refuse_fee" name="refuse_fee" readonly class="form-control"/>
                </div>
            </div>


            <div class="form-group m-t-lg">
                <div class="col-sm-offset-2 col-sm-4">
                    <button type="submit" class="btn btn-primary">退&nbsp;&nbsp;款</button>
                    <button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>