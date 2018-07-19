(function () {

  function is_weixin() {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == "micromessenger") {
      return true;
    } else {
      return false;
    }
  }
  var u = navigator.userAgent;
  if (is_weixin() && u.indexOf('Android') > -1 || is_weixin() && u.indexOf('Linux') > -1 || u.indexOf('Windows Phone') > -1) {
    if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
      handleFontSize();
    } else {
      document.addEventListener("WeixinJSBridgeReady", handleFontSize, false);
    }

    function handleFontSize() {
      // 设置网页字体为默认大小
      WeixinJSBridge.invoke('setFontSizeCallback', {
        'fontSize': 0
      });
      // 重写设置网页字体大小的事件
      WeixinJSBridge.on('menu:setfont', function () {
        WeixinJSBridge.invoke('setFontSizeCallback', {
          'fontSize': 0
        });
      });
    }
  }

})();