$(function(window){
  
  var html =`
      <div class="d-flex justify-content-around align-items-center">
        <div class="menue-item text-center point" data-url="/resources/wechat/market-manager-point.html" onclick="navigateTo('/resources/wechat/market-manager-point.html')">
          <div class="menue-icon"><i class="iconfont icon-indent f-40"></i></div>
          <div class="nenue-text">公益分</div>
        </div>

        <div class="menue-item text-center stamp relative" data-url="/resources/wechat/about-manager.html" onclick="navigateTo('/resources/wechat/market-manager-review.html')">
          <div class="badge-position hidden"><span class="badge badge-style"></span></div>
          <div class="menue-icon"><i class="iconfont icon-stamp f-40"></i></div>
          <div class="nenue-text">审核</div>
        </div>

        <div class="menue-item text-center me" data-url="/resources/wechat/about-market.html" onclick="navigateTo('/resources/wechat/about-market.html')">
          <div class="menue-icon"><i class="iconfont icon-me f-40"></i></div>
          <div class="nenue-text">我的</div>
        </div>
    </div>
  `;
  $('#menu').append(html)
  $(`#menu .${active_menu}`).addClass('active');
  var navigateTo = function (url) {
    if (url === $(`#menu .active`).data().url) {
      return;
    }
    Router.navigate(url,{sid: sid});
  }
  window.navigateTo = navigateTo;
  
  apiByObj({
    path:'/mobile/msgtip/',
    service: 'get_noaudit_num',
    data: {
      type: 'area_manager',
      sid:sid
    }
  })
  .then(res => {
    res = JSON.parse(res);
    if (res && res.data) {
      let badge = Number(res.data.num);
      badge =  badge > 99 ? '99+' : (badge > 0 ? badge :undefined);
      if (badge) {
        $('.badge-position').removeClass('hidden');
        $('.badge-style').html(badge);
      } else {
        $('.badge-position').addClass('hidden');
      }
    }
  })
  .catch(function (error) {
    console.log(error);
  });

}(window))