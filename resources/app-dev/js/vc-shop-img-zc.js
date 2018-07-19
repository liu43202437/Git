Vue.component('load-img', {
  template: `
  <div class="load-box">
      <div class="background-img">
        <img src="images/zc-bg.png" alt="">
      </div>
      <div class="load-img-box">
          <div class="img-position" v-for="item in imgArr" >
            <div class="img-container">
              <img  width="100%" :src="item.bg_src" alt="">
              <input type="file"  accept="image/*" @change="loadFile($event,item)" />
            </div>
          </div>
      </div>

      <div class="mt-20">
        <button  @click="nextStep" class="sub-btn">去填写</button>
      </div>

      <div v-if="loading" class="loading-box">
          <div class="loaders">
            <div class="loader">
              <div class="loader-inner ball-pulse">
                <div></div>
                <div></div>
                <div></div>
              </div>
            </div>
          </div>  
        <div>
          图片正在上传
        </div>
      </div>
    </div>
  `,

  data: function() {
    return {
      imgArr:[
        {
          name: 'front',
          bg_src:'images/zc-front.png',
          src: '',
          type:'id_number',
        },
        {
          name: 'back',
          bg_src:'images/zc-back.png',
          src: '',
          type:'id_number',
        },
        {
          name: 'yan_code',
          bg_src:'images/zc-yan.png',
          src:'',
          type:'yan_code',
        }
      ],
      loading: false,
      refuse:"",
    }
  },
  created: function() {
    this.initImgArrData();
    this.refuse = axiosUrlParams('refuse');
  },
  methods: {
    initImgArrData: function () {
      var _vue = this;
      apiRequest('/mobile/Register/getClubImage')
      .then((res)=>{
        if (res.code === 0) {
          if (res.data.front) {
            _vue.imgArr[0].src =  _vue.imgArr[0].bg_src = res.data.front;
          }
          if (res.data.back) {
            _vue.imgArr[1].src =  _vue.imgArr[1].bg_src = res.data.back;
          }
          if (res.data.yan_image) {
            _vue.imgArr[2].src =  _vue.imgArr[2].bg_src = res.data.yan_image;
          }
        }
      })
      .catch(err => {
        console.log(err)
      })
    },
    nextStep: function() {
      var reqData = {};
      for ( let item of this.imgArr) {
        if (item.src) {
          reqData[item.name] = item.src;
        }else {
          swal({button:'关闭', text: "请完成照片上传！"})
          return ;
        }
      }
      var _vue = this;
      apiRequest('/mobile/Register/uploadClubImage',reqData)
      .then((res)=> {
        if (res.code === 0) {
          Router.navigateWithSid('/resources/app/shopzc.html', {refuse:_vue.refuse});
        }else {
          swal({button:'关闭', text: res.mag});
        } 
      })
      .catch(err => {
        console.log(err);
        swal({button:'关闭', text: '信息提交失败,请联系客服'});
      });

      
    },
    loadFile: function(e,item){
      var imageBase64  = '';
      var file = e.target.files[0];
      if (file) {
        this.loading = true;
      } else {
        return;
      }     
      var reader = new FileReader();
      reader.readAsDataURL(file);
      var _vue = this;
      reader.onloadend = function () {
        imageBase64 = reader.result;
        if (file.size/1024/1024 > 1) {
          // swal({button:'关闭', text:'图片太大，正在压缩'});
          _vue.compressImage(imageBase64,{width:1240},item);
        } else {
          _vue.uploadImage(imageBase64,item);
        }
      }
    },

    uploadImage: function(url,item) {
      var reqData = {
        type: item.type,
        base64: encodeURIComponent(url),
      };
      var _vue = this;
      apiRequest('/mobile/Register/uploadBase64',reqData)
      .then(res => {
        if (res.data) {
          item.bg_src = url;
          item.src =  res.data;
        } else {
          item.src = '';
          swal({button:'关闭', text: '图片上传失败,'+ res.msg});
        }
        _vue.loading = false;
      })
      .catch(err => {
        item.src = '';
        swal({button:'关闭', text: '图片上传失败,请联系客服'});
        _vue.loading = false;
        console.log(err);
      });
    },
    compressImage: function(path,obj,item) {
      var _vue = this;
      var img = new Image();
      img.src = path;
      img.onload = function(){
          var that = this;
          // 默认按比例压缩
          var w = that.width,
              h = that.height,
              scale = w / h;
          w = obj.width || w;
          h = obj.height || (w / scale);
          var quality = 0.8;  // 默认图片质量为0.7
          //生成canvas
          var canvas = document.createElement('canvas');
          var ctx = canvas.getContext('2d');
          // 创建属性节点
          var anw = document.createAttribute("width");
          anw.nodeValue = w;
          var anh = document.createAttribute("height");
          anh.nodeValue = h;
          canvas.setAttributeNode(anw);
          canvas.setAttributeNode(anh);
          ctx.drawImage(that, 0, 0, w, h);
          // 图像质量
          if(obj.quality && obj.quality <= 1 && obj.quality > 0){
              quality = obj.quality;
          }
          // quality值越小，所绘制出的图像越模糊
          var base64 = canvas.toDataURL('image/jpeg', quality);
          // 回调函数返回base64的值
          // console.log(base64);
          _vue.uploadImage(base64,item);
      }
    }
  }
})