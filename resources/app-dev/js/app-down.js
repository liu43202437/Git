var ua = window.navigator.userAgent.toLowerCase();
var UA = navigator.userAgent;

getAppUrl();

//判断是否是微信浏览器
function isWeiXin(){
  if(ua.match(/MicroMessenger/i) == 'micromessenger'){
      return true;
  }else{
      return false;
  }
}

//打开弹出层
$('.js-down').click(function(){
  if (isWeiXin()) {
    $('.js-alert').removeClass('hidden');
  }   
})

//关闭弹出层
$('.js-close').click(function(){
  $('.js-alert').addClass('hidden');
})

//获取app下载地址
function getAppUrl () {
  api('checkVersion').then((data) => {
    var resData = JSON.parse(data);
    if (resData.code === 200) {
      var downloadUrl = resData.data.downloadUrl;
      $('.js-href').attr('href',downloadUrl);
    }else {
      swal({
        button:'关闭',
        text: `${resData.msg}`
      });
    }
  }).catch(err => swal({button:'关闭', text: err}));
}