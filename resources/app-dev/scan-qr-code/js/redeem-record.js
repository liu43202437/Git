$(function (window) {
  // get sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  var sid = GetQueryString("sid");
  var urlType =  GetQueryString("type");

  var $listEle = $('.record-list');
  var type = $('.nav-item.nav-active').data('type');
  if (urlType) {
    type = urlType;
    $('.nav-item.nav-active').removeClass('nav-active');
    $(`.${urlType}`).addClass('nav-active');
    if (urlType === 'redeem') {
      // show withdraw box
      $('.withdraw-box').addClass('show');
    } else {
      $('.withdraw-box').removeClass('show');
    }
  }
  var req = {
    sid: sid,
    type: type,
    loadmore: false,
  };
  req = loadMoreInit(req);
  getRecordListBy(req);

  // get banlance
  var banlance = 0;
  getBanlance();

  // bind route jump event
  $('.withdraw-box .withdraw-btn').click(function() {
    Router.navigate('/resources/app/scan-qr-code/withdraw.html', {sid: sid});
  });

  $('.nav-box .nav-item').click(function(e) {
    var target = $(e.target);
    if (type === target.data('type')) { return; }
    $listEle.html('');
    $('.nav-item.nav-active').removeClass('nav-active');
    target.addClass('nav-active');
    type = target.data('type');
    if (type === 'redeem') {
      // show withdraw box
      $('.withdraw-box').addClass('show');
    } else {
      $('.withdraw-box').removeClass('show');
    }
    req = loadMoreInit({
      sid: sid,
      type: type,
      loadmore: false,
    });
    getRecordListBy(req);
  });

  // get account's money remain
  function getBanlance() {
    apiByObj({
      service: 'moneyRemaining',
      path: '/mobile/Redeem/',
      data: { sid: sid }
    }).then(data => {
      data = JSON.parse(data);
      if (!Number(data.code)) {
        banlance = data.moneyRemaining ? data.moneyRemaining : 0;
        $('.withdraw-box .account-banlance.text-danger').html(banlance);
      } else {
        swal({button: '关闭', text:"获取账户余额失败," + data.msg, type: 'error' });
      }
    }).catch(error => {
      swal({button: '关闭', text:"获取账户余额失败, 请检查网络"});
    });
  }

  function getRecordListBy(req) {
    let service = req.type === 'redeem' ? 'redeemList' : 'withdrawList';
    apiByObj({
      service: service,
      path: '/mobile/Redeem/',
      data: req
    }).then(data => {
      data = JSON.parse(data);
      if (req.type !== $('.nav-item.nav-active').data('type')) {
        return;
      }
      if (!req.loadmore) {
        $listEle.html('');
      }
      if (data && data.data && data.data.list) {
        let resData = data.data;
        checkLoadMore(resData.list.length, resData.length, getRecordListBy);

        if (resData.list.length) {
          createRecordListHtml(resData.list);
        } else {
          $listEle.append('<div class="empty-container"><p class="empty-info">记录为空.</p></div>');
        }

      } else {
        swal({button: '关闭', text:"获取记录列表失败," + data.msg});
      }
    }).catch(error => {
      swal({button: '关闭', text:"获取记录列表失败, 请检查网络"});
    });
  }

  function createRecordListHtml(list) {
    for (var i = 0; i < list.length; i++) {
      var item = list[i];
      var html = (type === 'redeem') ? `
        <div class="record-list-item mb-md">
          <div class="record-list-title">账户返奖: <span class="text-danger">￥${item.prize}</span></div>
          <div>${item.time}</div>
          <div><span class="ml">${item.gameName}</span>${item.ticketNo}</div>
        </div>
      ` : `
        <div class="record-list-item border-bottom d-flex justify-content-between">
          <div class="f-16">${item.transfer_time}</div>
          <div class="record-list-title">￥${parseInt(item.amount)/100}</div>
        </div>
      `;
      $listEle.append(html);
    }
  }

}(window));
