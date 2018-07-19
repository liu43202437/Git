"use strict";Vue.component("shop-zc",{template:'\n    <div>\n      <div v-for="item in peoData">\n        <div v-if="item.type === \'input\'">\n          <list-item :item="item"></list-item>\n        </div>\n        <div v-if="item.type === \'select\'">\n          <mobile-select :info="item.info" :list="item.list" :init="item.initData" @selectChange="selectChange($event,item)"></mobile-select>\n        </div>\n        <div v-if="item.type === \'verify\'">\n          <verify-item :item="item" :show="btnClicked"></verify-item>\n        </div>\n      </div>\n      <div class="check-box" v-if="agreementState">\n        <input class="agree-checkbox" type="checkbox" name="agree" v-model="agreeCheck">\n        <span class="ml-5 open-model">我已阅读并同意\n          <span style=" color: #2A6853;" @click="openAgreement">《卷烟零售户公益票代销协议》</span>\n        </span>\n      </div>\n      <div class="model-box" v-if="isOpen" :style= "{ height:pageHeight+ \'px\' }">\n          <agreements :id="agreementId" :pageHeight="pageHeight" @close="closeAgreement"></agreements>\n        </div>\n      <div class="mt-30">\n        <button class="sub-btn" @click="submit">下一步</button>\n      </div>\n    </div>\n  ',data:function(){return{peoData:[{type:"input",lab:"姓名",value:"",placeholder:"请输入烟草专卖许可证上的名字",show:!1,verifyFn:function(e){return!!/^[\u4e00-\u9fa5]{2,}$/.test(e)}},{type:"input",lab:"所持烟草专卖证许可证编号",value:"",show:!1,verifyFn:function(e){return!!e}},{type:"input",lab:"身份证号",value:"",show:!1,verifyFn:function(e){return!!/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test(e)}},{type:"select",info:{lab:"地区",title:"请选择地区",id:"province"},initData:{id:"",code:""},list:CITY_CODE,selectItem:null},{type:"input",lab:"详细地址",value:"",show:!1,verifyFn:function(e){return!!e}},{type:"select",info:{lab:"客户经理",title:"请选择",id:"manager"},initData:{id:""},list:[{id:"null",value:"暂无人员信息"}],selectItem:null},{type:"input",lab:"您的店铺名称",value:"",show:!1,verifyFn:function(e){return!!e}},{type:"verify",lab:"手机号",value:"",varifyLab:"验证码",verifyVal:"",show:!1,verifyFn:function(e){return!!/^1[34578]\d{9}$/.test(e)}}],btnClicked:!1,submitting:!1,refuse:"0",errText:"信息填写有误",managername:"",id:"",agreementState:!1,isOpen:!1,agreementId:"",agreeCheck:!1,pageHeight:""}},mounted:function(){},created:function(){this.pageHeight=this.$root.$el.parentElement.parentElement.clientHeight,this.refuse=axiosUrlParams("refuse"),this.getinfo()},methods:{openAgreement:function(){this.isOpen=!0},closeAgreement:function(){this.isOpen=!1},getinfo:function(){var e=this;apiRequest("/mobile/Register/getDetail",{type:"club"}).then(function(t){0===t.code&&t.data&&(e.refuse=t.data.refuse,"1"===e.refuse&&(e.id=t.data.id),e.peoData[0].value=t.data.name||"",e.peoData[1].value=t.data.yan_code||"",e.peoData[2].value=t.data.id_number||"",e.peoData[3].initData={id:t.data.area_id,code:t.data.area_code,value:t.data.address},e.peoData[5].initData={id:t.data.manager_id},e.managername=t.data.manager_name,e.peoData[6].value=t.data.view_name||"",e.peoData[7].value=t.data.phone||"",e.btnClicked=!0)}).catch(function(e){console.log(e)})},selectChange:function(e,t){t.selectItem=e,console.log(e),"province"===t.info.id&&(e.detailValue&&(this.peoData[4].value=e.detailValue),"14"===e.id||"7"===e.id?(this.agreementState=!0,this.agreementId=e.id):this.agreementState=!1,this.getmangerList(e.code))},getmangerList:function(e){var t=this;apiRequest("/mobile/msgtip/get_manager_list",{area_code:e,type:"manager"}).then(function(e){if(e.data&&e.data.length){t.peoData[5].list=[];var a=!0,i=!1,n=void 0;try{for(var s,r=e.data[Symbol.iterator]();!(a=(s=r.next()).done);a=!0){var l=s.value;t.peoData[5].list.push({id:l.phone,value:l.name+" ("+l.phone+")",name:l.name})}}catch(e){i=!0,n=e}finally{try{!a&&r.return&&r.return()}finally{if(i)throw n}}}else t.peoData[5].list=[{id:"null",value:"暂无人员信息"}]}).catch(function(e){cosole.log(e)})},submit:function(){var e=this;if(!this.agreementState||this.agreeCheck){if(!this.submitting){this.submitting=!0;var t={name:"",yan_code:"",phone:"",id_number:"",view_name:"",manager_name:"",manager_id_number:"",area_id:"",area_code:"",address:"",city:"",code:"",type:"club",update:!1};if(this.bindData(this.peoData,t)){t.address=t.address+t.detail,delete t.detail;var a="/mobile/wechat/updateShop";"1"===this.refuse&&(a="/mobile/Register/modifyInfo",t.id=this.id,t.update=!0);var i=this;apiRequest(a,t).then(function(e){if(1===e.status.succeed)switch(t.area_id){case"14":Router.navigateWithSid("/resources/wechat/vc-bank.html",{name:escape(t.name),refuse:i.refuse,code:"14"});break;default:Router.navigateWithSid("/resources/wechat/vc-bank.html",{name:escape(t.name),refuse:i.refuse,code:"7"})}else swal({button:"关闭",text:"#"+e.status.error_code+": 注册失败，请检查信息！"+e.status.error_desc})}).catch(function(e){console.log(e),swal({button:"关闭",text:"注册失败，请检查网络！"})}).then(function(){e.submitting=!1})}else swal({button:"关闭",text:this.errText})}}else swal({button:"确定",text:"请阅读并同意《卷烟零售户公益票代销协议》"})},bindData:function(e,t){this.errText="信息填写有误";var a,i=this.listItemData(e[0],t,"name"),n=this.listItemData(e[1],t,"yan_code"),s=this.listItemData(e[2],t,"id_number"),r=this.selectAddressItemData(e[3],t),l=this.listItemData(e[4],t,"detail"),o=this.listItemData(e[6],t,"view_name"),c=this.listItemData(e[7],t,"phone"),d=this.checkCode(e[7],t);return r&&(a=this.selectManagerItemData(e[5],t)),!!(i&&n&&s&&r&&l&&a&&o&&c&&d)||(this.submitting=!1,!1)},listItemData:function(e,t,a){return e.verifyFn(e.value)?(t[a]=e.value,!0):(e.show=!0,!1)},checkCode:function(e,t){return e.verifyVal&&6===e.verifyVal.length?(t.code=e.verifyVal,!0):(this.errText="验证码位数有误",!1)},selectAddressItemData:function(e,t){return e.selectItem?(t.area_id=e.selectItem.id,t.area_code=e.selectItem.code,t.address=e.selectItem.value,t.city=e.selectItem.secondVal,!0):(this.errText="地区选择错误",!1)},selectManagerItemData:function(e,t){return e.selectItem&&e.selectItem.id&&"null"!==e.selectItem.id?(t.manager_name=e.selectItem.name||this.managername,t.manager_id_number=e.selectItem.id,!0):(this.errText="客户经理选择错误",!1)}}});