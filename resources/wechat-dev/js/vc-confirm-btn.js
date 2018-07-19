Vue.component('confirm-btn',{
  props: ['type','id','action'],
  template: `
    <div>
      <div class="confirm-btns">
        <button class="no-btn" @click="showrepulse">拒绝</button>
        <button class="yes-btn" @click="showconfirm">通过</button>
      </div>
      <div  v-if="iSShow" class="alert-model" :style="{ height:imgBoxHeight + 'px' }">
        <div class="alert-box">
          <div class="text">
            <textarea v-if="!isConfirm" v-model="reason" placeholder="请填写拒绝理由" cols="30" rows="2"></textarea>
            <div v-if="err" class="err-text">* &nbsp;&nbsp;请填写拒绝理由</div>
            <span v-if="isConfirm">您确定通过该申请吗？</span>
          </div>
          <div v-if="!isConfirm" class="btn">
            <div class="no" @click="close">取消操作</div>
            <div class="yes" @click="repulse" >确定拒绝</div>
          </div>
          <div v-if="isConfirm" class="btn">
            <div class="no" @click="close">取消操作</div>
            <div class="yes" @click="confirm" >确定通过</div>
          </div>
        </div>
      </div>
    </div>
  `,
    data: function() {
      return{
        iSShow: false,
        err: false,
        imgBoxHeight:0,
        isConfirm: true,
        reason: '',
        typeMap: {
          'club': 'manager-review.html',
          'manager': 'market-manager-review.html',
          'area_manager': 'area-manager-review.html'
        }
      }
    },
    methods:{
      show:function() {
        this.iSShow = true;
        this.imgBoxHeight = document.documentElement.clientHeight;
      },
      close: function() {
        this.iSShow = false;
        this.err = false;
      },
      showrepulse:function() {
        this.show();
        this.isConfirm = false;
      },
      showconfirm:function() {
        this.show();
        this.isConfirm = true;
      },
      confirm:function() {
        this.close();
        apiRequest('/wechat/Audit/pass', {
          type: this.type,
          id: this.id
        })
        .then( res => {
          if (res.code === 0){
            var url = '/resources/wechat/'+this.typeMap[this.type];
            Router.navigateWithSid(url, {action:'done'}, true);
          }else {
            swal({
              button: '关闭',
              text: '操作失败,'+res.msg,
            });
          }
        })
        .catch(function (error) {
          console.log(error);
        });

      },
      repulse: function() {
        if(!this.reason){
          this.err = true;
          return;
        }
        this.close();
        apiRequest('/wechat/Audit/nopass', {
          type: this.type,
          id: this.id,
          reason: this.reason
        })
        .then( res => {
          if (res.code === 0){
            var url = '/resources/wechat/'+this.typeMap[this.type];
            Router.navigateWithSid(url, {action:'refuse'}, true)
          }else {
            swal({
              button: '关闭',
              text: '操作失败,'+res.msg,
            });
          }
        })
        .catch(function (error) {
          console.log(error);
        })
      }
    }
})