"use strict";$(function(e){var a={id_number:"",area_manager_name:"",area_manager_id:"",phone:"",sheng:"",city:"",address:""};var n,t,r=(n=new RegExp("(^|&)"+"sid"+"=([^&]*)(&|$)"),null!=(t=e.location.search.substr(1).match(n))?unescape(t[2]):null);null!==r||""===r?api("getBazaarManagerInfo",{sid:r}).then(function(e){if(1===(e=JSON.parse(e)).status.succeed){var n=e.data;t=n,a.id_number=t.id_number,$(".id_numbers").find(".info>p").text(a.id_number),a.area_manager_name=t.area_manager_name,$(".manager-name").find(".info>p").text(a.area_manager_name),a.area_manager_id=t.area_manager_id,$(".manager-id").find(".info>p").text(a.area_manager_id),a.phone=t.phone,$(".phones").find(".info>p").text(a.phone),a.city=t.city,$(".citys").find(".info>p").text(a.city),a.address=t.address,$(".addresses").find(".info>p").text(a.address),a.sheng=t.sheng,$(".shengs").find(".info>p").text(a.sheng)}else swal({button:"关闭",text:"获取区域经理信息失败,"+e.status.error_desc,type:"error"});var t}).catch(function(){swal({button:"关闭",text:"获取区域经理信息失败，请稍后重试！"})}):swal({button:"关闭",text:"获取sid失败,请重试！"}),e.active_menu="me",e.sid=r}(window));