$(function() {
   //get sid
   function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  var sid = GetQueryString("sid");
  var subState = true;

  // TODO: get account banlance
  var banlance = 0;
  getBanlance();

  // bind event for check withdraw money
  var moneyEle = $('#withdraw-money');
  var val = moneyEle.val();
  var btn = $('.withdraw');
  // $('#withdraw-money').keyup(function() {
  //   var val = moneyEle.val();
  //   if (val <= 0 || val > banlance) {
  //     btn.addClass('disabled');
  //   } else {
  //     btn.removeClass('disabled');
  //   }
  // });

  function getBanlance() {
    apiByObj({
      service: 'moneyRemaining',
      path: '/mobile/Redeem/',
      data: { sid: sid }
    }).then(data => {
      data = JSON.parse(data);

      if (!Number(data.code)) {
        banlance = data.moneyRemaining ? data.moneyRemaining : 0;
        $('.account-banlance').html(`当前账户余额${banlance}元.`);
      } else {
        swal({button: '关闭', text:"获取账户余额失败," + data.status.error_desc, type: 'error' });
      }
    }).catch(() => {
      swal({button: '关闭', text:"获取账户余额失败, 请检查网络"});
    });
  }

  btn.click(function() {
    if (subState) {
      var val = moneyEle.val();
      var reg = /^[0-9]\d*$/;
      var money = parseInt(val);
      if (reg.test(val) && money>0){
        if( money > Number(banlance)) {
          swal({button: '关闭', text:"余额不足"});
        }else {
          subState = false;
          apiByObj({service:'excReceipt',path:'/mobile/Wechat_receipt/',data:{sid:sid,money: val}}).then((data)=>{
            var data = JSON.parse(data);
            if (data.status && data.status.succeed === 0){
              swal({button: '关闭', text:data.status.error_desc}).then(() =>{
                subState = true;
              });
            } else {
              Router.navigate('/resources/app/scan-qr-code/withdraw-after.html', {sid: sid,code: 0,msg:"提现成功"});
            }
          });
        }
      } else {
        swal({button: '关闭', text:"每次提现只能是大于0的整数"});
      }
    }
  });
}());