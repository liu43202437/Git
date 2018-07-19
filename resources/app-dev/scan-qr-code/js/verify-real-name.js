
$(function(window){

  var peoData = {
    "sid": "", //sid
    "id_number": "", //身份证号
    "name": "", //姓名
  };
  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
  // alert(GetQueryString("sid"));
  peoData.sid = GetQueryString("sid");

  // js验证姓名
  var regName = /^[\u4e00-\u9fa5]{2,4}$/;
  var $name = $(".name-txt");
  var $nameIcon = $(".name");
  $name.focus(function () {
    $nameIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
  });
  $name.blur(nameValidation);

  function nameValidation() {
    if (!regName.test($name.val())) {
      // alert('真实姓名填写有误');
      $nameIcon.children("span").addClass("glyphicon-remove red");
      return false;
    } else {
      // alert('身份证号填写正确');
      $nameIcon.children("span").addClass("glyphicon-ok green");
      peoData.name = $name.val();
      return true;
    }
  }

    // js验证身份证号
    var regIdNo = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
    var $idNo = $(".id-num");
    var $idNoIcon = $(".id-number");
  
    $idNo.blur(idNoValidation);
    $idNo.focus(function () {
      $idNoIcon.children("span").removeClass("glyphicon-remove red glyphicon-ok green");
    });
    function idNoValidation() {
      if (!regIdNo.test($idNo.val())) {
        $idNoIcon.children("span").addClass("glyphicon-remove red");
        return false;
      } else {
        $idNoIcon.children("span").addClass("glyphicon-ok green");
        peoData.id_number = $idNo.val();
        return true;
      }
    }

    $('.js-sub-btn').click(function(){
      var a = nameValidation();
      var b = idNoValidation();

      if ( a && b) {
        //TODO: 提交数据
      }
    })
}(window));