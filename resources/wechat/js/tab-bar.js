"use strict";Vue.component("tab-bar",{props:["active","badge","tabs"],template:'\n<div class="menu">\n  <v-ons-tabbar>\n    <v-ons-tab v-for="(tab, i) in tabs"\n      :key="tab.label"\n      :icon="tab.icon"\n      :label="tab.label"\n      :badge="tab.badge"\n      :active = "active === i"\n      @click="navigateTo(tab, i)"\n      class="icon-f-40"\n      ></v-ons-tab>\n  </v-ons-tabbar>\n</div>\n',methods:{navigateTo:function(a,n){var t,e,i=(t=new RegExp("(^|&)"+"sid"+"=([^&]*)(&|$)"),null!=(e=window.location.search.substr(1).match(t))?unescape(e[2]):null);n!==this.active&&Router.navigate(a.url,{sid:i})}}});