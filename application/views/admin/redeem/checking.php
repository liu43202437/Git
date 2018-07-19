<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>对账管理</title>
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
        对账管理
        <!-- <?php if ($isEditable): ?>
            <div class="pull-right op-wrapper">
                <a href="addClub"><i class="fa fa-plus"></i> 添加对账管理</a>
            </div>
        <?php endif; ?> -->
    </div>
    <div class="list-wrapper">

            <table id="listTable" class="list table" style="font-size:150%">
                <tr>
                    <th class="number">
                        <a href="javascript:;">总兑奖金额</a>
                    </th>
                    <th class="number">
                        <a href="javascript:;">总提现金额</a>
                    </th>
                    <th class="number">
                        <a href="javascript:;">总账户剩余金额</a>
                    </th>
                    <th class="number">
                        <a href="javascript:;" title="总兑奖金额 - 总提现金额">兑奖提现差值</a>
                    </th>
                    <th class="number">
                        <a href="javascript:;" title="总账户剩余金额 - 兑奖提现差值">差值</a>
                    </th>
                    
                </tr>
                <tr>
                    <td>
                        <?= $totalRedeem ?>
                    </td>
                    <td>
                        <?= $totalReceipt ?>
                    </td>
                    <td>
                        <?= $remaining ?>
                    </td>
                    <td>
                        <?= $difference ?>
                    </td>
                    <td>
                        <?= $other ?>
                    </td>
                    
                </tr>
            </table>
        </form>
</div>
</body>
</html>