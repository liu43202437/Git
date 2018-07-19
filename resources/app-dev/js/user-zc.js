$(function () {
  var peoData = {
    "sid": "47eccd96f3a5199018538298e5e7b50e", //sid
    "user_name": "", //姓名
    "user_phone": "", //手机号
    "code": "", //验证码
    // "area_id": "", //省号码
    // "city": "", //城市
    // "address": "", //详细地址
    "shop_name": "", // 店铺姓名
    "shop_phone": "", // 店铺手机号
  };

  var regular = /\s+/g; //空格
  //获取sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }

  peoData.sid = GetQueryString("sid");

  // user_name 验证姓名
  var regName = /^[\u4e00-\u9fa5]{2,4}$/;
  var $name = $(".name-txt");
  var $nameIcon = $(".name");
  $name.focus(function () {
    $nameIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $name.blur(nameValidation);

  function nameValidation() {
    if (!regName.test($name.val())) {
      $nameIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      $nameIcon.children("span").addClass("glyphicon-ok green");
      peoData.user_name = $name.val();
      return true;
    }
  }



  // shop_name
  var $manager = $(".manager-name-txt");
  var $managerIcon = $(".manager-name");
  $manager.focus(function () {
    $managerIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $manager.blur(managerValidation);

  function managerValidation() {
    if (!regName.test($manager.val())) {
      $managerIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      $managerIcon.children("span").addClass("glyphicon-ok green");
      peoData.shop_name = $manager.val();
      return true;
    }
  }

  // shop_phone
  var regIdNos = /^1[34578]\d{9}$/;
  var $areaidNo = $(".area-id-num");
  var $areaidNoIcon = $(".area-id-number");

  $areaidNo.blur(areaidNoValidation);
  $areaidNo.focus(function () {
    $areaidNoIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });

  function areaidNoValidation() {
    if (!regIdNos.test($areaidNo.val())) {
      $areaidNoIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      $areaidNoIcon.children("span").addClass("glyphicon-ok green");
      peoData.shop_phone = $areaidNo.val();
      return true;
    }
  }

  // //省市县街道四级联动
  // var provinceArr = CITY_CODE;
  // var provinceSelect = new MobileSelect({
  //   trigger: '#province',
  //   title: '请选择地区',
  //   wheels: [{
  //     data: provinceArr
  //   }],
  //   position: [2],
  //   transitionEnd: function (indexArr, data) {
  //     // console.log(data);
  //   },
  //   callback: function (indexArr, data) {
  //     peoData.area_id = data[0] ? data[0].id : '';
  //     peoData.city = data[1] ? data[1].value : '';
  //     var county = data[2] ? data[2].value : '';
  //     var street = data[3] ? data[3].value : '';
  //     peoData.address = county + street;
  //   }
  // });

  // // 详细店铺地址
  // var detailaAddress = '';
  // var $shopAdsTxt = $(".shop-ads-txt");
  // var $shopAdsIcon = $(".shop-ads");
  // $shopAdsTxt.focus(adsFocus);
  // $shopAdsTxt.blur(shopAdsTxtValidation);

  // function adsFocus() {
  //   $shopAdsIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  // }

  // function shopAdsTxtValidation() {
  //   if ($shopAdsTxt.val() === "" || $shopAdsTxt.val() === null || regular.test($shopAdsTxt.val())) {
  //     // alert('许可证填写有误');
  //     $shopAdsIcon.children("span").addClass("glyphicon-remove red");
  //     return false;
  //   } else {
  //     // alert('许可证填写正确');
  //     $shopAdsIcon.children("span").addClass("glyphicon-ok green");
  //     detailaAddress = $shopAdsTxt.val();
  //     return true;
  //   }
  // }

  // 获取短信验证码
  var regPhoneNum = /^1[34578]\d{9}$/;
  var phoneCode = false;
  var $phone = $('.phone');
  var $phoneNum = $(".phone-num");
  var $msgsBtn = $(".msgs");

  $phoneNum.focus(function () {
    $phone.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  })

  $phoneNum.blur(phoneValidation);
  $phoneNum.on('input propertychange', function () {
    $msgsBtn.text("点击获取验证码");
    if (regPhoneNum.test($(this).val())) {
      $msgsBtn.removeClass("disable-btn").addClass("able-btn");
      phoneCode = true;
      peoData.user_phone = $phoneNum.val();
    } else {
      //手机号不可以使用
      $msgsBtn.removeClass("able-btn").addClass("disable-btn");
      peoData.user_phone = "";
      phoneCode = false;
    }
  });

  function phoneValidation() {
    if (!$phoneNum.val() || !regPhoneNum.test($phoneNum.val())) {
      $phone.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      $phone.children("span").addClass("glyphicon-ok green");
      peoData.user_phone = $phoneNum.val();
      return true;
    }
  }
  var timer = null;
  if (timer) {
    window.clearInterval(timer);
    timer = null;
    $msgsBtn.prop('disabled', false);
  }


  $msgsBtn.click(function () {
    if (phoneCode) {
      //手机号可以使用
      if (timer) {
        return;
      }

      var ntime = 60;
      $msgsBtn.text(ntime + 's后重新获取');
      timer = window.setInterval(function () {
        ntime--;
        $msgsBtn.text(ntime + 's后重新获取');
        if (ntime < 0) {
          $msgsBtn.text('获取验证码');
          $msgsBtn.prop('disabled', false);
          window.clearInterval(timer);
          timer = null;
        }
      }, 1000);

      apiType({
        type: 'POST',
        timeout: 5000
      }, 'ajaxSend', {
        "mobile": peoData.user_phone
      }).then(function (data) {
        data = JSON.parse(data);
        if (data.success) {} else {
          swal({
            button: '关闭',
            text: `${data.error}`,
            type: 'error'
          });
          $msgsBtn.text('获取验证码');
          window.clearInterval(timer);
          timer = null;
          $msgsBtn.prop('disabled', false);
        }
      }).catch(function () {
        swal({
          button: '关闭',
          text: `网络连接异常，请稍后重试`,
          type: 'error'
        });
        $msgsBtn.text('获取验证码');
        window.clearInterval(timer);
        timer = null;
        $msgsBtn.prop('disabled', false);
      });
    }
  });
  //取输入的验证码
  var $msgNum = $(".msg-num");

  //提交
  var submiting = false;
  $("#sub-btn-shop").click(function () {
    var a = nameValidation();
    var b = managerValidation();
    // var e = shopAdsTxtValidation();
    var f = phoneValidation();
    var g = areaidNoValidation();
    if ($msgNum.val().length === 6 && a && b && f && g) {
      peoData.code = $msgNum.val();
      if (submiting) {
        return;
      }
      submiting = true;
      // peoData.address = peoData.address + detailaAddress;
      apiByObj({
        service: 'clubClanRegister',
        path: '/mobile/Register/',
        data: peoData
      }).then(function (data) {
        data = JSON.parse(data);
        if (data.code === 0) {
          swal({
            button: '关闭',
            text: "注册成功"
          }).then(function (e) {
            Router.navigate('/resources/app/scan-qr-code/scan-qr-code.html', {
              sid: peoData.sid
            });
          });
        } else {
          swal({
            button: '关闭',
            text: "注册失败，" + data.msg,
          });
        }
      }).catch(function (err) {
        swal({
          button: '关闭',
          text: "注册失败，请检查网络！"
        });
      }).then(function () {
        submiting = false;
      });
    } else {
      swal({
        button: '关闭',
        text: "信息不完整，请完善信息"
      });
    }
  })
});