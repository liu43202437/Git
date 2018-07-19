// http://yan/mobile/Register/getDetail

function EditInfo(options) {
  this.options = options
  this.map = options.map;
  this.serviceData = {};
  this.selectData = {};
  this.original = {};
}

EditInfo.prototype = {
  update: function(peoData) {
    this.initData(peoData, this.options.type)
  },
  bindData: function(original) {
    original.phoneCode = true;
    original.update = true;
    for ( var key in original) {
      if (this.map[key]) {
        original[key] = this.serviceData[this.map[key]];
      }else if (this.serviceData[key]){
        original[key] = this.serviceData[key];
      }
    }
    this.original = original;
    console.log(this.original,this.serviceData);
  },
  bindHtml: function () {
    for (var item of this.options.eleArr) {
      var key = Object.keys(item)[0];
      $(key).val(this.original[item[key]]);
    }
    $('.msgs').removeClass("disable-btn").addClass("able-btn");
  },

  bindAdress: function(address,id,code) {
    this.selectData = new AreaCodeIndex(id,code);
    this.selectData.startCount(address.list);
    var IndexArr = this.selectData.areaIndex;
    for (var i = 0; i <  IndexArr.length; i++) {
      address.selectedObj.locatePosition(i,IndexArr[i]);
    } 
    $(address.ele).html(this.selectData.address);
    if (address.detail) {
      var detailText = this.original.address.replace(this.selectData.cityAddress,"");
      $(address.detail).val(detailText);
    }
  },

  bindManger: function(manager,code) {
    manager.getList(code);
  },
  initData: function(original, type) {
    var _this = this;
    apiByObj({
      path: '/mobile/Register/',
      service: 'getDetail',
      data: {
        sid: original.sid,
        type: type
      }
    }).then(function (data) {
      var newData = JSON.parse(data).data;
      if (newData) {
        _this.serviceData = newData;
        _this.bindData(original);
        _this.bindHtml();

        if (_this.options.address) {
          _this.bindAdress(_this.options.address,newData.area_id,newData.area_code);
        }

        if (_this.options.manager) {
          _this.bindManger(_this.options.manager,newData.area_code);
        }
      }
    }).catch(function(err) {
      console.log(err);
    })
  }
}