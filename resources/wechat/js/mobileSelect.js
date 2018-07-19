"use strict";var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};!function(){function e(e,t){return e.getElementsByClassName(t)}function t(e){this.mobileSelect,this.wheelsData=e.wheels,this.jsonType=!1,this.cascadeJsonData=[],this.displayJson=[],this.curValue=null,this.curIndexArr=[],this.cascade=!1,this.startY,this.moveEndY,this.moveY,this.oldMoveY,this.offset=0,this.offsetSum=0,this.oversizeBorder,this.curDistance=[],this.clickStatus=!1,this.isPC=!0,this.init(e)}t.prototype={constructor:t,init:function(t){var i=this;if(i.keyMap=t.keyMap?t.keyMap:{id:"id",value:"value",childs:"childs"},i.checkDataType(),i.renderWheels(i.wheelsData,t.cancelBtnText,t.ensureBtnText),i.trigger=document.querySelector(t.trigger),!i.trigger)return console.error("mobileSelect has been successfully installed, but no trigger found on your page."),!1;if(i.wheel=e(i.mobileSelect,"wheel"),i.slider=e(i.mobileSelect,"selectContainer"),i.wheels=i.mobileSelect.querySelector(".wheels"),i.liHeight=i.mobileSelect.querySelector("li").offsetHeight,i.ensureBtn=i.mobileSelect.querySelector(".ensure"),i.cancelBtn=i.mobileSelect.querySelector(".cancel"),i.grayLayer=i.mobileSelect.querySelector(".grayLayer"),i.popUp=i.mobileSelect.querySelector(".content"),i.callback=t.callback?t.callback:function(){},i.cancel=t.cancel?t.cancel:function(){},i.transitionEnd=t.transitionEnd?t.transitionEnd:function(){},i.initPosition=t.position?t.position:[],i.titleText=t.title?t.title:"",i.connector=t.connector?t.connector:" ",i.triggerDisplayData=void 0===t.triggerDisplayData||t.triggerDisplayData,i.trigger.style.cursor="pointer",i.setStyle(t),i.setTitle(i.titleText),i.checkIsPC(),i.checkCascade(),i.cascade&&i.initCascade(),i.initPosition.length<i.slider.length)for(var n=i.slider.length-i.initPosition.length,s=0;s<n;s++)i.initPosition.push(0);i.setCurDistance(i.initPosition),i.addListenerAll(),i.cancelBtn.addEventListener("click",function(){i.mobileSelect.classList.remove("mobileSelect-show"),i.cancel(i.curIndexArr,i.curValue)}),i.ensureBtn.addEventListener("click",function(){i.mobileSelect.classList.remove("mobileSelect-show");for(var e="",t=0;t<i.wheel.length;t++)t==i.wheel.length-1?e+=i.getInnerHtml(t):e+=i.getInnerHtml(t)+i.connector;i.triggerDisplayData&&(i.trigger.innerHTML=e),i.curIndexArr=i.getIndexArr(),i.curValue=i.getCurValue(),i.callback(i.curIndexArr,i.curValue)}),i.trigger.addEventListener("click",function(){i.mobileSelect.classList.add("mobileSelect-show")}),i.grayLayer.addEventListener("click",function(){i.mobileSelect.classList.remove("mobileSelect-show"),i.cancel(i.curIndexArr,i.curValue)}),i.popUp.addEventListener("click",function(){event.stopPropagation()}),i.fixRowStyle()},setTitle:function(e){var t=this;t.titleText=e,t.mobileSelect.querySelector(".title").innerHTML=t.titleText},setStyle:function(e){var t=this;e.ensureBtnColor&&(t.ensureBtn.style.color=e.ensureBtnColor),e.cancelBtnColor&&(t.cancelBtn.style.color=e.cancelBtnColor),e.titleColor&&(t.title=t.mobileSelect.querySelector(".title"),t.title.style.color=e.titleColor),e.textColor&&(t.panel=t.mobileSelect.querySelector(".panel"),t.panel.style.color=e.textColor),e.titleBgColor&&(t.btnBar=t.mobileSelect.querySelector(".btnBar"),t.btnBar.style.backgroundColor=e.titleBgColor),e.bgColor&&(t.panel=t.mobileSelect.querySelector(".panel"),t.shadowMask=t.mobileSelect.querySelector(".shadowMask"),t.panel.style.backgroundColor=e.bgColor,t.shadowMask.style.background="linear-gradient(to bottom, "+e.bgColor+", rgba(255, 255, 255, 0), "+e.bgColor+")")},checkIsPC:function(){var e=navigator.userAgent.toLowerCase(),t="ipad"==e.match(/ipad/i),i="iphone os"==e.match(/iphone os/i),n="midp"==e.match(/midp/i),s="rv:1.2.3.4"==e.match(/rv:1.2.3.4/i),o="ucweb"==e.match(/ucweb/i),a="android"==e.match(/android/i),r="windows ce"==e.match(/windows ce/i),l="windows mobile"==e.match(/windows mobile/i);(t||i||n||s||o||a||r||l)&&(this.isPC=!1)},show:function(){this.mobileSelect.classList.add("mobileSelect-show")},renderWheels:function(e,t,i){var n=this,s=t||"取消",o=i||"确认";n.mobileSelect=document.createElement("div"),n.mobileSelect.className="mobileSelect",n.mobileSelect.innerHTML='<div class="grayLayer"></div><div class="content"><div class="btnBar"><div class="fixWidth"><div class="cancel">'+s+'</div><div class="title"></div><div class="ensure">'+o+'</div></div></div><div class="panel"><div class="fixWidth"><div class="wheels"></div><div class="selectLine"></div><div class="shadowMask"></div></div></div></div>',document.body.appendChild(n.mobileSelect);for(var a="",r=0;r<e.length;r++){if(a+='<div class="wheel"><ul class="selectContainer">',n.jsonType)for(var l=0;l<e[r].data.length;l++)a+='<li data-id="'+e[r].data[l][n.keyMap.id]+'">'+e[r].data[l][n.keyMap.value]+"</li>";else for(l=0;l<e[r].data.length;l++)a+="<li>"+e[r].data[l]+"</li>";a+="</ul></div>"}n.mobileSelect.querySelector(".wheels").innerHTML=a},addListenerAll:function(){for(var e,t=this,i=0;i<t.slider.length;i++)e=i,t.addListenerWheel(t.wheel[e],e),t.addListenerLi(e)},addListenerWheel:function(e,t){var i=this;e.addEventListener("touchstart",function(){i.touch(event,this.firstChild,t)},!1),e.addEventListener("touchend",function(){i.touch(event,this.firstChild,t)},!1),e.addEventListener("touchmove",function(){i.touch(event,this.firstChild,t)},!1),i.isPC&&(e.addEventListener("mousedown",function(){i.dragClick(event,this.firstChild,t)},!1),e.addEventListener("mousemove",function(){i.dragClick(event,this.firstChild,t)},!1),e.addEventListener("mouseup",function(){i.dragClick(event,this.firstChild,t)},!0))},addListenerLi:function(e){for(var t=this,i=t.slider[e].getElementsByTagName("li"),n=0;n<i.length;n++)!function(n){i[n].addEventListener("click",function(){t.singleClick(this,n,e)},!1)}(n)},checkDataType:function(){"object"==_typeof(this.wheelsData[0].data[0])&&(this.jsonType=!0)},checkCascade:function(){var e=this;if(e.jsonType){for(var t=e.wheelsData[0].data,i=0;i<t.length;i++)if(e.keyMap.childs in t[i]&&t[i][e.keyMap.childs].length>0){e.cascade=!0,e.cascadeJsonData=e.wheelsData[0].data;break}}else e.cascade=!1},generateArrData:function(e){for(var t=[],i=this.keyMap.id,n=this.keyMap.value,s=0;s<e.length;s++){var o={};o[i]=e[s][this.keyMap.id],o[n]=e[s][this.keyMap.value],t.push(o)}return t},initCascade:function(){var e=this;e.displayJson.push(e.generateArrData(e.cascadeJsonData)),e.initPosition.length>0?(e.initDeepCount=0,e.initCheckArrDeep(e.cascadeJsonData[e.initPosition[0]])):e.checkArrDeep(e.cascadeJsonData[0]),e.reRenderWheels()},initCheckArrDeep:function(e){var t=this;if(e&&t.keyMap.childs in e&&e[t.keyMap.childs].length>0){t.displayJson.push(t.generateArrData(e[t.keyMap.childs])),t.initDeepCount++;var i=e[t.keyMap.childs][t.initPosition[t.initDeepCount]];i?t.initCheckArrDeep(i):t.checkArrDeep(e[t.keyMap.childs][0])}},checkArrDeep:function(e){var t=this;e&&t.keyMap.childs in e&&e[t.keyMap.childs].length>0&&(t.displayJson.push(t.generateArrData(e[t.keyMap.childs])),t.checkArrDeep(e[t.keyMap.childs][0]))},checkRange:function(e,t){for(var i,n=this,s=n.displayJson.length-1-e,o=0;o<s;o++)n.displayJson.pop();for(o=0;o<=e;o++)i=0==o?n.cascadeJsonData[t[0]]:i[n.keyMap.childs][t[o]];n.checkArrDeep(i),n.reRenderWheels(),n.fixRowStyle(),n.setCurDistance(n.resetPosition(e,t))},resetPosition:function(e,t){var i,n=t;if(this.slider.length>t.length){i=this.slider.length-t.length;for(var s=0;s<i;s++)n.push(0)}else if(this.slider.length<t.length){i=t.length-this.slider.length;for(s=0;s<i;s++)n.pop()}for(s=e+1;s<n.length;s++)n[s]=0;return n},reRenderWheels:function(){var e=this;if(e.wheel.length>e.displayJson.length)for(var t=e.wheel.length-e.displayJson.length,i=0;i<t;i++)e.wheels.removeChild(e.wheel[e.wheel.length-1]);for(i=0;i<e.displayJson.length;i++)!function(t){var i="";if(e.wheel[t]){for(var n=0;n<e.displayJson[t].length;n++)i+='<li data-id="'+e.displayJson[t][n][e.keyMap.id]+'">'+e.displayJson[t][n][e.keyMap.value]+"</li>";e.slider[t].innerHTML=i}else{var s=document.createElement("div");s.className="wheel",i='<ul class="selectContainer">';for(n=0;n<e.displayJson[t].length;n++)i+='<li data-id="'+e.displayJson[t][n][e.keyMap.id]+'">'+e.displayJson[t][n][e.keyMap.value]+"</li>";i+="</ul>",s.innerHTML=i,e.addListenerWheel(s,t),e.wheels.appendChild(s)}e.addListenerLi(t)}(i)},updateWheels:function(e){var t=this;if(t.cascade){if(t.cascadeJsonData=e,t.displayJson=[],t.initCascade(),t.initPosition.length<t.slider.length)for(var i=t.slider.length-t.initPosition.length,n=0;n<i;n++)t.initPosition.push(0);t.setCurDistance(t.initPosition),t.fixRowStyle()}},updateWheel:function(e,t){var i=this,n="";if(i.cascade)return console.error("级联格式不支持updateWheel(),请使用updateWheels()更新整个数据源"),!1;if(i.jsonType){for(var s=0;s<t.length;s++)n+='<li data-id="'+t[s][i.keyMap.id]+'">'+t[s][i.keyMap.value]+"</li>";i.wheelsData[e]={data:t}}else{for(s=0;s<t.length;s++)n+="<li>"+t[s]+"</li>";i.wheelsData[e]=t}i.slider[e].innerHTML=n,i.addListenerLi(e)},fixRowStyle:function(){for(var e=(100/this.wheel.length).toFixed(2),t=0;t<this.wheel.length;t++)this.wheel[t].style.width=e+"%"},getIndex:function(e){return Math.round((2*this.liHeight-e)/this.liHeight)},getIndexArr:function(){for(var e=[],t=0;t<this.curDistance.length;t++)e.push(this.getIndex(this.curDistance[t]));return e},getCurValue:function(){var e=this,t=[],i=e.getIndexArr();if(e.cascade)for(var n=0;n<e.wheel.length;n++)t.push(e.displayJson[n][i[n]]);else if(e.jsonType)for(n=0;n<e.curDistance.length;n++)t.push(e.wheelsData[n].data[e.getIndex(e.curDistance[n])]);else for(n=0;n<e.curDistance.length;n++)t.push(e.getInnerHtml(n));return t},getValue:function(){return this.curValue},calcDistance:function(e){return 2*this.liHeight-e*this.liHeight},setCurDistance:function(e){for(var t=this,i=[],n=0;n<t.slider.length;n++)i.push(t.calcDistance(e[n])),t.movePosition(t.slider[n],i[n]);t.curDistance=i},fixPosition:function(e){return-(this.getIndex(e)-2)*this.liHeight},movePosition:function(e,t){e.style.webkitTransform="translate3d(0,"+t+"px, 0)",e.style.transform="translate3d(0,"+t+"px, 0)"},locatePosition:function(e,t){this.curDistance[e]=this.calcDistance(t),this.movePosition(this.slider[e],this.curDistance[e]),this.cascade&&this.checkRange(e,this.getIndexArr())},updateCurDistance:function(e,t){this.curDistance[t]=parseInt(e.style.transform.split(",")[1])},getDistance:function(e){return parseInt(e.style.transform.split(",")[1])},getInnerHtml:function(e){var t=this.getIndex(this.curDistance[e]);return this.slider[e].getElementsByTagName("li")[t].innerHTML},touch:function(e,t,i){var n=this;switch((e=e||window.event).type){case"touchstart":n.startY=e.touches[0].clientY,n.oldMoveY=n.startY;break;case"touchend":n.moveEndY=e.changedTouches[0].clientY,n.offsetSum=n.moveEndY-n.startY,n.updateCurDistance(t,i),n.curDistance[i]=n.fixPosition(n.curDistance[i]),n.movePosition(t,n.curDistance[i]),n.oversizeBorder=-(t.getElementsByTagName("li").length-3)*n.liHeight,n.curDistance[i]+n.offsetSum>2*n.liHeight?(n.curDistance[i]=2*n.liHeight,setTimeout(function(){n.movePosition(t,n.curDistance[i])},100)):n.curDistance[i]+n.offsetSum<n.oversizeBorder&&(n.curDistance[i]=n.oversizeBorder,setTimeout(function(){n.movePosition(t,n.curDistance[i])},100)),n.transitionEnd(n.getIndexArr(),n.getCurValue()),n.cascade&&n.checkRange(i,n.getIndexArr());break;case"touchmove":e.preventDefault(),n.moveY=e.touches[0].clientY,n.offset=n.moveY-n.oldMoveY,n.updateCurDistance(t,i),n.curDistance[i]=n.curDistance[i]+n.offset,n.movePosition(t,n.curDistance[i]),n.oldMoveY=n.moveY}},dragClick:function(e,t,i){var n=this;switch((e=e||window.event).type){case"mousedown":n.startY=e.clientY,n.oldMoveY=n.startY,n.clickStatus=!0;break;case"mouseup":n.moveEndY=e.clientY,n.offsetSum=n.moveEndY-n.startY,n.updateCurDistance(t,i),n.curDistance[i]=n.fixPosition(n.curDistance[i]),n.movePosition(t,n.curDistance[i]),n.oversizeBorder=-(t.getElementsByTagName("li").length-3)*n.liHeight,n.curDistance[i]+n.offsetSum>2*n.liHeight?(n.curDistance[i]=2*n.liHeight,setTimeout(function(){n.movePosition(t,n.curDistance[i])},100)):n.curDistance[i]+n.offsetSum<n.oversizeBorder&&(n.curDistance[i]=n.oversizeBorder,setTimeout(function(){n.movePosition(t,n.curDistance[i])},100)),n.clickStatus=!1,n.transitionEnd(n.getIndexArr(),n.getCurValue()),n.cascade&&n.checkRange(i,n.getIndexArr());break;case"mousemove":e.preventDefault(),n.clickStatus&&(n.moveY=e.clientY,n.offset=n.moveY-n.oldMoveY,n.updateCurDistance(t,i),n.curDistance[i]=n.curDistance[i]+n.offset,n.movePosition(t,n.curDistance[i]),n.oldMoveY=n.moveY)}},singleClick:function(e,t,i){var n=this;if(n.cascade){var s=n.getIndexArr();s[i]=t,n.checkRange(i,s)}else n.curDistance[i]=(2-t)*n.liHeight,n.movePosition(e.parentNode,n.curDistance[i])}},"object"==("undefined"==typeof exports?"undefined":_typeof(exports))?module.exports=t:"function"==typeof define&&define.amd?define([],function(){return t}):window.MobileSelect=t}();