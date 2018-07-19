Vue.component('manager-info', {
  props: ['type','id','action'],
  template: `
    <ul class="list">
      <li class="list-item" v-for="item in list">
        <div>
          <span>{{item.key}}：</span>
          <span>{{item.value}}</span>
        </div>
      </li>
    </ul>
  `,
  data: function () {
    return {
      list: [],
      imgs: [],
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
              key: '身份证号',
              value: res.data.id_number
            },
            {
              key: '手机号',
              value: res.data.phone
            },
            {
              key: '省份',
              value: res.data.province
            },
            {
              key: '地址',
              value: res.data.city + res.data.address
            },
            {
              key: '申请时间',
              value: res.data.create_date
            }];

            if (res.data.audit_time){
              this.list.push({
                key: '审核时间',
                value: res.data.audit_time
              })
            }
            if (res.data.reason) {
              this.list.push({
                key: '拒绝原因',
                value: res.data.reason
              });
            }
        }else {
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