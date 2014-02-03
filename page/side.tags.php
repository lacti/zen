<div class="sideMain">
<p class="sideTitle">tags...</p>
<ul class="tags">
<?php
foreach (array ("thread", "comment") as $table) {
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `pzen_{$table}_tags` ORDER BY `count` DESC, `tag` ASC LIMIT 0, 5");
	while ($data = $statement->fetchArray ()) {
		echo "
	<li>
		<div>
			<a href=\"". HOME_URL ."tag/{$table}s/{$data['tag']}\">{$data['tag']}<span class=\"meta\">({$data['count']})</span></a>
		</div>
	</li>
	";
	}
}
?>
</ul>
<div style="float: right;"><a href="<?php echo HOME_URL; ?>tag/list" style="font-size: 88%; font-weight: bold; color: black;">all</a></div>
<div style="clear: both;"></div>
</div>
