$(function(window) {
  var sid = GetQueryString("sid");
  window.sid = sid;
  // default get week's top sales
  var type = $('.nav-item.nav-active').attr('value');
  getRankListBy(type);

  $('.nav-box .nav-item').click(function(e) {
    var target = $(e.target);
    if (type === target.attr('value')) { return; }
    // toggle actived nav
    $('.nav-item.nav-active').removeClass('nav-active');
    target.addClass('nav-active');
    type = target.attr('value');
    // get list
    getRankListBy(type);
  });
  
  function getRankListBy(type) {
    var req = { type: type, sid: sid };
    api('api_salesRanking',req).then(function(data){
      data = JSON.parse(data);
      var list = [];
      var listEle = $('.rank-list');
      // clear last list
      listEle.html('');
      if (data && data.data) {
        if (data.data.length) {
          createRankListHtml(data.data, listEle);
        } else {
          listEle.append('<div class="empty-rank empty-container"><p class="empty-info">排行榜为空</p></div>');
        }
      } else {
        swal({button: '关闭', text:`获取排行榜失败，${data.status.error_desc}`, type: 'error' });
      }
    }).catch(function(err){
      swal({button: '关闭', text:`获取排行榜失败, 请检查网络`});
    })
  }
  
  function createRankListHtml(list, listEle) {
    for (var i = 0; i < list.length; i++) {
      var item = list[i];
      listEle.append(`
        <div class='rank-list-item d-flex justify-content-between align-items-center'>
          <div class='d-flex justify-content-end align-items-center'>
            <img class='item-img img-thumbnail' src=${item.avatar_url}>
            <div class="item-content">
              <div class="item-title">${item.name}</div>
              <div class="item-info">${item.total}</div>
            </div>
          </div>
          <div class='item-badge'>${i+1}</div>
        </div>
      `);
    }
  }
  
  //获取sid
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }

  window.active_menu='rank'
}(window));

