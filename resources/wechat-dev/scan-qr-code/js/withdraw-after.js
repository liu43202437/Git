$(function() {
   //get sid
   function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return r[2];
    return null;
  }
  var sid = GetQueryString('sid');
  var code = GetQueryString("code");
  var msg = GetQueryString('msg');
   msg =  decodeURI(msg);
   if (msg) {
     $('.back-info').text(msg);
   }
  if (code == 0){
    $('.back-success').removeClass('hidden');
    $('.back-fail').addClass('hidden');
  } else {
    $('.back-success').addClass('hidden');
    $('.back-fail').removeClass('hidden');
  }
  $('.js-back').click(function(){
    Router.navigate('/resources/wechat/scan-qr-code/redeem-record.html', {sid: sid,type:'extract'});
  })

}());