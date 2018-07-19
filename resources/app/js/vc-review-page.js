"use strict";var BASE_PAGE={template:'<v-ons-page :infinite-scroll="infiniteScroll">\n    <div class="content" :style= "{ height:pageHeight + \'px\' }" :id="action">\n      <div v-if="loading" class="loading">\n        <v-ons-progress-circular indeterminate></v-ons-progress-circular>\n      </div>\n      <div v-else>\n        <div v-if="len!=0" class="review-page">\n          <div class="review-item" v-for="(item, i) in list" :key="i" @click.prevent="detail(item)">\n            <div class="review-item-content" >\n              <div>申请人&nbsp;:&nbsp;{{item.name}}</div>\n              <div>联系方式&nbsp;:&nbsp;{{item.phone}}</div>\n              <div>地址&nbsp;:&nbsp;{{item.city + item.address}}</div>\n              <div v-if="action===\'refuse\'">拒绝理由&nbsp;:&nbsp;{{item.reason}}</div>\n            </div>\n            <div class="review-item-footer">\n              <div>申请提交&nbsp;:&nbsp;{{item.create_date}}</div>\n              <div v-if="action===\'refuse\'&&item.audit_time">审核拒绝&nbsp;:&nbsp;{{item.audit_time}}</div>\n              <div v-if="action===\'done\'&&item.audit_time">审核通过&nbsp;:&nbsp;{{item.audit_time}}</div>\n            </div>\n          </div>\n          <div class="text-center notice-info" v-if="loadingMore">\n            <v-ons-progress-circular indeterminate class="loading-more"></v-ons-progress-circular>\n          </div>\n          <div class="text-center notice-info" v-else-if="list.length >= len">全部显示完毕</div>\n        </div>\n        <div v-else class="empty-review-page">\n          <div>列表为空</div>\n        </div>\n      </div>\n    </div>\n  </v-ons-page>',props:["action","identity","activeAction"],data:function(){return{ENTRY_NUM:20,list:[],pageIndex:1,entryNum:0,len:0,pageHeight:"",currentIndex:0,updateRequired:!1,loading:!0,loadingMore:!1}},computed:{isActive:function(){return this.activeAction()===this.action}},methods:{infiniteScroll:function(e){var t=this;!this.loading&&!this.loadingMore&&this.list.length<this.len&&(this.pageIndex++,this.loadingMore=!0,this.getList().then(function(i){t.loadingMore=!1,t.list=t.list.concat(i),i.length&&e()}))},detail:function(e,t){localStorage.setItem("action",this.action),localStorage.setItem("currentLen",this.list.length),localStorage.setItem("scrollTop",document.getElementById(this.action).scrollTop);var i={type:e.type,id:e.id,action:this.action};"refuse"===this.action&&(i.id=e.recordId),Router.navigateWithSid("/resources/app/vc-review-info.html",i)},getList:function(){var e=this;return apiRequest("/mobile/Audit/audit_",{identity:this.identity,pageIndex:this.pageIndex,entryNum:this.entryNum,type:this.action}).then(function(t){return t&&!Number(t.code)&&t.data?(e.len=t.data.length,t.data.lists):(e.len=0,swal({button:"关闭",text:"获取信息失败,"+t.msg,type:"error"}),[])}).catch(function(){swal({button:"关闭",text:"获取信息失败,请稍后重试!"})})}},created:function(){var e=this;this.entryNum=this.ENTRY_NUM,this.isActive&&(this.entryNum=localStorage.getItem("currentLen")||this.entryNum,localStorage.removeItem("currentLen")),this.loading=!0,this.getList().then(function(t){e.loading=!1,e.list=t,e.isActive&&(e.entryNum!==e.ENTRY_NUM&&(e.pageIndex=Math.ceil(e.entryNum/e.ENTRY_NUM)),e.entryNum=e.ENTRY_NUM,e.updateRequired=!0)})},mounted:function(){this.pageHeight=this.$el.clientHeight},updated:function(){if(this.updateRequired){var e=localStorage.getItem("scrollTop");e&&(document.getElementById(this.action).scrollTop=e,localStorage.removeItem("scrollTop")),this.updateRequired=!1}}},toReviewPage=Object.assign({},BASE_PAGE),passPage=Object.assign({},BASE_PAGE),rejectedPage=Object.assign({},BASE_PAGE),testPage={template:"#test"};Vue.component("vc-review-page",{props:["identity"],template:'\n    <v-ons-page>\n      <v-ons-tabbar swipeable tab-border position="top"\n        :tabs="listTabs"\n        :index.sync="activeIndex"\n        @postchange="onSwipe($event)"\n      >\n      </v-ons-tabbar>\n    </v-ons-page>\n  ',data:function(){return{activeIndex:0,listTabs:[{label:"待审核",page:toReviewPage,props:{action:"undone",identity:this.identity,activeAction:this.activeAction}},{label:"已通过",page:passPage,props:{action:"done",identity:this.identity,activeAction:this.activeAction}},{label:"已拒绝",page:rejectedPage,props:{action:"refuse",identity:this.identity,activeAction:this.activeAction}}]}},methods:{activeAction:function(){return this.listTabs[this.activeIndex].props.action},onSwipe:function(e){localStorage.setItem("action",this.activeAction()),localStorage.removeItem("currentLen"),localStorage.removeItem("scrollTop")}},created:function(){var e="",t=new RegExp("(^|&)action=([^&]*)(&|$)"),i=window.location.search.substr(1).match(t);i&&(e=unescape(i[2]));var n=localStorage.getItem("action");e?e!==n&&(localStorage.removeItem("currentLen"),localStorage.removeItem("scrollTop")):e=n,e&&(this.activeIndex="done"===e?1:"refuse"===e?2:0)}});