Vue.component('shop-zc', {
  template: `
    <div>
      <div v-for="item in peoData">
        <div v-if="item.type === 'input'">
          <list-item :item="item"></list-item>
        </div>
        <div v-if="item.type === 'select'">
          <mobile-select :info="item.info" :list="item.list" :init="item.initData" @selectChange="selectChange($event,item)"></mobile-select>
        </div>
        <div v-if="item.type === 'verify'">
          <verify-item :item="item" :show="btnClicked"></verify-item>
        </div>
      </div>
      <div class="check-box" v-if="agreementState">
        <input class="agree-checkbox" type="checkbox" name="agree" v-model="agreeCheck">
        <span class="ml-5 open-model">我已阅读并同意
          <span style=" color: #2A6853;" @click="openAgreement">《卷烟零售户公益票代销协议》</span>
        </span>
      </div>
      <div class="model-box" v-if="isOpen" :style= "{ height:pageHeight+ 'px' }">
          <agreements :id="agreementId" :pageHeight="pageHeight" @close="closeAgreement"></agreements>
        </div>
      <div class="mt-30">
        <button class="sub-btn" @click="submit">下一步</button>
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
          placeholder:'请输入烟草专卖许可证上的名字',
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
          lab: '所持烟草专卖证许可证编号',
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
          initData:{
            id: '',
            code: ''
          },
          list: CITY_CODE,
          selectItem:null
        },
        {
          type: 'input',
          lab: '详细地址',
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
          type: 'select',
          info: {
            lab: '客户经理',
            title: '请选择',
            id: 'manager'
          },
          initData:{
            id: '',
          },
          list: [{
            id: 'null',
            value: '暂无人员信息'
          }],
          selectItem:null
        },
        {
          type: 'input',
          lab: '您的店铺名称',
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
      btnClicked:false,
      submitting: false,
      refuse: '0',
      errText:'信息填写有误',
      managername:'',
      id:"",
      agreementState: false,
      isOpen:false,
      agreementId:'',
      agreeCheck: false,
      pageHeight:'',
    }
  },

  mounted: function(){

  },
  created: function() {
    this.pageHeight = this.$root.$el.parentElement.parentElement.clientHeight;
    this.refuse = axiosUrlParams('refuse');
    this.getinfo();

  },
  methods: {
    openAgreement: function() {
      this.isOpen = true;
    },
    closeAgreement: function() {
      this.isOpen = false;
    },
    getinfo: function () {
      var _vue = this;
      apiRequest('/mobile/Register/getDetail',{type:'club'})
      .then((res)=>{
        if(res.code === 0 && res.data){
          _vue.refuse = res.data.refuse;
          if(_vue.refuse === '1') {
            _vue.id = res.data.id;
          }
          _vue.peoData[0].value = res.data.name || '';
          _vue.peoData[1].value = res.data.yan_code || '';
          _vue.peoData[2].value = res.data.id_number || '';
          _vue.peoData[3].initData = {
            id:res.data.area_id,
            code:res.data.area_code,
            value:res.data.address
          };
          _vue.peoData[5].initData = {
            id:res.data.manager_id,
          };

          _vue.managername = res.data.manager_name;

          _vue.peoData[6].value = res.data.view_name || '';
          _vue.peoData[7].value = res.data.phone || '';
          _vue.btnClicked= true;
        }
      })
      .catch(err => {
        console.log(err);
      })
    },
    selectChange (e,item) {
     item.selectItem = e;
     console.log(e);
     if(item.info.id === 'province') {
       if(e.detailValue) {
         this.peoData[4].value = e.detailValue;
       }
       if(e.id === '14' || e.id === '7') {
         this.agreementState = true;
         this.agreementId = e.id;
       } else {
        this.agreementState = false;
       }
       this.getmangerList(e.code);
     }
    },
    getmangerList: function(code) {
      var _vue = this;
      var reqData = {
        area_code: code,
        type:'manager',
      };
      apiRequest('/mobile/msgtip/get_manager_list',reqData)
      .then(res => {
        if (res.data && res.data.length) {
          _vue.peoData[5].list = [];
          for (let item of res.data) {
            _vue.peoData[5].list.push({
              id: item.phone,
              value: item.name + " (" + item.phone + ")",
              name: item.name
            })
          }
        }else {
          _vue.peoData[5].list = [
            {
              id: 'null',
              value: '暂无人员信息'
            }
          ];
        }
      })
      .catch(err => {
        cosole.log(err);
      });



    },
    submit: function () {
      if (this.agreementState && !this.agreeCheck) {
        swal({
          button:'确定',
          text:'请阅读并同意《卷烟零售户公益票代销协议》'
        })
        return;
      }
      if ( this.submitting) {
        return;
      }

      this.submitting = true;
      // var sid = axiosUrlParams('sid')
      var reqData = {
        name:'',
        yan_code:'',
        phone:'',
        id_number:'',
        view_name: '',
        manager_name: '',
        manager_id_number: '',
        area_id:'',
        area_code:'',
        address:'',
        city:'',
        code:'',
        type:'club',
        update: false,
      }
      if( this.bindData(this.peoData,reqData)){
        reqData['address'] = reqData['address'] + reqData['detail'];
        delete reqData['detail'];
        let url = '/mobile/wechat/updateShop';
        if(this.refuse === '1') {
          url = '/mobile/Register/modifyInfo';
          reqData['id'] = this.id;
          reqData['update'] = true;
        }
        var _vue = this;
        apiRequest(url,reqData)
        .then(res=>{
          if (res.status.succeed === 1) {
            // if (reqData.area_id === '14') {
            //   Router.navigateWithSid('/resources/wechat/vc-bank.html', {name:escape(reqData.name),refuse:_vue.refuse});
            // } else {
            //   Router.navigateWithSid('/resources/wechat/question.7.html',{});
            // }
            switch (reqData.area_id) {
              case '14': Router.navigateWithSid('/resources/wechat/vc-bank.html', {name:escape(reqData.name),refuse:_vue.refuse,code: '14'});break;
              default: Router.navigateWithSid('/resources/wechat/vc-bank.html',{name:escape(reqData.name),refuse:_vue.refuse,code: '7'});break;
            }
          } else {
            swal({
              button: '关闭',
              text: `#${res.status.error_code}: 注册失败，请检查信息！${res.status.error_desc}`
            });
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
      var verifyYanCode = this.listItemData(peoData[1],reqData,'yan_code')
      var verifyId_number = this.listItemData(peoData[2],reqData,'id_number');
      var VerifyAddress = this.selectAddressItemData(peoData[3],reqData);
      var verifyDetailAddress = this.listItemData(peoData[4],reqData,'detail');
      var verifyManager;
      var verifyShopName =  this.listItemData(peoData[6],reqData,'view_name');
      var verifyPhone = this.listItemData(peoData[7],reqData,'phone');
      var verifyCode = this.checkCode(peoData[7],reqData);

      if (VerifyAddress){
        verifyManager = this.selectManagerItemData(peoData[5],reqData);
      }

      if (verifyName && 
        verifyYanCode &&
        verifyId_number &&
        VerifyAddress &&
        verifyDetailAddress &&
        verifyManager &&
        verifyShopName &&
        verifyPhone && 
        verifyCode) {
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
    selectAddressItemData: function(data,reqData) {
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
    },
    selectManagerItemData: function(data,reqData) {
      if (data.selectItem && data.selectItem.id && data.selectItem.id !== 'null') {
        reqData['manager_name'] = data.selectItem.name || this.managername;
        reqData['manager_id_number'] = data.selectItem.id;
        return true;
      } else {
        this.errText = "客户经理选择错误";
        return false;
      }
    },
  }
})
