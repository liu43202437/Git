Vue.component('manager-info', {
  template: `
    <div>
      <div v-for="item in infoData">
        <div v-if="item.type==='text'&& item.show">
          <div class="lists">
            <div class="lab">{{item.lab}}：</div>
            <div class="value">
               {{item.value}}
            </div>
          </div>
        </div>
        <div  v-if="item.type==='img'&& item.show">
          <div class="img-item lists">
            <div class="item-key lab">
              <p>{{item.lab}}：</p>
            </div>
            <div class="item-value">
              <div class="img-box">
                <div class="img-box-item" v-for="img in item.value">
                  <div class="zoomImage"  @click="showImg(img)">
                    <img :src="img" alt="暂无图片"  class="img-position font-img">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  data: function () {
    return {
      infoData: []
    }
  },
  methods: {
    showImg:function(src){
      window.location.href = src;
    },
    getinfo: function () {
      this.infoData = [];
      var _vue = this;
      apiRequest('/mobile/wechat/getManagerInfo')
        .then((res) => {
          if (res.status.succeed === 1 && res.data) {
            _vue.infoData = [
              {
                type: 'text',
                lab: '客户经理姓名',
                value: res.data.manager_name ? res.data.manager_name : '',
                show: res.data.manager_name ?true : false
              },
              {
                type: 'text',
                lab: '客户经理身份证号',
                value: res.data.id_number ? res.data.id_number : '',
                show: res.data.id_number ?true : false
              },

              {
                type: 'text',
                lab: '客户经理工作手机号',
                value: res.data.phone ? res.data.phone : '',
                show: res.data.phone ?true : false
              },
              {
                type: 'text',
                lab: '省份',
                value: res.data.sheng ? res.data.sheng : '',
                show: res.data.sheng ?true : false
              },
              {
                type: 'text',
                lab: '城市',
                value: res.data.city ? res.data.city : '',
                show: res.data.city ?true : false
              },
              {
                type: 'text',
                lab: '地址',
                value: res.data.address ? res.data.address : '',
                show: res.data.address ?true : false
              },
            ];
          } else {
            swal({
              button: '关闭',
              text: `用户信息获取失败,${res.status.error_desc}`,
            });
          }
        })
        .catch(err => {
          console.log(err);
          swal({
            button: '关闭',
            text: `信息获取失败,请联系客服`
          });
        })
    }
  },

  created: function () {
    this.getinfo();
  }
})