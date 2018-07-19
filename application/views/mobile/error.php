<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript">
        var base_url = "<?=base_url()?>";
    </script>

    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta content="telephone=no" name="format-detection"/>
    <link rel="canonical" href="<?=base_url()?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?=base_url()?>www/images/logo.png">
    <title>等待付款</title>
    <link rel="stylesheet" href="<?=base_url()?>www/css/normalize.min.css">
    <link rel="stylesheet" href="<?=base_url()?>www/css/pay_1452839045580.css">
    <style>
        .radio-group .cur-arrow{border-color:  #1e83d3 transparent transparent  #1e83d3;}
        .description a {color: #1e83d3;}
    </style>
    <style>
        .info-family .num,.info-family .plus,.price,.pay-back{color: #1e83d3;}
        #submit{background-color:#1e83d3;}
    </style>
</head>
<body>
<div id="db-content">
    <div class="item-info">
        <div class="info-family">
            <p class="title"><?=$title?></p>
        </div>
    </div>
    <div class="btn-group">
        <button type="button" id="submit"><?=$title?></button>
    </div>
</div>
<div class="pay-result">
    <div class="dialog">
        <i class="success"></i>
        <i class="error"></i>
    </div>
</div>
</body>
<script src="<?=base_url()?>www/js/zepto.min.js"></script>
<script src="<?=base_url()?>www/js/fastclick.js"></script>
<script src="<?=base_url()?>www/js/modal.min.js"></script>
<script>
    $(function(){
        //fastclick
        Origami.fastclick(document.body);


        $('#submit').prop('disabled',true).html("<?=$title?>");

        //UI自适应
        function responsive(){
            $('.type-group .right').css('width',$('body').width()-30-54);
            $('.info-family p').css('width',$('body').width()-30-75);
        }
        responsive();



    })
</script>
</html>