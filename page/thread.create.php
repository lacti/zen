<?php
if (!$user) {
	error ("no authority.");
	return false;
}
?>
<form name="threadCreate" method="post" action="<?php echo HOME_URL; ?>thread/action/create" enctype="multipart/form-data">
	<div style="border: 1px solid #d9d9d9; background: #e1e1e1; padding: 6px;">
		<input name="category" id="threadCreateCategory" type="text" placeholder="category" size="48" style="width: 323px; border: 1px solid #aaa; padding: 4px;" />
		<select name="categories" style="width: 292px; height: 28px;" onchange="document.getElementById('threadCreateCategory').value=this.value">
			<option value="기타" selected="selected">기타</option>
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT `name` FROM `pzen_category` WHERE `index` > 1");
while ($category = $statement->fetchArray ()) {
	echo "\t\t<option value=\"{$category['name']}\">{$category['name']}</option>\n";
}
?>
		</select>
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e9e9e9; padding: 6px;">
		<input name="subject" type="text" placeholder="subject" size="48" style="width: 623px; border: 1px solid #aaa; padding: 4px;" />
	</div>
	<div style="border: 1px solid #eee; background: #fafafa; padding: 6px;">
		<textarea name="content" placeholder="content..." cols="48" rows="18" style="width: 623px; border: 1px solid #aaa; padding: 4px;"></textarea>
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e9e9e9; padding: 6px;">
		<input name="tags" type="text" placeholder="tags.." size="48" style="width: 623px; border: 1px solid #aaa; padding: 4px;" />
	</div>
	<div style="border: 1px solid #d9d9d9; background: #e1e1e1; padding: 6px; display: none;" id="threadAttachFiles">
	</div>
	<div style="margin-top: 8px;">
		<button type="button" class="classy" onclick="javascript:addAttachField ('threadAttachFiles');">
			<span>Add File</span>
		</button>
		<div style="float: right;">
			<button type="submit" class="classy">
				<span>Post</span>
			</button>
			<button type="button" onclick="history.go (-1);" class="classy">
				<span>Cancel</span>
			</button>
		</div>
	</div>
</form>
