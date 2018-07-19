Vue.component('mobile-select', {
  template: `
    <div class="lists">
      <div class="lab">
        {{info.lab}}：
      </div>
      <div class="value selec-box" :id="info.id">
        <p>{{info.title}}</p>
      </div>
      <div class="">
        <svg style="height:15px;fill:#000000a5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"/></svg>
      </div>
    </div>
  `,
  props: ['list', 'info', 'init', ],

  data: function () {
    return {
      show: true,
      selectObj: null,
      selectData: {
        id: '',
        code: '',
        secondVal: '',
        value: '',
        detailValue: '',
        name: '',
      },
      clear:false
    }
  },
  watch: {
    init: function (newValue, oldValue) {
      if (newValue.id && newValue.code) {
        this.initDataFn(newValue.id, newValue.code, newValue.value);
      }　　
    },
    list: function (n, o) {
      this.selectObj.updateWheel(0,n);
      this.selectObj.locatePosition(0,0);
      this.$el.querySelector('.selec-box').innerHTML = `<p>${this.info.title}</p>`;
      this.initDataFn(this.init.id, this.init.value, this.init.value);
    }
  },
  methods: {
    initDataFn: function (id, code, value) {
      if (!id) {
        return;
      }
      var initData = new AreaCodeIndex(id, code);
      initData.startCount(this.list);
      for (var i = 0; i < initData.areaIndex.length; i++) {
        this.selectObj.locatePosition(i, initData.areaIndex[i]);
      }
      console.log(initData)
      this.$el.querySelector('.selec-box').innerHTML=`${initData.address || this.info.title}`;
      this.selectData = {
        id: initData.areaId || '',
        code: initData.areaCode || '',
        secondVal: initData.nextValue || '',
        value: initData.cityAddress || '',
        detailValue: value ? value.replace(initData.cityAddress, "") : '',
        data: initData.data
      }
      this.$emit('selectChange', this.selectData);
    }
  },
  mounted: function () {
    var _vue = this;
    this.selectObj = new MobileSelect({
      trigger: '#' + this.info.id,
      title: this.info.title,
      wheels: [{
        data: this.list
      }],
      position: [0],
      callback: function (indexArr, data) {
        _vue.selectData.data = data;
        _vue.selectData.value = '';
        _vue.selectData.detailValue = '';
        _vue.selectData.id = data[0] ? data[0].id : '';
        _vue.selectData.name = data[0] ? data[0].name : '';
        _vue.selectData.secondVal = data[1] ? data[1].value : '';
        for (var i = 0; i < data.length; i++) {
          _vue.selectData.code = data[i].id;
          if (i > 1) {
            _vue.selectData.value += data[i].value;
          }
        }
        _vue.$emit('selectChange', _vue.selectData);
      }
    })
  },
});