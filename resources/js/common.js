/*
 * JavaScript - Common
 * Version: 3.0
 */

var setting = {
    uploadImageExtension: "jpg,jpeg,bmp,gif,png",
    uploadFlashExtension: "swf,flv",
    uploadMediaExtension: "swf,flv,mp3,wav,avi,rm,rmvb",
    uploadFileExtension: "zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,pdf"
};

var messages = {
    "admin.message.success": "操作成功",
    "admin.message.error": "操作错误",
    "admin.dialog.ok": "确&nbsp;&nbsp;定",
    "admin.dialog.cancel": "取&nbsp;&nbsp;消",
    "admin.dialog.deleteConfirm": "您确定要删除吗？",
    "admin.dialog.checkConfirmOn": '你确定要审核通过吗？',
    "admin.dialog.refuseConfirmOn": '你确定审核不通过吗？',
    "admin.dialog.checkConfirmOff": "你确定要审核<span style='color:red;'>拒绝</span>吗？",
    "admin.dialog.clearConfirm": "您确定要清空吗？",
    "admin.browser.title": "选择文件",
    "admin.browser.upload": "本地上传",
    "admin.browser.parent": "上级目录",
    "admin.browser.orderType": "排序方式",
    "admin.browser.name": "名称",
    "admin.browser.size": "大小",
    "admin.browser.type": "类型",
    "admin.browser.select": "选择文件",
    "admin.upload.sizeInvalid": "上传文件大小超出限制",
    "admin.upload.typeInvalid": "上传文件格式不正确",
    "admin.upload.invalid": "上传文件格式或大小不正确",
    "admin.upload.error": "上传文件出现错误",
    "admin.validate.required": "必填",
    "admin.validate.email": "E-mail格式错误",
    "admin.validate.url": "网址格式错误",
    "admin.validate.date": "日期格式错误",
    "admin.validate.dateISO": "日期格式错误",
    "admin.validate.pointcard": "信用卡格式错误",
    "admin.validate.number": "只允许输入数字",
    "admin.validate.digits": "只允许输入零或正整数",
    "admin.validate.minlength": "长度不允许小于{0}",
    "admin.validate.maxlength": "长度不允许大于{0}",
    "admin.validate.rangelength": "长度必须在{0}-{1}之间",
    "admin.validate.min": "不允许小于{0}",
    "admin.validate.max": "不允许大于{0}",
    "admin.validate.range": "必须在{0}-{1}之间",
    "admin.validate.accept": "输入后缀错误",
    "admin.validate.equalTo": "两次输入不一致",
    "admin.validate.remote": "输入错误",
    "admin.validate.integer": "只允许输入整数",
    "admin.validate.positive": "只允许输入正数",
    "admin.validate.negative": "只允许输入负数",
    "admin.validate.decimal": "数值超出了允许范围",
    "admin.validate.pattern": "格式错误",
    "admin.validate.extension": "文件格式错误",
    "admin.validate.illegal": "非法字符",
    "admin.validate.exist": "已存在"
};

// add Cookie
function addCookie(name, value, options) {
    if (arguments.length > 1 && name != null) {
        if (options == null) {
            options = {};
        }
        if (value == null) {
            options.expires = -1;
        }
        if (typeof options.expires == "number") {
            var time = options.expires;
            var expires = options.expires = new Date();
            expires.setTime(expires.getTime() + time * 1000);
        }
        document.cookie = encodeURIComponent(String(name)) + "=" + encodeURIComponent(String(value)) + (options.expires ? "; expires=" + options.expires.toUTCString() : "") + (options.path ? "; path=" + options.path : "") + (options.domain ? "; domain=" + options.domain : ""), (options.secure ? "; secure" : "");
    }
}

// get Cookie
function getCookie(name) {
    if (name != null) {
        var value = new RegExp("(?:^|; )" + encodeURIComponent(String(name)) + "=([^;]*)").exec(document.cookie);
        return value ? decodeURIComponent(value[1]) : null;
    }
}

// remove Cookie
function removeCookie(name, options) {
    addCookie(name, null, options);
}

