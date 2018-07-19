if (window.Vue) {
  // Blank components just for tabbar.
  Vue.component('page-review', {
    template: '<v-ons-page></v-ons-page>'
  })

  Vue.component('page-point', {
    template: '<v-ons-page></v-ons-page>'
  })

  // Actual page component
  Vue.component('page-me', {
    template: '#page-me'
  })

  new Vue({
    el: '#app'
  })
}

$(function (window) {
  //获取sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  var sid = GetQueryString("sid");
  var toShop = function () {
    $('.shop-item').attr('href', `${HOST}/mobile/wechat/credits_shop?sid=${sid}`);
  }
  window.sid = sid;
  window.toShop = toShop;
  window.navigateTo = function (url) {
    Router.navigate(url, { sid: sid });
  }

  window.active_menu = 'me';
}(window));
