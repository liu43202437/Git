(function(window, undefined) {
  var Router = function() {}

  var __DEV__ = 1;

  Router.prototype = {
    navigate: function(baseUrl, params, replace) {
      var url = baseUrl;

      if (__DEV__) {
        url = url.replace('/resources/wechat', '/resources/wechat-dev');
      }

      var routerParams = `?${new Date().getTime()}=`;
      if (params) {
        var routeparamsArr = [];
        for ( let key in params) {
          routeparamsArr.push(`${key}=${params[key]}`);
        }
        routerParams += '&';
        routerParams += routeparamsArr.join('&');
        // window.location.search = routerParams;
      }
      window.location.pathname = url;

      if (!!replace) {
        window.location.replace(url + routerParams);
      } else {
        window.location.href = url + routerParams;
      }
      
    },

    navigateWithSid: function(baseUrl, params, replace) {
      var GetQueryString = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
      };

      var sid = GetQueryString("sid");
      params.sid = sid;
      this.navigate(baseUrl, params, !!replace);
    }
  }

  window.Router = new Router();
})(window);