// currency
function currency(value, showSign, showUnit) {
    if (value != null) {
        var price;
        if (setting.priceRoundType == "roundHalfUp") {
            price = (Math.round(value * Math.pow(10, setting.priceScale)) / Math.pow(10, setting.priceScale)).toFixed(setting.priceScale);
        } else if (setting.priceRoundType == "roundUp") {
            price = (Math.ceil(value * Math.pow(10, setting.priceScale)) / Math.pow(10, setting.priceScale)).toFixed(setting.priceScale);
        } else {
            price = (Math.floor(value * Math.pow(10, setting.priceScale)) / Math.pow(10, setting.priceScale)).toFixed(setting.priceScale);
        }
        if (showSign) {
            price = setting.currencySign + price;
        }
        if (showUnit) {
            price += setting.currencyUnit;
        }
        return price;
    }
}

function message(code) {
    if (code != null) {
        var content = messages[code] != null ? messages[code] : code;
        if (arguments.length == 1) {
            return content;
        } else {
            if ($.isArray(arguments[1])) {
                $.each(arguments[1], function (i, n) {
                    content = content.replace(new RegExp("\\{" + i + "\\}", "g"), n);
                });
                return content;
            } else {
                $.each(Array.prototype.slice.apply(arguments).slice(1), function (i, n) {
                    content = content.replace(new RegExp("\\{" + i + "\\}", "g"), n);
                });
                return content;
            }
        }
    }
}

