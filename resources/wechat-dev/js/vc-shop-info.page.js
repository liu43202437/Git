Vue.component('img-list-item', {
  props: ['imgs','title'],
  template: `
  <div>
    <li v-if="imgs.length" class="img-item">
        <div class="item-key">
          <span>{{title}}：</span>
        </div>
        <div class="item-value">
          <div class="img-box">
            <div class="img-box-item" v-for='img in imgs' @click="showImg(img)">
              <div class="zoomImage">
                <img :src="img" alt=""  class="img-position" >
              </div>
            </div>
          </div>
        </div>
    </li>
    <div  v-if="iSShow" class="img-modle"  :style="{ height:imgBoxHeight + 'px' }">
      <img :src="showImgSrc" alt="" class="selef-img-height">
      <div class="close-btn" @click="closeImg">
        <img src="images/close.png" width="30" height="30" alt=""  class="img-position">
      </div>
    </div>
  </div>
  `,
  data: function () {
    return {
      iSShow: false,
      showImgSrc: '',
      imgBoxHeight: 0
    }
  },
  mounted () {  
    this.init();  
  }, 
  methods: {
    init:function() {
     var el = document.domain
    },
    showImg: function (src) {
      window.location.href = src;
      // this.iSShow = true;
      // this.showImgSrc = src;
      // this.imgBoxHeight = document.documentElement.clientHeight;
    },
    closeImg: function () {
      this.iSShow = false;
    }
  }
})

Vue.component('shop-info', {
  props: ['type','id','action'],
  template: `
    <ul class="list">
      <div class="lists" v-for="item in list">
        <div class="lab">{{item.key}}：</div>
        <div class="value">
          {{item.value}}
        </div>
      </div>
      <img-list-item :imgs="imgs" :title="'身份证照'"></img-list-item>
      <img-list-item :imgs="img" :title="'烟草证照'"></img-list-item>
    </ul>
  `,
  data: function () {
    return {
      list: [],
      imgs: [],
      img:[]
    }
  },
  created: function () {
    this.list = [];

    var resData = {
      type: this.type,
      id: this.id
    }
    if (this.action === 'refuse') {
      resData['refuse'] = true;
    }else {
      delete resData.refuse;
    }
    apiRequest('/mobile/Audit/getDetail',resData)
      .then(res => {
        if (res.data) {
          this.list = [{
              key: '申请人',
              value: res.data.name,
            },
            {
              key: '所持烟草专卖许可证编号',
              value: res.data.yan_code
            },
            {
              key: '身份证号',
              value: res.data.id_number
            },
            {
              key: '手机号',
              value: res.data.phone
            },
            {
              key: '店铺名称',
              value: res.data.view_name
            },
            {
              key: '省份',
              value: res.data.province
            },
            {
              key: '地址',
              value: res.data.city + res.data.address
            }
          ];
          if(res.data.bank_name) {
            this.list.push({
              key: '开户行',
              value: res.data.bank_name
            });
          }
          if(res.data.bank_card_id){
            this.list.push({
              key: '银行卡号',
              value: res.data.bank_card_id
            });
          }
          this.list.push({
            key: '申请时间',
            value: res.data.create_date
          });

          if (res.data.audit_time){
            this.list.push({
              key: '审核时间',
              value: res.data.audit_time
            });
          }
          if (res.data.reason) {
            this.list.push({
              key: '拒绝原因',
              value: res.data.reason
            });
          }
          this.imgs = [];
          this.img = [];
          if (res.data.id_number_image){
            this.imgs = [
              res.data.id_number_image.front,
              res.data.id_number_image.back
            ];
            if ( res.data.id_number_image.yan_image){
              this.img = [
                res.data.id_number_image.yan_image,
              ];
            }
          }
        }else{
          swal({
            button: '关闭',
            text: '获取信息失败,'+res.msg,
          });
        }
      })
      .catch(function (error) {
        console.log(error);
      });
  }
})