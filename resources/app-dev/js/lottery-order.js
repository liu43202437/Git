$(function (window) {
  //获取sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  var sid = GetQueryString("sid");

  var status = $('.nav-item.nav-active').data('state');
  var displayStatus = 'none';
  var noDisplayStatus = 'none';

  var req = {
    "sid": sid,
    "status": status,
    "loadmore": false
  };

  req = loadMoreInit(req);
  getOrderListBy(req);

  $('.nav-box .nav-item').click(function (e) {
    var target = $(e.target);
    if (status === target.data('state')) { return; }
    // clear last list
    $listEle.html('');
    $('.nav-item.nav-active').removeClass('nav-active');
    target.addClass('nav-active');
    status = target.data('state');
    displayStatus = (status==='wait') ? 'inline-block' : 'none';
    noDisplayStatus = (status === 'payed') ? 'none' : 'block';
    var req = {
      "sid": sid,
      "status": status,
      "loadmore": false
    };
    req = loadMoreInit(req);
    getOrderListBy(req);
  });

  var $listEle = $('.order-list');

  function getOrderListBy(req) {
    api('get_order_list', req)
      .then(function(data) {
        if (req.status !== $('.nav-item.nav-active').data('state')) {
          return;
        }

        if (!req.loadmore) {
          $listEle.html('');
        }
        data = JSON.parse(data);
        if (data && data.data) {
          var resData = data.data;
          checkLoadMore(resData.lists.length, resData.length, getOrderListBy);

          if (resData.lists.length) {
            createOrderListHtml(resData.lists, $listEle);
          } else {
            $listEle.append('<div class="empty-container"><p class="empty-info">订单列表为空.</p></div>');
          }
        } else {
          swal({button: '关闭', text:"获取订单列表失败，" + data.status.error_desc, type: 'error' });
        }
      }).catch(function(err){
        swal({button: '关闭', text:"获取订单列表失败, 请检查网络"});
      });
  }

   function createOrderListHtml(data, $listEle) {
    for ( var i=0; i<data.length; i++) {
      var item = data[i];
      displayStatus = item.delivery ? 'block': 'none';
      var disabled = (item.status == '1') ? false : true;
      var rebateHtml = (item.rebate == '0.00') ? '<div></div>':`<div>返佣金额：<span class="text-red">￥${item.rebate}</span></div>`;
      var noteHtml  = (item.note)?`<div class="mr-15">备注：<span>${item.note}</span></div>`:'';
      var stateHtml = (item.status == '2') ? '': 
                      item.delivery ? `<button data-id="${item.id}" class="btn btn-default btn-color btn-self js_confirm"><i class="iconfont icon-querenshouhuo f-12" aria-hidden="true"></i> 确认收货</button>`:
                      '<div class="text-p d-flex align-items-center"><i class="iconfont icon-delivery f-18" aria-hidden="true"></i> &nbsp;<span>等待配送</span></div>';
      var html = `
      <div class="order-item">
        <div class="order-price d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div class="item-img mr-15">
              <img src="images/order-img.png" height="60px" width="60px" alt="order img">
            </div>
            <div class="item-price mr-3">
              <div>订单金额：￥${item.money}</div>
              <div style="display: ${noDisplayStatus}">获得公益分：<i class="iconfont icon-integral2 f-14 text-yellow"></i> ${item.getwelfare}</div>
              <div>实际应付：<span class="text-red">￥${item.relmoney}</span></div>
              ${rebateHtml}
            
            </div>
          </div>
          <div class="item-btn pr-15">
            ${stateHtml}
          </div>
        </div>
        <div class="item-detail">
            <div class="detail-title">
              ${noteHtml}
              <div class="mr-15">订单号：${item.trade_no}</div> 
              <div>下单时间: ${item.create_date}</div>
              <div style="display: ${noDisplayStatus}">订单完成: ${item.update_date}</div>
            </div>
            <div class="detail-text">
  
            </div>
        </div>
      </div>
      `;
      $listEle.append(html);
      var $btn = $('.order-item:last .order-price .js_confirm');
      if(disabled){
        $btn.html('');
        $btn.attr('disabled', true);
      } else {
        $btn.removeAttr('disabled');
        $btn.html(`<i class="iconfont icon-querenshouhuo f-12" aria-hidden="true"></i> <span data-state="wait">确认收货</span>`);
      }
      var detailItem = data[i].detail;
      var $detail = $('.order-item:last .item-detail>.detail-text');
      for (var j=0; j<detailItem.length; j++) {
        var detail = detailItem[j];
        var detailHtml = `
        <div class="item"><span class="mr-3">${detail.title}</span> x${detail.ticket_num}</div>
        `;
        $detail.append(detailHtml);
      }
    }
  }

  $('.order-list').on('click', '.js_confirm', function(e){
    var $target = $(e.target);
    if($target.is('span')) {
      $target = $target.parent('.js_confirm');
    }
    var id = $target.data('id');
    var type = $target.find('span').data('state');
    var req = {
      "id": id,
      "sid": sid
    };
    confirmOrder(req)
  });

  var submitting = false;

  function confirmOrder(req) {
    if (submitting) { return; }
    submitting = true;
    api('get_order_update',req).then(function(data){
      data = JSON.parse(data);
      if (data.status.succeed === 1) {
        $('.nav-item.nav-active').removeClass('nav-active');
        $('.nav-item.nav-finish').addClass('nav-active');
        status = 'finish';
        $listEle.html('');
        displayStatus = 'none';
        noDisplayStatus = 'block';
        var req = {
          "sid": sid,
          "status": "finish"
        }
        req = loadMoreInit(req);
        getOrderListBy(req);

      }
      if (data.status.succeed !== 1) {
        swal({button: '关闭', text:`订单确认失败,${data.status.error_desc}`, type: 'error' });
      }
    }).catch(function(err){
      console.error('1', err);
      swal({button: '关闭', text:`订单确认失败，请检查网络！`});
    }).then(function() {
      submitting = false;
    });
  }

  window.sid = sid;
  window.active_menu = 'indent';
}(window));