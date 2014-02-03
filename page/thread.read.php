<?php
$threadIndex = $info[1];
$thread = readThread ($threadIndex);
?>
<script type="text/javascript">
<!--
function deleteThread (index) {
	if (confirm ("Do you want delete this thread?")) {
		location.href = "<?php echo HOME_URL; ?>thread/action/delete/" + index;
	}
}
//-->
</script>

<?php
if ($user && $user['index'] == $thread['userIndex']) {
	echo "<h2>
	{$thread['subject']}
	<span class=\"tools\">(
		<a href=\"". HOME_URL ."thread/update/{$thread['index']}\" class=\"tools\" title=\"edit\">e</a>
		<a href=\"#\" onclick=\"deleteThread({$thread['index']});\" class=\"tools\" title=\"delete\">d</a>
	)</span>
</h2>";
} else {
	echo "<h2>{$thread['subject']}</h2>";
}
?>

<ul id="attachments" style="float: right;">
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT * FROM `pzen_thread_attach` WHERE `threadIndex` = %1",
	array ($threadIndex));
while ($attach = $statement->fetchArray ()) {
	echo "\t<li><a href=\"". HOME_URL ."attach/thread/{$attach['index']}\" title=\"{$attach['name']}\">". cut_str ($attach['name'], 20) ." (". byteToString ($attach['size']) .")</a></li>\n";
}
?>
</ul>

<div class="meta">
	<div class="writer">
	<span class="author"><?php echo $thread['name']; ?></span>
	<span class="published"><?php echo date ("M d Y", $thread['date']); ?></span>
	</div>
</div>
<div style="word-break: break-all; line-height: 1.6em;">
<?php echo CTextProcessor::parse ($thread['content']); ?>
</div>
<div style="word-break: break-all; line-height: 1.4em; text-align: right; font-size: 88%; color: #888;">
<?php echo convertTagsDisplayFormatFromDatabase ($thread['tags']); ?>
</div>
</div>

<?php require "page/comment.read.php"; ?>
<?php require "page/comment.create.php"; ?>