Vue.component('agreements',{
  template:`
    <div style="padding:20px;overflow: auto; margin:80px 20px; background:#fff" :style= "{ height:pageHeight-200 + 'px' }">
      <guangxi v-if="id === '7'"></guangxi>
       <hunan v-if="id === '14'"></hunan>
      <div class="py-40">
        <button class="sub-btn" @click="closeAgreement">阅读完毕</button>
      </div>
    </div>
   
  `,
  props:['id','pageHeight'],
  methods:{
    closeAgreement: function() {
      this.$emit('close', true);
    }
  },
  mounted:function() {
    window.addEventListener('popstate', (e) => {
      e.preventDefault();
      console.log('Pop state');
    })
  }
})
