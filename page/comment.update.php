<?php
if (!$user) {
	return false;
}
$commentIndex = $info[1];
$comment = readComment ($commentIndex);
$thread = readThread ($comment['threadIndex']);
?>
<p class="pageTitle">Comment at <?php echo $thread['subject']; ?></p>
<form name="commentUpdate" method="post" action="<?php echo HOME_URL; ?>comment/action/update">
	<input type="hidden" name="threadIndex" value="<?php echo $comment['threadIndex']; ?>" />
	<input type="hidden" name="index" value="<?php echo $commentIndex; ?>" />
	<div style="border: 1px solid #eee; background: #fafafa; padding: 6px;">
		<textarea name="content" placeholder="content..." cols="48" rows="12" style="width: 623px; border: 1px solid #aaa; padding: 4px;"><?php echo $comment['content']; ?></textarea>
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e9e9e9; padding: 6px;">
		<input name="tags" type="text" placeholder="tags.." size="48" style="width: 623px; border: 1px solid #aaa; padding: 4px;" value="<?php echo convertTagsDisplayFormatFromDatabase ($comment['tags']); ?>" />
	</div>
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT * FROM `pzen_comment_attach` WHERE `commentIndex` = %1",
	array ($commentIndex));
$displayable = $statement->hasTuple ()? "": " display: none;";
?>
	<div style="border: 1px solid #d9d9d9; background: #e1e1e1; padding: 6px;<?php echo $displayable; ?>" id="commentAttachFiles">
<?php
while ($attach = $statement->fetchArray ()) {
	echo "		<div>
			<button type=\"button\" onclick=\"javascript:deleteAttachField (this);\" class=\"minibutton\">
				<span>delete</span>
			</button>
			<input type=\"hidden\" name=\"oldFileIndex[]\" value=\"{$attach['index']}\" />
			<span>{$attach['name']}</span>
		</div>
";
}
?>
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
