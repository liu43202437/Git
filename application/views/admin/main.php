<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>中维合众 后台</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />

<link href="<?=base_url()?>resources/css/font-awesome.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/bootstrap.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/animate.min.css" rel="stylesheet">
<link href="<?=base_url()?>resources/css/style.css" rel="stylesheet">

<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/bootstrap.min.js"></script>

<script type="text/javascript">
$().ready(function() {

	var $iframe = $("#contentFrame");
	var $menuItem = $(".menuItem");
	var $subMenu = $(".subMenu");
	var $subMenuItem = $(".subMenuItem");
	var $addItem = $(".add-item");
	
	$menuItem.click(function() {
		var $this = $(this);
		$menuItem.removeClass("current");
		$this.addClass("current");
		$subMenu.hide();
		var $currentMenu = $($this.attr("href"));
		if ($currentMenu.size() == 0) {
			$(".nav-sub-menu").hide();
			$("#page-wrapper").addClass("expanded");
			if ($this.attr("target")) {
				$iframe.attr("src", $this.attr("href"));
			} else {
				location.href = $this.attr("href");
			}
			return false;
		} else {
			$(".nav-sub-menu").show();
			$("#page-wrapper").removeClass("expanded");
			$currentMenu.show();
			return false;
		}
	});
	
	$subMenuItem.click(function() {
		var $this = $(this);
		if ($this.hasClass("add-new")) {
			$dlg = $($this.attr("href"));
			$dlg.modal('show');
			return false;
		}
		$subMenuItem.removeClass("current");
		$this.addClass("current");
		$iframe.attr("src", $this.attr("href"));
		return false;
	});
	
	$addItem.click(function() {
		var $this = $(this);
		var $dlg = $this.parents(".modal");
		var href = $this.attr("href");
		var target = $this.attr("target");
		$dlg.modal('hide');
		$iframe.attr("src", href);
		//$subMenuItem.removeClass("current");
		//$(target).addClass("current");
		return false;
	});

	$menuItem.eq(0).click();
	
	$(".nav-header a").click(function() {
		$iframe.attr("src", $(this).attr("href"));
		return false;
	})
});
</script>
</head>
<?php
	function getMenuItem($label, $url, $icon, $roles)
	{
		$rslt = '';
		if (in_array($url, $roles)) {
			$rslt = "<li>
					<a class='menuItem' href='#$url'>
						<i class='fa $icon'></i> 
						<span class='nav-label'>$label</span>
					</a>
				</li>";
		}
		return $rslt;
	}
	
	function getSubMenuItem($label, $url, $icon, $roles)
	{
		$rslt = '';
		if (in_array($url, $roles)) {
			$rslt = "<li>
					<a class='subMenuItem' href='$url' target='contentFrame'>
						<i class='fa $icon'></i><span class='nav-label'>$label</span>
					</a>
				</li>";
		}
		return $rslt;
	}
