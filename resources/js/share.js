var UA = {
    parse: function () {
        var e = navigator.userAgent, t = '';
        var n = this.os = {}, i = this.browser = {}, r = e.match(/Web[kK]it[\/]{0,1}([\d.]+)/), o = e.match(/(Android);?[\s\/]+([\d.]+)?/), a = !!e.match(/\(Macintosh\; Intel /), s = e.match(/(iPad).*OS\s([\d_]+)/), u = e.match(/(iPod)(.*OS\s([\d_]+))?/), c = !s && e.match(/(iPhone\sOS)\s([\d_]+)/), l = e.match(/(webOS|hpwOS)[\s\/]([\d.]+)/), f = /Win\d{2}|Windows/.test(t), d = e.match(/Windows Phone ([\d.]+)/), h = l && e.match(/TouchPad/), p = e.match(/Kindle\/([\d.]+)/), m = e.match(/Silk\/([\d._]+)/), g = e.match(/(BlackBerry).*Version\/([\d.]+)/), v = e.match(/(BB10).*Version\/([\d.]+)/), w = e.match(/(RIM\sTablet\sOS)\s([\d.]+)/), y = e.match(/PlayBook/), b = e.match(/Chrome\/([\d.]+)/) || e.match(/CriOS\/([\d.]+)/), j = e.match(/Firefox\/([\d.]+)/), x = e.match(/\((?:Mobile|Tablet); rv:([\d.]+)\).*Firefox\/[\d.]+/), q = e.match(/MSIE\s([\d.]+)/) || e.match(/Trident\/[\d](?=[^\?]+).*rv:([0-9.].)/), A = !b && e.match(/(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/), T = A || e.match(/Version\/([\d.]+)([^S](Safari)|[^M]*(Mobile)[^S]*(Safari))/);
        (i.webkit = !!r) && (i.version = r[1]), o && (n.android = !0, n.version = o[2]), c && !u && (n.ios = n.iphone = !0, n.version = c[2].replace(/_/g, ".")), s && (n.ios = n.ipad = !0, n.version = s[2].replace(/_/g, ".")), u && (n.ios = n.ipod = !0, n.version = u[3] ? u[3].replace(/_/g, ".") : null), d && (n.wp = !0, n.version = d[1]), l && (n.webos = !0, n.version = l[2]), h && (n.touchpad = !0), g && (n.blackberry = !0, n.version = g[2]), v && (n.bb10 = !0, n.version = v[2]), w && (n.rimtabletos = !0, n.version = w[2]), y && (i.playbook = !0), p && (n.kindle = !0, n.version = p[1]), m && (i.silk = !0, i.version = m[1]), !m && n.android && e.match(/Kindle Fire/) && (i.silk = !0), b && (i.chrome = !0, i.version = b[1]), j && (i.firefox = !0, i.version = j[1]), x && (n.firefoxos = !0, n.version = x[1]), q && (i.ie = !0, i.version = q[1]), T && (a || n.ios || f) && (i.safari = !0, n.ios || (i.version = T[1])), A && (i.webview = !0), n.version = parseFloat(n.version), i.ucbrowser = e.match(/ucbrowser/gi) ? !0 : !1, i.qqbrowser = e.match(/qqbrowser/gi) ? !0 : !1, n.tablet = !!(s || y || o && !e.match(/Mobile/) || j && e.match(/Tablet/) || q && !e.match(/Phone/) && e.match(/Touch/)), n.phone = !(n.tablet || n.ipod || !(o || c || l || g || v || b && e.match(/Android/) || b && e.match(/CriOS\/([\d.]+)/) || j && e.match(/Mobile/) || q && e.match(/Touch/)))
    }
}
UA.parse();
var SHARE = {
    bottomTitle: '查看更多精彩内容 》',
    apkUrl: "http://down.kunlunjue.com/kunlunjue-925.apk",
    iosUrl: "https://itunes.apple.com/cn/app/id806078745?mt=8",
    yingyongbaoUrl: "http://a.app.qq.com/o/simple.jsp?pkgname=com.iss.kunlunjue",
    imageFolder: "",
    openAppData: "",
    ajax: function (d, e, f) {
        $.ajax({
            type: "GET", url: d, data: e, timeout: 20000, dataType: "json", success: function (a) {
                if (a.code != 200) {
                    if ($.isFunction(f.error)) {
                        f.error(a.code, a.message)
                    }
                } else if ($.isFunction(f.success)) {
                    f.success(a)
                }
            }, error: function (a, b, c) {
                if ($.isFunction(f.error)) {
                    f.error(-1, b)
                }
            }
        })
    },
    getParam: function (a) {
        var b = this.parseURL(location.href)
        return b.params[a] || ""
    },
    parseURL: function (c) {
        c = c || window.location.href
        var a = document.createElement('a');
        a.href = c;
        return {
            source: c,
            protocol: a.protocol.replace(':', ''),
            host: a.hostname,
            port: a.port,
            query: a.search,
            params: (function () {
                var b = {}, seg = a.search.replace(/^\?/, '').split('&'), len = seg.length, i = 0, s;
                for (; i < len; i++) {
                    if (!seg[i]) {
                        continue
                    }
                    s = seg[i].split('=');
                    b[s[0]] = decodeURIComponent(s[1])
                }
                return b
            })(),
            file: (a.pathname.match(/\/([^\/?#]+)$/i) || [, ''])[1],
            hash: a.hash.replace('#', ''),
            path: a.pathname.replace(/^([^\/])/, '/$1'),
            relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [, ''])[1],
            segments: a.pathname.replace(/^\//, '').split('/')
        }
    },
    isAndroid: (function () {
        return /(Android|Adr)/.test(navigator.userAgent)
    })(),
    isApple: (function () {
        return !this.isAndroid && /(iPhone|iPod|iPad)/.test(navigator.userAgent)
    })(),
    isWeibo: (function () {
        return /weibo/i.test(navigator.userAgent)
    })(),
    isInApp: (function () {
        return /kunlunjue-app/.test(navigator.userAgent) || location.href.indexOf("/title/") > 0
    })(),
    isAppInstalled: function () {
        return this.getParam("isappinstalled") == '1'
    },
    isInWeiXin: (function () {
        return /\sMicroMessenger/.test(navigator.userAgent)
    })(),
    hideWeiXinTip: function () {
        document.getElementById("weixin_tip").style.display = 'none'
    },
    showWeiXinTip: function (a) {
        if (UA.os.ios && UA.os.version >= 9) {
            location.href = "//kunlunjue.com/open.html?d=" + a
        } else {
            document.getElementById("weixin_tip").style.display = 'block'
        }
    },
    getTopBannerHtml: function (a, b) {
        return '' + '<div onclick="SHARE.hideWeiXinTip();" id="weixin_tip" class="mark" style="display: none;">' + '       <div class="fxmark"></div>' + '       <div class="share_img share_' + (this.isAndroid ? "android" : "ios") + '"></div>' + '</div>' + '<section class="share_banner">' + '<a href="' + b + '">' + '<img src="' + this.imageFolder + '/logo.png" width="50px">' + '<ul class="share_slogan">' + '<li class="ss_logo">' + '<img src="' + this.imageFolder + '/kunlun.png" width="70px">' + '</li>' + '<li class="ss_slogan">' + '<span>世界极限格斗赛官方App</span>' + '</li>' + '</ul>' + '<div class="share_down">' + '<span>立即' + (a ? "下载" : "打开") + '</span>' + '</div>' + '</a>' + '</section>'
    },
    getBottomBannerHtml: function (a, b, c) {
        var d = b.indexOf("javascript:") == 0 ? b : "window.location='" + b + "'"
        if (c == 2) {
            return '' + '<div class="share_bottom_banner">' + '<button onclick="' + d + '">' + (a ? "下载" : "打开") + "昆仑决APP，" + this.bottomTitle + "</button>" + '</div>'
        } else if (c == 3) {
            return '' + '<div style="height: 50px;"></div>' + '<table class="downbar3">' + '    <tr>' + '        <td width="1">' + '            <img src="' + this.imageFolder + '/logo2.png">' + '        </td>' + '        <td>' + this.bottomTitle + '</td>' + '        <td width="1" align="right">' + '            <button onclick="' + d + '">' + '立即' + (a ? "下载" : "打开") + '</button>' + '        </td>' + '    </tr>' + '</table>'
        }
        return ""
    },
    getBannerHtml: function (a, b, c) {
        return c != 1 ? this.getBottomBannerHtml(a, b, c) : this.getTopBannerHtml(a, b)
    },
    getAutoOpenAppHtml: function (a) {
        return !a ? '<script>' + 'setTimeout(function(){' + '   location.href="' + this.getOpenAppUrl(this.openAppData) + '"' + '},500)' + '</script>' : ''
    },
    getOpenAppUrl: function (a) {
        return "wxa89b29b7fe046913://" + encodeURIComponent(JSON.stringify(a || {}))
    },
    writeDownloadBanner: function (a, b) {
        this.imageFolder = a;
        this.openAppData = b;
        document.write(this.getDownloadBannerHtml(false) || "")
    },
    writeBottomDownloadBanner: function (a, b) {
        if (!!a) {
            this.bottomTitle = a
        }
        document.write(this.getDownloadBannerHtml(typeof b == 'undefined' ? 2 : b) || "")
    },
    getUrl: function (a) {
        if (this.isApple || this.isAndroid) {
            if (this.isInWeiXin) {
                if (this.isAppInstalled()) {
                    return "javascript:SHARE.showWeiXinTip('" + encodeURIComponent(JSON.stringify(a || {})) + "');"
                } else {
                    return this.yingyongbaoUrl
                }
            } else {
                if (this.isAppInstalled()) {
                    return this.getOpenAppUrl(a)
                } else {
                    return this.isApple ? this.iosUrl : this.apkUrl
                }
            }
        } else {
            return "http://www.kunlunjue.com"
        }
    },
    getDownloadBannerHtml: function (a) {
        if (typeof a == 'boolean') {
            a = !a ? 1 : 2
        }
        var b = this.getUrl(this.openAppData)
        if (this.isApple || this.isAndroid) {
            if (this.isInWeiXin) {
                if (this.isAppInstalled()) {
                    return this.getBannerHtml(false, b, a)
                } else {
                    return this.getBannerHtml(true, b, a)
                }
            } else if (this.isInApp) {
                return ""
            } else {
                if (this.isAppInstalled()) {
                    return this.getBannerHtml(false, b, a) + this.getAutoOpenAppHtml(a)
                } else {
                    return this.getBannerHtml(true, b, a)
                }
            }
        } else {
            return this.getBannerHtml(true, b, a)
        }
    },
    writeMark: function () {
        document.write('' + '<div id="weixin_tip" class="mark" style="display: block;">' + '       <div class="fxmark"></div>' + '       <div class="share_img"><img src="' + this.imageFolder + '/' + (this.isAndroid ? "android" : "ios") + '.png"></div>' + '</div>')
    },
    writeDownloadHtml: function (a) {
        var b = this.apkUrl;
        this.imageFolder = a;
        if (this.isApple || this.isAndroid) {
            if (this.isInWeiXin) {
                location.href = this.yingyongbaoUrl
            } else if (this.isWeibo) {
                if (this.isApple)this.writeMark(); else location.href = this.apkUrl
            } else {
                location.href = this.isApple ? this.iosUrl : b
            }
        } else {
            location.href = b
        }
    },
    writeDownloadAppleHtml: function (a) {
        this.imageFolder = a;
        if (this.isApple) {
            if (this.isInWeiXin) {
                location.href = this.yingyongbaoUrl;
                return
            } else if (this.isWeibo) {
                this.writeMark();
                return
            }
        }
        location.href = this.iosUrl
    },
    getShareTitle: function (a, b) {
        if (a.type == 'match') {
            switch (b) {
                case'weixin':
                case'qq':
                    return a.title;
                case'weibo':
                case'timeline':
                case'qzone':
                    return this.getShareDesc(a, b)
            }
        } else if (a.type == 'matcha') {
            switch (b) {
                case'weixin':
                case'qq':
                    return a.desc;
                case'weibo':
                    return a.title + " " + a.desc + " " + a.link + " #昆仑决#";
                case'timeline':
                case'qzone':
                    return a.title + " " + a.desc
            }
        } else if (a.type == 'article' || a.type == 'image') {
            if (b == 'weibo') {
                return this.getShareDesc(a, b)
            }
        }
        return a.title
    },
    getShareDesc: function (a, b) {
        if (a.type == 'match') {
            switch (b) {
                case'weixin':
                case'qq':
                    return a.date + a.address;
                case'weibo':
                    return a.title + a.link + " #昆仑决#";
                default:
                    return a.title
            }
        } else if (a.type == 'matcha') {
            switch (b) {
                case'weixin':
                case'qq':
                    return a.title + " " + a.date + " " + " " + a.address;
                case'weibo':
                    return a.title + " " + a.desc + " " + a.link + " #昆仑决#";
                case'timeline':
                case'qzone':
                    return a.title + " " + a.desc
            }
        } else if (a.type == 'article' || a.type == 'image') {
            if (b == 'weibo') {
                return a.title + a.link + " #昆仑决#"
            } else if (!a.desc) {
                return " "
            }
        }
        return a.desc
    },
    getWeixinConfig: function (a) {
        API.get('getWeixinConfig', {url: encodeURI(location.href)}, a)
    },
    decodeHtml: function (s) {
        var a = document.createElement('div');
        a.innerHTML = s;
        return a.innerText || a.textContent
    },
    getShareDataFinal: function (a, b) {
        return {
            title: this.decodeHtml(this.getShareTitle(a, b)),
            desc: this.decodeHtml(this.getShareDesc(a, b)),
            link: a.link,
            imgUrl: a.image
        }
    },
    weixinShare: function (b) {
        var c = this;
        this.getWeixinConfig({
            success: function (a) {
                if (a.code == 200) {
                    wx.config({
                        appId: a.data.appId,
                        timestamp: a.data.timestamp,
                        nonceStr: a.data.nonceStr,
                        signature: a.data.signature,
                        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone']
                    });
                    wx.ready(function () {
                        b.link = location.href;
                        b.type = b.type || 'article';
                        wx.onMenuShareAppMessage(c.getShareDataFinal.call(c, b, 'weixin'));
                        wx.onMenuShareTimeline(c.getShareDataFinal.call(c, b, 'timeline'));
                        wx.onMenuShareQQ(c.getShareDataFinal.call(c, b, 'qq'));
                        wx.onMenuShareWeibo(c.getShareDataFinal.call(c, b, 'weibo'));
                        wx.onMenuShareQZone(c.getShareDataFinal.call(c, b, 'qzone'))
                    })
                }
            }
        })
    }
}