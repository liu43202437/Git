<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>反馈详情</title>
<meta name="author" content="STSOFT Team" />
<meta name="copyright" content="T-One" />
<link href="<?=base_url()?>resources/css/common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript"> var BASE = '<?=base_url()?>'; </script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.tools.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/common.js"></script>
<script type="text/javascript" src="<?=base_url()?>resources/js/input.js"></script>
<script type="text/javascript">
$().ready(function() {

	<?php if (isset($message)): ?>
		$.message("<?=$message['type']?>", "<?=$message['content']?>");
	<?php endif; ?>
	
	var $inputForm = $("#inputForm");

	
});
</script>
</head>
<body>
	<div class="path">
		<a href="<?=base_url()?>admin/home">首页</a> &raquo; 系统 &raquo; 反馈列表 &raquo; 反馈详情
	</div>
	<form id="inputForm" action="save" method="post">
		<input type="hidden" name="id" value="<?=$feedback['id']?>" />
		
		<table class="input tabContent">
			<tr>
				<th>反馈用户:</th>
				<td>
					<a href="<?=base_url()?>admin/member/edit?id=<?= $feedback['user_id'] ?>"><?=$feedback['username']?></a>
				</td>
			</tr>
			<tr>
				<th>反馈日期:</th>
				<td>
					<?= $feedback['create_date'] ?>
				</td>
			</tr>
			<tr>
				<th>反馈内容:</th>
				<td>
					<?= $feedback['content'] ?>
				</td>
			</tr>
			<tr>
				<th>状&nbsp;&nbsp;态:</th>
				<td>
					<?php if ($feedback['status'] == FEEDBACK_STATUS_REQUESTED): ?>
						<span class="red"><?= $FeedbackStatus[$feedback['status']] ?></span>
					<?php elseif ($feedback['status'] == FEEDBACK_STATUS_PROCEED): ?>
						<span class="green"><?= $FeedbackStatus[$feedback['status']] ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<?php if ($feedback['status'] == FEEDBACK_STATUS_PROCEED): ?>
			<tr>
				<th>处理日期:</th>
				<td>
					<?= $feedback['proceed_date'] ?>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		
		<table class="input">
			<tr>
				<th>
					&nbsp;
				</th>
				<td>
					<?php if ($feedback['status'] == FEEDBACK_STATUS_REQUESTED): ?>
						<input type="submit" class="button" value="解&nbsp;&nbsp;决" />
					<?php endif; ?>
					
					<input type="button" class="button" value="返&nbsp;&nbsp;回" onclick="location.href='lists'" />
				</td>
			</tr>
		</table>
	</form>
</body>
</html>