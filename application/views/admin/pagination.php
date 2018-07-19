<input type="hidden" id="pageSize" name="pageSize" value="<?=$pager['pageSize']?>" />
<input type="hidden" id="orderProperty" name="orderProperty" value="<?=$pager['orderProperty']?>" />
<input type="hidden" id="orderDirection" name="orderDirection" value="<?=$pager['orderDirection']?>" />
<?php if ($pager['totalPages'] > 1): ?>
	<div class="pagination">
		<?php if ($pager['isFirst']): ?>
			<span class="firstPage"><i class="fa fa-step-backward"></i></span>
		<?php else: ?>
			<a class="firstPage" href="javascript: $.pageSkip(<?=$pager['firstPageNumber']?>);"><i class="fa fa-step-backward"></i></a>
		<?php endif; ?>

		<?php if ($pager['hasPrevious']): ?>
			<a class="previousPage" href="javascript: $.pageSkip(<?=$pager['previousPageNumber']?>);"><i class="fa fa-chevron-left"></i></a>
		<?php else: ?>
			<span class="previousPage"><i class="fa fa-chevron-left"></i></span>
		<?php endif; ?>
		
		<?php foreach ($pager['segment'] as $index => $segmentPageNumber): ?>
			<?php if ($index == 0 && $segmentPageNumber > $pager['firstPageNumber'] + 1): ?>
				<span class="pageBreak">...</span>
			<?php endif; ?>
			
			<?php if ($segmentPageNumber != $pager['pageNumber']): ?>
				<a href="javascript: $.pageSkip(<?=$segmentPageNumber?>);"><?=$segmentPageNumber?></a>
			<?php else: ?>
				<span class="currentPage"><?=$segmentPageNumber?></span>
			<?php endif; ?>
			
			<?php if (($index + 1) == count($pager['segment']) && $segmentPageNumber < $pager['lastPageNumber'] - 1): ?>
				<span class="pageBreak">...</span>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if ($pager['hasNext']): ?>
			<a class="nextPage" href="javascript: $.pageSkip(<?=$pager['nextPageNumber']?>);"><i class="fa fa-chevron-right"></i></a>
		<?php else: ?>
			<span class="nextPage"><i class="fa fa-chevron-right"></i></span>
		<?php endif; ?>
		
		<?php if ($pager['isLast']): ?>
			<span class="lastPage"><i class="fa fa-step-forward"></i></span>
		<?php else: ?>
			<a class="lastPage" href="javascript: $.pageSkip(<?=$pager['lastPageNumber']?>);"><i class="fa fa-step-forward"></i></a>
		<?php endif; ?>
		
		<span class="pageSkip">
			共 <?=$pager['totalPages']?> 页 
			到第<input id="pageNumber" name="pageNumber" value="<?=$pager['pageNumber']?>" maxlength="9" onpaste="return false;" />页
			<button type="submit"><i class="fa fa-forward"></i></button>
		</span>
	</div>
<?php endif; ?>