<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';


$body = (isset($_REQUEST['body'])) ? urldecode($_REQUEST['body']) : '北京中维票卷邮费';
$attach = '北京中维';
$out_trade_no = (isset($_REQUEST['ordernum'])) ? $_REQUEST['ordernum'] : WxPayConfig::MCHID.date("YmdHis");
$price = (isset($_REQUEST['price'])) ? $_REQUEST['price'] : 1;				// 分
$goodsTag = (isset($_REQUEST['tag'])) ? $_REQUEST['tag'] : 'product';		// product, activity
$notify_url = (isset($_REQUEST['nurl'])) ? urldecode($_REQUEST['nurl']) : '';
$tradeType = 'JSAPI';
$showPrice = sprintf("%.2f", $price/100);

$surl = urldecode($_REQUEST['surl']);
$furl = urldecode($_REQUEST['furl']);

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
//打印输出数组信息

function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();


//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);
$input->SetAttach($attach);
$input->SetOut_trade_no($out_trade_no);
$input->SetTotal_fee($price);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($goodsTag);
$input->SetNotify_url($notify_url);
$input->SetTrade_type($tradeType);
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();
//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信支付</title>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);

                    if(res.err_msg == "get_brand_wcpay_request:ok"){
                        window.location.href = '<?=$surl?>';
                    } else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        alert('用户取消支付！');
                        window.location.href = '<?=$furl?>';
                    } else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        alert('支付失败！（' + res.err_code + '：' + res.err_desc+'）');
                        window.location.href = '<?=$furl?>';
                    } else{
                        alert(JSON.stringify(res));
                        window.location.href = '<?=$furl?>';
                    }
                }
            );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
    <script type="text/javascript">
        /*
        //获取共享地址
        function editAddress()
        {
            WeixinJSBridge.invoke(
                'editAddress',

                function(res){
                    var value1 = res.proviceFirstStageName;
                    var value2 = res.addressCitySecondStageName;
                    var value3 = res.addressCountiesThirdStageName;
                    var value4 = res.addressDetailInfo;
                    var tel = res.telNumber;

                    alert(value1 + value2 + value3 + value4 + ":" + tel);
                }
            );
        }*/

        window.onload = function(){
            /*if (typeof WeixinJSBridge == "undefined"){
             if( document.addEventListener ){
             document.addEventListener('WeixinJSBridgeReady', editAddress, false);
             }else if (document.attachEvent){
             document.attachEvent('WeixinJSBridgeReady', editAddress);
             document.attachEvent('onWeixinJSBridgeReady', editAddress);
             }
             }else{
             editAddress();
             }*/

            callpay();
        };

    </script>
</head>

<body>
<div align="center">
    <font color="#333333"><b>支付金额<span style="color:#f00;font-size:40px"><?=$showPrice?></span>元</b></font><br/><br/>
</div>
<div align="center">
    <button style="width:210px; height:50px; border-radius: 15px;background-color:#3492fe; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
</div>
</body>
</html>