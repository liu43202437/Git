/**
 * Created by wangzhongmin on 2017/11/23.
 */
$(function () {
    var shopData = {
        "id": "",   //店铺编号
        "name": "",   //姓名
        "id_number": "",   //身份证号
        "phone": "",   //手机号
    };

    //获取sid
    function GetQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)return unescape(r[2]);
        return null;
    }

    var sid = GetQueryString("sid");


    //数据绑定
    function bangding(datas) {
        shopData.id = datas.id;
        $(".ids").find(".info>p").text(shopData.id);

        shopData.name = datas.real_name;
        $(".names").find(".info>p").text(shopData.name);

        shopData.id_number = datas.id_number;
        $(".id_numbers").find(".info>p").text(shopData.id_number);

        shopData.phone = datas.username;
        $(".phones").find(".info>p").text(shopData.phone);
        

    }

    // var a = 0;
    // if (a === 0) {
    if (sid !== null || sid === "") {
        $.ajax({
            //提交数据的类型 POST GET
            type: "POST",
            //提交的网址
            url: "http://yan.ishizhuan.com/mobile/wechat/api_myid",
            //提交的数据
            data: {"sid": sid},
            //返回数据的格式
            datatype: "json",
            //在请求之前调用的函数
            // beforeSend:function(){console.log(peoData);},
            //成功返回之后调用的函数
            success: function (data) {
                data = JSON.parse(data);
                if (data.status.succeed === 1) {
                    var datas = data.data;
                    bangding(datas);
                } else {
                    alert("获取零售店信息失败，" + data.status.error_desc);
                }
            },
            //调用出错执行的函数
            error: function (err) {
                //请求出错处理
                alert("注册失败，请检查网络！");
            }
        });
    } else {
        alert("获取sid失败,请重试！");
    }


});