<div class="sideMain">
<p class="sideTitle">comments...</p>
<ul id="comments">
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT `index`, `threadIndex`, `name`, `content`, UNIX_TIMESTAMP(`regdate`) AS `regdate`, UNIX_TIMESTAMP(`editdate`) AS `date` FROM `pzen_comment` ORDER BY `regdate` DESC LIMIT 0, 8");
while ($data = $statement->fetchArray ()) {
	$data['content'] = cut_str ($data['content'], 30);
	$data['date'] = date ("M d", $data['date']);
	echo "
	<li>
		<div>
			<a href=\"". HOME_URL ."thread/read/{$data['threadIndex']}#comment{$data['index']}\">{$data['content']}</a>
		</div>
		<div class=\"meta\">{$data['name']} at {$data['date']}</div>
	</li>
";
}
?>
</ul>
</div>
