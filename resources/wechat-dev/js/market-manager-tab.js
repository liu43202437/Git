ons.disableIconAutoPrefix()

Vue.component('vc-market-manager-tab', {
  props: ['active'],
  data() {
    return {
      type: 'area_manager',
      activeIndex: this.active,
      tabs: [{
        icon: 'icon-indent',
        label: '公益分',
        url: '/resources/wechat/market-manager-point.html'
      },
      {
        icon: 'icon-stamp',
        label: '审核',
        badge: '',
        url: '/resources/wechat/market-manager-review.html'
      },
      {
        icon: 'icon-me',
        label: '我的',
        url: '/resources/wechat/about-market.html'
      }]
    }
  },
  template: `
  <v-ons-tabbar position="bottom" class="bottom_tabbar" :index="activeIndex">
    <template slot="pages">
      <page-point></page-point>
      <page-review></page-review>
      <page-me></page-me>
    </template>

    <v-ons-tab v-for="(tab, i) in tabs"
      :key="tab.label"
      :icon="tab.icon"
      :label="tab.label"
      :badge="tab.badge"
      :active="activeIndex === i"
      @click.prevent="navigateTo(tab, i)"
      class="icon-f-40"
    ></v-ons-tab>
  </v-ons-tabbar>
  `,
  created: function () {
    apiRequest('/mobile/msgtip/get_noaudit_num', {
      type: this.type
    })
      .then(res => {
        if (res && res.data) {
          let badge = Number(res.data.num);
          this.tabs[1].badge = badge > 99 ? '99+' : (badge > 0 ? badge : undefined);
        }
      })
      .catch(function (error) {
        console.log(error);
      });
  },
  methods: {
    navigateTo: function (tab, index) {
      if (index === this.activeIndex) {
        return;
      }
      Router.navigateWithSid(tab.url, {
        type: this.type
      });
    }
  }
})