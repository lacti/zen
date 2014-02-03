<div class="sideMain">
<p class="sideTitle">threads...</p>
<ul id="threads">
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT `index`, `name`, `subject`, UNIX_TIMESTAMP(`regdate`) AS `date` FROM `pzen_thread` ORDER BY `regdate` DESC LIMIT 0, 10");
while ($data = $statement->fetchArray ()) {
	$data['cutSubject'] = cut_str ($data['subject'], 30);
	$data['date'] = date ("M d", $data['date']);
	$data['commentCount'] = countThreadComment ($data['index']);
	echo "
	<li>
		<div>
			<a href=\"". HOME_URL ."thread/read/{$data['index']}\" title=\"{$data['subject']}\">{$data['cutSubject']}</a>
			<span class=\"commentCount\">[{$data['commentCount']}]</span>
		</div>
		<div class=\"meta\">{$data['name']} at {$data['date']}</div>
	</li>
";
}
?>
</ul>
</div>
