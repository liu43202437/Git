Vue.component('visiter-zc', {
  template: `
    <div>
      <div v-for="item in peoData">
        <div v-if="item.type === 'input'">
          <list-item :item="item"></list-item>
        </div>
        <div v-if="item.type === 'select'">
          <mobile-select :info="item.info" :list="item.list" :init="initData" @selectChange="selectChange($event,item)"></mobile-select>
        </div>
        <div v-if="item.type === 'verify'">
          <verify-item :item="item"></verify-item>
        </div>
      </div>
      <div class="mt-30">
        <button class="sub-btn" @click="submit">提交</button>
      </div>
    </div>
  `,
  data: function () {
    return {
      peoData: [
        {
          type: 'input',
          lab: '姓名',
          value: '',
          show: false,
          verifyFn: function (value) {
            var reg = /^[\u4e00-\u9fa5]{2,}$/;
            if (!reg.test(value)) {
              return false
            } else {
              return true;
            }
          }
        },
        {
          type: 'input',
          lab: '身份证号',
          value: '',
          show: false,
          verifyFn: function (value) {
            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (!reg.test(value)) {
              return false
            } else {
              return true;
            }
          }
        },
        {
          type: 'select',
          info: {
            lab: '地区',
            title: '请选择地区',
            id: 'province'
          },
          list: CITY_CODE,
          selectItem:null
        },
        {
          type: 'input',
          lab: '所属公司',
          value: '',
          show: false,
          verifyFn: function (value) {
            if (!value) {
              return false
            } else {
              return true;
            }
          }
        },
        {
          type: 'verify',
          lab: '手机号',
          value: '',
          varifyLab:'验证码',
          verifyVal: '',
          show: false,
          verifyFn: function (value) {
            var reg = /^1[34578]\d{9}$/;
            if (!reg.test(value)) {
              return false
            } else {
              return true;
            }
          }
        },
      ],
      submitting: false,
      errText:'信息填写有误',
      refuse:"0",
      initData:{
        id:'',
        code:'',
      },
    }
  },

  created: function() {
    this.refuse = axiosUrlParams('refuse');
    if (this.refuse === "1") {
      this.getinfo();
    }
  },
  methods: {
    getinfo: function () {
      var _vue = this;
      apiRequest('/app/lottery/send_lotteryinfo')
      .then((res)=>{
        if(res.status.succeed === 1 && res.data && res.data.refuse === '1'){
          _vue.refuse = "1";
          _vue.peoData[0].value = res.data.name || '';
          _vue.peoData[1].value = res.data.id_number || '';
          _vue.peoData[3].value = res.data.company || '';
          _vue.peoData[4].value = res.data.phone || '';
          _vue.initData = {
            id: res.data.area_id || '',
            code:res.data.area_code || ''
          }
        }
      })
      .catch(err => {
        console.log(err);
        swal({ button: '关闭',  text: `信息获取失败,请联系客服`});
      })
    },
    selectChange (e,item) {
      console.log('selected change',e);
     item.selectItem = e;
    },
    submit: function () {

      if ( this.submitting) {
        return;
      }

      this.submitting = true;
      // var sid = axiosUrlParams('sid')
      var reqData = {
        name:'',
        componey:'',
        phone:'',
        id_number:'',
        area_id:'',
        area_code:'',
        address:'',
        city:'',
        code:'',
      }
      if( this.bindData(this.peoData,reqData)){
        apiRequest('/app/lottery/enroll_lottery',reqData)
        .then(res=>{
          if (res.status.succeed === 1) {
            swal({  button: '确定',text: `信息提交成功，请您耐心等待审核！`})
            .then(()=>{
              setTimeout(function () {
                Router.navigateWithSid('/resources/app/vc-visiter-manager-info.html',{});
              }, 1000);
            });
          } else {
            swal({ button: '关闭',  text: `注册失败,${res.status.error_desc}`,});
          }
        })
        .catch((err)=>{
          console.log(err);
          swal({button: '关闭',text:"注册失败，请检查网络！"});
        }).then(()=>{
          this.submitting = false;
        })
      } else {
        swal({button:"关闭",text:this.errText});
      };
    },
    bindData: function(peoData,reqData) {
      this.errText = '信息填写有误';
      var verifyName = this.listItemData(peoData[0],reqData,'name');
      var verifyId_number = this.listItemData(peoData[1],reqData,'id_number');
      var verifyComponey = this.listItemData(peoData[3],reqData,'componey');
      var verifyPhone = this.listItemData(peoData[4],reqData,'phone');
      var verifyCode = this.checkCode(peoData[4],reqData);
      var VerifyAddress = this.selectItemData(peoData[2],reqData);

      if (verifyName && verifyId_number && verifyComponey && verifyPhone && verifyCode && VerifyAddress) {
        return true;
      } else {
        this.submitting = false;
        return false;
      }
    },
    listItemData: function(data,Obj, key) {
      if (data.verifyFn(data.value)) {
        Obj[key] = data.value;
        return true;
      } else {
        data.show = true;
        return false;
      }
    },

    checkCode:function(data,Obj) {
      if (data.verifyVal &&  data.verifyVal.length === 6) {
        Obj.code = data.verifyVal;
        return true;
      } else {
        this.errText = "验证码位数有误";
        return false;
      }
    },
    selectItemData: function(data,reqData) {
      if (data.selectItem) {
        reqData.area_id = data.selectItem.id;
        reqData.area_code = data.selectItem.code;
        reqData.address = data.selectItem.value;
        reqData.city = data.selectItem.secondVal;
        return true;
      } else {
        this.errText = "地区选择错误";
        return false;
      }
    }
  }
})
