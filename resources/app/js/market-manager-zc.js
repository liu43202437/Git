"use strict";$(function(){var e={sid:"47eccd96f3a5199018538298e5e7b50e",phone:"",id_number:"",area_manager_name:"",bazaar_phone:"",bazaar_name:"",area_id:"",area_code:"",city:"",address:"",id:"",type:"area_manager",update:!1,phoneCode:!1};function a(e){var a=new RegExp("(^|&)"+e+"=([^&]*)(&|$)"),n=window.location.search.substr(1).match(a);return null!=n?unescape(n[2]):null}e.sid=a("sid");var n=$(".manager-name-txt"),r=$(".manager-name"),t=/^[\u4e00-\u9fa5]{2,4}$/;function o(){return t.test(n.val())?(r.children("span").addClass("glyphicon-ok green"),e.area_manager_name=n.val(),!0):(r.children("span").addClass("glyphicon-remove red"),!1)}n.focus(function(){r.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),n.blur(o);var l=$(".area-manager-name-txt"),i=$(".area-manager-name");l.focus(function(){i.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),l.blur(function(){return t.test(l.val())?(i.children("span").addClass("glyphicon-ok green"),e.bazaar_name=l.val(),!0):(i.children("span").addClass("glyphicon-remove red"),!1)});var d=CITY_CODE,s=new MobileSelect({trigger:"#province",title:"请选择地区",wheels:[{data:d}],position:[2],transitionEnd:function(e,a){},callback:function(a,n){e.area_id=n[0]?n[0].id:"",e.city=n[1]?n[1].value:"";var r=n[2]?n[2].value:"",t=n[3]?n[3].value:"";e.address=r+t,e.area_code=n[n.length-1].id,p=[{id:"1"}],m(),u()}}),c=new MobileSelect({trigger:"#managerSelected",title:"请选择",wheels:[{data:[{id:"1",value:"暂无人员信息"}]}],position:[0],callback:function(e,a){p=a,m()}});function u(a){var n=a||e.area_code;$(".chosen-select").html(""),apiByObj({path:"/mobile/msgtip/",service:"get_manager_list",data:{sid:e.sid,area_code:n,type:"bazaar_manager"}}).then(function(a){var n=[{id:"1",value:"暂无人员信息"}];if((a=JSON.parse(a)).data&&a.data.length){n=[];var r=!0,t=!1,o=void 0;try{for(var l,i=a.data[Symbol.iterator]();!(r=(l=i.next()).done);r=!0){var d=l.value;n.push({id:d.phone,value:d.name+" ("+d.phone+")",name:d.name})}}catch(e){t=!0,o=e}finally{try{!r&&i.return&&i.return()}finally{if(t)throw o}}}if(c.updateWheel(0,n),e.bazaar_phone){var s=n.findIndex(function(a){return a.id===e.bazaar_phone});s>-1?(p=[n[s]],$("#managerSelected").html(n[s].value),c.locatePosition(0,s)):$("#managerSelected").html("<p>请选择</p>")}else $("#managerSelected").html("<p>请选择</p>")})}var p=[{id:"1"}];function m(){return"1"!==p[0].id?(e.bazaar_name=p[0].name,e.bazaar_phone=p[0].id,!0):(e.bazaar_name="",e.bazaar_phone="",!1)}var h=/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/,v=$(".id-num"),g=$(".id-number");function b(){return h.test(v.val())?(g.children("span").addClass("glyphicon-ok green"),e.id_number=v.val(),!0):(g.children("span").addClass("glyphicon-remove red"),!1)}v.focus(function(){g.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),v.blur(b);var f=/^1[34578]\d{9}$/,y=$(".area-id-num"),_=$(".area-id-number");y.blur(function(){return f.test(y.val())?(_.children("span").addClass("glyphicon-ok green"),e.bazaar_phone=y.val(),!0):(_.children("span").addClass("glyphicon-remove red"),!1)}),y.focus(function(){_.children("span").removeClass("glyphicon-remove red glyphicon-ok green")});var C=/^1[34578]\d{9}$/,w=$(".phone-num"),x=$(".phone"),k=$(".msgs");function S(){return w.val()&&C.test(w.val())?(x.children("span").addClass("glyphicon-ok green"),e.phone=w.val(),!0):(x.children("span").addClass("glyphicon-remove red"),!1)}w.focus(function(){x.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),w.blur(S),w.on("input propertychange",function(){k.text("点击获取验证码"),C.test($(this).val())?(k.removeClass("disable-btn").addClass("able-btn"),e.phoneCode=!0,e.phone=w.val()):(k.removeClass("able-btn").addClass("disable-btn"),e.phoneCode=!1)});var z=null;k.click(function(){if(e.phoneCode){if(z)return;var a=60;k.text(a+"s后重新获取"),z=window.setInterval(function(){a--,k.text(a+"s后重新获取"),a<0&&(k.text("获取验证码"),k.prop("disabled",!1),window.clearInterval(z),z=null)},1e3),apiType({type:"POST",timeout:5e3},"ajaxSend",{mobile:e.phone}).then(function(e){(e=JSON.parse(e)).success||(swal({button:"关闭",text:""+e.error,type:"error"}),k.text("获取验证码"),window.clearInterval(z),z=null,k.prop("disabled",!1))}).catch(function(){swal({button:"关闭",text:"网络连接异常，请稍后重试",type:"error"}),k.text("获取验证码"),window.clearInterval(z),z=null,k.prop("disabled",!1)})}});var I=$(".msg-num"),O=!1;new EditInfo({type:"area_manager",map:{area_manager_name:"name"},eleArr:[{".manager-name-txt":"area_manager_name"},{".id-num":"id_number"},{".phone-num":"phone"}],address:{ele:"#province",selectedObj:s,list:CITY_CODE},manager:{ele:"#managerSelected",getList:u}}).update(e),$("#sub-btn-manager").click(function(){var n=o(),r=b(),t=S(),l=m();if(6===I.val().length&&n&&l&&r&&t){e.code=I.val();l=!$("#province").text().includes("请选择地区")||(swal({button:"关闭",text:"请选择地区"}),!1);if(O||!l)return;O=!0;var i=a("refuse");apiByObj(i?{path:"/mobile/Register/",service:"modifyInfo",data:e}:{path:"/mobile/wechat/",service:"addarea_Manager",data:e}).then(function(a){1===(a=JSON.parse(a)).status.succeed?(swal({button:"确定",text:"信息提交成功，请您耐心等待审核！"}),setTimeout(function(){Router.navigate("/resources/app/market-manager-info.html",{sid:e.sid})},1e3)):swal({button:"关闭",text:"注册失败，请检查信息！"+a.status.error_desc,type:"error"})}).catch(function(){swal({button:"关闭",text:"注册失败，请检查网络！"})}).then(function(){O=!1})}else swal({button:"关闭",text:"输入有误，请重新输入！"})})});