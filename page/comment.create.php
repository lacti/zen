<?php
if (!$user) {
	return false;
}
?>
<p></p>
<form name="commentCreate" method="post" action="<?php echo HOME_URL; ?>comment/action/create" enctype="multipart/form-data">
	<input type="hidden" name="threadIndex" value="<?php echo $threadIndex; ?>" />
	<div style="border: 1px solid #eee; background: #fafafa; padding: 6px;">
		<textarea name="content" placeholder="content..." cols="48" rows="5" style="width: 623px; border: 1px solid #aaa; padding: 4px;"></textarea>
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e9e9e9; padding: 6px;">
		<input name="tags" type="text" placeholder="tags.." size="40" style="width: 623px; border: 1px solid #aaa; padding: 4px;" />
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e1e1e1; padding: 6px; display: none;" id="commentAttachFiles">
	</div>
	<div style="margin-top: 1px;">
		<button type="button" class="minibutton" onclick="javascript:addAttachField ('commentAttachFiles');">
			<span>Add File</span>
		</button>
		<button type="submit" class="minibutton" style="float: right;">
			<span>comment</span>
		</button>
	</div>
</form>
