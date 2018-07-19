/**
 * Created by wangzhongmin on 2017/11/20.
 */
$(function () {
    var peoData = {
        "sid": "",  //sid
        "phone": "",   //手机号
        "idNumber": "",   //身份证号
        "code": "",       //验证码
        "realName": ""    //姓名
    };
    var subFlag = false;  //提交按钮标识
    var serverData = [8954, 3562, 9438, 2234, 5982];
    //获取sid
    function GetQueryString(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }
    peoData.sid = GetQueryString("sid");

    // js验证姓名
    var regName = /^[\u4e00-\u9fa5]{2,4}$/;
    var $name = $(".name-txt");
    var $nameIcon = $(".name");
    $name.focus(function () {
        $nameIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
    });
    $name.blur(function () {
        if (!regName.test($name.val())) {
            // alert('真实姓名填写有误');
            $nameIcon.children("span").addClass("glyphicon-remove red");
            subFlag = false;
        } else {
            // alert('身份证号填写正确');
            $nameIcon.children("span").addClass("glyphicon-ok green");
            peoData.realName = $name.val();
            subFlag = true;
        }
    });

    // js验证身份证号
    var regIdNo = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
    var $idNo = $(".id-num");
    var $idNoIcon = $(".id-number");

    $idNo.blur(function () {
        if (!regIdNo.test($idNo.val())) {
            // alert('身份证号填写有误');
            $idNoIcon.children("span").addClass("glyphicon-remove red");
            subFlag = false;
        } else {
            // alert('身份证号填写正确');
            $idNoIcon.children("span").addClass("glyphicon-ok green");
            peoData.idNumber = $idNo.val();
            subFlag = true;
        }
    });
    $idNo.focus(function () {
        $idNoIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
    });

    // 获取短信验证码
    var regPhoneNum = /^1[34578]\d{9}$/;
    var phoneCode = false;
    var $phoneNum = $(".phone-num");
    var $msgsBtn = $(".msgs");
    $phoneNum.on('input propertychange', function () {
        $msgsBtn.text("点击获取验证码");
        if (regPhoneNum.test($(this).val())) {
            $msgsBtn.removeClass("disable-btn").addClass("able-btn");
            phoneCode = true;
            peoData.phone = $phoneNum.val();
        } else {
            //手机号不可以使用
            $msgsBtn.removeClass("able-btn").addClass("disable-btn");
            phoneCode = false;
        }

    });
    $msgsBtn.click(function () {
        if (phoneCode) {
            //手机号可以使用

            $.ajax({
                url: `${HOST}/mobile/wechat/ajaxSend`,
                type: 'POST',
                timeout: 5000,
                dataType: 'json',
                data: {mobile: peoData.phone},
                success: function (result) {
                    if (result.success) {
                        var ntime = 30;
                        var timer = window.setInterval(function () {
                            $msgsBtn.text(ntime + 's后重新获取');
                            ntime--;
                            if (ntime < 0) {
                                $msgsBtn.text('获取验证码');
                                $msgsBtn.prop('disabled', false);
                                window.clearInterval(timer);
                            }
                        }, 1000);
                    } else {
                        $msgsBtn.prop('disabled', false);
                    }
                },
                error: function (e) {
                    $msgsBtn.prop('disabled', false);
                }
            });

        }


    });

    //取输入的验证码
    var $msgNum = $(".msg-num");
    
    //提交
    console.log(peoData);
    $("#sub-btn-user").click(function () {
        if ($msgNum.val().length === 6 && subFlag) {
            peoData.code = $msgNum.val();
            $.ajax({
                //提交数据的类型 POST GET
                type: "POST",
                //提交的网址
                url: `${HOST}/mobile/wechat/addUser`,
                //提交的数据
                data: peoData,
                //返回数据的格式
                datatype: "json",//"xml", "html", "script", "json", "jsonp", "text".
                //在请求之前调用的函数
                // beforeSend:function(){},
                //成功返回之后调用的函数
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status.succeed && !data.status.error_code) {
                        alert("注册成功！");
                        $name.val("");
                        $idNo.val("");
                        $phoneNum.val("");
                        $msgNum.val("");
                    } else {
                        alert("注册失败，请检查信息！"+ data.status.error_desc);
                    }
                },
                //调用出错执行的函数
                error: function (err) {
                    //请求出错处理
                    alert("注册失败，请检查网络！" + err);
                }
            });
        } else {
            alert("输入有误，请重新输入！");
        }
    });
});


// $.ajax({
//     type: "get",    //请求方式
//     async: true,    //是否异步
//     url: "http://www.domain.net/url",
//     dataType: "jsonp",    //跨域json请求一定是jsonp
//     jsonp: "callbackparam",    //跨域请求的参数名，默认是callback
//     //jsonpCallback:"successCallback",    //自定义跨域参数值，回调函数名也是一样，默认为jQuery自动生成的字符串
//     data: {"query": "civilnews"},    //请求参数
//
//     beforeSend: function () {
//         //请求前的处理
//     },
//
//     success: function (data) {
//         //请求成功处理，和本地回调完全一样
//     },
//
//     complete: function () {
//         //请求完成的处理
//     },
//
//     error: function () {
//         //请求出错处理
//     }
// });
