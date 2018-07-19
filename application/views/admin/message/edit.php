<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
<?php if ($isNew): ?>发送消息<?php else: ?>消息详情<?php endif; ?>
</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.tools.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");
	var $isDraft = $("#isDraft");
	var $receiverType = $("#receiverType");
	var $receiverTd = $("#receiverTd");
	var $receiverId = $("#receiverId");
	var $receiverSelect = $("#receiverSelect");

	<?php if ($isEditable): ?>
	$inputForm.validate({
		rules: {
			title: {
				required: true,
				minlength: 2,
				maxlength: 50
			},
			content: {
				required: true,
				minlength: 2,
				maxlength: 250
			}
		}
	});
	<?php endif; ?>

	var typeSingle = <?= RECEIVER_TYPE_SINGLE ?>;
	$receiverType.change(function() {
		if ($(this).val() == typeSingle) {
			$receiverTd.show();
		} else {
			$receiverTd.hide();
		}
	});
	
	$("#send").click(function() {
		if ($receiverType.val() == typeSingle && $receiverId.val().length == 0) {
			alert('必须选定接收用户!');
			return false;
		}
		
		$isDraft.val(0);
		$inputForm.submit();
	});
	
	$("#save").click(function() {
		$isDraft.val(1);
		$inputForm.submit();
	});

	$receiverSelect.autocomplete('find_user', {
		dataType: "json",
		max: 20,
		width: 190,
		scrollHeight: 300,
		parse: function(data) {
			return $.map(data, function(item) {
				return {
					data: item,
					value: item.username
				}
			});
		},
		formatItem: function(item) {
			var html = '<span title="' + item.username + '">' + item.username;
			if (item.nickname) {
				html += ' (' +  item.nickname + ')';
			}
			html += '<\/span>';
			return html;
		}
	}).result(function(event, item) {
		$receiverSelect.val(item.username);
		$receiverId.val(item.id);
	});
});
</script>
</head>
<body>
	<div class="path">
		<a href="<?=base_url()?>admin/home">首页</a> &raquo; 系统 &raquo;
		<?php if ($isNew): ?>
		发送消息
		<?php else: ?>
		消息列表 &raquo; 消息详情
		<?php endif; ?>
	</div>
	<form id="inputForm" action="save" method="post">
		<?php if (!$isNew): ?>
		<input type="hidden" name="id" value="<?=$messageItem['id']?>" />
		<?php endif; ?>
		<input type="hidden" id="isDraft" name="is_draft" <?php if (!$isNew): ?>value="<?=$messageItem['is_draft']?>"<?php endif; ?> />
		
		<table class="input tabContent">
			<tr>
				<th>发送类型:</th>
				<td>
					<?php if ($isEditable): ?>
						<select id="receiverType" name="receiver_type">
							<option value="<?= RECEIVER_TYPE_SINGLE ?>" <?php if (!$isNew && $messageItem['receiver_type'] == RECEIVER_TYPE_SINGLE): ?>selected="selected"<?php endif; ?>>
								<?= getReceiverType(RECEIVER_TYPE_SINGLE) ?>
							</option>
							<option value="<?= RECEIVER_TYPE_MALE ?>" <?php if (!$isNew && $messageItem['receiver_type'] == RECEIVER_TYPE_MALE): ?>selected="selected"<?php endif; ?>>
								<?= getReceiverType(RECEIVER_TYPE_MALE) ?>
							</option>
							<option value="<?= RECEIVER_TYPE_FEMALE ?>" <?php if (!$isNew && $messageItem['receiver_type'] == RECEIVER_TYPE_FEMALE): ?>selected="selected"<?php endif; ?>>
								<?= getReceiverType(RECEIVER_TYPE_FEMALE) ?>
							</option>
							<option value="<?= RECEIVER_TYPE_ALL ?>" <?php if (!$isNew && $messageItem['receiver_type'] == RECEIVER_TYPE_ALL): ?>selected="selected"<?php endif; ?>>
								<?= getReceiverType(RECEIVER_TYPE_ALL) ?>
							</option>
						</select>
					<?php else: ?>
						<?= getReceiverType($messageItem['receiver_type']) ?>
					<?php endif; ?>
				</td>
			</tr>
			<tr id="receiverTd" <?php if (!$isNew && $messageItem['receiver_type'] != RECEIVER_TYPE_SINGLE): ?>style="display: none;"<?php endif; ?>>
				<th>接收用户:</th>
				<td>
					<?php if (!$isNew && !$isEditable && $messageItem['receiver_type'] == RECEIVER_TYPE_SINGLE): ?>
						<a href="<?=base_url()?>admin/member/edit?id=<?= $messageItem['receiver_id'] ?>"><?=$messageItem['receiver_name']?></a>
					<?php else: ?>
						<input type="text" id="receiverSelect" name="receiverSelect" class="text" maxlength="50" value="<?= $isNew?'':$messageItem['receiver_name']?>" title="请输入手机号码，呢成查找用户" />
						<input type="hidden" id="receiverId" name="receiver_id" value="<?= $isNew?'':$messageItem['receiver_id']?>"/>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php if ($isEditable): ?>
					<span class="requiredField">*</span>
					<?php endif; ?>
					消息标题:
				</th>
				<td>
					<?php if ($isEditable): ?>
						<input type="text" name="title" class="text" maxlength="50" value="<?= $isNew?'':$messageItem['title']?>"/>
					<?php else: ?>
						<?= $messageItem['title'] ?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php if ($isEditable): ?>
					<span class="requiredField">*</span>
					<?php endif; ?>
					消息内容:
				</th>
				<td>
					<?php if ($isEditable): ?>
						<textarea name="content" class="text"><?= $isNew?'':$messageItem['content']?></textarea>
					<?php else: ?>
						<?= $messageItem['content'] ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if (!$isEditable): ?>
			<tr>
				<th>
					发送时间:
				</th>
				<td>
					<?= $messageItem['send_date'] ?>
				</td>
			</tr>
			<?php endif; ?>
			
			<tr>
				<th>
					&nbsp;
				</th>
				<td>
					<?php if ($isEditable): ?>
					<input type="button" id="send" class="button" value="立即发送" />
					<?php endif; ?>
					<?php if ($isNew): ?>
						<input type="button" id="save" class="button" value="保存为草稿" />
					<?php endif; ?>
					<input type="button" class="button" value="返&nbsp;&nbsp;回" onclick="location.href='lists'" />
				</td>
			</tr>
		</table>
	</form>
</body>
</html>