"use strict";$(function(){var t,e,a=(t=new RegExp("(^|&)"+"sid"+"=([^&]*)(&|$)"),null!=(e=window.location.search.substr(1).match(t))?unescape(e[2]):null),n=!0,s=0;apiByObj({service:"moneyRemaining",path:"/mobile/Redeem/",data:{sid:a}}).then(function(t){t=JSON.parse(t),Number(t.code)?swal({button:"关闭",text:"获取账户余额失败,"+t.status.error_desc,type:"error"}):(s=t.moneyRemaining?t.moneyRemaining:0,$(".account-banlance").html("当前账户余额"+s+"元."))}).catch(function(){swal({button:"关闭",text:"获取账户余额失败, 请检查网络"})});var c=$("#withdraw-money");c.val();$(".withdraw").click(function(){if(n){var t=c.val(),e=parseInt(t);/^[0-9]\d*$/.test(t)&&e>0?e>Number(s)?swal({button:"关闭",text:"余额不足"}):(n=!1,apiByObj({service:"excReceipt",path:"/mobile/Wechat_receipt/",data:{sid:a,money:t}}).then(function(t){(t=JSON.parse(t)).status&&0===t.status.succeed?swal({button:"关闭",text:t.status.error_desc}).then(function(){n=!0}):Router.navigate("/resources/app/scan-qr-code/withdraw-after.html",{sid:a,code:0,msg:"提现成功"})})):swal({button:"关闭",text:"每次提现只能是大于0的整数"})}})}());