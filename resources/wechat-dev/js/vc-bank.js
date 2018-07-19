Vue.component('bind-bank', {
  template: `
    <div>
    <div v-if="codeID === '14'" class="notes">
      <svg xmlns="http://www.w3.org/2000/svg" style="height:15px;fill:#F29A76" viewBox="0 0 512 512"><path d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"/></svg>
      <span class="pl-5"> 销售佣金返还银行卡信息绑定</span>
    </div>
      <div v-for="item in peoData">
        <div v-if="item.type === 'text'">
          <div class="lists">
            <div class="lab">{{item.lab}}：</div>
            <div class="value">
              {{item.value}}
            </div>
          </div>
        </div>
        <div v-if="item.type === 'input'">
          <list-item :item="item"></list-item>
        </div>
        <div v-if="item.type === 'select'">
          <mobile-select :info="item.info" :list="item.list" :init="item.initData" @selectChange="selectChange($event,item)"></mobile-select>
        </div>
      </div>
      <div class="check-box" v-if="codeID !=='14'">
        <input class="agree-checkbox" type="checkbox" name="agree" v-model="agreeCheck">
        <span class="ml-5 open-model">我已阅读并同意
          <span style=" color: #2A6853;" @click="openAgreement">《银行代扣款协议》</span>
        </span>
      </div>
      <div class="model-box" v-if="isOpen" :style= "{ height:pageHeight+ 'px' }">
        <agreements-bank :id="codeID" :pageHeight="pageHeight" @close="closeAgreement"></agreements-bank>
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
          type:'text',
          lab:"持卡人",
          value:'',
        },
        {
          type: 'select',
          info: {
            lab: '开户行地区',
            title: '请选择开户行所在地区',
            id: 'area'
          },
          initData:{
            id: '',
          },
          list: CITY_CODE,
          selectItem:null
        },
        {
          type: 'select',
          info: {
            lab: '银行',
            title: '请选择开户行类型',
            id: 'banks'
          },
          initData:{
            id: '',
          },
          list: [
            {
              id:'1026',
              value:"中国银行"
            }
          ],
          selectItem:null
        },
        {
          type: 'select',
          info: {
            lab: '开户行',
            title: '请选择开户行',
            id: 'childBanks'
          },
          initData:{
            id: '',
            value: ''
          },
          list:  [{
            id: 'null',
            value: '暂无银行信息',
          }],
          selectItem:null
        },
        {
          type: 'input',
          lab: '银行卡号',
          value: '',
          show: false,
          verifyFn: function (value) {
            let reg = /\d{15,19}/;
            if (!reg.test(value)) {
              return false
            } else {
              return true;
            }
          }
        },
      ],
      codeID:'',
      submitting: false,
      errText:'信息填写有误',
      refuse:'0',
      cityID:'',
      bankID:'',
      isGetBank: false,
      pageHeight:'',
      isOpen: false,
      agreeCheck: false,
    }
  },

  created: function() {
    this.pageHeight = this.$root.$el.parentElement.parentElement.clientHeight;
    var name = axiosUrlParams('name');
    this.peoData[0].value = unescape(name);
    this.codeID = axiosUrlParams('code');
    this.refuse = axiosUrlParams('refuse');
    if (this.refuse === "1") {
      this.getinfo();
    }
    this.getBankNames();
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
      apiRequest('/app/lottery/send_lotteryinfo')
      .then((res)=>{
        if(res.status.succeed == 1 && res.data && res.data.refuse === '1'){
          _vue.refuse = "1";
          _vue.peoData[2].value = res.data. bank_card_id|| '';
        }
      })
      .catch(err => {
        swal({ button: '关闭',  text: `信息获取失败,请联系客服`});
      })
    },
    selectChange (e,item) {
      console.log(e);
     item.selectItem = e;
     if(item.info.id === 'area') {
       this.isGetBank = true;
        this.cityID = e.code;
     }
     if(item.info.id === 'banks') {
       this.bankID = e.code;
       this.isGetBank = true;
     }

     if(item.info.id === 'childBanks') {
      this.isGetBank = false;
     }

     if (this.cityID && this.bankID && this.isGetBank) {
       this.getBanks(this.cityID,this.bankID);
     }
    },
    getBankNames: function() {
      apiRequest('/public/Common/getBankList')
        .then(res => {
          if(res.data && res.data.length) {
            this.peoData[2].list = res.data;
          }
        }).catch(err => console.log(err));
    },
    getBanks:function(cityId,bankId) {
      var _vue = this;
      var reqData = {
        city_id:cityId,
        bank_id:bankId
      };
      _vue.peoData[3].selectItem = null;
      apiRequest('/public/Common/getBankChilds', reqData)
      .then(res => {
        if(res.code == 0 ) {
          _vue.peoData[3].list = res.data;
        }
        console.log(res);
      })
      .catch(err => {
        console.log(err);
      })
    },
    submit: function () {
      if (this.codeID !== '14' && !this.agreeCheck) {
        swal({
          button: '关闭',
          text: "请阅读并同意《银行代扣款协议》!"
        });
        return;
      }
      let _vue = this;

      if(this.submitting) {
        return;
      }

      this.submitting = true;

      let reqData = {
        name:  this.peoData[0].value,
        bank_areaCode: this.peoData[1].selectItem? this.peoData[1].selectItem.code : '',
        bank_id:this.peoData[2].selectItem? this.peoData[2].selectItem.code : '',
        bank_name: this.peoData[3].selectItem? this.peoData[3].selectItem.data[0].value : '',
        bank_card_id: this.peoData[4].value || ''
      };

      if(!reqData.bank_areaCode){
        swal({
          button: '关闭',
          text: "请选择地区!"
        });
        this.submitting = false;
        return;
      }

      if(!reqData.bank_id){
        swal({
          button: '关闭',
          text: "请选择银行!"
        });
        this.submitting = false;
        return;
      }

      if(!reqData.bank_name) {
        swal({
          button: '关闭',
          text: "请选择开户行!"
        });
        this.submitting = false;
        return;
      }
      var reg = /\d{13,19}/;
      if (!reg.test(reqData.bank_card_id)) {
        swal({
          button: '关闭',
          text: "银行卡号填写有误!"
        });
        this.peoData[4].show = true;
        this.submitting = false;
        return;
      }
      apiRequest('/mobile/Register/bindBankCard',reqData)
      .then( res => {
        if(res.code == 0 ) {
          Router.navigateWithSid(`/resources/wechat/question.${_vue.codeID}.html`,{});
        } else {
          swal({
            button: '关闭',
            text: `#${res.code}: 注册失败，请检查信息！${res.msg}`
          });
        }
      })
      .catch( err => {
        swal({
          button: '关闭',
          text: "注册失败，请检查网络！"
        });
        console.log(err);
      })
      .then(() => {
        _vue.submitting = false;
      });
    }
  }
})
