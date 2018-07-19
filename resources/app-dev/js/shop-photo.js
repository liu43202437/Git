var timeOutEvent = null; // 定时器
var $event, // 点击图片的事件暂存对象
  selecteImgs = [], // 选择删除图片id数组
  imgData = []; // 获取服务器图片的src数组


getImgData();

function getImgData() {
  imgData = [
    {
    id: 0,
    src: 'images/test.png'
   }
  ];

  updateImgList(imgData);

  //TODO: use api to get imgData
}

function updateImgList(imgArr) {
  var imgList = ``;
  for (let item of imgData) {
    imgList += `
    <div data-id="${item.id}" class="zoomImage">
      <img class="img-position" src="${item.src}" alt="">
      <div class="modal-box">
        <div class="mycheck box-position">
          <input type="checkbox">
          <label></label>
        </div>
      </div>
    </div>
    `;
  }
  $('.photo-box').html(imgList);
}


// 上传图片
// 获取 图片base64位
// function getBase64(thisFiles) {
//   var reader = new FileReader();
//   reader.readAsDataURL(thisFiles);
//   reader.onload = function (e) {
//     var imgId = imgData.length;
//     imgData.push({
//       src: this.result,
//       id: imgId
//     });
//     updateImgList(imgData);
//   }
// };

// send form data
// function sendData(file) {
//   var formData = new FormData();
//   formData.append('file', file);

//   console.log(formData);

// };

// 开始上传
// var upload = document.getElementById('uploadImg');
// upload.addEventListener('change', function(){
//   var file = upload.files[0];
//   if (!upload.value.match(/.jpg|.jepg|.gif|.png|.bmp/i)) {
//     console.log('上传图片的格式不正确，请重新选择~');
//   } else {
//     getBase64(file);
//     sendData(file);
//   }
// });

$('.upload-box').click(function () {
  wx.chooseImage({
    count: 3, // 默认9
    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
    success: function (res) {
      var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
      for (let src of localIds) {
        var imgId = imgData.length;
        imgData.push({
          src: src,
          id: imgId
        });
      }
      updateImgList(imgData);
    }
  });


});

// 点击图片
$('.photo-box').on({
  touchstart: function (e) {
    $event = e;
    // console.log(e.target);
    // var isInput = $(e.target).is("input[type='checkbox']");
    var haveLongPress = $(e.target).hasClass('show') || $(e.target).parent().parent().hasClass('show');
    if (!haveLongPress) {
      timeOutEvent = setTimeout(longPress, 500);
    } else {
      selectedImg(e.target);
    }
  },
  touchmove: function () {
    clearTimeout(timeOutEvent);
    timeOutEvent = null;
  },
  touchend: function (e) {
    if (timeOutEvent) {
      var src = e.target.src;
      var urls = [];
      for (let item of imgData) {
        urls.push(item.src);
      }
      wx.previewImage({
        current: src, // 当前显示图片的http链接
        urls: urls // 需要预览的图片http链接列表
      });
    }
    clearTimeout(timeOutEvent);
    timeOutEvent = null;
  }
});

// 删除图片
$('.js-delete').click(function () {
  selecteImgs = [];
  $('.zoomImage>.modal-box').each((i, e) => {
    $(e).removeClass('show');
    var isSelected = $(e).find("input[type='checkbox']").prop("checked");
    if (isSelected) {
      var imgId = $(e).parent().data('id');
      selecteImgs.push(imgId);
    }
    $('.footer').addClass('hidden');
    $('.upload-box').removeClass('hidden');
  })

  for (let id of selecteImgs) {
    imgData.splice(id, 1);
  }
  updateImgList(imgData);
});

// 取消删除
$('.js-cancel').click(function () {
  $('.zoomImage>.modal-box').each((i, e) => {
    $(e).removeClass('show');
    $(e).removeClass('selected');
    $(e).find("input[type='checkbox']").prop("checked", false);
    $('.footer').addClass('hidden');
    $('.upload-box').removeClass('hidden');
  })
});

// 长按处理函数
function longPress() {
  var modals = $($event.currentTarget).find('.zoomImage>.modal-box');
  modals.each((i, e) => {
    $(e).addClass('show');
  });
  var longPressModalBox = $($event.target).find('.modal-box').get(0);
  selectedImg(longPressModalBox);
  $('.footer').removeClass('hidden');
  $('.upload-box').addClass('hidden');
  clearTimeout(timeOutEvent);
  timeOutEvent = null;
}

// 选择删除图片
function selectedImg(target) {
  var document = $(target);
  var isInput = document.is("input[type='checkbox']");
  // if(isInput){
  //   target =  document.parent().parent().get(0);
  //   document = $(target);
  // }
  // var hasSelected = document.hasClass('selected');
  // if (!hasSelected) {
  //   document.addClass('selected');
  //   document.find("input[type='checkbox']").prop("checked", true);
  // } else {
  //   document.removeClass('selected');
  //   document.find("input[type='checkbox']").prop("checked", false);
  // }
  if (!isInput) {
  } else {
    if ($(target).prop('checked')) {
      $(target).parent().parent().removeClass('selected');
    } else {
      $(target).parent().parent().addClass('selected');
    }
  }
}