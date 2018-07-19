<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>消息列表</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/list.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $listForm = $("#listForm");
	var $filterSelect = $("#filterSelect");
	var $filterOption = $("#filterOption a");
	
	$filterSelect.mouseover(function() {
		var $this = $(this);
		var offset = $this.offset();
		var $menuWrap = $this.closest("div.menuWrap");
		var $popupMenu = $menuWrap.children("div.popupMenu");
		$popupMenu.css({left: offset.left, top: offset.top + $this.height() + 2}).show();
		$menuWrap.mouseleave(function() {
			$popupMenu.hide();
		});
	});
	
	$filterOption.click(function() {
		var $this = $(this);
		var $dest = $("#" + $this.attr("name"));
		if ($this.hasClass("checked")) {
			$dest.val("");
		} else {
			$dest.val($this.attr("val"));
		}
		$listForm.submit();
		return false;
	});
	
});
</script>
</head>
<body>
	<div class="path">
		<a href="<?=base_url()?>admin/home">首页</a> &raquo; 系统 &raquo; 消息列表 <span>(共<span id="pageTotal"><?= $pager['total'] ?></span>条记录)</span>
	</div>
	<form id="listForm" action="lists" method="get">
		<input type="hidden" id="receiverType" name="receiverType" value="<?= $receiverType ?>" />
		<input type="hidden" id="isDraft" name="isDraft" value="<?= $isDraft ?>" />
		
		<div class="bar">			
			<div class="buttonWrap">
				<a href="javascript:;" id="deleteButton" class="iconButton disabled">
					<span class="deleteIcon">&nbsp;</span>删除
				</a>
				<a href="javascript:;" id="refreshButton" class="iconButton">
					<span class="refreshIcon">&nbsp;</span>刷新
				</a>
				<div class="menuWrap">
					<a href="javascript:;" id="filterSelect" class="button">
						消息筛选<span class="arrow">&nbsp;</span>
					</a>
					<div class="popupMenu">
						<ul id="filterOption" class="check">
							<li>
								<a href="javascript:;" name="receiverType" val="<?=RECEIVER_TYPE_SINGLE?>" <?php if ($receiverType == RECEIVER_TYPE_SINGLE): ?>class="checked"<?php endif; ?>>
									<?= getReceiverType(RECEIVER_TYPE_SINGLE) ?>
								</a>
							</li>
							<li>
								<a href="javascript:;" name="receiverType" val="<?=RECEIVER_TYPE_MALE?>" <?php if ($receiverType == RECEIVER_TYPE_MALE): ?>class="checked"<?php endif; ?>>
									<?= getReceiverType(RECEIVER_TYPE_MALE) ?>
								</a>
							</li>
							<li>
								<a href="javascript:;" name="receiverType" val="<?=RECEIVER_TYPE_FEMALE?>" <?php if ($receiverType == RECEIVER_TYPE_FEMALE): ?>class="checked"<?php endif; ?>>
									<?= getReceiverType(RECEIVER_TYPE_FEMALE) ?>
								</a>
							</li>
							<li>
								<a href="javascript:;" name="receiverType" val="<?=RECEIVER_TYPE_ALL?>" <?php if ($receiverType == RECEIVER_TYPE_ALL): ?>class="checked"<?php endif; ?>>
									<?= getReceiverType(RECEIVER_TYPE_ALL) ?>
								</a>
							</li>
							<li class="separator">
								<a href="javascript:;" name="isDraft" val="1" <?php if ($isDraft === '1'): ?>class="checked"<?php endif; ?>>
									<?= getMessageType(1) ?>
								</a>
							</li>
							<li>
								<a href="javascript:;" name="isDraft" val="0" <?php if ($isDraft === '0'): ?>class="checked"<?php endif; ?>>
									<?= getMessageType(0) ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="menuWrap">
					<a href="javascript:;" id="pageSizeSelect" class="button">
						每页显示<span class="arrow">&nbsp;</span>
					</a>
					<div class="popupMenu">
						<ul id="pageSizeOption">
							<li>
								<a href="javascript:;" <?php if ($pager['pageSize'] == 10): ?>class="current"<?php endif; ?> val="10">10</a>
							</li>
							<li>
								<a href="javascript:;" <?php if ($pager['pageSize'] == 20): ?>class="current"<?php endif; ?> val="20">20</a>
							</li>
							<li>
								<a href="javascript:;" <?php if ($pager['pageSize'] == 50): ?>class="current"<?php endif; ?> val="50">50</a>
							</li>
							<li>
								<a href="javascript:;" <?php if ($pager['pageSize'] == 100): ?>class="current"<?php endif; ?> val="100">100</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="menuWrap">
				<div class="search">
					<span id="searchPropertySelect" class="arrow">&nbsp;</span>
					<input type="text" id="searchValue" name="searchValue" value="<?= $pager['searchValue'] ?>" maxlength="200" />
					<button type="submit">&nbsp;</button>
				</div>
				<div class="popupMenu">
					<ul id="searchPropertyOption">
						<li>
							<a href="javascript:;" <?php if ($pager['searchProperty'] == "title"): ?>class="current"<?php endif; ?> val="title">标题</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<table id="listTable" class="list">
			<tr>
				<th class="check">
					<input type="checkbox" id="selectAll" <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?>/>
				</th>
				<th>
					<a href="javascript:;" class="sort" name="receiver_name">接收用户</a>
				</th>
				<th>
					<a href="javascript:;" class="sort" name="title">标题</a>
				</th>
				<th>
					<a href="javascript:;" class="sort" name="is_draft">类型</a>
				</th>
				<th>
					<a href="javascript:;" class="sort" name="create_date">发送日期</a>
				</th>
				<th>
					<a href="javascript:;" class="sort" name="create_date">创建日期</a>
				</th>
				<th>
					<span>操作</span>
				</th>
			</tr>
			<?php foreach ($messageList as $item): ?>
				<tr>
					<td>
						<input type="checkbox" name="ids[]" value="<?= $item['id'] ?>" <?php if (!$isEditable): ?>disabled="disabled"<?php endif; ?>/>
					</td>
					<td>
						<?php if ($item['receiver_type'] == RECEIVER_TYPE_SINGLE): ?>
							<?= $item['receiver_name'] ?>
						<?php else: ?>
							<?= getReceiverType($item['receiver_type']) ?>
						<?php endif; ?>
					</td>
					<td>
						<?= $item['title'] ?>
					</td>
					<td>
						<?php if ($item['is_draft'] == 1): ?>
							<span class="green"><?= getMessageType($item['is_draft']) ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?= $item['send_date'] ?>
					</td>
					<td>
						<?= $item['create_date'] ?>
					</td>
					<td>
						<?php if ($isEditable): ?>						
						<a href="edit?id=<?= $item['id'] ?>">[查看]</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
			$this->load->view('admin/pagination');
		?>
	</form>
</body>
</html>