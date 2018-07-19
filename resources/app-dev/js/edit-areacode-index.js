
function AreaCodeIndex (areaId,areaCode,) {
  this.areaCode = areaCode || "0";
  this.areaId = areaId;
  this.areaIndex = [];
  this.count = 0;
  this.address = '';
  this.cityAddress = '';
  this.nextValue = '';
  this.data = [];
}

AreaCodeIndex.prototype = {
  findCodeIndex: function(id,arrar) {
    var index = -1;
    if (arrar) {
      index =  arrar.findIndex(function(item){
        return item.id === id;
      })
    } 
    return index;
  },
  startCount:function(areaArr){
    var tempArr = areaArr;
    var tempIdex = -1;
    var id;
    switch(this.count){
      case 0: id = this.areaId; break;
      case 1: id = this.areaCode.substr(0,4); break;
      case 2: id = this.areaCode.substr(0,6);break;
      case 3: id =  this.areaCode.substr(0,9);break;
    }
    tempIdex = this.findCodeIndex(id,tempArr);
    if (tempIdex > -1) {
      let item = tempArr[tempIdex];
      let tempItem = {};
      for(let key in item) {
        if (key !=="childs") {
          tempItem[key] = item[key];
        }
      }
      this.data.push(tempItem);
      this.address += ' ' + tempArr[tempIdex].value;
      this.areaIndex.push(tempIdex);
      switch(this.count){
        case 1: this.nextValue = tempArr[tempIdex].value;break;
        case 2: this.cityAddress = tempArr[tempIdex].value;break;
        case 3: this.cityAddress += tempArr[tempIdex].value;break;
      }
      tempArr = tempArr[tempIdex].childs;
      this.count++;
      this.startCount(tempArr);
    } else {
      return;
    };
  }
}

