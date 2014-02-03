<?php
$categoryIndex = $info[0];
if (!$categoryIndex) $categoryIndex = 1;

$category = getCategoryInformation ($categoryIndex);
if (!$category) {
	error ("invalid access.");
	return false;
}
?>
<p class="pageTitle"><?php echo $category['count']; ?> Threads in <?php echo $category['name']; ?></p>
<ul class="list">
<?php
$HOME_URL = HOME_URL;
$statement = $database->createStatement ();
$statement->set ("SELECT *, UNIX_TIMESTAMP(`regdate`) AS `regdate`, UNIX_TIMESTAMP(`editdate`) AS `date` FROM `pzen_thread` WHERE `categoryIndex` = %1 ORDER BY `regdate` DESC",
	array ($categoryIndex));
while ($data = $statement->fetchArray ()) {
	$data['cutSubject'] = cut_str ($data['subject'], 66);
	$data['content'] = cut_str ($data['content'], 200);
	$data['fullDate'] = date ("H:i:s M d, Y", $data['date']);
	$data['date'] = date ("M d", $data['date']);
	$data['commentCount'] = countThreadComment ($data['index']);
	echo <<<LIST
<li>
	<dl>
		<dt>
			<a href="{$HOME_URL}thread/read/{$data['index']}" title="{$data['subject']}"><span class="subject">{$data['cutSubject']}</span></a>
			<span class="author">{$data['name']}</span>
			<span class="date" title="{$data['fullDate']}">at {$data['date']}</span>
			<span class="commentCount">({$data['commentCount']} comments)</span>
		</dt>
		<dd class="description">{$data['content']}</dd>
	</dl>
</li>
LIST;
}
?>
</ul>
