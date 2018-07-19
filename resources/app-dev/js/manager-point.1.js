$(function (window) {

  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }

  var sid = GetQueryString("sid");

  var $listEle = $('.point-list .list-body');
  $('.all_point').text(0);
  $('.today_point').text(0);

  api('getCreditsByManager', {
    "sid": sid
  }).then(function (data) {
    data = JSON.parse(data);
    if (data.status.succeed === 1) {
      if (data.data) {
        $('.all_point').text(`${data.data.total}`);
        $('.today_point').text(`${data.data.dayTotal ? '+ ' + data.data.dayTotal : 0}`);
        $('.today_pay').text(`${data.data.expend ? '- ' + data.data.expend : 0}`);
      }
    }
  }).catch(function (err) {
    console.log(err);
    swal({
      button: '关闭',
      text: `公益分详情获取失败`
    });
  })

  // nav 导航切换
  var req_type = $('.nav-item.nav-active').data('state');
  var req = {
    "type": req_type,
    "sid": sid,
    "entryNum": 20,
    "loadmore": false
  };
  req = loadMoreInit(req);
  getListByType(req);

  $('.nav-box .nav-item').click(function (e) {
    var target = $(e.target);
    if (req_type === target.data('state')) {
      return;
    }
    $listEle.html('');
    $('.nav-item.nav-active').removeClass('nav-active');
    target.addClass('nav-active');
    req_type = target.data('state');
    var req = {
      "type": req_type,
      "sid": sid,
      "entryNum": 20,
      "loadmore": false
    }
    req = loadMoreInit(req);
    getListByType(req);
  });

  function getListByType(req) {
    api('get_credits_detail', req).then(function (data) {
      data = JSON.parse(data);

      if (!req.loadmore) {
        $listEle.html('');
      }

      if (data.status.succeed === 1) {
        if (data.data && data.data.list) {
          checkLoadMore(data.data.list.length, data.data.length, getListByType, true);
          creatPointList(data.data.list, $listEle, data.data.type);
        } else if (data.data && !data.data.length) {
          checkLoadMore([], 0, null, true);
        }
      }
    }).catch(function (err) {
      swal({
        button: '关闭',
        text: `公益分详情列表获取失败`
      });
    });
  }

  function creatPointList(data, listEle, type) {
    var current_type = $('.nav-item.nav-active').data('state');
    if (current_type !== type) {
      return;
    }
    for (var i = 0; i < data.length; i++) {
      var item = data[i];
      switch (item.type) {
        case 1:
          var html = `
        <div class="list-item-title f-12"><i class="iconfont icon-indent1 f-12 text-yellow"></i> 订单公益分</div>
        <div class="list-item d-flex justify-content-between align-items-center">
        <div>
          <div class="text-gray-65 d-flex align-items-center"><span>订单号：</span> <span>${item.trade_no}</span></div>
          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span> </div>
        </div>
        <div class="text-gray-45"><span class="f-20 text-red">+${item.credits}</span></div>
      </div>
        `;
          listEle.append(html);
          break;
        case 2:
          var html = `
        <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-blue"></i> 客户经理公益分</div>
        <div class="list-item d-flex justify-content-between align-items-center">
        <div>
          <div class="text-gray-65 d-flex align-items-center"><span>店主姓名：</span> <span>${item.name}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>店铺地址：</span><span>${item.address}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span> </div>
        </div>
        <div class="text-gray-45"><span class="f-20 text-red">+${item.credits}</span></div>
      </div>
        `;
          listEle.append(html);
          break;
        case 3:
          var html = `
        <div class="list-item d-flex justify-content-between align-items-center pt-10">
          <div>
            <div class="text-gray-65 d-flex align-items-center"><span>订单号：</span> <span>${item.trade_no}</span></div>
            <div class="text-gray-45 d-flex align-items-center"><span>兑换：</span><span>${item.product_title} x${item.num}</span></div>
            <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span></div>
          </div>
          <div class="text-gray-45"><span class="f-20 text-gray-65">-${item.credits}</span></div>
        </div>
        `;
          listEle.append(html);
          break;
        case 4:
          var html = `
        <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-green"></i> 市场经理公益分</div>
        <div class="list-item d-flex justify-content-between align-items-center">
        <div>
          <div class="text-gray-65 d-flex align-items-center"><span>客户经理姓名：</span> <span>${item.name}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span> </div>
        </div>
        <div class="text-gray-45"><span class="f-20 text-red">+${item.credits}</span></div>
      </div>
        `;
          listEle.append(html);
          break;
        case 5:
          var html = `
        <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-green"></i> 区域经理公益分</div>
        <div class="list-item d-flex justify-content-between align-items-center">
        <div>
          <div class="text-gray-65 d-flex align-items-center"><span>市场经理姓名：</span> <span>${item.name}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span> </div>
        </div>
        <div class="text-gray-45"><span class="f-20 text-red">+${item.credits}</span></div>
      </div>
        `;
          listEle.append(html);
          break;
      case 6:
      var html = `
      <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-green"></i> 客户经理公益分</div>
        <div class="list-item d-flex justify-content-between align-items-center">
        <div>
          <div class="text-gray-65 d-flex align-items-center"><span>店主姓名：</span> <span>${item.name}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>店铺地址：</span><span>${item.address}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span> </div>
        </div>
        <div class="text-gray-45"><span class="f-20 text-red">+${item.credits}</span></div>
      </div>
      `;
      listEle.append(html);
      break;
      case 7:
      var html = `
      <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-green"></i> 客户经理公益分</div>
        <div class="list-item d-flex justify-content-between align-items-center">
        <div>
          <div class="text-gray-65 d-flex align-items-center"><span>姓名：</span> <span>${item.name}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>店铺地址：</span><span>${item.address}</span> </div>
          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>${item.update_date}</span> </div>
        </div>
        <div class="text-gray-45"><span class="f-20 text-red">+${item.credits}</span></div>
      </div>
      `;
      listEle.append(html);
      break;
      }
    }
  }

  window.active_menu = 'point';
  window.sid = sid;
}(window))