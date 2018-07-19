/**
 * Created by wangzhongmin on 2017/11/23.
 */
$(function (window) {
  var shopData = {
      "id_number": "",   //身份证号
      "area_manager_name": "", // 区域经理姓名
      "area_manager_id": "", // 区域经理证件号
      "phone": "",   //手机号
      "sheng": "", //省份名称
      "city": "", //城市
      "address": "",   //详细地址
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
      shopData.id_number = datas.id_number;
      $(".id_numbers").find(".info>p").text(shopData.id_number);

      shopData.area_manager_name = datas.area_manager_name;
      $(".manager-name").find(".info>p").text(shopData.area_manager_name);

      shopData.area_manager_id = datas.area_manager_id;
      $(".manager-id").find(".info>p").text(shopData.area_manager_id);

      shopData.phone = datas.phone;
      $(".phones").find(".info>p").text(shopData.phone);

      shopData.city = datas.city;
      $(".citys").find(".info>p").text(shopData.city);

      shopData.address = datas.address;
      $(".addresses").find(".info>p").text(shopData.address);

      shopData.sheng = datas.sheng;
      $(".shengs").find(".info>p").text(shopData.sheng);

  }

  // var a = 0;
  // if (a === 0) {
  if (sid !== null || sid === "") {
    api('getBazaarManagerInfo',{"sid": sid}).then(function(data){
        data = JSON.parse(data);
        if (data.status.succeed === 1) {
            var datas = data.data;
            bangding(datas);
        } else {
            swal({button: '关闭', text:`获取区域经理信息失败,${data.status.error_desc}`, type: 'error' });
        }
    }).catch(function(){
        swal({button: '关闭', text:`获取区域经理信息失败，请稍后重试！`});
    });
  } else {
      swal({button: '关闭', text:`获取sid失败,请重试！`});
  }

  window.active_menu = 'me';
  window.sid = sid;
}(window));
