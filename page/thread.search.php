<?php
$searchKeyword = trim ($_POST['searchKeyword']);
if (empty ($searchKeyword))
	redirectBack ();

$queries = array ();
$targets = array ('subject', 'content', 'searchHint');
$keywords = explode (" ", $searchKeyword);
foreach ($targets as $target) {
	$targetQueries = array ();
	foreach ($keywords as $keyword) {
		$escapedKeyword = $database->escapeString ($keyword);
		array_push ($targetQueries, "`{$target}` LIKE '%{$escapedKeyword}%'");
	}
	array_push ($queries, implode (" AND ", $targetQueries));
}
$whereQuery = "(" . implode (") OR (", $queries) . ")";

$statement = $database->createStatement ();
$statement->set ("SELECT *, UNIX_TIMESTAMP(`regdate`) AS `regdate`, UNIX_TIMESTAMP(`editdate`) AS `date` FROM `pzen_thread`
	WHERE {$whereQuery} ORDER BY `regdate` DESC");
$count = $statement->numRows ();
?>
<p class="pageTitle"><?php echo $count; ?> Threads searching in '<?php echo $searchKeyword; ?>'</p>
<ul class="list">
<?php
$HOME_URL = HOME_URL;
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
