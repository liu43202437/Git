/*
 * JavaScript - List
 */

$().ready(function () {

    var $listForm = $("#listForm");
    var $listTable = $("#listTable");
    var $backButton = $("#backButton");
    var $batchButtons = $(".batch-btn");
    var $deleteButton = $("#deleteButton");
    var $deleteItemButton = $("#listTable .deleteItemBtn");
    var $checkItemButton = $("#listTable .checkItemBtn");

    var $ajaxOpButton = $("#listTable a[role=ajax]");
    var $refreshButton = $("#refreshButton");
    var $selectAll = $("#selectAll");
    var $selectedCount = $("#selectedCount");
    var $ids = $("#listTable input[name^='ids']");
    var $contentRow = $("#listTable tr:gt(0)");
    var $sort = $("#listTable a.sort");
    var $orderProperty = $("#orderProperty");
    var $orderDirection = $("#orderDirection");
    var $pageNumber = $("#pageNumber");
    var $pageSize = $("#pageSize");
    var $moreButton = $("#moreButton");

    // 返回
    $backButton.click(function () {
        var $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }
        if ($this.data('url')) {
            location.href = $this.data('url');
        } else {
            history.back();
        }
    });

    // 更多条件
    $moreButton.click(function () {
        $(".more-filters").toggleClass("hidden");
        $i = $moreButton.children("i");
        $i.toggleClass("fa-angle-double-down").toggleClass("fa-angle-double-up");
    });

    // 删除
    $deleteButton.click(function () {
        var $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }
        var url = 'delete';
        if ($this.data('url')) {
            url = $this.data('url');
        }
        var $checkedIds = $("#listTable input[name^='ids']:enabled:checked");
        var data = $checkedIds.serialize();
        if ($this.data('params')) {
            var params = $this.data('params').split('&');
            for (var i in params) {
                var pair = params[i].split('=');
                //data[pair[0]] = pair[1];
                data += '&' + params[i];
            }
        }

        $.dialog({
            type: "warn",
            content: message("admin.dialog.deleteConfirm"),
            ok: message("admin.dialog.ok"),
            cancel: message("admin.dialog.cancel"),
            onOk: function () {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: data,
                    dataType: "json",
                    cache: false,
                    success: function (message) {
                        $.message(message);
                        if (message.type == "success") {
                            $checkedIds.closest("tr").remove();
                            if ($listTable.find("tr").size() <= 2) {
                                setTimeout(function () {
                                    location.reload(true);
                                }, 3000);
                            }
                        }
                        $batchButtons.addClass("disabled");
                        //$selectAll.prop("checked", false);
                        //$checkedIds.prop("checked", false);
                        $selectAll.ifCheck("uncheck");
                    }
                });
            }
        });
        return false;
    });
    //start
    $checkItemButton.click(function () {
        var $this = $(this);
        var shop = $this.attr('shop');
        if(shop == 1){
            return;
        }
        if ($this.hasClass("disabled")) {
            return false;
        }
        var url = 'check';
        if ($this.data('url')) {
            url = $this.data('url');
        }
        var func = $this.data('func');

        var id = $this.data('id');
        var status = $this.data('status');
        var m="admin.dialog.checkConfirmOff";
        if (status == 1) {
             m='admin.dialog.checkConfirmOn';
        }

        $.dialog({
            type: "warn",
            content: message(m),
            ok: message("admin.dialog.ok"),
            cancel: message("admin.dialog.cancel"),
            onOk: function () {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {'ids[]': id,'status':status},
                    dataType: "json",
                    cache: false,
                    success: function (message) {
                        $.message(message);
                        if (message.type == "success") {
                            $this.parent().remove();
                            if ($listTable.find("tr").size() <= 2) {
                                setTimeout(function () {
                                    location.reload(true);
                                }, 3000);
                            }
                        }
                        if (func) {
                            window[func]($this, message);
                        }
                    }
                });
            }
        });
        return false;
    });
    //end
    $deleteItemButton.click(function () {
        var $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }
        var url = 'delete';
        if ($this.data('url')) {
            url = $this.data('url');
        }
        var func = $this.data('func');

        var id = $this.data('id');

        $.dialog({
            type: "warn",
            content: message("admin.dialog.deleteConfirm"),
            ok: message("admin.dialog.ok"),
            cancel: message("admin.dialog.cancel"),
            onOk: function () {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {'ids[]': id},
                    dataType: "json",
                    cache: false,
                    success: function (message) {
                        $.message(message);
                        if (message.type == "success") {
                            $this.closest("tr").remove();
                            if ($listTable.find("tr").size() <= 2) {
                                setTimeout(function () {
                                    location.reload(true);
                                }, 3000);
                            }
                        }
                        if (func) {
                            window[func]($this, message);
                        }
                    }
                });
            }
        });
        return false;
    });

    $ajaxOpButton.click(function () {
        var $this = $(this);
        if ($this.hasClass("disabled")) {
            return false;
        }

        var url = $this.data('url');
        var func = $this.data('func');
        var reload = $this.data('reload');
        var params = $this.data('params');

        var data = null;
        if ($this.hasClass("batch-btn")) {
            var $checkedIds = $("#listTable input[name^='ids']:enabled:checked");
            data = $checkedIds.serialize();
            if (params) {
                var params = params.split('&');
                for (var i in params) {
                    data += '&' + params[i];
                }
            }
        } else {
            var id = $this.data('id');
            data = {'id': id, '_time': $.now()};
            if (params) {
                var params = params.split('&');
                for (var i in params) {
                    var pair = params[i].split('=');
                    data[pair[0]] = pair[1];
                }
            }
        }

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "json",
            cache: false,
            success: function (message) {
                $.message(message);
                if (message.type == "success") {
                    if (func) {
                        window[func]($this, message);
                    }
                    if (reload) {
                        setTimeout(function () {
                            location.reload(true);
                        }, 2000);
                    }
                }
            }
        });
        return false;
    });

    // 刷新
    $refreshButton.click(function () {
        location.reload(true);
        return false;
    });

    // 全选
    $selectAll.on("ifChanged", function () {
        var $this = $(this);
        var $enabledIds = $("#listTable input[name^='ids']:enabled");
        if ($this.prop("checked")) {
            //$enabledIds.prop("checked", true);
            $enabledIds.iCheck("check");
            if ($enabledIds.filter(":checked").size() > 0) {
                $batchButtons.removeClass("disabled");
                $contentRow.addClass("selected");
            } else {
                $batchButtons.addClass("disabled");
            }
        } else {
            //$enabledIds.prop("checked", false);
            $enabledIds.iCheck("uncheck");
            $batchButtons.addClass("disabled");
            $contentRow.removeClass("selected");
        }
        $selectedCount.text($enabledIds.filter(":checked").size());
    });

    // 选择
    $ids.on("ifChanged", function () {
        var $this = $(this);
        var $enabledIds = $("#listTable input[name^='ids']:enabled");
        if ($this.prop("checked")) {
            $this.closest("tr").addClass("selected");
            $batchButtons.removeClass("disabled");
            if ($enabledIds.filter(":checked").size() == $enabledIds.size()) {
                $selectAll.prop("checked", true);
            }
        } else {
            $selectAll.prop("checked", false);
            $this.closest("tr").removeClass("selected");
            if ($enabledIds.filter(":checked").size() > 0) {
                $batchButtons.removeClass("disabled");
            } else {
                $batchButtons.addClass("disabled");
            }
        }
        $selectAll.iCheck("update");
        $selectedCount.text($enabledIds.filter(":checked").size());
    });

    // 排序
    $sort.click(function () {
        var orderProperty = $(this).attr("name");
        if ($orderProperty.val() == orderProperty) {
            if ($orderDirection.val() == "asc") {
                $orderDirection.val("desc")
            } else {
                $orderDirection.val("asc");
            }
        } else {
            $orderProperty.val(orderProperty);
            $orderDirection.val("asc");
        }
        $pageNumber.val("1");
        $listForm.submit();
        return false;
    });

    // 排序图标
    if ($orderProperty.val() != "") {
        $sort = $("#listTable a[name='" + $orderProperty.val() + "']");
        if ($orderDirection.val() == "asc") {
            $sort.removeClass("desc").addClass("asc");
        } else {
            $sort.removeClass("asc").addClass("desc");
        }
    }

    // 页码输入
    $pageNumber.keypress(function (event) {
        var key = event.keyCode ? event.keyCode : event.which;
        if ((key == 13 && $(this).val().length > 0) || (key >= 48 && key <= 57)) {
            return true;
        } else {
            return false;
        }
    });

    // 表单提交
    $listForm.submit(function () {
        if (!/^\d*[1-9]\d*$/.test($pageNumber.val())) {
            $pageNumber.val("1");
        }
    });

    // 页码跳转
    $.pageSkip = function (pageNumber) {
        $pageNumber.val(pageNumber);
        $listForm.submit();
        return false;
    }

    // 列表查询
    if (location.search != "") {
        addCookie("listQuery", location.search);
    } else {
        removeCookie("listQuery");
    }

});