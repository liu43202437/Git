var pageIndex = 1, entryNum = 6, param, loadMoreTrigger;

if (typeof Object.assign != 'function') {
  Object.defineProperty(Object, "assign", {
    value: function assign(target, varArgs) { // .length of function is 2
      'use strict';
      if (target == null) { // TypeError if undefined or null
        throw new TypeError('Cannot convert undefined or null to object');
      }

      var to = Object(target);

      for (var index = 1; index < arguments.length; index++) {
        var nextSource = arguments[index];

        if (nextSource != null) { // Skip over if undefined or null
          for (var nextKey in nextSource) {
            // Avoid bugs when hasOwnProperty is shadowed
            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
              to[nextKey] = nextSource[nextKey];
            }
          }
        }
      }
      return to;
    },
    writable: true,
    configurable: true
  });
}

function loadMoreInit(req) {
  loadMoreTrigger = null;
  pageIndex = 1;
  entryNum = req.entryNum || 6;
  param = Object.assign({}, req);
  param.json = '{"pagination":{"count": '+entryNum +' ,"page": ' + pageIndex + '}}',
  $('.load-more-container').html('');
  return param;
}

function checkLoadMore(_, len, fun, empty) {
  if (Number(len)) {
    if (pageIndex * entryNum < len) {
      $('.load-more-container').html('上拉加载更多');

      loadMoreTrigger = function() {
        $('.load-more-container').html('正在加载...');
        pageIndex++;
        param.json = '{"pagination":{"count": '+entryNum +' ,"page": ' + pageIndex + '}}',
        param.loadmore = true;
        fun(param);
      };
    } else {
      $('.load-more-container').html('全部显示完毕');
      loadMoreTrigger = null;
    }
  } else if (empty) {
    $('.load-more-container').html('暂无记录');
  }
}

function getScrollTop() {
  var scrollTop = 0;
  if (document.documentElement && document.documentElement.scrollTop) {
    scrollTop = document.documentElement.scrollTop;
  }
  else if (document.body) {
    scrollTop = document.body.scrollTop;
  }
  return scrollTop;
}

function getClientHeight() {
  var clientHeight = 0;
  if (document.body.clientHeight && document.documentElement.clientHeight) {
    clientHeight = Math.min(document.body.clientHeight, document.documentElement.clientHeight);
  }
  else {
    clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight);
  }
  return clientHeight;
}

function getScrollHeight() {
  return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
}

window.onscroll = function () { 
  if (getScrollTop() + getClientHeight() == getScrollHeight()) {
    if (loadMoreTrigger) {
      loadMoreTrigger();
    }
  }
}