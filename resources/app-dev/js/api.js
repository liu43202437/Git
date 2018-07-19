$(function (window) {
  var __DEV__ = 1;
  var host = (__DEV__ ) ? 'http://yan.eeseetech.cn': '';

  var GetQueryString = function(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  // var host = 'http://yan.eeseetech.cn';
  var path = '/mobile/wechat/';
  var api = function (service, data) {
    var url = host + path + service;

    return new Promise(function(resolve, reject){
      $.ajax({
        url: url,
        type: "POST",
        data: data || '',
        success: function(data){
          resolve(data);
        },
        error: function(data) {
          reject(data)
        }
      });
    });
  }

  var apiByObj = function(obj, type) {
    var url = host + obj.path + obj.service;
    var apiType = type || "POST";
    return new Promise(function(resolve, reject){
      $.ajax({
        url: url,
        type: apiType,
        data: (obj && obj.data) ? obj.data : '',
        success: function(data){
          resolve(data);
        },
        error: function(data) {
          reject(data)
        }
      });
    });
  }

  var apiType = function (options, service, data) {
    var url = host + path + service;
    return new Promise(function(resolve, reject) {
      var ajaxOptions = {
        url: url,
        type: options.type,
        data: data || '',
        success: function(data){
          resolve(data);
        },
        error: function(data) {
          reject(data)
        }
      };
      if (options.timeout) {
        ajaxOptions['timeout'] = options.timeout;
      }
      $.ajax(ajaxOptions);
    });
  }

  window.api = api;
  window.apiType = apiType;
  window.apiByObj = apiByObj;
  window.urlParams = GetQueryString;
  window.HOST = host;
}(window));