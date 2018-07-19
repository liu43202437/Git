  $(function (window) {
    //获取sid
    function GetQueryString(name) {
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
      var r = window.location.search.substr(1).match(reg);
      if (r != null) return unescape(r[2]);
      return null;
    }
    var sid = GetQueryString("sid");
    var loadBox = $('.js-load-box');
    var loadText = $('.load-text');
    if (loadBox) {
      loadBox.addClass('loading');
      $('.load-box').removeClass('loaded');
      loadText.text('正在加载...');
    }
    apiByObj({
      service: 'getTicket',
      path: '/mobile/Js_interface/'
    }).then(data => {
      if (data) {
        data = JSON.parse(data);
        wxConfig(data);
      }
    }).catch(function (err) {
      loadBox.removeClass('loading');
      $('.load-box').addClass('loaded');
      swal({
        button: '关闭',
        text: '扫码功能加载失败请再次加载'
      });
    });

    function wxConfig(data) {
      var appid = data.appid;
      var ticket = data.ticket;
      var timestamp = parseInt((new Date()).valueOf() / 1000);
      var nonceStr = createNonceStr(16);
      var httpurl = window.location.href.split("#")[0];
      var str = `jsapi_ticket=${ticket}&noncestr=${nonceStr}&timestamp=${timestamp}&url=${httpurl}`;
      var signature = $.sha1(str);

      // alert(`appid:${appid}`);
      // alert(`ticket:${ticket}`);
      // alert(`timestamp:${timestamp}`);
      // alert(`nonceStr:${nonceStr}`);
      // alert(`httpurl:${httpurl}`);
      // alert(`str:${str}`);
      // alert(`signature:${signature}`);
      function createNonceStr($length) {
        var $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        var $str = [];
        for (var $i = 0; $i < $length; $i++) {
          var str = $chars.substr(parseInt(Math.random() * $chars.length), 1);
          $str.push(str);
        }
        return $str.join('');
      }

      wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: appid, // 必填，公众号的唯一标识
        timestamp: timestamp, // 必填，生成签名的时间戳
        nonceStr: nonceStr, // 必填，生成签名的随机串
        signature: signature, // 必填，签名，见附录1
        jsApiList: ['scanQRCode'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
      });
    }

    wx.error(function (res) {
      if (res) {
        var re = JSON.stringify(res);
        // alert(`${re} 点击确定重新加载`);
        if (res.errMsg === 'config:invalid signature') {
          apiByObj({
            service: 'getNewTicket',
            path: '/mobile/Js_interface/'
          }).then(data => {
            if (data) {
              data = JSON.parse(data);
              wxConfig(data);
            }
          }).catch(function (err) {
            loadBox.removeClass('loading');
            $('.load-box').addClass('loaded');
            swal({
              button: '关闭',
              text: '扫码功能加载失败请再次加载'
            });
          });
        }
        if (res.errMsg === 'config:invalid url domain') {
          loadBox.removeClass('loading');
          $('.load-box').addClass('loaded');
          swal({
            button: '关闭',
            text: '扫码功能加载失败请再次加载'
          });
        }
      }
    });


    wx.ready(function () {
      if (loadBox) {
        loadBox.removeClass('loading');
        $('.load-box').addClass('loaded');
      }
      $('.redeem-prize').click(() => {


        wx.scanQRCode({
          desc: 'scanQRCode desc',
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode", "barCode", "PDF417"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var url = res.resultStr;
            var code = url.split(',')[1];
            var reqData = {
              code: code,
              sid: sid
            }
            $('.load-box').removeClass('loaded');
            $('.js-load-box').addClass('loading');
            $('.load-text').text('正在查询...');
            apiByObj({
              service: 'doRedeem',
              path: '/mobile/Redeem/',
              data: reqData
            }).then((data) => {
              // alert(data)
              var data = JSON.parse(data);
              // alert(data.msg);
              // alert(data.data);
              // alert(data.code)
              scanCodeResult(data);
            }).catch(function (err) {
              $('.load-box').addClass('loaded');
              $('.js-load-box').removeClass('loading');
              var err = JSON.stringify(err);
              swal({
                button: '关闭',
                text: '网络连接错误，请重试'
              });
            })
          },
          error: function (res) {
            if (res.errMsg.indexOf('function_not_exist') > 0) {
              swal({
                button: '关闭',
                text: "版本过低请升级"
              });
            }
          }
        });
      });


    });

    function scanCodeResult(data) {
      var countData = data.data ? JSON.parse(data.data) : null;
      var content = countData ? countData.content : null;
      var errMsg = data.msg;
      var winHtml = ``
      if (countData && countData.ret == '1301') {
        if (content && content.prize) {
          winHtml += `<div class="py-10 f-20">兑奖成功，奖金 ${content.prize}元</div>`;
        }
      } else {
        if (countData && countData.msg) {
          winHtml += `<div class="py-10 f-20">${countData.msg}</div>`;
        }
      }

      if (content && content.gameName) {
        winHtml += `<div class='pt-10 text-left'><span class="s-l">兑换票种：</span><span class="s-r">${content.gameName}</span></div>`;
      }
      if (content && content.transacId) {
        var time = content.transacId.substr(0, 14);
        time = moment(time)
        winHtml += `<div class='pt-10 text-left'><span class="s-l">扫码时间：</span><span class="s-r">${time}</span></div>`;
      }
      if (content && content.ticketNo) {
        winHtml += ` <div class='pt-10 text-left'><span class="s-l">兑奖单号：</span><span class="s-r">${content.ticketNo}</sapn></div>`;
      }

      var swalHtml = `<div>${winHtml}</div>`
      if (winHtml) {
        $('.load-box').addClass('loaded');
        $('.js-load-box').removeClass('loading');

        if (countData && countData.ret == '1301') {
          swal({
            content: $(swalHtml).get(0),
            type: 'info',
            buttons: ['关闭','确定']
          }).then(function(e){
            if (e) {
              Router.navigate('/resources/app/scan-qr-code/redeem-record.html', {
                sid: sid
              });
            }
          })
        } else {
          swal({
            content: $(swalHtml).get(0),
            type: 'info',
            button: '关闭'
          });
        }
      }

      if (errMsg) {
        var errHtml = `<div class="py-10 f-20">${errMsg}</div>`;
        $('.load-box').addClass('loaded');
        $('.js-load-box').removeClass('loading');
        swal({
          content: $(errHtml).get(0),
          type: 'error',
          button: '关闭'
        })
      }
    }

    function moment(str) {
      var str = String(str);
      var Y = str.substring(0, 4);
      var M = str.substring(4, 6);
      var D = str.substring(6, 8);
      var h = str.substring(8, 10);
      var m = str.substring(10, 12);
      var s = str.substring(12, 14);
      return `${Y}-${M}-${D} ${h}:${m}:${s}`;
    }


    $('.redeem-record').click(() => {
      Router.navigate('/resources/app/scan-qr-code/redeem-record.html', {
        sid: sid
      });
    });

  }(window));