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
    <div class="section">
        <h3>
            <span class="paied">
                <?php if($pay_status == '1'): ?>
                    您已支付&nbsp;
                    <span class="credits">
                        <?=($pay_money)?>,&nbsp;
                    </span>
             </span>
                <?php endif; ?>
                <?php if($pay_status == 0): ?>
                请在&nbsp;
                <span class="timedown">
                    00:00
                </span>
                &nbsp;内支付中维商品费用：
            </span>
            <span class="price">
                <span class="rmb">
                    ￥
                </span>
                <?=($pay_money)?>
            </span>
                <?php endif; ?>
        </h3>
        <div class="payType">
            <div class="type-group weixin" data-paytype="weixin">
                <i class="logo"></i>
                <div class="right">
                    <h2>微信支付</h2>
                    <p>使用微信客户端支付</p>
                    <i class="select"></i>
                </div>
            </div>
            <div class="type-group alipay" data-paytype="alipay">
                <i class="logo"></i>
                <div class="right">
                    <h2>支付宝支付</h2>
                    <p>使用支付宝账号支付</p>
                    <i class="select"></i>
                </div>
            </div>
        </div>
    </div>
    <p class="pay-back">切换至<span></span></p>
    <div class="btn-group">
        <button type="button" id="submit">马上支付</button>
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

        <?php if($canPay): ?>
            var remainTime = '<?=$remainTime?>';
            function countDown(remainTimeStemp) {
                var m = parseInt(remainTimeStemp / 1000 / 60);
                var s = parseInt(remainTimeStemp / 1000 % 60);
                m = (m < 10 && String(m).length < 2) ? '0'+m : m;
                s = (s < 10 && String(s).length < 2) ? '0'+s : s;
                $('.timedown').html(m + ':' + s);

                if(remainTimeStemp<=0){
                    clearInterval(timer);
                    $('#submit').prop('disabled',true).html('支付超时');
                }
            }
            var remainM = parseInt(remainTime.split(':')[0]);
            var remainS = parseInt(remainTime.split(':')[1]);
            var remainTimeStemp = remainM * 60 * 1000 + remainS * 1000;
            countDown(remainTimeStemp);
            if(remainTimeStemp>0){
                var timer = setInterval(function() {
                    remainTimeStemp -= 1000;
                    countDown(remainTimeStemp);
                }, 1000);
            }
        <?php else: ?>
        $('#submit').prop('disabled',true).html('已支付');
        <?php endif; ?>

        //UI自适应
        function responsive(){
            $('.type-group .right').css('width',$('body').width()-30-54);
            $('.info-family p').css('width',$('body').width()-30-75);
        }
        responsive();
        window.onresize=function(event) {
            responsive();
        };

        //支付类型选择
        var defaultType;

        function is_weixn(){
            var ua = navigator.userAgent.toLowerCase();
            if(ua.match(/MicroMessenger/i)=="micromessenger") {
                return true;
            } else {
                return true;
            }
        }

        if(is_weixn()){
            $('.alipay').hide();
            defaultType = 'weixn';
        }
        else{
            $('.weixin').hide();
            defaultType = 'alipay';
        }

        var curPayType=defaultType;
        $('.type-group').each(function(index,obj){
            if($(obj).data('paytype')==defaultType){
                $(obj).addClass('active');
                return false;
            }
        })
        $('.type-group').on('click',function(){
            $('.type-group').removeClass('active');
            $(this).addClass('active');
            curPayType=$(this).data('paytype');
        })

        //提交支付
        $('#submit').on('click',function(){

            //$(this).prop('disabled',true);
            $.ajax({
                type:'post',
                url:'<?=base_url()?>mobile/wechatpay/ajaxCanPay',
                data:{'ordernum':"<?=$ordernum?>",'area_id':"<?=$area_id?>"
                },
                dataType:'json',
                success:function(result){

                    if(result.data.success){
                        if(curPayType=='weixin') {

                            window.location.href = "<?=base_url()?>mobile/wechatpay/wxPay?ordernum=<?=$ordernum?>&area_id=<?=$area_id?>";
                        }
                        else if(curPayType=='alipay') {
                            window.location.href = "<?=base_url()?>mobile/payWithAlipay";
                        }
                    } else {
                        // 扣积分失败怎么办？
                        $.modal({
                            type:'alert',
                            title:result.data.message
                        })
                    }
                }
            });
        })
    })
</script>
</html>