?>
<body class="main gray-bg">
	<script type="text/javascript">
		if (self != top) {
			top.location = self.location;
		};
	</script>
	<div id="wrapper">
		<!--左侧导航开始-->
        <nav class="navbar-default nav-main-menu" role="navigation">
            <ul class="nav">
                <li class="nav-header">
                    <span><img alt="image" class="img-logo" src="<?=base_url()?>resources/images/logo.png" /></span>
                    <div class="m-t-xs">
	                    <a href="admin/edit_me" target="contentFrame">
	                        <strong class="font-bold"><?=$adminName?></strong>
	                    </a>
                    </div>
                </li>
				<li>
					<a class="menuItem" href="home" target="contentFrame">
						<i class="fa fa-home"></i> 
						<span class="nav-label">首页</span>
					</a>
				</li>
				<?= getMenuItem('内容', 'content', 'fa-bars', $roles) ?>
				<?= getMenuItem('认证', 'member', 'fa-info-circle', $roles) ?>
				<?= getMenuItem('访销', 'lottery', 'fa-info-circle', $roles) ?>
				<?= getMenuItem('票券', 'ticket', 'fa-flag-o', $roles) ?>
				<?= getMenuItem('兑奖', 'redeem', 'fa-gift', $roles) ?>
				<!--<?= getMenuItem('活动', 'events', 'fa-flag-o', $roles) ?>-->
				<?= getMenuItem('审核', 'audit', 'fa-certificate', $roles) ?>
				<?= getMenuItem('用户', 'user', 'fa-user', $roles) ?>
				<?= getMenuItem('管理员', 'admin', 'fa-user-secret', $roles) ?>
                <?= getMenuItem('订单', 'order', 'fa-file-text', $roles) ?>
                <?= getMenuItem('彩票机', 'machine', 'fa-file-text', $roles) ?>
				<?= getMenuItem('配置', 'config', 'fa-gear', $roles) ?>
				<li class="logout">
					<a class="menuItem" href="logout"> 
						<span class="nav-label">退出</span>
					</a>
				</li>
            </ul>
        </nav>
        
        <nav class="navbar-default nav-sub-menu">
        	<?php if (in_array('content', $roles)): ?>
            <ul class="nav subMenu" id="content">
                <li class="nav-header">
                	<h2>内容</h2>
                	<div class="">管理客户端内容</div>
                </li>
                <?= getSubMenuItem('Banner', 'banner/lists', 'fa-history', $roles) ?>
                <?= getSubMenuItem('Banner2', 'banner/lists2', 'fa-history', $roles) ?>
                <?= getSubMenuItem('文章', 'content/lists/1', 'fa-newspaper-o', $roles) ?>
                <?= getSubMenuItem('图集', 'content/lists/2', 'fa-image', $roles) ?>
                <?= getSubMenuItem('视频', 'content/lists/3', 'fa-tv', $roles) ?>
                <?= getSubMenuItem('链接', 'video_link/lists', 'fa-link', $roles) ?>
                <?= getSubMenuItem('直播', 'content/lists/4', 'fa-video-camera', $roles) ?>
                <?= getSubMenuItem('图文', 'baby/lists', 'fa-photo', $roles) ?>
                <?= getSubMenuItem('广告', 'content/lists/8', 'fa-film', $roles) ?>
                <?php if (in_array('content/add', $roles)): ?>
				<li>
					<a class="subMenuItem add-new" href="#dlgAddContent">
						<i class="fa fa-plus-square"></i><span class="nav-label">添加</span>
					</a>
				</li>
				<?php endif; ?>
            </ul>
            <?php endif; ?>
            <?php if (in_array('member', $roles)): ?>
            <ul class="nav subMenu" id="member">
                <li class="nav-header">
                	<h2>认证页面</h2>
                	<div class="">查看与审核</div>
                </li>
				<?= getSubMenuItem('零售店', 'club/lists', 'fa-columns', $roles) ?>

				<?= getSubMenuItem('客户经理', 'manager/lists', 'fa-columns', $roles) ?>

				<?= getSubMenuItem('市场经理', 'area_manager/lists', 'fa-columns', $roles) ?>

				<?= getSubMenuItem('区域经理', 'bazaar_manager/lists', 'fa-columns', $roles) ?>

				<?= getSubMenuItem('零售店批量审核', 'club/batchCheck', 'fa-columns', $roles) ?>
            </ul>
            <?php endif; ?>

            <?php if (in_array('lottery', $roles)): ?>
                <ul class="nav subMenu" id="lottery">
                    <li class="nav-header">
                        <h2>访销经理</h2>
                        <div class="">查看与审核</div>
                    </li>
                    <?= getSubMenuItem('访销经理管理', 'lottery/lists', 'fa-columns', $roles) ?>
                </ul>
            <?php endif; ?>


            <?php if (in_array('ticket', $roles)): ?>

            <ul class="nav subMenu" id="ticket">
                <li class="nav-header">
                	<h2>票券</h2>
                	<div class="">票券管理</div>
                </li>
				<?= getSubMenuItem('票券管理', 'ticket/lists', 'fa-columns', $roles) ?>
            </ul>
            <?php endif; ?>

            <?php if (in_array('redeem', $roles)): ?>

            <ul class="nav subMenu" id="redeem">
                <li class="nav-header">
                	<h2>兑奖</h2>
                	<div class="">兑奖管理</div>
                </li>
				<?= getSubMenuItem('兑奖管理', 'redeem/redeemLists', 'fa-columns', $roles) ?>
				<?= getSubMenuItem('提现管理', 'redeem/receiptLists', 'fa-columns', $roles) ?>
				<?= getSubMenuItem('对账管理', 'redeem/checking', 'fa-columns', $roles) ?>
            </ul>
            <?php endif; ?>

            <?php if (in_array('events', $roles)): ?>
            <ul class="nav subMenu" id="events">
                <li class="nav-header">
                	<h2>活动</h2>
                	<div class="">T-ONE举办的比赛</div>
                </li>
                <?= getSubMenuItem('赛事', 'events/lists/1', 'fa-trophy', $roles) ?>
                <?= getSubMenuItem('比赛', 'events/lists/2', 'fa-institution', $roles) ?>
                <?= getSubMenuItem('售票', 'events/ticket_orders', 'fa-cart-arrow-down', $roles) ?>
                <?php if (in_array('events/add', $roles)): ?>
				<li>
					<a class="subMenuItem add-new" href="#dlgAddEvent">
						<i class="fa fa-plus-square"></i><span class="nav-label">添加</span>
					</a>
				</li>
				<?php endif; ?>
            </ul>
            <?php endif; ?>
            <?php if (in_array('audit', $roles)): ?>
            <ul class="nav subMenu" id="audit">
                <li class="nav-header">
                	<h2>审核</h2>
                	<div class="">管理员查看审核客户端提交内容</div>
                </li>
				<!--
                <?= getSubMenuItem('意见反馈', 'feedback/lists', 'fa-sticky-note', $roles) ?>
                <?= getSubMenuItem('评论', 'comment/lists', 'fa-comments', $roles) ?>
	-->
                <?= getSubMenuItem('公益彩票专卖', 'audit/lists/1', 'fa-columns', $roles) ?>