(function ($) {

    Array.prototype.move = function (from, to) {
        this.splice(to, 0, this.splice(from, 1)[0]);
    };

    var zIndex = 10000;

    var $message;
    var messageTimer;
    $.message = function () {
        var message = {};
        if ($.isPlainObject(arguments[0])) {
            message = arguments[0];
        } else if (typeof arguments[0] === "string" && typeof arguments[1] === "string") {
            message.type = arguments[0];
            message.content = arguments[1];
        } else {
            return false;
        }

        if (message.type == null || message.content == null) {
            return false;
        }

        if ($message == null) {
            $message = $('<div class="xxMessage"><div class="messageContent message' + message.type + 'Icon"><\/div><\/div>');
            if (!window.XMLHttpRequest) {
                $message.append('<iframe class="messageIframe"><\/iframe>');
            }
            $message.appendTo("body");
        }

        $message.children("div").removeClass("messagewarnIcon messageerrorIcon messagesuccessIcon").addClass("message" + message.type + "Icon").html(message.content);
        $message.css({"margin-left": -parseInt($message.outerWidth() / 2), "z-index": zIndex++}).show();

        clearTimeout(messageTimer);
        messageTimer = setTimeout(function () {
            $message.hide();
        }, 2000);
        return $message;
    }

    $.dialog = function (options) {
        var settings = {
            width: 320,
            height: "auto",
            modal: true,
            ok: message("admin.dialog.ok"),
            cancel: message("admin.dialog.cancel"),
            onShow: null,
            onClose: null,
            onOk: null,
            onCancel: null
        };
        $.extend(settings, options);

        if (settings.content == null) {
            return false;
        }

        var $dialog = $('<div class="xxDialog"><\/div>');
        var $dialogTitle;
        var $dialogClose = $('<div class="dialogClose"><\/div>').appendTo($dialog);
        var $dialogContent;
        var $dialogBottom;
        var $dialogOk;
        var $dialogCancel;
        var $dialogOverlay;
        if (settings.title != null) {
            $dialogTitle = $('<div class="dialogTitle"><\/div>').appendTo($dialog);
        }
        if (settings.type != null) {
            $dialogContent = $('<div class="dialogContent dialog' + settings.type + 'Icon"><\/div>').appendTo($dialog);
        } else {
            $dialogContent = $('<div class="dialogContent"><\/div>').appendTo($dialog);
        }
        if (settings.ok != null || settings.cancel != null) {
            $dialogBottom = $('<div class="dialogBottom"><\/div>').appendTo($dialog);
        }
        if (settings.ok != null) {
            $dialogOk = $('<input type="button" class="button" value="' + settings.ok + '" \/>').appendTo($dialogBottom);
        }
        if (settings.cancel != null) {
            $dialogCancel = $('<input type="button" class="button" value="' + settings.cancel + '" \/>').appendTo($dialogBottom);
        }
        if (!window.XMLHttpRequest) {
            $dialog.append('<iframe class="dialogIframe"><\/iframe>');
        }
        $dialog.appendTo("body");
        if (settings.modal) {
            $dialogOverlay = $('<div class="dialogOverlay"><\/div>').insertAfter($dialog);
        }

        var dialogX;
        var dialogY;
        if (settings.title != null) {
            $dialogTitle.text(settings.title);
        }
        $dialogContent.html(settings.content);
        $dialog.css({
            "width": settings.width,
            "height": settings.height,
            "margin-left": -parseInt(settings.width / 2),
            "z-index": zIndex++
        });
        dialogShow();

        if ($dialogTitle != null) {
            $dialogTitle.mousedown(function (event) {
                $dialog.css({"z-index": zIndex++});
                var offset = $(this).offset();
                if (!window.XMLHttpRequest) {
                    dialogX = event.clientX - offset.left;
                    dialogY = event.clientY - offset.top;
                } else {
                    dialogX = event.pageX - offset.left;
                    dialogY = event.pageY - offset.top;
                }
                $("body").bind("mousemove", function (event) {
                    $dialog.css({"top": event.clientY - dialogY, "left": event.clientX - dialogX, "margin": 0});
                });
                return false;
            }).mouseup(function () {
                $("body").unbind("mousemove");
                return false;
            });
        }

        if ($dialogClose != null) {
            $dialogClose.click(function () {
                dialogClose();
                return false;
            });
        }

        if ($dialogOk != null) {
            $dialogOk.click(function () {
                if (settings.onOk && typeof settings.onOk == "function") {
                    if (settings.onOk($dialog) != false) {
                        dialogClose();
                    }
                } else {
                    dialogClose();
                }
                return false;
            });
        }

        if ($dialogCancel != null) {
            $dialogCancel.click(function () {
                if (settings.onCancel && typeof settings.onCancel == "function") {
                    if (settings.onCancel($dialog) != false) {
                        dialogClose();
                    }
                } else {
                    dialogClose();
                }
                return false;
            });
        }

        function dialogShow() {
            if (settings.onShow && typeof settings.onShow == "function") {
                if (settings.onShow($dialog) != false) {
                    $dialog.show();
                    $dialogOverlay.show();
                }
            } else {
                $dialog.show();
                $dialogOverlay.show();
            }
        }

        function dialogClose() {
            if (settings.onClose && typeof settings.onClose == "function") {
                if (settings.onClose($dialog) != false) {
                    $dialogOverlay.remove();
                    $dialog.remove();
                }
            } else {
                $dialogOverlay.remove();
                $dialog.remove();
            }
        }

        return $dialog;
    }

    $.uploader = function (options) {
        var settings = {
            url: BASE + "/admin/files/upload",
            formElement: "",
            contextElement: "",
            workingClass: "uploading",
            successClass: "uploaded",
            fileType: "image",
            dataType: "json",
            prependFiles: true,
            makeThumb: false,
            maxHeight: 0,
            minHeight: 0,
            ratioWidth: 0,
            ratioHeight: 0,
            ratioScope: 2,
            previewElement: "",
            resultElement: "",
            thumbElement: "",
            before: null,
            fail: null,
            done: null,
            progress: null,
            always: null
        };
        $.extend(settings, options);

        var formData = {
            file_type: settings.fileType,
            make_thumb: settings.makeThumb,
            ratioWidth: settings.ratioWidth,
            ratioHeight: settings.ratioHeight
        };

        $(settings.formElement).fileupload({
            url: settings.url,
            dataType: settings.dataType,
            dropZone: $(settings.contextElement),
            prependFiles: settings.prependFiles,
            formData: formData,
            add: function (e, data) {
                data.context = $(settings.contextElement);
                data.context.addClass(settings.workingClass);
                data.context.removeClass(settings.successClass);

                if (settings.before && typeof settings.before == "function") {
                    settings.before(data);
                }

                if (settings.fileType == 'image' &&
                    (settings.maxWidth > 0 || settings.maxHeight > 0 || settings.ratioWidth > 0 || settings.ratioHeight > 0)) {

                    var reader = new FileReader();
                    reader.readAsDataURL(data.files[0]);
                    reader.onload = function (e) {
                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function () {
                            var height = this.height;
                            var width = this.width;
                            var isValid = true;
                            if (settings.maxWidth > 0) {
                                if (width > settings.maxWidth) {
                                    isValid = false;
                                    alert("图片宽度不能大于" + width + "px！");
                                }
                            }
                            if (settings.maxHeight > 0) {
                                if (height > settings.maxHeight) {
                                    isValid = false;
                                    alert("图片高度不能大于" + height + "px！");
                                }
                            }
                            if (settings.ratioHeight > 0) {
                                diff = (settings.ratioWidth / settings.ratioHeight).toFixed(2) - (width / height).toFixed(2);
                                if (Math.abs(diff) > settings.ratioScope) {
                                    isValid = false;
                                    alert("图片宽度高度比例" + settings.ratioWidth + ":" + settings.ratioHeight + "！");
                                }
                            }

                            if (!isValid) {
                                data.context.removeClass(settings.workingClass);
                                return false;
                            }

                            var jqXHR = data.submit();
                            return true;
                        };
                    }
                    return false;
                } else {
                    var jqXHR = data.submit();
                    return true;
                }
            },
            progress: function (e, data) {
                if (settings.progress && typeof settings.progress == "function") {
                    settings.progress(data);
                }
            },
            fail: function (e, data) {
                data.context.removeClass(settings.workingClass);
                $.message('error', '网路错误！');
                if (settings.fail && typeof settings.fail == "function") {
                    settings.fail(data);
                }
            },
            done: function (e, data) {
                data.context.removeClass(settings.workingClass);
                if (data.result.message.type == 'success') {
                    data.context.addClass(settings.successClass);
                    if (settings.previewElement) {
                        var url = settings.makeThumb ? data.result.thumb : data.result.url;
                        $(settings.previewElement).attr('src', BASE + url);
                    }
                    if (settings.resultElement) {
                        $(settings.resultElement).val(data.result.url);
                    }
                    if (settings.thumbElement) {
                        $(settings.thumbElement).val(data.result.thumb);
                    }
                    if (settings.done && typeof settings.done == "function") {
                        settings.done(data.result);
                    }
                } else {
                    $.message(data.result.message);
                }
            },
            always: function (e, data) {
                if (settings.always && typeof settings.always == "function") {
                    settings.always(data);
                }
            }
        });
    }

    $.fn.extend({
        browser: function (options) {
            var settings = {
                type: "image",
                title: message("admin.browser.title"),
                isUpload: true,
                browserUrl: BASE + "/admin/files/browser",
                uploadUrl: BASE + "/admin/files/upload",
                callback: null
            };
            $.extend(settings, options);

            var token = getCookie("token");
            var cache = {};
            return this.each(function () {
                var browserFrameId = "browserFrame" + (new Date()).valueOf() + Math.floor(Math.random() * 1000000);
                var $browserButton = $(this);
                $browserButton.click(function () {
                    var $browser = $('<div class="xxBrowser"><\/div>');
                    var $browserBar = $('<div class="browserBar"><\/div>').appendTo($browser);
                    var $browserFrame;
                    var $browserForm;
                    var $browserUploadButton;
                    var $browserUploadInput;
                    var $browserParentButton;
                    var $browserOrderType;
                    var $browserLoadingIcon;
                    var $browserList;
                    if (settings.isUpload) {
                        $browserFrame = $('<iframe id="' + browserFrameId + '" name="' + browserFrameId + '" style="display: none;"><\/iframe>').appendTo($browserBar);
                        $browserForm = $('<form action="' + settings.uploadUrl + '" method="post" encType="multipart/form-data" target="' + browserFrameId + '"><input type="hidden" name="token" value="' + token + '" \/><input type="hidden" name="fileType" value="' + settings.type + '" \/><\/form>').appendTo($browserBar);
                        $browserUploadButton = $('<a href="javascript:;" class="browserUploadButton button">' + message("admin.browser.upload") + '<\/a>').appendTo($browserForm);
                        $browserUploadInput = $('<input type="file" name="file" \/>').appendTo($browserUploadButton);
                    }
                    $browserParentButton = $('<a href="javascript:;" class="button">' + message("admin.browser.parent") + '<\/a>').appendTo($browserBar);
                    $browserBar.append(message("admin.browser.orderType") + ": ");
                    $browserOrderType = $('<select name="orderType" class="browserOrderType"><option value="name">' + message("admin.browser.name") + '<\/option><option value="size">' + message("admin.browser.size") + '<\/option><option value="type">' + message("admin.browser.type") + '<\/option><\/select>').appendTo($browserBar);
                    $browserLoadingIcon = $('<span class="loadingIcon" style="display: none;">&nbsp;<\/span>').appendTo($browserBar);
                    $browserList = $('<div class="browserList"><\/div>').appendTo($browser);

                    var $dialog = $.dialog({
                        title: settings.title,
                        content: $browser,
                        width: 470,
                        modal: true,
                        ok: null,
                        cancel: null
                    });

                    browserList("/");

                    function browserList(path) {
                        var key = settings.type + "_" + path + "_" + $browserOrderType.val();
                        if (cache[key] == null) {
                            $.ajax({
                                url: settings.browserUrl,
                                type: "GET",
                                data: {fileType: settings.type, orderType: $browserOrderType.val(), path: path},
                                dataType: "json",
                                cache: false,
                                beforeSend: function () {
                                    $browserLoadingIcon.show();
                                },
                                success: function (data) {
                                    createBrowserList(path, data);
                                    cache[key] = data;
                                },
                                complete: function () {
                                    $browserLoadingIcon.hide();
                                }
                            });
                        } else {
                            createBrowserList(path, cache[key]);
                        }
                    }

                    function createBrowserList(path, data) {
                        var browserListHtml = "";
                        $.each(data, function (i, fileInfo) {
                            var iconUrl;
                            var title;
                            if (fileInfo.isDirectory) {
                                iconUrl = BASE + "/resources/images/folder_icon.gif";
                                title = fileInfo.name;
                            } else if (new RegExp("^\\S.*\\.(jpg|jpeg|bmp|gif|png)$", "i").test(fileInfo.name)) {
                                iconUrl = fileInfo.url;
                                title = fileInfo.name + " (" + Math.ceil(fileInfo.size / 1024) + "KB, " + new Date(fileInfo.lastModified).toLocaleString() + ")";
                            } else {
                                iconUrl = BASE + "/resources/images/file_icon.gif";
                                title = fileInfo.name + " (" + Math.ceil(fileInfo.size / 1024) + "KB, " + new Date(fileInfo.lastModified).toLocaleString() + ")";
                            }
                            browserListHtml += '<div class="browserItem"><img src="' + iconUrl + '" title="' + title + '" url="' + fileInfo.url + '" isDirectory="' + fileInfo.isDirectory + '" \/><div>' + fileInfo.name + '<\/div><\/div>';
                        });
                        $browserList.html(browserListHtml);

                        $browserList.find("img").bind("click", function () {
                            var $this = $(this);
                            var isDirectory = $this.attr("isDirectory");
                            if (isDirectory == "true") {
                                var name = $this.next().text();
                                browserList(path + name + "/");
                            } else {
                                var url = $this.attr("url");
                                if (settings.input != null) {
                                    settings.input.val(url);
                                } else {
                                    $browserButton.prev(":text").val(url);
                                }
                                if (settings.callback != null && typeof settings.callback == "function") {
                                    settings.callback(url);
                                }
                                $dialog.next(".dialogOverlay").andSelf().remove();
                            }
                        });

                        if (path == "/") {
                            $browserParentButton.unbind("click");
                        } else {
                            var parentPath = path.substr(0, path.replace(/\/$/, "").lastIndexOf("/") + 1);
                            $browserParentButton.unbind("click").bind("click", function () {
                                browserList(parentPath);
                            });
                        }
                        $browserOrderType.unbind("change").bind("change", function () {
                            browserList(path);
                        });
                    }

                    $browserUploadInput.change(function () {
                        var allowedUploadExtensions;
                        if (settings.type == "flash") {
                            allowedUploadExtensions = setting.uploadFlashExtension;
                        } else if (settings.type == "media") {
                            allowedUploadExtensions = setting.uploadMediaExtension;
                        } else if (settings.type == "file") {
                            allowedUploadExtensions = setting.uploadFileExtension;
                        } else {
                            allowedUploadExtensions = setting.uploadImageExtension;
                        }
                        if ($.trim(allowedUploadExtensions) == "" || !new RegExp("^\\S.*\\.(" + allowedUploadExtensions.replace(/,/g, "|") + ")$", "i").test($browserUploadInput.val())) {
                            $.message("warn", message("admin.upload.typeInvalid"));
                            return false;
                        }
                        $browserLoadingIcon.show();
                        $browserForm.submit();
                    });

                    $browserFrame.load(function () {
                        var text;
                        var io = document.getElementById(browserFrameId);
                        if (io.contentWindow) {
                            text = io.contentWindow.document.body ? io.contentWindow.document.body.innerHTML : null;
                        } else if (io.contentDocument) {
                            text = io.contentDocument.document.body ? io.contentDocument.document.body.innerHTML : null;
                        }
                        if ($.trim(text) != "") {
                            $browserLoadingIcon.hide();
                            var data = $.parseJSON(text);
                            if (data.message.type == "success") {
                                if (settings.input != null) {
                                    settings.input.val(data.url);
                                } else {
                                    $browserButton.prev(":text").val(data.url);
                                }
                                if (settings.callback != null && typeof settings.callback == "function") {
                                    settings.callback(data.url);
                                }
                                cache = {};
                                $dialog.next(".dialogOverlay").andSelf().remove();
                            } else {
                                $.message(data.message);
                            }
                        }
                    });

                });
            });
        }
    });

    $(document).ajaxSend(function (event, request, settings) {
        if (!settings.crossDomain && settings.type != null && settings.type.toLowerCase() == "post") {
            var token = getCookie("token");
            if (token != null) {
                request.setRequestHeader("token", token);
            }
        }
    });

    $(document).ajaxComplete(function (event, request, settings) {
        var loginStatus = request.getResponseHeader("loginStatus");
        var tokenStatus = request.getResponseHeader("tokenStatus");

        if (loginStatus == "accessDenied") {
            $.message("warn", "登录超时，请重新登录");
            setTimeout(function () {
                location.reload(true);
            }, 2000);
        } else if (loginStatus == "unauthorized") {
            $.message("warn", "对不起，您无此操作权限！");
        } else if (tokenStatus == "accessDenied") {
            var token = getCookie("token");
            if (token != null) {
                $.extend(settings, {
                    global: false,
                    headers: {token: token}
                });
                $.ajax(settings);
            }
        }
    });

})(jQuery);

$().ready(function () {

    $("form").submit(function () {
        var $this = $(this);
        if ($this.attr("method") != null && $this.attr("method").toLowerCase() == "post" && $this.find("input[name='token']").size() == 0) {
            var token = getCookie("token");
            if (token != null) {
                $this.append('<input type="hidden" name="token" value="' + token + '" \/>');
            }
        }
    });

    if ($(".i-check").length > 0) {
        $(".i-check").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green",
        });
    }

    if ($(".fancybox").length > 0) {
        $(".fancybox").fancybox({
            openEffect: "none",
            closeEffect: "none"
        });
    }

    if ($("#summernote").length > 0) {
        var toolbars = [
            ['style', ['bold', 'italic', 'underline']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ];
        if ($("#summernote").hasClass("simple")) {
            toolbars = [
                ['style', ['bold', 'italic', 'underline']]
            ];
        }
        $("#summernote").summernote({
            lang: "zh-CN",
            minHeight: 200,
            maxHeight: 400,
            toolbar: toolbars,
            callbacks: {
                onBlur: function () {
                    $(this).siblings("textarea").eq(0).val($(this).summernote('code'));
                }
            }
        });
    }
});