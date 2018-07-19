"use strict";function GetQueryString(t){var e=new RegExp("(^|&)"+t+"=([^&]*)(&|$)"),a=window.location.search.substr(1).match(e);return null!=a?unescape(a[2]):null}window.Vue&&(Vue.component("page-review",{template:"<v-ons-page></v-ons-page>"}),Vue.component("page-me",{template:"<v-ons-page></v-ons-page>"}),Vue.component("page-point",{template:"#page-point",data:function(){return{pageHeight:""}},mounted:function(){this.pageHeight=this.$el.clientHeight},methods:{infiniteScroll:function(t){getListByType(req)}}}),new Vue({el:"#app"}));var sid=GetQueryString("sid"),$listEle=$(".point-list .list-body");$(".all_point").text(0),$(".today_point").text(0),$(".today_pay").text(0),api("getCreditsByManager",{sid:sid}).then(function(t){1===(t=JSON.parse(t)).status.succeed&&t.data&&($(".all_point").text(""+t.data.total),$(".today_point").text(""+(t.data.dayTotal?"+ "+t.data.dayTotal:0)),$(".today_pay").text(""+(t.data.expend?"- "+t.data.expend:0)))}).catch(function(t){console.log(t),swal({button:"关闭",text:"公益分详情列表获取失败"})});var req_type=$(".nav-item.nav-active").data("state"),req={type:req_type,sid:sid,entryNum:20,loadmore:!1};function getListByType(t){api("get_credits_detail",t).then(function(e){e=JSON.parse(e),t.loadmore||$listEle.html(""),1===e.status.succeed&&(e.data&&e.data.list?(checkLoadMore(e.data.list.length,e.data.length,getListByType,!0),creatPointList(e.data.list,$listEle,e.data.type)):e.data&&!e.data.length&&checkLoadMore([],0,null,!0))}).catch(function(t){swal({button:"关闭",text:"公益分详情列表获取失败"})})}function creatPointList(t,e,a){if($(".nav-item.nav-active").data("state")===a)for(var n=0;n<t.length;n++){var s=t[n];switch(s.type){case 1:var i='\n        <div class="list-item-title f-12"><i class="iconfont icon-indent1 f-12 text-yellow"></i> 订单公益分</div>\n        <div class="list-item d-flex justify-content-between align-items-center">\n        <div>\n          <div class="text-gray-65 d-flex align-items-center"><span>订单号：</span> <span>'+s.trade_no+'</span></div>\n          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>'+s.update_date+'</span> </div>\n        </div>\n        <div class="text-gray-45"><span class="f-20 text-red">+'+s.credits+"</span></div>\n      </div>\n        ";e.append(i);break;case 2:i='\n        <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-blue"></i> 客户经理公益分</div>\n        <div class="list-item d-flex justify-content-between align-items-center">\n        <div>\n          <div class="text-gray-65 d-flex align-items-center"><span>店主姓名：</span> <span>'+s.name+'</span> </div>\n          <div class="text-gray-45 d-flex align-items-center"><span>店铺地址：</span><span>'+s.address+'</span> </div>\n          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>'+s.update_date+'</span> </div>\n        </div>\n        <div class="text-gray-45"><span class="f-20 text-red">+'+s.credits+"</span></div>\n      </div>\n        ";e.append(i);break;case 3:i='\n        <div class="list-item d-flex justify-content-between align-items-center pt-10">\n          <div>\n            <div class="text-gray-65 d-flex align-items-center"><span>订单号：</span> <span>'+s.trade_no+'</span></div>\n            <div class="text-gray-45 d-flex align-items-center"><span>兑换：</span><span>'+s.product_title+" x"+s.num+'</span></div>\n            <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>'+s.update_date+'</span></div>\n          </div>\n          <div class="text-gray-45"><span class="f-20 text-gray-65">-'+s.credits+"</span></div>\n        </div>\n        ";e.append(i);break;case 4:i='\n        <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-green"></i> 市场经理公益分</div>\n        <div class="list-item d-flex justify-content-between align-items-center">\n        <div>\n          <div class="text-gray-65 d-flex align-items-center"><span>客户经理姓名：</span> <span>'+s.name+'</span> </div>\n          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>'+s.update_date+'</span> </div>\n        </div>\n        <div class="text-gray-45"><span class="f-20 text-red">+'+s.credits+"</span></div>\n      </div>\n        ";e.append(i);break;case 5:i='\n        <div class="list-item-title f-12"><i class="iconfont icon-shop f-12 text-green"></i> 区域经理公益分</div>\n        <div class="list-item d-flex justify-content-between align-items-center">\n        <div>\n          <div class="text-gray-65 d-flex align-items-center"><span>市场经理姓名：</span> <span>'+s.name+'</span> </div>\n          <div class="text-gray-45 d-flex align-items-center"><span>时间：</span><span>'+s.update_date+'</span> </div>\n        </div>\n        <div class="text-gray-45"><span class="f-20 text-red">+'+s.credits+"</span></div>\n      </div>\n        ";e.append(i)}}}getListByType(req=loadMoreInit(req)),$(".nav-box .nav-item").click(function(t){var e=$(t.target);if(req_type!==e.data("state")){$listEle.html(""),$(".nav-item.nav-active").removeClass("nav-active"),e.addClass("nav-active");var a={type:req_type=e.data("state"),sid:sid,entryNum:20,loadmore:!1};getListByType(a=loadMoreInit(a))}}),window.active_menu="point",window.sid=sid;