<!--
                <?= getSubMenuItem('裁判报名', 'audit/lists/2', 'fa-columns', $roles) ?>
                <?= getSubMenuItem('教练报名', 'audit/lists/3', 'fa-columns', $roles) ?>


                <?= getSubMenuItem('公益票专卖', 'audit/lists/5', 'fa-columns', $roles) ?>

				<?= getSubMenuItem('道馆加盟', 'audit/lists/4', 'fa-columns', $roles) ?>	-->

            </ul>
            <?php endif; ?>
            <?php if (in_array('user', $roles)): ?>
            <ul class="nav subMenu" id="user">
                <li class="nav-header">
                	<h2>用户</h2>
                	<div class="">T-One的用户列表</div>
                </li>
                <?= getSubMenuItem('用户列表', 'user/lists', 'fa-users', $roles) ?>
                <?= getSubMenuItem('等级', 'user/ranks', 'fa-street-view', $roles) ?>
                <?= getSubMenuItem('客户经理列表', 'user/manager_lists', 'fa-users', $roles) ?>
            </ul>
            <?php endif; ?>
            <?php if (in_array('order', $roles)): ?>
                <ul class="nav subMenu" id="order">
                    <li class="nav-header">
                        <h2>订单</h2>
                        <div class="">订单列表</div>
                    </li>
                    <?= getSubMenuItem('订单列表', 'order/lists', 'fa-users', $roles) ?>

                    <?= getSubMenuItem('订单报表统计', 'order/statistics', 'fa-photo', $roles) ?>
                    <?= getSubMenuItem('报表导出', 'order/area', 'fa-users', $roles) ?>
                    <?= getSubMenuItem('彩票统计', 'order/lottery', 'fa-users', $roles) ?>
                </ul>
            <?php endif; ?>
            <?php if (in_array('admin', $roles)): ?>
            <ul class="nav subMenu" id="admin">
                <li class="nav-header">
                	<h2>管理员</h2>
                	<div class="">后台管理员</div>
                </li>
                <?= getSubMenuItem('管理员', 'admin/lists', 'fa-users', $roles) ?>
                <?= getSubMenuItem('添加管理员', 'admin/edit', 'fa-user-plus', $roles) ?>
            </ul>
            <?php endif; ?>
			<?php if (in_array('machine', $roles)): ?>
				<ul class="nav subMenu" id="machine">
					<li class="nav-header">
						<h2>彩票机</h2>
						<div class="">彩票机管理</div>
					</li>
					<?= getSubMenuItem('基本信息', 'machine/lists', 'fa-users', $roles) ?>
					<?= getSubMenuItem('订单', 'machine/order', 'fa-user-plus', $roles) ?>
				</ul>
			<?php endif; ?>
            <?php if (in_array('config', $roles)): ?>
            <ul class="nav subMenu" id="config">
                <li class="nav-header">
                	<h2>配置</h2>
                	<div class="">客户端系统内容格式设置</div>
                </li>
               <!-- <?= getSubMenuItem('栏目名称', 'config/menu_item', 'fa-users', $roles) ?>-->
                <?= getSubMenuItem('分类管理', 'config/category', 'fa-code-fork', $roles) ?>
                <?= getSubMenuItem('版本更新', 'config/version', 'fa-external-link-square', $roles) ?>
                <?= getSubMenuItem('启动闪播', 'splash/lists', 'fa-bolt', $roles) ?>
                <?= getSubMenuItem('刷新背景', 'bg_image/lists', 'fa-image', $roles) ?>
                <?= getSubMenuItem('报名设置', 'config/audit/1', 'fa-random', $roles) ?>
               <!-- <?= getSubMenuItem('礼物管理', 'config/gift', 'fa-gift', $roles) ?>-->
                <?= getSubMenuItem('基本设置', 'config/basis', 'fa-map', $roles) ?>
                <?= getSubMenuItem('页面内容', 'config/layout', 'fa-retweet', $roles) ?>
                <?= getSubMenuItem('分享二维码', 'config/share', 'fa-qrcode', $roles) ?>
                <!--<?= getSubMenuItem('排行耪', 'config/ranking', 'fa-sort-numeric-asc', $roles) ?>-->
            </ul>
            <?php endif; ?>
        </nav>
        
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg">
			<div class="modal fade" tabindex="-1" role="dialog" id="dlgAddContent">
				<div class="modal-dialog add-modal">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">添加内容</h3>
						</div>
						<div class="modal-body">
							<div class="row text-center m-b-md">
								<div class="col-xs-4">
									<a href="content/edit/1" target="" class="add-item">
									<i class="fa fa-newspaper-o fa-3x"></i>
									<span>添加文章</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="content/edit/2" target="" class="add-item">
									<i class="fa fa-image fa-3x"></i>
									<span>添加图集</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="content/edit/3" target="" class="add-item">
									<i class="fa fa-tv fa-3x"></i>
									<span>添加视频</span>
									</a>
								</div>
							</div>
							<div class="row text-center">
								<div class="col-xs-4">
									<a href="video_link/edit" target="" class="add-item">
									<i class="fa fa-link fa-3x"></i>
									<span>添加链接</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="content/edit/4" target="" class="add-item">
									<i class="fa fa-video-camera fa-3x"></i>
									<span>添加直播</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="baby/edit" target="" class="add-item">
									<i class="fa fa-photo fa-3x"></i>
									<span>添加图文</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" tabindex="-1" role="dialog" id="dlgAddMember">
				<div class="modal-dialog add-modal">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">添加认证</h3>
						</div>
						<div class="modal-body">
							<div class="row text-center m-b-md">
								<div class="col-xs-4">
									<a href="member/edit/1" target="" class="add-item">
									<i class="fa fa-columns fa-3x"></i>
									<span>添加选手</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="member/edit/2" target="" class="add-item">
									<i class="fa fa-columns fa-3x"></i>
									<span>添加裁判</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="member/edit/3" target="" class="add-item">
									<i class="fa fa-columns fa-3x"></i>
									<span>添加教练</span>
									</a>
								</div>
							</div>
							<div class="row text-center">
								<div class="col-xs-2">
								</div>
								<div class="col-xs-4">
									<a href="club/edit" target="" class="add-item">
									<i class="fa fa-columns fa-3x"></i>
									<span>添加道馆</span>
									</a>
								</div>
								<div class="col-xs-4">
									<a href="member/edit_organization" target="" class="add-item">
									<i class="fa fa-columns fa-3x"></i>
									<span>添加举办方</span>
									</a>
								</div>
								<div class="col-xs-2">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" tabindex="-1" role="dialog" id="dlgAddEvent">
				<div class="modal-dialog add-modal">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">添加活动</h3>
						</div>
						<div class="modal-body">
							<div class="row text-center">
								<div class="col-xs-6">
									<a href="events/edit/1" target="" class="add-item">
									<i class="fa fa-trophy fa-3x"></i>
									<span>添加赛事</span>
									</a>
								</div>
								<div class="col-xs-6">
									<a href="events/edit/2" target="" class="add-item">
									<i class="fa fa-institution fa-3x"></i>
									<span>添加比赛</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
        	<div class="row" id="content-main">
                <iframe id="contentFrame" name="contentFrame" width="100%" height="100%" src="home" frameborder="0" seamless></iframe>
            </div>
        </div>
	</div>
</body>
</html>