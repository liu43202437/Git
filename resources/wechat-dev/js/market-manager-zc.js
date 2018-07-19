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
    "area_manager_name": "", // 市场经理姓名
    "bazaar_phone": "", //区域经理身份证号
    "bazaar_name": "", // 区域经理姓名
    "area_id": "",
    "area_code": "",
    "city": "",
    "address": "",
    "id":"",
    "type":'area_manager',
    update: false,
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

  // js验证姓名

  // js验证市场经理姓名 
  var $manager = $(".manager-name-txt");
  var $managerIcon = $(".manager-name");
  var regName = /^[\u4e00-\u9fa5]{2,4}$/;
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
      peoData.area_manager_name = $manager.val();
      return true;
    }
  }

  // 验证区域经理姓名
  var $areamanager = $(".area-manager-name-txt");
  var $areamanagerIcon = $(".area-manager-name");
  $areamanager.focus(function () {
    $areamanagerIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $areamanager.blur(areamanagerValidation);

  function areamanagerValidation() {
    if (!regName.test($areamanager.val())) {
      // alert('真实姓名填写有误');
      $areamanagerIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('身份证号填写正确');
      $areamanagerIcon.children("span").addClass("glyphicon-ok green");
      peoData.bazaar_name = $areamanager.val();
      return true;
    }
  }

  //省市县街道四级联动
  var provinceArr = CITY_CODE;
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
      peoData.area_id = data[0] ? data[0].id : '';
      peoData.city = data[1] ? data[1].value : '';
      var county = data[2] ? data[2].value : '';
      var street = data[3] ? data[3].value : '';
      peoData.address = county + street;
      peoData.area_code = data[data.length - 1].id;
      selectedItem = [{
        id: '1'
      }];
      getManagerInfo();
      getManagerList();
    }
  });

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
    $('.chosen-select').html('');
    apiByObj({
      path: '/mobile/msgtip/',
      service: 'get_manager_list',
      data: {
        sid: peoData.sid,
        area_code: area_code,
        type: 'bazaar_manager'
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
            value: item.name +" (" + item.phone + ")",
            name: item.name 
          })
        }
      }
      managerSelected.updateWheel(0,listData);
      if (peoData.bazaar_phone) {
        var index =  listData.findIndex(function(item){
          return item.id === peoData.bazaar_phone;
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

  var selectedItem = [{id:"1"}];
  function getManagerInfo() {
   if (selectedItem[0].id !== '1') {
      peoData.bazaar_name = selectedItem[0].name;
      peoData.bazaar_phone = selectedItem[0].id;
      return true;
    } else {
      peoData.bazaar_name = '';
      peoData.bazaar_phone = '';
      return false;
    }
  }

  // js验证身份证号
  var regIdNo = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
  var $idNo = $(".id-num");
  var $idNoIcon = $(".id-number");
  $idNo.focus(function () {
    $idNoIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $idNo.blur(idNoValidation);

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
  // 区域经理手机号验证
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
      peoData.bazaar_phone = $areaidNo.val();
      return true;
    }
  }


  // 获取短信验证码
  var regPhoneNum = /^1[34578]\d{9}$/;
  var $phoneNum = $(".phone-num");
  var $phone = $('.phone');
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
  $msgsBtn.click(function () {
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
  //提交
  var submitting = false;

  function addressValidation() {
    if ($("#province").text().includes('请选择地区')) {
      swal({
        button: '关闭',
        text: "请选择地区"
      });
      return false;
    } else {
      return true;
    }
  }

    //....................编辑信息..............................
    var editInit = new EditInfo({
      type: 'area_manager',
      map: {
        "area_manager_name":"name"
      },
      eleArr: [{
          '.manager-name-txt': 'area_manager_name'
        },
        {
          '.id-num': 'id_number'
        },
        {
          '.phone-num': 'phone'
        },
      ],
      address: {
        ele: '#province',
        selectedObj: provinceSelect,
        list: CITY_CODE
      },
      manager: {
        ele: '#managerSelected',
        getList: getManagerList
      },
    })
  
    editInit.update(peoData);
  

  $("#sub-btn-manager").click(function () {
    var a = managerValidation();
    // var b = areamanagerValidation();
    // var c = areaidNoValidation();
    var d = idNoValidation();
    var e = phoneValidation();
    var f = getManagerInfo();


    if ($msgNum.val().length === 6 && a && f && d && e) {
      peoData.code = $msgNum.val();
      var f = addressValidation();
      if (submitting || !f) {
        return;
      }
      submitting = true;
      var refuse = GetQueryString('refuse');
      var reqData = (refuse === "1") ?{
        path:'/mobile/Register/',
        service:'modifyInfo',
        data:peoData
      } : {
        path:'/mobile/wechat/',
        service:'addarea_Manager',
        data:peoData
      }

      apiByObj(reqData).then(function (data) {
          data = JSON.parse(data);
          if (data.status.succeed === 1) {
            swal({
              button: '确定',
              text: `信息提交成功，请您耐心等待审核！`
            });
            setTimeout(function () {
              Router.navigate('/resources/wechat/market-manager-info.html', {
                sid: peoData.sid
              });
            }, 1000);
          } else {
            swal({
              button: '关闭',
              text: `注册失败，请检查信息！${data.status.error_desc}`,
              type: 'error'
            });
          }
        }).catch(function () {
          swal({
            button: '关闭',
            text: "注册失败，请检查网络！"
          });
        })
        .then(function () {
          submitting = false;
        })
    } else {
      swal({
        button: '关闭',
        text: `输入有误，请重新输入！`
      });
    }
  })
});