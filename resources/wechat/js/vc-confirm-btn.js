"use strict";Vue.component("confirm-btn",{props:["type","id","action"],template:'\n    <div>\n      <div class="confirm-btns">\n        <button class="no-btn" @click="showrepulse">拒绝</button>\n        <button class="yes-btn" @click="showconfirm">通过</button>\n      </div>\n      <div  v-if="iSShow" class="alert-model" :style="{ height:imgBoxHeight + \'px\' }">\n        <div class="alert-box">\n          <div class="text">\n            <textarea v-if="!isConfirm" v-model="reason" placeholder="请填写拒绝理由" cols="30" rows="2"></textarea>\n            <div v-if="err" class="err-text">* &nbsp;&nbsp;请填写拒绝理由</div>\n            <span v-if="isConfirm">您确定通过该申请吗？</span>\n          </div>\n          <div v-if="!isConfirm" class="btn">\n            <div class="no" @click="close">取消操作</div>\n            <div class="yes" @click="repulse" >确定拒绝</div>\n          </div>\n          <div v-if="isConfirm" class="btn">\n            <div class="no" @click="close">取消操作</div>\n            <div class="yes" @click="confirm" >确定通过</div>\n          </div>\n        </div>\n      </div>\n    </div>\n  ',data:function(){return{iSShow:!1,err:!1,imgBoxHeight:0,isConfirm:!0,reason:"",typeMap:{club:"manager-review.html",manager:"market-manager-review.html",area_manager:"area-manager-review.html"}}},methods:{show:function(){this.iSShow=!0,this.imgBoxHeight=document.documentElement.clientHeight},close:function(){this.iSShow=!1,this.err=!1},showrepulse:function(){this.show(),this.isConfirm=!1},showconfirm:function(){this.show(),this.isConfirm=!0},confirm:function(){var i=this;this.close(),apiRequest("/wechat/Audit/pass",{type:this.type,id:this.id}).then(function(t){if(0===t.code){var e="/resources/wechat/"+i.typeMap[i.type];Router.navigateWithSid(e,{action:"done"},!0)}else swal({button:"关闭",text:"操作失败,"+t.msg})}).catch(function(i){console.log(i)})},repulse:function(){var i=this;this.reason?(this.close(),apiRequest("/wechat/Audit/nopass",{type:this.type,id:this.id,reason:this.reason}).then(function(t){if(0===t.code){var e="/resources/wechat/"+i.typeMap[i.type];Router.navigateWithSid(e,{action:"refuse"},!0)}else swal({button:"关闭",text:"操作失败,"+t.msg})}).catch(function(i){console.log(i)})):this.err=!0}}});