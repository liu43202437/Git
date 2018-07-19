/**
 * Created by wangzhongmin on 2017/11/23.
 */
$(function () {
    var shopData = {
        "id": "",   //店铺编号
        "name": "",   //姓名
        "view_name": "",  //店铺名称
        "yan_code": "", //烟草许可证
        "id_number": "",   //身份证号
        "manager_name": "", // 客户经理姓名
        "manager_id": "", // 客户经理身份证号
        "sheng": "", //省份名称
        "city": "", //城市
        "address": "",   //详细地址
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

        shopData.name = datas.name;
        $(".names").find(".info>p").text(shopData.name);

        shopData.view_name = datas.view_name;
        $(".view_name").find(".info>p").text(shopData.view_name);

        shopData.yan_code = datas.yan_code;
        $(".yan_codes").find(".info>p").text(shopData.yan_code);

        shopData.id_number = datas.id_number;
        $(".id_numbers").find(".info>p").text(shopData.id_number);
        // 客户经理姓名
        shopData.manager_name = datas.manager_name;
        $(".manager-name").find(".info>p").text(shopData.manager_name);
        // 客户经理身份证号
        shopData.manager_id = datas.manager_id;
        $(".manager-id-number").find(".info>p").text(shopData.manager_id );

        shopData.city = datas.city;
        $(".citys").find(".info>p").text(shopData.city);

        shopData.address = datas.address;
        $(".addresses").find(".info>p").text(shopData.address);

        shopData.phone = datas.phone;
        $(".phones").find(".info>p").text(shopData.phone);

        shopData.sheng = datas.sheng;
        $(".shengs").find(".info>p").text(shopData.sheng);

        if(datas.bank_name) {
          $('.bank_name').removeClass('hidden');
          $('.bank_name').find(".info>p").text(datas.bank_name);
        }
        if(datas.bank_card_id) {
          $('.bank_id').removeClass('hidden');
          $('.bank_id').find(".info>p").text(datas.bank_card_id);
        }

        if (datas.id_number_image){
          var imgs = datas.id_number_image;
          $('.img-item.id-image').removeClass('hidden');
          $('.font-img').attr('src',imgs.front);
          $('.back-img').attr('src',imgs.back);
          if (imgs.yan_image){
            $('.img-item.yan').removeClass('hidden');
            $('.tobacco-card').attr('src',imgs.yan_image);
          }
        }
    }

    // var a = 0;
    // if (a === 0) {
    if (sid !== null || sid === "") {
        api('api_shopid',{"sid": sid}).then(function(data){
            data = JSON.parse(data);
            if (data.status.succeed === 1) {
                var datas = data.data;
                bangding(datas);
            } else {
                swal({button: '关闭', text:`获取零售店信息失败，${data.status.error_desc}`, type: 'error' });
            }
        }).catch(function(err){
            console.log(err);
            swal({button: '关闭', text:`获取零售店信息失败，请检查网络！`});
        });
    } else {
        swal({button: '关闭', text:`获取sid失败,请重试`});
    }


    $('.zoomImage > img').click(function(e){
        console.log($(e.target).attr('src'));
         var src = $(e.target).attr('src');
        window.location.href = src;
        
    })
});
