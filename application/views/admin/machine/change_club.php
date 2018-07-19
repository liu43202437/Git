<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        更改绑定店铺
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

            $.post('getarea',{area_id:1},function (res) {
                $.each(res,function (i,v) {
                    var html="<option value='"+ v.area_id +"'>"+v.name+"</option>";
                    $("#selectprovince").append(html);
                })
            },'json');


            var area='';
            var key='';
            $("#selectprovince").change(function () {
                $('.moment_city').remove();
                $('.moment_country').remove();
                $('.moment_street').remove();
                $('.club').remove();
                $.post('getarea',{area_id:$(this).val()},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='moment_city' value='"+ v.area_id +"'>"+v.name+"</option>";
                        $("#selectcity").append(html);
                    })
                },'json');

                key = 'area_id';
                area=$("#selectprovince").val();

                $.post('get_club',{key:key,filter:area},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='club' value='"+ v.id +"'>"+v.name+"</option>";
                        $('#club').append(html);
                    })
                },'json');
            });

            $("#selectcity").change(function () {
                $('.moment_country').remove();
                $('.moment_street').remove();
                $('.club').remove();
                $.post('getarea',{area_id:$(this).val()},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='moment_country' value='"+ v.area_id +"'>"+v.name+"</option>";
                        $("#selectcounty").append(html);
                    })
                },'json');

                key = 'left(area_code,4)';
                area=$("#selectcity").val();
                $.post('get_club',{key:key,filter:area},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='club' value='"+ v.id +"'>"+v.name+"</option>";
                        $('#club').append(html);
                    })
                },'json');
            });

            $("#selectcounty").change(function () {
                $('.moment_street').remove();
                $('.club').remove();
                $.post('getarea',{area_id:$(this).val()},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='moment_street' value='"+ v.area_id +"'>"+v.name+"</option>";
                        $("#selectstreet").append(html);
                    })
                },'json');

                key = 'left(area_code,6)';
                area=$("#selectcity").val();
                $.post('get_club',{key:key,filter:area},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='club' value='"+ v.id +"'>"+v.name+"</option>";
                        $('#club').append(html);
                    })
                },'json');
            });

            $("#selectstreet").change(function () {
                $('.club').remove();
                key = 'area_code';
                area=$("#selectstreet").val();
                $.post('get_club',{key:key,filter:area},function (res) {
                    $.each(res,function (i,v) {
                        var html="<option class='club' value='"+ v.id +"'>"+v.name+"</option>";
                        $('#club').append(html);
                    })
                },'json');
            })


            $("#submit").click(function () {
                if ($("#club").val() == 0){
                    alert("请选择店铺");
                    return false;
                }else{
                    $("#inputForm").submit();
                }
            })
        });
    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="title-bar">
        更改绑定店铺
    </div>
    <div class="input-wrapper">
        <form id="inputForm" action="on_change_club" method="post" class="form-horizontal">
          
             <input type="hidden" name="id" value="<?=$id?>" />
          

            <div class="form-group">
                <label for="oid" class="col-sm-2 control-label">
                    机器号：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="oid" name="machine_id" class="form-control" readonly maxlength="50" value="<?=$itemInfo['machine_id']?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">
                    现店铺：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="title" name="view_name" class="form-control" readonly value="<?=$itemInfo['view_name']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="ticket_num" class="col-sm-2 control-label">
                    店铺负责人：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="ticket_num" name="name" class="form-control" readonly value="<?=$itemInfo['name']?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="real_ticket_num" class="col-sm-2 control-label">
                    电话：
                </label>
                <div class="col-sm-4">
                    <input type="text" id="real_ticket_num" name="phone" class="form-control" readonly value="<?=$itemInfo['phone']?>"/>
                </div>
            </div>

            <div class="form-group" style="display:flex">
                <label class="col-sm-3 col-md-2 control-label required">
                    <span>重新选择店铺:</span>
                </label>
                <div class="col-sm-6 col-md-4" style="display: flex">
                    <select class="form-control" name="selectprovince" id="selectprovince">
                        <option value=0 > 省 </option>
                    </select>
                    <select  class="form-control" name="selectcity" id='selectcity'>
                        <option value=0 > 市 </option>
                    </select>
                    <select  class="form-control" name="selectcounty" id='selectcounty'>
                        <option value=0 > 区域 </option>
                    </select>
                    <select  class="form-control" name="selectstreet" id='selectstreet'>
                        <option value=0 > 街道 </option>
                    </select>
                    <select  class="form-control" name="club" id='club'>
                        <option value=0 > 店铺 </option>
                    </select>
                </div>
            </div >


            <div class="form-group m-t-lg">
                <div class="col-sm-offset-2 col-sm-4">
                    <button type="button" id="submit" class="btn btn-primary">更&nbsp;&nbsp;改</button>
                    <button type="button" class="btn btn-white m-l-md" onclick="history.back()">返&nbsp;&nbsp;回</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>