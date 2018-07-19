$(function(window){

  var html =`
      <div class="d-flex justify-content-around align-items-center">
        <div class="menue-item text-center apply" data-url="/resources/wechat/lottery.html" onclick="navigateTo('/resources/wechat/lottery.html')">
          <div class="menue-icon"><i class="iconfont icon-apply f-40"></i></div>
          <div class="nenue-text">申领</div>
        </div>
        <div class="menue-item text-center rank" data-url="/resources/wechat/rank-list.html" onclick="navigateTo('/resources/wechat/rank-list.html')">
          <div class="menue-icon">
            <i class="iconfont icon-ranking-list f-40"></i>
          </div>
          <div class="nenue-text">排行榜</div>
        </div>
        <div class="menue-item text-center indent " data-url="/resources/wechat/lottery-order.html" onclick="navigateTo('/resources/wechat/lottery-order.html')">
          <div class="menue-icon"><i class="iconfont icon-indent f-40"></i></div>
          <div class="nenue-text">订单</div>
        </div>
        <div class="menue-item text-center me" data-url="/resources/wechat/about-me.html" onclick="navigateTo('/resources/wechat/about-me.html')">
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
}(window))