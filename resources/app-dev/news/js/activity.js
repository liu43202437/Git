$(function () {

    var itemIndex = 0;

    var tabLoadEndArray = [false, false, false];
    var tabLenghtArray = [28, 0, 47];
    var tabScroolTopArray = [0, 0, 0];
    var pages = 1;


//取新闻banner数据
    function loadBanner() {
        var vbanner;
        $.ajax({
            //提交数据的类型 POST GET
            type: "POST",
            async: false,
            //提交的网址
            url: "/mobile/loader/banner_list",
            //提交的数据
            // data: {},
            //返回数据的格式
            datatype: "json",
            //在请求之前调用的函数
            // beforeSend:function(){console.log(peoData);},
            //成功返回之后调用的函数
            success: function (data) {
                data = JSON.parse(data);
                if (data.status.succeed === 1) {
                    vbanner = data;
                } else {
                    alert("获取新闻banner失败，" + data.status.error_desc);
                }
            },
            //调用出错执行的函数
            error: function (err) {
                //请求出错处理
                alert("注册失败，请检查网络！" + err);
            }
        });
        return vbanner;
    }

    var bannerData = loadBanner();
    var bannerResult = '';
    if (bannerData && bannerData.data.banners.length > 0) {
        for (var n = 0; n < bannerData.data.banners.length; n++) {
            bannerResult
                += ''
                + '<div class="swiper-slide banner-parent">'
                + '<a href="/portal/contents/' + bannerData.data.banners[n].item_info + '">'
                + '<img src="' + bannerData.data.banners[n].image + '"/>'
                + '<div class="banner-tit">' + bannerData.data.banners[n].title + '</div>'
                + '</a>'
                + '</div>';
        }

        $('.swiper-wrapper').eq(itemIndex).append(bannerResult);

        var swiper = new Swiper('.swiper-container', {
            autoplay: 2000,//可选选项，自动滑动
            pagination: '.swiper-pagination',
            paginationClickable: true
        });

    } else {
        return;
    }


// 获取新闻列表数据
    function loadData(pages) {
        var vdata;
        $.ajax({
            //提交数据的类型 POST GET
            type: "post",
            async: false,
            //提交的网址
            url: "/mobile/contents/get_list/article",
            //提交的数据
            data: {
                "json": '{"pagination":{"count":6,"page": ' + pages + '}}',
                "category_id": 4
            },
            //返回数据的格式
            datatype: "json",
            //在请求之前调用的函数
            // beforeSend:function(){console.log(peoData);},
            //成功返回之后调用的函数
            success: function (data) {
                data = JSON.parse(data);
                if (data.status.succeed === 1) {
                    vdata = data
                } else {
                    alert("获取新闻失败，" + data.status.error_desc);
                }
            },
            //调用出错执行的函数
            error: function (err) {
                //请求出错处理
                alert("注册失败，请检查网络！");
            }
        });
        return vdata;
    }


    // dropload
    var dropload = $('.khfxWarp').dropload({
        scrollArea: window,
        domDown: {
            domClass: 'dropload-down',
            domRefresh: '<div class="dropload-refresh">上拉加载更多</div>',
            domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
            domNoData: '<div class="dropload-noData">已无数据</div>'
        },
        loadDownFn: function (me) {

            if (tabLoadEndArray[itemIndex]) {  //false
                // me.resetload();
                me.lock();
                me.noData();
                me.resetload();
                return;
            }
            var getDatas = loadData(pages);
            var result = '';
            if (getDatas.data.items.length > 0) {
                for (var n = 0; n < getDatas.data.items.length; n++) {
                    result
                        += ''
                        + '<a href="' + getDatas.data.items[n].url + '">'
                        + '    <hgroup class="khfxRow">'
                        + '      <div class="mid">'
                        + '        <img class="photo" src="' + getDatas.data.items[n].image + '" >'
                        + '        <div class="new-tit">' + getDatas.data.items[n].title + '</div><br/> '
                        + '        <header>' + getDatas.data.items[n].content_date + '</header> '
                        + '      </div>'
                        + '    </hgroup>'
                        + '</a>';
                }


                $('.khfxPane').eq(itemIndex).append(result);
            } else {
                tabLoadEndArray[itemIndex] = true;
            }
            pages++;
            me.resetload();
        }
    });


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
});
