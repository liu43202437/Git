function Question () {

}
Question.prototype = {
  getdata: function() {
    var _this = this;
    apiByObj({
      path:'/mobile/Register/',
      service: 'getQuestion',
      data:{
        sid:urlParams('sid')
      }
    }).then(function(data){
      data = JSON.parse(data);
      if (data.code === 0) {
        if (data.data) {
          _this.bindData(data.data);
        }
      }
    })
  },
  bindData: function(data) {
    $(`input[name='answer[73]'][value='${data[1]}']`).attr("checked",true); 
    $(`input[name='answer[74]'][value='${data[2]}']`).attr("checked",true); 
    $(`input[name='answer[75]'][value='${data[3]}']`).attr("checked",true); 
    $(`input[name='answer[76]'][value='${data[4]}']`).attr("checked",true); 
    $(`input[name='answer[77]'][value='${data[5]}']`).attr("checked",true); 
    $(`input[name='answer[78]'][value='${data[6]}']`).attr("checked",true); 
  }
}
var question = new Question();
question.getdata();
