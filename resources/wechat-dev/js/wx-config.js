 var loadBox = $('.js-load-box');
 var loadText = $('.load-text');
 if (loadBox) {
   loadBox.addClass('loading');
   loadText.text('正在加载...');
 }
 apiByObj({
   service: 'getTicket',
   path: '/mobile/Js_interface/'
 }).then(data => {
   if (data) {
     data = JSON.parse(data);
     wxConfig(data);
     if (loadBox) {
       loadBox.removeClass('loading');
       loadText.text('正在查询...');
     }
   }
 }).catch(function (err) {
   loadBox.removeClass('loading');
   $('.load-box').addClass('loaded');
   swal({
     button: '关闭',
     text: '加载失败请再次加载'
   });
 });

 function wxConfig(data) {
   var appid = data.appid;
   var ticket = data.ticket;
   var timestamp = parseInt((new Date()).valueOf() / 1000);
   var nonceStr = createNonceStr(16);
   var httpurl = window.location.href.split("#")[0];
   var str = `jsapi_ticket=${ticket}&noncestr=${nonceStr}&timestamp=${timestamp}&url=${httpurl}`;
   var signature = $.sha1(str);

   // alert(`appid:${appid}`);
   // alert(`ticket:${ticket}`);
   // alert(`timestamp:${timestamp}`);
   // alert(`nonceStr:${nonceStr}`);
   // alert(`httpurl:${httpurl}`);
   // alert(`str:${str}`);
   // alert(`signature:${signature}`);
   function createNonceStr($length) {
     var $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
     var $str = [];
     for (var $i = 0; $i < $length; $i++) {
       var str = $chars.substr(parseInt(Math.random() * $chars.length), 1);
       $str.push(str);
     }
     return $str.join('');
   }

   wx.config({
     debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
     appId: appid, // 必填，公众号的唯一标识
     timestamp: timestamp, // 必填，生成签名的时间戳
     nonceStr: nonceStr, // 必填，生成签名的随机串
     signature: signature, // 必填，签名，见附录1
     jsApiList: ['scanQRCode','chooseImage','previewImage','uploadImage','downloadImage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
   });
 }

 wx.error(function (res) {
   if (res) {
     var re = JSON.stringify(res);
     // alert(`${re} 点击确定重新加载`);
     if (res.errMsg === 'config:invalid signature') {
       apiByObj({
         service: 'getNewTicket',
         path: '/mobile/Js_interface/'
       }).then(data => {
         if (data) {
           data = JSON.parse(data);
           wxConfig(data);
         }
       }).catch(function (err) {
         loadBox.removeClass('loading');
         $('.load-box').addClass('loaded');
         swal({
           button: '关闭',
           text: '加载失败请再次加载'
         });
       });
     }
     if (res.errMsg === 'config:invalid url domain') {
       loadBox.removeClass('loading');
       $('.load-box').addClass('loaded');
       swal({
         button: '关闭',
         text: '加载失败请再次加载'
       });
     }
   }
 });

 wx.ready(function () {
   if (loadBox) {
     loadBox.removeClass('loading');
     $('.load-box').addClass('loaded');
   }
 })