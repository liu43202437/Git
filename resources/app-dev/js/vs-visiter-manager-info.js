Vue.component('visiter-info',{
  template: `
    <div>
      <div class="lists" v-for="item in infoData">
        <div class="lab">{{item.lab}}：</div>
        <div class="value">
           {{item.value}}
        </div>
      </div>
    </div>
  `,
  data: function(){
    return {
      infoData: []
    }
  },
  methods: {
    getinfo: function () {
      this.infoData = [];
      var _vue = this;
      apiRequest('/app/lottery/send_lotteryinfo')
      .then((res)=>{
        if(res.status.succeed === 1 && res.data){
          _vue.infoData = [
            {
              lab:'姓名',
              value: res.data.name ? res.data.name : '',
            },
            {
              lab:'身份证号',
              value: res.data.id_number ? res.data.id_number : '',
            },
            {
              lab:'地区',
              value: res.data.address ? res.data.address : '',
            },
            {
              lab:'所属公司',
              value: res.data.company ? res.data.company : '',
            },
            {
              lab:'手机号',
              value: res.data.phone ? res.data.phone : '',
            },
          ];
          if (res.data.refuse === '1') {
            _vue.infoData.push({
              lab:'拒绝原因',
              value: res.data.reason
            })
          }

        } else {
          swal({ button: '关闭',  text: `用户信息获取失败,${res.status.error_desc}`,});
        }
      })
      .catch(err => {
        console.log(err);
        swal({ button: '关闭',  text: `信息获取失败,请联系客服`});
      })
    }
  },

  created: function() {
    this.getinfo();
  }
})