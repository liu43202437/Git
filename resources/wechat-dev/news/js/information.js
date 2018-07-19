$(function () {

  var itemIndex = 0;
  var tabLoadEndArray = [false, false, false];
  var tabLenghtArray = [28, 0, 47];
  var tabScroolTopArray = [0, 0, 0];
  var pages = 1;

  /* ...........................轮播图................................... */
 
  loadBanner();
  function loadBanner() {
    apiByObj({
        service: 'banner_list',
        path: '/mobile/loader/'
      })
      .then(function (data) {
        data = JSON.parse(data);
        if (data.status.succeed === 1) {
          loadBannerHtml(data);
        } else {
          alert("获取新闻banner失败，" + data.status.error_desc);
        }
      }).catch(function (err) {
        alert("注册失败，请检查网络！" + err);
      })
  }

  function loadBannerHtml(bannerData) {
    if (bannerData.data.banners.length > 0) {
      var bannerResult = '';
      for (var n = 0; n < bannerData.data.banners.length; n++) {
        bannerResult
          += '' +
          '<div class="swiper-slide banner-parent">' +
          '<a href="/portal/contents/' + bannerData.data.banners[n].item_info + '">' +
          '<img src="' + bannerData.data.banners[n].image + '"/>' +
          '<div class="banner-tit">' + bannerData.data.banners[n].title + '</div>' +
          '</a>' +
          '</div>';
      }

      $('.swiper-wrapper').eq(itemIndex).append(bannerResult);

      var swiper = new Swiper('.swiper-container', {
        autoplay: 2000, //可选选项，自动滑动
        pagination: '.swiper-pagination',
        paginationClickable: true
      });
    } else {
      return;
    }
  }

  $('.tabHead span').on('click', function () {
    tabScroolTopArray[itemIndex] = $(window).scrollTop();
    var $this = $(this);
    itemIndex = $this.index();
    $(window).scrollTop(tabScroolTopArray[itemIndex]);

    $(this).addClass('active').siblings('.tabHead span').removeClass('active');
    $('.tabHead .border').css('left', $(this).offset().left + 'px');
    $('.khfxPane').eq(itemIndex).show().siblings('.khfxPane').hide();

    if (!tabLoadEndArray[itemIndex]) {
      dropload.unlock();
      dropload.noData(false);
    } else {
      dropload.lock('down');
      dropload.noData();
    }
    dropload.resetload();
  });


  /* .................新闻列表加载....................................................... */

  var req = {
    category_id: 1,
    loadmore: false,
  }
  $('.new-list').html('');
  req = loadMoreInit(req);
  getNewList(req);

  function getNewList(req) {
    apiByObj({
        service: 'article',
        path: '/mobile/contents/get_list/',
        data: req
      })
      .then(function (data) {
        data = JSON.parse(data);
        if (data.status.succeed === 1) {
          if (data.data.items.length) {
            checkLoadMore('', data.paginated.total, getNewList);
            updateListHtml(data);
          }
        } else {
          alert("获取新闻失败，" + data.status.error_desc);
        }
      })
  }

  function updateListHtml(getDatas) {
    var result = '';
    for (var n = 0; n < getDatas.data.items.length; n++) {
      result
        += '' +
        '<a href="' + getDatas.data.items[n].url + '">' +
        '    <hgroup class="khfxRow">' +
        '      <div class="mid">' +
        '        <img class="photo" src="' + getDatas.data.items[n].image + '" >' +
        '        <div class="new-tit">' + getDatas.data.items[n].title + '</div><br/> ' +
        '        <header>' + getDatas.data.items[n].content_date + '</header> ' +
        '      </div>' +
        '    </hgroup>' +
        '</a>';
    }
    $('.new-list').append(result);
  }
});