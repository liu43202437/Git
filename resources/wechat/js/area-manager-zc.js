"use strict";$(function(){var e={sid:"47eccd96f3a5199018538298e5e7b50e",phone:"",id_number:"",area_manager_name:"",area_id:"",area_code:"",city:"",address:"",id:"",type:"bazaar_manager",update:!1,phoneCode:!1};function n(e){var n=new RegExp("(^|&)"+e+"=([^&]*)(&|$)"),a=window.location.search.substr(1).match(n);return null!=a?unescape(a[2]):null}e.sid=n("sid");var a=$(".manager-name-txt"),t=$(".manager-name"),r=/^[\u4e00-\u9fa5]{2,4}$/;function o(){return r.test(a.val())?(t.children("span").addClass("glyphicon-ok green"),e.area_manager_name=a.val(),!0):(t.children("span").addClass("glyphicon-remove red"),!1)}a.focus(function(){t.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),a.blur(o);var l=/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/,i=$(".id-num"),s=$(".id-number");function d(){return l.test(i.val())?(s.children("span").addClass("glyphicon-ok green"),e.id_number=i.val(),!0):(s.children("span").addClass("glyphicon-remove red"),!1)}i.focus(function(){s.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),i.blur(d);var c=CITY_CODE,u=new MobileSelect({trigger:"#province",title:"请选择地区",wheels:[{data:c}],position:[2],transitionEnd:function(e,n){},callback:function(n,a){console.log(a),e.area_id=a[0]?a[0].id:"",e.city=a[1]?a[1].value:"";var t=a[2]?a[2].value:"",r=a[3]?a[3].value:"";e.address=t+r,e.area_code=a[a.length-1].id}}),p=/^1[34578]\d{9}$/,m=$(".phone-num"),h=$(".phone"),v=$(".msgs");function g(){return m.val()&&p.test(m.val())?(h.children("span").addClass("glyphicon-ok green"),!0):(h.children("span").addClass("glyphicon-remove red"),!1)}m.focus(function(){h.children("span").removeClass("glyphicon-remove red glyphicon-ok green")}),m.blur(g),m.on("input propertychange",function(){v.text("点击获取验证码"),p.test($(this).val())?(v.removeClass("disable-btn").addClass("able-btn"),e.phoneCode=!0,e.phone=m.val()):(v.removeClass("able-btn").addClass("disable-btn"),e.phoneCode=!1)});var b=null;v.click(function(){if(e.phoneCode){if(b)return;var n=60;v.text(n+"s后重新获取"),b=window.setInterval(function(){n--,v.text(n+"s后重新获取"),n<0&&(v.text("获取验证码"),v.prop("disabled",!1),window.clearInterval(b),b=null)},1e3),apiType({type:"POST",timeout:5e3},"ajaxSend",{mobile:e.phone}).then(function(e){(e=JSON.parse(e)).success||(swal({button:"关闭",text:""+e.error,type:"error"}),v.text("获取验证码"),window.clearInterval(b),b=null,v.prop("disabled",!1))}).catch(function(){swal({button:"关闭",text:"网络连接异常，请稍后重试",type:"error"}),v.text("获取验证码"),window.clearInterval(b),b=null,v.prop("disabled",!1)})}});var f=$(".msg-num"),y=!1;new EditInfo({type:"bazaar_manager",map:{area_manager_name:"name"},eleArr:[{".manager-name-txt":"area_manager_name"},{".id-num":"id_number"},{".phone-num":"phone"}],address:{ele:"#province",selectedObj:u,list:CITY_CODE}}).update(e),$("#sub-btn-manager").click(function(){var a=o(),t=d(),r=g();if(6===f.val().length&&a&&t&&r){e.code=f.val();var l=!$("#province").text().includes("请选择地区")||(swal({button:"关闭",text:"请选择地区"}),!1);if(y||!l)return;y=!0;var i=n("refuse");apiByObj("1"===i?{path:"/mobile/Register/",service:"modifyInfo",data:e}:{path:"/mobile/wechat/",service:"add_bazaar_Manager",data:e}).then(function(n){1===(n=JSON.parse(n)).status.succeed?(swal({button:"确定",text:"信息提交成功，请您耐心等待审核！"}),setTimeout(function(){Router.navigate("/resources/wechat/area-manager-info.html",{sid:e.sid})},1e3)):swal({button:"关闭",text:"注册失败，请检查信息！"+n.status.error_desc,type:"error"})}).catch(function(){swal({button:"关闭",text:"注册失败，请检查网络！"})}).then(function(){y=!1})}else swal({button:"关闭",text:"输入有误，请重新输入！"})})});