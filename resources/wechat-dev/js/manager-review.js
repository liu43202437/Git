// Blank components just for tabbar.
Vue.component('page-point', {
  template: '<v-ons-page></v-ons-page>'
})

Vue.component('page-me', {
  template: '<v-ons-page></v-ons-page>'
})

// Actual page component
Vue.component('page-review', {
  template: '#page-review'
})

new Vue({
  el: '#app'
});
