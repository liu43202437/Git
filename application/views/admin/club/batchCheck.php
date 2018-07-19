<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>零售店批量审核</title>
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
        零售店批量审核
        <!-- <?php if ($isEditable): ?>
            <div class="pull-right op-wrapper">
                <a href="addClub"><i class="fa fa-plus"></i> 添加零售店批量审核</a>
            </div>

        <?php endif; ?> -->
    </div>
    <div class="list-wrapper">
        <form id="listForm" class="form-inline" action="doBatchCheck" enctype="multipart/form-data" method="post">
            <table id="listTable" class="list table" style="font-size:150%">
                <tr>
                    <th class="number">
                        <td>
                            请上传需要审核的店铺excel文件，并确保格式正确。
                        </td>
                    </th>
                    <th class="number">
                        <input type="file" name="file" id="file">
                    </th>
                </tr>
            </table>
            <button class="btn btn-white m-l-sm" type="submit">提交</button>
        </form>
</div>
</body>
</html>