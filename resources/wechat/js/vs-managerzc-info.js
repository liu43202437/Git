"use strict";Vue.component("manager-info",{template:'\n    <div>\n      <div v-for="item in infoData">\n        <div v-if="item.type===\'text\'&& item.show">\n          <div class="lists">\n            <div class="lab">{{item.lab}}：</div>\n            <div class="value">\n               {{item.value}}\n            </div>\n          </div>\n        </div>\n        <div  v-if="item.type===\'img\'&& item.show">\n          <div class="img-item lists">\n            <div class="item-key lab">\n              <p>{{item.lab}}：</p>\n            </div>\n            <div class="item-value">\n              <div class="img-box">\n                <div class="img-box-item" v-for="img in item.value">\n                  <div class="zoomImage"  @click="showImg(img)">\n                    <img :src="img" alt="暂无图片"  class="img-position font-img">\n                  </div>\n                </div>\n              </div>\n            </div>\n          </div>\n        </div>\n      </div>\n    </div>\n  ',data:function(){return{infoData:[]}},methods:{showImg:function(a){window.location.href=a},getinfo:function(){this.infoData=[];var a=this;apiRequest("/mobile/wechat/getManagerInfo").then(function(t){1===t.status.succeed&&t.data?a.infoData=[{type:"text",lab:"客户经理姓名",value:t.data.manager_name?t.data.manager_name:"",show:!!t.data.manager_name},{type:"text",lab:"客户经理身份证号",value:t.data.id_number?t.data.id_number:"",show:!!t.data.id_number},{type:"text",lab:"客户经理工作手机号",value:t.data.phone?t.data.phone:"",show:!!t.data.phone},{type:"text",lab:"省份",value:t.data.sheng?t.data.sheng:"",show:!!t.data.sheng},{type:"text",lab:"城市",value:t.data.city?t.data.city:"",show:!!t.data.city},{type:"text",lab:"地址",value:t.data.address?t.data.address:"",show:!!t.data.address}]:swal({button:"关闭",text:"用户信息获取失败,"+t.status.error_desc})}).catch(function(a){console.log(a),swal({button:"关闭",text:"信息获取失败,请联系客服"})})}},created:function(){this.getinfo()}});