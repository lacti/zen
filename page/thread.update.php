<?php
$threadIndex = $info[1];
$thread = readThread ($threadIndex);
if (!$thread) {
	error ("cannot access this thread.");
	return false;
}

if (!$user || $user['index'] != $thread['userIndex']) {
	error ("no authority.");
	return false;
}
?>
<form name="threadUpdate" method="post" action="<?php echo HOME_URL; ?>thread/action/update" enctype="multipart/form-data">
	<div style="border: 1px solid #d9d9d9; background: #e1e1e1; padding: 6px;">
		<input name="category" id="threadCreateCategory" type="text" placeholder="category" size="48" style="width: 323px; border: 1px solid #aaa; padding: 4px;" value="<?php echo getCategoryName ($thread['categoryIndex']); ?>" />
		<select name="categories" style="width: 292px; height: 28px;" onchange="document.getElementById('threadCreateCategory').value=this.value">
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT * FROM `pzen_category`");
while ($category = $statement->fetchArray ()) {
	$selected = $thread['categoryIndex'] == $category['index']? " selected=\"selected\"": "";
	echo "\t\t<option value=\"{$category['name']}\"{$selected}>{$category['name']}</option>\n";
}
?>
		</select>
	</div>
	<input type="hidden" name="index" value="<?php echo $thread['index']; ?>" />
	<div style="border: 1px solid #d9d9d9; background: #e9e9e9; padding: 6px;">
		<input name="subject" type="text" placeholder="subject" size="48" style="width: 623px; border: 1px solid #aaa; padding: 4px;" value="<?php echo $thread['subject']; ?>" />
	</div>
	<div style="border: 1px solid #eee; background: #fafafa; padding: 6px;">
		<textarea name="content" placeholder="content..." cols="48" rows="18" style="width: 623px; border: 1px solid #aaa; padding: 4px;"><?php echo htmlspecialchars ($thread['content']); ?></textarea>
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e9e9e9; padding: 6px;">
		<input name="tags" type="text" placeholder="tags.." size="48" style="width: 623px; border: 1px solid #aaa; padding: 4px;" value="<?php echo convertTagsDisplayFormatFromDatabase ($thread['tags']); ?>" />
	</div>
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT * FROM `pzen_thread_attach` WHERE `threadIndex` = %1",
	array ($threadIndex));
$displayable = $statement->hasTuple ()? "": " display: none;";
?>
	<div style="border: 1px solid #d9d9d9; background: #e1e1e1; padding: 6px;<?php echo $displayable; ?>" id="threadAttachFiles">
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
	<div style="margin-top: 8px;">
		<button type="button" class="classy" onclick="javascript:addAttachField ('threadAttachFiles');">
			<span>Add File</span>
		</button>
		<div style="float: right;">
			<button type="submit" class="classy">
				<span>Update</span>
			</button>
			<button type="button" onclick="history.go (-1);" class="classy">
				<span>Cancel</span>
			</button>
		</div>
	</div>
</form>
