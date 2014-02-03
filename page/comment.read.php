<div class="content commentArea">
<?php
$HOME_URL = HOME_URL;
$statement = $database->createStatement ();
$statement->set ("SELECT *, UNIX_TIMESTAMP(`regdate`) AS `regdate`, UNIX_TIMESTAMP(`editdate`) AS `date` FROM `pzen_comment` WHERE `threadIndex` = %1 ORDER BY `regdate` ASC",
	array ($threadIndex));
if (!$statement->hasTuple ()) {
?>
<p id="commentTitle"><a id="comments">T</a>here are no user comments for this thread.</p>
<?php } else { ?>
<p id="commentTitle"><?php echo $statement->numRows (); ?> <a id="comments">C</a>omments..</p>
<ul class="list">
<?php
while ($data = $statement->fetchArray ()) {
	$data['fullDate'] = date ("H:i:s M d, Y", $data['date']);
	$data['date'] = date ("M d", $data['date']);
	$data['content'] = CTextProcessor::parse ($data['content']);
	echo <<<LIST
<li>
	<dl>
		<dt>
LIST;
	if ($user['index'] == $data['userIndex']) {
		echo <<<LIST
			<a href="{$HOME_URL}comment/update/{$data['index']}" title="Edit this comment">e</a>
			<a href="{$HOME_URL}comment/action/delete/{$data['index']}" title="Delete this comment">d</a>
LIST;
	}
	echo <<<LIST
			<a href="#comment{$data['index']}" id="comment{$data['index']}"><span class="author">{$data['name']}</span></a>
			<span class="date" title="{$data['fullDate']}">at {$data['date']}</span>
		</dt>
		<dd class="description">
			<div>{$data['content']}</div>
LIST;
	$attachStatement = $database->createStatement ();
	$attachStatement->set ("SELECT * FROM `pzen_comment_attach` WHERE `commentIndex` = %1",
		array ($data['index']));
	if ($attachStatement->hasTuple ()) {
		echo <<<LIST
			<div style="word-break: break-all; line-height: 1.4em; text-align: right; font-size: 88%; color: #888;">
				<ul class="commentAttachments">
LIST;
		while ($attach = $attachStatement->fetchArray ()) {
			echo "\t\t\t\t\t<li><a href=\"". HOME_URL ."attach/comment/{$attach['index']}\" title=\"{$attach['name']}\">". cut_str ($attach['name'], 20) ." (". byteToString ($attach['size']) .")</a></li>\n";
		}
		echo <<<LIST
				</ul>
				<div class="commentAttachmentClear"></div>
			</div>
LIST;
	}
	echo <<<LIST
		</dd>
	</dl>
</li>
LIST;
}
}
?>
</ul>
