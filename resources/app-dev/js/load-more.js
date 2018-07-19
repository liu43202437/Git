var pageIndex = 1, entryNum = 10, param, loadMoreTrigger;

function loadMoreInit(req) {
  loadMoreTrigger = null;
  pageIndex = 1;
  entryNum = req.entryNum || 10;
  param = Object.assign({}, req);
  param.pageIndex = pageIndex;
  param.entryNum = entryNum;
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
        param.pageIndex = pageIndex;
        param.entryNum = entryNum;
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