<?php
$tag = $info[1];
if (!$tag) {
	error ("invalid access.");
	return false;
}

$tag = $database->escapeString ($tag);
$HOME_URL = HOME_URL;
$statement = $database->createStatement ();
$statement->set ("SELECT *, UNIX_TIMESTAMP(`regdate`) AS `regdate`, UNIX_TIMESTAMP(`editdate`) AS `date` FROM `pzen_thread` WHERE `tags` LIKE '%*{$tag}*%' ORDER BY `regdate` DESC");
$count = $statement->numRows ();
?>
<p class="pageTitle"><?php echo $count; ?> Threads in <?php echo $tag; ?></p>
<ul class="list">
<?php
while ($data = $statement->fetchArray ()) {
	$data['cutSubject'] = cut_str ($data['subject'], 66);
	$data['content'] = cut_str ($data['content'], 200);
	$data['fullDate'] = date ("H:i:s M d, Y", $data['date']);
	$data['date'] = date ("M d", $data['date']);
	echo <<<LIST
<li>
	<dl>
		<dt><a href="{$HOME_URL}thread/read/{$data['index']}" title="{$data['subject']}"><span class="subject">{$data['cutSubject']}</span></a> <span class="author">{$data['name']}</span> <span class="date" title="{$data['fullDate']}">at {$data['date']}</span></dt>
		<dd class="description">{$data['content']}</dd>
	</dl>
</li>
LIST;
}
?>
</ul>
