function clearNextOption(number){
    var eleArr = ['#selectstreet','#selectcounty','#selectcity',]
    for ( var i = 0; i<number; i++) {
        $(eleArr[i]).html('<option value=0 > 全部 </option>');
    }
}

function findChilds(id,arr){
    var childs = null;
    if(arr){
        for(var i=0; i<arr.length; i++){
            if(arr[i].childs && id === arr[i].id){
                return arr[i].childs
            }
        }
        return null
    } else {
        return null;
    }
}

function updatelist(eleId,arr,initCode){
    $(eleId).html('');
    var html = '<option value=0 > 全部 </option>';
    if (arr) {
        for( let item of arr) {
            var options = '<option value="' + item.id + '">' + item.value + '</option>';
            if (initCode && item.id === initCode) {
                options = '<option  selected value="' + item.id + '">' + item.value + '</option>';
            }
            html += options;
        }
    }
    $(eleId).append(html);
}
// 地址选择............................................................................................................