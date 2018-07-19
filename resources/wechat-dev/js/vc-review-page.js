
var BASE_PAGE = {
  template: `<v-ons-page :infinite-scroll="infiniteScroll">
    <div class="content" :style= "{ height:pageHeight + 'px' }" :id="action">
      <div v-if="loading" class="loading">
        <v-ons-progress-circular indeterminate></v-ons-progress-circular>
      </div>
      <div v-else>
        <div v-if="len!=0" class="review-page">
          <div class="review-item" v-for="(item, i) in list" :key="i" @click.prevent="detail(item)">
            <div class="review-item-content" >
              <div>申请人&nbsp;:&nbsp;{{item.name}}</div>
              <div>联系方式&nbsp;:&nbsp;{{item.phone}}</div>
              <div>地址&nbsp;:&nbsp;{{item.city + item.address}}</div>
              <div v-if="action==='refuse'">拒绝理由&nbsp;:&nbsp;{{item.reason}}</div>
            </div>
            <div class="review-item-footer">
              <div>申请提交&nbsp;:&nbsp;{{item.create_date}}</div>
              <div v-if="action==='refuse'&&item.audit_time">审核拒绝&nbsp;:&nbsp;{{item.audit_time}}</div>
              <div v-if="action==='done'&&item.audit_time">审核通过&nbsp;:&nbsp;{{item.audit_time}}</div>
            </div>
          </div>
          <div class="text-center notice-info" v-if="loadingMore">
            <v-ons-progress-circular indeterminate class="loading-more"></v-ons-progress-circular>
          </div>
          <div class="text-center notice-info" v-else-if="list.length >= len">全部显示完毕</div>
        </div>
        <div v-else class="empty-review-page">
          <div>列表为空</div>
        </div>
      </div>
    </div>
  </v-ons-page>`,
  props: ['action', 'identity', 'activeAction'],
  data() {
    return {
      ENTRY_NUM: 20,
      list: [],
      pageIndex: 1,
      entryNum: 0,
      len: 0,
      pageHeight: '',
      currentIndex: 0,
      updateRequired: false,
      loading: true,
      loadingMore: false
    }
  },
  computed: {
    isActive: function() {
      return this.activeAction() === this.action;
    }
  },
  methods: {
    infiniteScroll:function(done){
      if (!this.loading && !this.loadingMore && this.list.length < this.len) {
        this.pageIndex++;
        this.loadingMore = true;
        this.getList()
        .then(list => {
          this.loadingMore = false;
          this.list = this.list.concat(list);
          if (list.length) {
            done();
          }
        })
      }
    },
    detail(item, index) {
      // cache current page detail
      localStorage.setItem('action', this.action);
      localStorage.setItem('currentLen', this.list.length);
      localStorage.setItem('scrollTop', document.getElementById(this.action).scrollTop);

      let param = {
        type: item.type,
        id: item.id,
        action: this.action
      };
      if (this.action === 'refuse') {
        param.id = item.recordId;
      }
      Router.navigateWithSid('/resources/wechat/vc-review-info.html', param);
    },
    getList() {
      return apiRequest('/mobile/Audit/audit_', {
        identity: this.identity,
        pageIndex: this.pageIndex,
        entryNum: this.entryNum,
        type: this.action
      }).then(data => {
        if (data && !Number(data.code) && data.data) {
          this.len = data.data.length;
          return data.data.lists;
        } else {
          this.len = 0;
          swal({button: '关闭', text:`获取信息失败,${data.msg}`, type: 'error' });
          return [];
        }
      }).catch(function(){
          swal({button: '关闭', text:`获取信息失败,请稍后重试!`});
      });

    }
  },
  created() {
    this.entryNum = this.ENTRY_NUM;
    
    if (this.isActive) {
      // get detail from localStorage
      this.entryNum = localStorage.getItem('currentLen') || this.entryNum;
      localStorage.removeItem('currentLen');
    }
    this.loading = true;
    this.getList().then(list => {
      this.loading = false;
      this.list = list;
      if (this.isActive) {
        if (this.entryNum !== this.ENTRY_NUM) {
          // get normal pageIndex
          this.pageIndex = Math.ceil(this.entryNum/this.ENTRY_NUM);
        }
        // return to normal enntry num
        this.entryNum = this.ENTRY_NUM;
        this.updateRequired = true;
      }
    });
  },
  mounted: function(){
    this.pageHeight = this.$el.clientHeight;
  },
  updated() {
    if (this.updateRequired) {
      let scrollTop = localStorage.getItem('scrollTop');
      if (scrollTop) {
        document.getElementById(this.action).scrollTop = scrollTop;
        localStorage.removeItem('scrollTop');
      }
      this.updateRequired = false;
    }
  }
}

var toReviewPage = Object.assign({}, BASE_PAGE);
var passPage = Object.assign({}, BASE_PAGE);
var rejectedPage = Object.assign({}, BASE_PAGE);
const testPage = {
  template: '#test'
};
Vue.component('vc-review-page', {
  props: ['identity'],
  template: `
    <v-ons-page>
      <v-ons-tabbar swipeable tab-border position="top"
        :tabs="listTabs"
        :index.sync="activeIndex"
        @postchange="onSwipe($event)"
      >
      </v-ons-tabbar>
    </v-ons-page>
  `,
  data() {
    return {
      activeIndex: 0,
      listTabs: [
        {
          label: '待审核',
          page: toReviewPage,
          props: {
            action: 'undone',
            identity: this.identity,
            activeAction: this.activeAction
          }
        },
        {
          label: '已通过',
          page: passPage,
          props: {
            action: 'done',
            identity: this.identity,
            activeAction: this.activeAction
          }
        },
        {
          label: '已拒绝',
          page: rejectedPage,
          props: {
            action: 'refuse',
            identity: this.identity,
            activeAction: this.activeAction
          }
        },
      ],
    }
  },
  methods: {
    activeAction() {
      return this.listTabs[this.activeIndex].props.action;
    },
    onSwipe(e) {
      localStorage.setItem('action', this.activeAction());
      localStorage.removeItem('currentLen');
      localStorage.removeItem('scrollTop');
    }
  },
  created() {
    let action = '';
    // get action from url
    let reg = new RegExp("(^|&)" + 'action' + "=([^&]*)(&|$)");
    let r = window.location.search.substr(1).match(reg);
    if (r) {
      action = unescape(r[2]);
    }
    // get action from localstorage
    let localAction = localStorage.getItem('action');
    if (!action) {
      action = localAction;
    } else if (action !== localAction) {
      localStorage.removeItem('currentLen');
      localStorage.removeItem('scrollTop');
    }
    
    if (action) {
      this.activeIndex = (action === 'done') ? 1:
                        action === 'refuse' ? 2 : 0;
    }
  }
})
