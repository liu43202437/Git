"use strict";$(function(e){function t(t){var a=new RegExp("(^|&)"+t+"=([^&]*)(&|$)"),i=e.location.search.substr(1).match(a);return null!=i?unescape(i[2]):null}var a=t("sid"),i=t("type"),n=$(".record-list"),s=$(".nav-item.nav-active").data("type");i&&(s=i,$(".nav-item.nav-active").removeClass("nav-active"),$("."+i).addClass("nav-active"),"redeem"===i?$(".withdraw-box").addClass("show"):$(".withdraw-box").removeClass("show"));var d={sid:a,type:s,loadmore:!1};o(d=loadMoreInit(d));var r=0;function o(e){var t="redeem"===e.type?"redeemList":"withdrawList";apiByObj({service:t,path:"/mobile/Redeem/",data:e}).then(function(t){if(t=JSON.parse(t),e.type===$(".nav-item.nav-active").data("type"))if(e.loadmore||n.html(""),t&&t.data&&t.data.list){var a=t.data;checkLoadMore(a.list.length,a.length,o),a.list.length?function(e){for(var t=0;t<e.length;t++){var a=e[t],i="redeem"===s?'\n        <div class="record-list-item mb-md">\n          <div class="record-list-title">账户返奖: <span class="text-danger">￥'+a.prize+"</span></div>\n          <div>"+a.time+'</div>\n          <div><span class="ml">'+a.gameName+"</span>"+a.ticketNo+"</div>\n        </div>\n      ":'\n        <div class="record-list-item border-bottom d-flex justify-content-between">\n          <div class="f-16">'+a.transfer_time+'</div>\n          <div class="record-list-title">￥'+parseInt(a.amount)/100+"</div>\n        </div>\n      ";n.append(i)}}(a.list):n.append('<div class="empty-container"><p class="empty-info">记录为空.</p></div>')}else swal({button:"关闭",text:"获取记录列表失败,"+t.msg})}).catch(function(e){swal({button:"关闭",text:"获取记录列表失败, 请检查网络"})})}apiByObj({service:"moneyRemaining",path:"/mobile/Redeem/",data:{sid:a}}).then(function(e){e=JSON.parse(e),Number(e.code)?swal({button:"关闭",text:"获取账户余额失败,"+e.msg,type:"error"}):(r=e.moneyRemaining?e.moneyRemaining:0,$(".withdraw-box .account-banlance.text-danger").html(r))}).catch(function(e){swal({button:"关闭",text:"获取账户余额失败, 请联系客服"})}),$(".withdraw-box .withdraw-btn").click(function(){Router.navigate("/resources/wechat/scan-qr-code/withdraw.html",{sid:a})}),$(".nav-box .nav-item").click(function(e){var t=$(e.target);s!==t.data("type")&&(n.html(""),$(".nav-item.nav-active").removeClass("nav-active"),t.addClass("nav-active"),"redeem"===(s=t.data("type"))?$(".withdraw-box").addClass("show"):$(".withdraw-box").removeClass("show"),o(d=loadMoreInit({sid:a,type:s,loadmore:!1})))})}(window));