/**
 * Created by wangzhongmin on 2017/11/21.
 */
/**
 * Created by wangzhongmin on 2017/11/20.
 */
$(function () {
  var peoData = {
    "sid": "47eccd96f3a5199018538298e5e7b50e", //sid
    "phone": "", //手机号
    "id_number": "", //身份证号
    "manager_id_number": "", //身份证号
    // "code": "",       //验证码
    "name": "", //姓名
    "yan_code": "", //烟草许可证
    "area_id": "", //省号码
    "area_code": "",
    "city": "", //城市
    "address": "", //详细地址
    "view_name": "", // 店铺名称
    "manager_name": "", // 客户经理姓名
    "id":"",
    "type":'club',
    update: false,
    //...........................编辑信息..................................
    phoneCode: false
  };
  var subFlag = false; //提交按钮标识
  var regular = /\s+/g; //空格
  var serverData = [8954, 3562, 9438, 2234, 5982]; //验证码

  //获取sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  // alert(GetQueryString("sid"));
  peoData.sid = GetQueryString("sid");



  // 同意协议操作
  $('.open-model').click(function () {
    var screenHeight = document.documentElement.clientHeight;
    $('.model-box').height(screenHeight);
    $('.model-box').removeClass('hidden');
  })

  $('.close-model').click(function () {
    $('.model-box').addClass('hidden');
  })

  // js验证姓名
  var regName = /^[\u4e00-\u9fa5]{2,4}$/;
  var $name = $(".name-txt");
  var $nameIcon = $(".name");
  $name.focus(function () {
    $nameIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $name.blur(nameValidation);

  function nameValidation() {
    if (!regName.test($name.val())) {
      // alert('真实姓名填写有误');
      $nameIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('身份证号填写正确');
      $nameIcon.children("span").addClass("glyphicon-ok green");
      peoData.name = $name.val();
      return true;
    }
  }

  // js验证客户经理姓名 
  var $manager = $(".manager-name-txt");
  var $managerIcon = $(".manager-name");
  $manager.focus(function () {
    $managerIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $manager.blur(managerValidation);

  function managerValidation() {
    if (!regName.test($manager.val())) {
      // alert('真实姓名填写有误');
      $managerIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('身份证号填写正确');
      $managerIcon.children("span").addClass("glyphicon-ok green");
      peoData.manager_name = $manager.val();
      return true;
    }
  }

  // js验证许可证号码
  var $xuke = $(".xuke-txt");
  var $xukeIcon = $(".xuke");
  $xuke.focus(function () {
    $xukeIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $xuke.blur(xukeValidation);

  function xukeValidation() {
    if ($xuke.val() === "" || $xuke.val() === null || regular.test($xuke.val())) {
      // alert('许可证填写有误');
      $xukeIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('许可证填写正确');
      $xukeIcon.children("span").addClass("glyphicon-ok green");
      peoData.yan_code = $xuke.val();
      return true;
    }
  }

  // js验证身份证号
  var regIdNo = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
  var $idNo = $(".id-num");
  var $idNoIcon = $(".id-number");

  $idNo.blur(idNoValidation);
  $idNo.focus(function () {
    $idNoIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });

  function idNoValidation() {
    if (!regIdNo.test($idNo.val())) {
      $idNoIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      $idNoIcon.children("span").addClass("glyphicon-ok green");
      peoData.id_number = $idNo.val();
      return true;
    }
  }
  // 验证客户经理身份证号
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
      peoData.manager_id_number = $areaidNo.val();
      return true;
    }
  }

  //省市县街道四级联动
  var provinceArr = CITY_CODE;
  var cityAddress = '';
  var provinceSelect = new MobileSelect({
    trigger: '#province',
    title: '请选择地区',
    wheels: [{
      data: provinceArr
    }],
    position: [2],
    transitionEnd: function (indexArr, data) {
      // console.log(data);
    },
    callback: function (indexArr, data) {
      console.log(data);
      peoData.area_id = data[0] ? data[0].id : '';
      var _province = data[0] ? data[0].value : '';
      peoData.city = data[1] ? data[1].value : '';
      var county = data[2] ? data[2].value : '';
      var street = data[3] ? data[3].value : '';
      cityAddress = county + street;
      peoData.area_code = data[data.length - 1].id;
      selectedItem = [{
        id: '1'
      }];

      if(peoData.area_id === '7') {
        $('.check-box').removeClass('hidden');
      } else {
        $('.check-box').addClass('hidden');
      }
      getManagerInfo();
      getManagerList();
    }
  });

  // 详细店铺地址
  var detailaAddress = '';
  var $shopAdsTxt = $(".shop-ads-txt");
  var $shopAdsIcon = $(".shop-ads");
  $shopAdsTxt.focus(adsFocus);
  $shopAdsTxt.blur(shopAdsTxtValidation);

  function adsFocus() {
    $shopAdsIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  }

  function shopAdsTxtValidation() {
    if ($shopAdsTxt.val() === "" || $shopAdsTxt.val() === null || regular.test($shopAdsTxt.val())) {
      // alert('许可证填写有误');
      $shopAdsIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('许可证填写正确');
      $shopAdsIcon.children("span").addClass("glyphicon-ok green");
      detailaAddress = $shopAdsTxt.val();
      return true;
    }
  }


  // 获取上一级信息列表
  var managerSelected = new MobileSelect({
    trigger: '#managerSelected',
    title: '请选择',
    wheels: [{
      data: [{
        id: '1',
        value: '暂无人员信息'
      }]
    }],
    position: [0],
    callback: function (indexArr, data) {
      selectedItem = data;
      getManagerInfo();
    }
  });

  function getManagerList(code) {
    var area_code = code || peoData.area_code;
    apiByObj({
      path: '/mobile/msgtip/',
      service: 'get_manager_list',
      data: {
        sid: peoData.sid,
        area_code: area_code,
        type: 'manager'
      }
    }).then((data) => {
      data = JSON.parse(data);
      var listData = [{
        id: '1',
        value: '暂无人员信息'
      }];
      if (data.data && data.data.length) {
        listData = [];
        for (let item of data.data) {
          listData.push({
            id: item.phone,
            value: item.name + " (" + item.phone + ")",
            name: item.name
          })
        }
      }
      managerSelected.updateWheel(0, listData);
      //...................编辑信息............................... 
      if (peoData.manager_id_number) {
        var index =  listData.findIndex(function(item){
          return item.id === peoData.manager_id_number;
        })
        if (index > -1) {
          selectedItem = [listData[index]]
          $('#managerSelected').html(listData[index].value);
          managerSelected.locatePosition(0,index);
        } else {
          $('#managerSelected').html('<p>请选择</p>');
        }
      }else {
        $('#managerSelected').html('<p>请选择</p>');
      }
    })
  }

  var selectedItem = [{
    id: "1"
  }];

  function getManagerInfo() {
    if (selectedItem[0].id !== '1') {
      peoData.manager_name = selectedItem[0].name;
      peoData.manager_id_number = selectedItem[0].id;
      return true;
    } else {
      peoData.manager_name = '';
      peoData.manager_id_number = '';
      return false;
    }
  }

  // 店铺名称验证

  var $shopName = $('.shop-name');
  var $shopNameIcon = $('.view_name');

  $shopName.focus(function () {
    $shopNameIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  })

  $shopName.blur(shopNameValidation);

  function shopNameValidation() {
    if ($shopName.val() === "" || $shopName.val() === null || regular.test($shopName.val())) {
      // alert('许可证填写有误');
      $shopNameIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('许可证填写正确');
      peoData.view_name = $shopName.val();
      $shopNameIcon.children("span").addClass("glyphicon-ok green");
      return true;
    }
  }



  // 获取短信验证码
  var regPhoneNum = /^1[34578]\d{9}$/;
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
      peoData.phoneCode = true;
      peoData.phone = $phoneNum.val();
    } else {
      //手机号不可以使用
      $msgsBtn.removeClass("able-btn").addClass("disable-btn");
      peoData.phone = "";
      peoData.phoneCode = false;
    }
  });

  function phoneValidation() {
    if (!$phoneNum.val() || !regPhoneNum.test($phoneNum.val())) {
      $phone.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      $phone.children("span").addClass("glyphicon-ok green");
      peoData.phone = $phoneNum.val();
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
    console.log('asdfdsaf')
    if (peoData.phoneCode) {
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
        "mobile": peoData.phone
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


  //....................编辑信息..............................
  var editInit = new EditInfo({
    type: 'club',
    map: {
      "manager_id_number": "manager_id"
    },
    eleArr: [{
        '.name-txt': 'name'
      },
      {
        '.xuke-txt': 'yan_code'
      },
      {
        '.id-num': 'id_number'
      },
      {
        '.shop-name': 'view_name'
      },
      {
        '.phone-num': 'phone'
      },
    ],
    address: {
      ele: '#province',
      detail: '.shop-ads-txt',
      selectedObj: provinceSelect,
      list: CITY_CODE
    },
    manager: {
      ele: '#managerSelected',
      getList: getManagerList
    },
  })

  editInit.update(peoData);




  //提交
  var submiting = false;
  $("#sub-btn-shop").click(function () {

    var a = nameValidation();
    // var b = managerValidation();
    // managerIdValidation();
    var c = xukeValidation();
    var d = idNoValidation();
    var e = shopAdsTxtValidation();
    var f = phoneValidation();
    // var g = areaidNoValidation();
    var h = shopNameValidation();
    var i = getManagerInfo();

    var isAgreAgreement = $('.agree-checkbox').prop("checked");
    if (peoData.area_id === '7' && !isAgreAgreement) {
      swal({
        button: '关闭',
        text: "请阅读并同意《卷烟零售户公益票代销协议》"
      });
      return;
    }

    if ($msgNum.val().length === 6 && a && c && d && e && f && h ) {
      if ($("#province").text().includes('请选择地区')) {
        swal({
          button: '关闭',
          text: "请选择地区"
        });
      } else {
        peoData.code = $msgNum.val();
        if (submiting) {
          return;
        }
        submiting = true;
        cityAddress = (cityAddress) ? cityAddress :(editInit.selectData.cityAddress) ? editInit.selectData.cityAddress:"";
        peoData.address = cityAddress + detailaAddress;

        var refuse = GetQueryString('refuse');
        var reqData = (refuse === "1") ?{
          path:'/mobile/Register/',
          service:'modifyInfo',
          data:peoData
        } : {
          path:'/mobile/wechat/',
          service:'updateShop',
          data:peoData
        }
        apiByObj(reqData).then(function (data) {
          data = JSON.parse(data);
          if (data.status.succeed === 1) {
            if (peoData.area_id === '14') {
              Router.navigateWithSid('/resources/wechat/vc-bank.html', {name:escape(peoData.name),refuse:refuse});
            } else {
              Router.navigateWithSid('/resources/wechat/question.html',{});
            }
          } else {
            swal({
              button: '关闭',
              text: `#${data.status.error_code}: 注册失败，请检查信息！${data.status.error_desc}`
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
      }
    } else {
      swal({
        button: '关闭',
        text: "信息不完整，请完善信息！"
      });
    }
  })
});