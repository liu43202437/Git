$(function (window) {
  var latitude, longitude;
  $('.js-get-position').click(function () {
    console.log('sdsdsd');
    wx.getLocation({
      type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
      success: function (res) {
        latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
        longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
      }
    });

    wx.openLocation({
      latitude: latitude, // 纬度，浮点数，范围为90 ~ -90
      longitude: longitude, // 经度，浮点数，范围为180 ~ -180。
      name: '', // 位置名
      address: '', // 地址详情说明
      scale: 1, // 地图缩放级别,整形值,范围从1~28。默认为最大
      infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
    });
  })
}(window))