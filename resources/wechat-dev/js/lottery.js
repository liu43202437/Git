$(function (window) {
  // 设置 list 区域高度
  var screenHeight = document.documentElement.clientHeight;
  $('.body-box').height(screenHeight - 215);
  $('.list.lottery_list .list-body').height(screenHeight - 215 - 52);
  $(window).resize(function () {
    screenHeight = document.documentElement.clientHeight;
    $('.body-box').height(screenHeight - 215);
    $('.list.lottery_list .list-body').height(screenHeight - 215 - 52);
  });

  //获取sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  var sid = GetQueryString("sid");

  var AMOUNT = 3000;


  // 导航内容
  var lottery_map = {
    '0': {
      price: '0',
      title: '热销',
      icon: 'iconfont icon-sell-hot-o'
    },
    '2': {
      price: '2',
      title: '二元票',
      icon: 'iconfont icon-two-o1'
    },
    '5': {
      price: '5',
      title: '五元票',
      icon: 'iconfont icon-five-o1'
    },
    '10': {
      price: '10',
      title: '十元票',
      icon: 'iconfont icon-ten-o1'
    },
    '20': {
      price: '20',
      title: '二十元票',
      icon: 'iconfont icon-icon-test'
    }
  };
  // 获取票种后再操作
  apiByObj({
    path: '/mobile/Ticket/',
    service: 'getTicketType',
    data: {
      sid: sid
    }
  }).then((data) => {
    var res = JSON.parse(data);
    var haveLottert = res.data.type;
    var lottery_type = [];
    if (res.code !== 0 && !Array.isArray(haveLottert)) {
      swal({
        button: '注册',
        text: res.msg
      }).then(e => {
        if (e) {
          Router.navigate('/resources/wechat/shopzc.html');
        }
      });
      return;
    }

    AMOUNT = Number(res.data.total);
    haveLottert.unshift('0');
    for (let item of haveLottert) {
      if (lottery_map[item]) {
        lottery_type.push(lottery_map[item]);
      }
    };

    var $list = $('.lottery_list>.list-body');
    var $item = $('.lottery_item:first');
    $('.lottery_money').data('money', 0);
    var $money = $('.lottery_money').data('money');

    var $nav = $('.nav>.nav-box');

    function creatNavHtml(list, listElem) {
      for (var i = 0; i < list.length; i++) {
        var item = list[i];
        var html = `<div class="nav-item"><i class="${item.icon} f-40" aria-hidden="true"></i></div>`;
        listElem.append(html);
        $('.nav-item:last').data('nav_data', item);
      }
    }
    creatNavHtml(lottery_type, $nav);
    $('.nav-item:first').addClass('nav-active');
    var ticketPrice = $('.nav-item.nav-active').data('nav_data').price;


    var startTime = parseInt((new Date('2018-02-08')).valueOf() / 1000);
    var endTime = parseInt((new Date('2018-02-21')).valueOf() / 1000);
    var nowTime = parseInt((new Date()).valueOf() / 1000);
    var noticeHtml = `
    <div>
      <div class="pt-10 text-left">亲爱的各位零售店主：</div>
      <div class="pt-10 text-left">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 春节即将来临，物流配送不好安排，2月8日12时-2月21日24时，不做物流配送，在此期间所下订单统一在2月22号配送。 </p>
      </div>
      <div class="pt-10 text-left">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 预祝大家新春快乐，阖家欢乐！</p>
      </div>
    </div>
  `;
    if(nowTime > startTime && nowTime < endTime){
      swal({
        button: '确定',
        content: $(noticeHtml).get(0)
      });
    }

    // 切换 公益票种类
    $(".nav-box .nav-item").click(function (e) {
      var $target = $(e.target);
      if ($target.is('.iconfont')) {
        $target = $target.parent('.nav-item');
      }
      var navData = $target.data('nav_data');
      if (ticketPrice === navData.price) {
        return;
      };
      ticketPrice = navData.price;
      $('.nav-item.nav-active').removeClass('nav-active');
      $target.addClass('nav-active');
      $('.list>.list-title').text(navData.title);
      var reqData = {
        "price": ticketPrice
      };
      GetLotterrryList(reqData);
    });

    var buyLottery = {}; // 购买的公益票集合
    // 初始化 list 数据
    var reqData = {
      price: '0'
    };
    GetLotterrryList(reqData);

    function GetLotterrryList(reqData) {
      var _data = {
        "price": reqData.price,
        "sid": sid
      };
      // $('.load-box').removeClass('loaded');
      api('get_ticket', _data)
        .then(function (data) {
          if (ticketPrice !== reqData.price) {
            return;
          }
          data = JSON.parse(data);
          $('.load-box').addClass('loaded');

          if (data.status.succeed === 1) {
            var datas = data.data;
            $list.html('');
            if (datas.length) {
              for (var i = 0; i < datas.length; i++) {
                if (!buyLottery[datas[i].id]) {
                  buyLottery[datas[i].id] = {
                    num: 0,
                    name: datas[i].title
                  };
                }
                creatLotteryList(datas[i], $list);
              }
            } else {
              if (reqData.price === '0') {
                $list.append('<div class="no-lottery"><p>暂无热销票种</p></div>');
              } else {
                $list.append('<div class="no-lottery"><p>您所注册的区域没有当前所选票种</p></div>');
              }
              $('.load-box').addClass('loaded');
            }
          } else {
            swal({
              button: '关闭',
              text: "获取公益票列表失败，" + data.status.error_desc,
              type: 'error'
            });
            $('.load-box').addClass('loaded');
          }
        }).catch(function (err) {
          $('.load-box').addClass('loaded');
          swal({
            button: '关闭',
            text: `获取数据失败，请检查网络！`
          });
        });
    }

    function creatLotteryList(data, listElem) {
      var item = data;
      listElem.append(`
          <div class="item-box lottery_item d-flex justify-content-between align-items-center">
            <div class="item-info lottery_info">
              <div class="item-title mb-3 lottery_title">${item.title}</div>
              <div class="item-award mb-3 lottery_award">${item.description}</div>
              <div class="item-price mb-3 lottery_price">￥${item.price}</div>
            </div>
            <div class="item-number with-inventory text-center">
              <div class="number-container">
                <span class="f-20 mr-3 minus"><i class="fa fa-minus-circle minus_btn" aria-hidden="true"></i></span>
                <span class="f-20 mr-3 loterry_number">${buyLottery[item.id].num}</span>
                <span class="text-red plus f-20"><i class="fa fa-plus-circle plus_btn" aria-hidden="true"></i></span>
              </div>
              <div class="inventory opacity mb-3">
                <div class="item-award" style="width:60px;height:17px">${item.inventory > 99 ? '': ('库存' + item.inventory)}</div>
              </div>
            </div>
            <div class="sold-out hidden"></div>
          </div>
          `);
      var $nextItem = $('.lottery_item:last');
      $nextItem.data('item_data', item);
      if (item.inventory <= 0) {
        $nextItem.find('.sold-out').removeClass('hidden');
        $nextItem.find('.item-number').addClass('hidden');
      } else {
        if (buyLottery[data.id].num > 0) {
          $nextItem.find('.item-number .minus').removeClass('hidden');
          $nextItem.find('.item-number .loterry_number').removeClass('hidden');
          $nextItem.find('.item-number .inventory').removeClass('opacity');
        } else {
          $nextItem.find('.item-number .minus').addClass('hidden');
          $nextItem.find('.item-number .loterry_number').addClass('hidden');
          $nextItem.find('.item-number .inventory').addClass('opacity');
        }
      }
    }

    // 选择订单
    $('.lottery_list').click(function (e) {
      var $target = $(e.target);
      if ($target.is('.plus_btn') || $target.is('.minus_btn')) {
        var itemData = $target.parents(".lottery_item").data('item_data');
        var $minus = $target.parents(".item-number").find(".minus");
        var $number = $target.parents(".item-number").find(".loterry_number");
        var $inventory = $target.parents(".item-number").find(".inventory");
        
        if ($target.is('.plus_btn')) {
          plusBtnClick();
        }

        if ($target.is('.minus_btn')) {
          minusBtnClick();
        }

        if (buyLottery[itemData.id].num > 0) {
          $number.removeClass('hidden');
          $minus.removeClass('hidden');
          $inventory.removeClass('opacity');
        } else {
          $number.addClass('hidden');
          $minus.addClass('hidden');
          $inventory.addClass('opacity');
        }

        if ($money !== AMOUNT) {
          $('.go_buy').prop("disabled", true);
        } else {
          $('.go_buy').prop("disabled", false);
        }
        $('.lottery_money').text('￥' + $money)
      }

      function plusBtnClick() {
        var item = buyLottery[itemData.id];
        if ((item.num + 1) > itemData.inventory) {
          // Inventory shortage notice
          swal({
            text: `${item.name}当前库存不足了`,
            button: '关闭'
          }).then(() => {console.log('swal');}).catch(swal.noop);
        } else if ((Number($money) + Number(itemData.price)) > AMOUNT) {
          swal({
            button: '关闭',
            text: "订单超过"+AMOUNT+"元, 请选择其他商品,使订单刚好"+AMOUNT+"元"
          }).catch(swal.noop);;
        } else {
          item.num++;
          $money+=Number(itemData.price);
          $number.text(item.num);
        }
      }

      function minusBtnClick() {
        buyLottery[itemData.id].num = buyLottery[itemData.id].num - 1;
        $number.text(buyLottery[itemData.id].num);
        $money = $money - Number(itemData.price);
      }
    });

  // 订单提交
  $('.go_buy').click(function (e) {
    var buyArr = [];
    // var hasAlert = false;
    $.each(buyLottery, function (i, v) {
      if (v.num !== 0) {
        buyArr.push({
          id: i,
          num: v.num,
          name: v.name
        });
        // if (!hasAlert && v.name === '蓝玫瑰') {
        //   hasAlert = true;
        // }
      }
    });
    var swalHtml = '<div><div>您已选择以下商品, 请确认下单</div><hr class="my">';
    for (let i = 0; i < buyArr.length; i++) {
      swalHtml += `<div class="row f-14 text-gray-45 text-left">
        <div class="col-xs-6 col-xs-offset-2">
          ${buyArr[i].name}
        </div>
        <div class="col-xs-4">
          x${buyArr[i].num}
        </div>
      </div>`;
    }
    // if (hasAlert) {
    //   swalHtml += `<hr class="my"><div class="text-danger f-14 row">
    //       <div class="col-xs-offset-1 col-xs-10 text-left">* 蓝玫瑰即将售馨, 若下单后无库存, 工作人员将与您联系.</div>
    //     </div>`;
    // }
    swalHtml += '</div>';
    
    swal({
      content: $(swalHtml).get(0),
      type: 'info',
      buttons: ['取消', "确定"],
    })
    .then(function(e){
      if(e) {
        submitForm(buyArr);
      }})
      .catch(swal.noop);
    })

    function submitForm(buyArr) {
      buyArr = JSON.stringify(buyArr);

      api('apply_welfare_ticket', {
          "data": buyArr,
          "sid": sid
        })
        .then(function (data) {
          data = JSON.parse(data);
          if (data.status.succeed === 1) {
            window.location.reload();
            Router.navigate('/resources/wechat/lottery-order.html', {
              sid: sid
            });
          }
          if (data.status.succeed !== 1) {
            swal({
              button: '关闭',
              text: `订单信息提交失败,${data.status.error_desc}`,
              type: 'error'
            });
          }
        })
        .catch(function (err) {
          swal({
            button: '关闭',
            text: `订单信息提交失败，请检查网络`
          });
        });
    }
  });
  window.sid = sid;
  window.active_menu = 'apply';
}(window));