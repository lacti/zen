<div id="tagList">
<?php
foreach (array ("thread", "comment") as $table) {
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `pzen_{$table}_tags` ORDER BY `count` DESC, `tag` ASC");
	$count = $statement->numRows ();
?>
<p class="pageTitle"><?php echo $count; ?> <?php echo ucfirst ($table); ?> tags</p>

<ul class="tags">
<?php
	while ($data = $statement->fetchArray ()) {
		echo "
	<li>
		<div>
			<a href=\"". HOME_URL ."tag/{$table}s/{$data['tag']}\">{$data['tag']}<span class=\"meta\">({$data['count']})</span></a>
		</div>
	</li>
	";
	}
?>
</ul>
<div style="clear: both; margin-bottom: 1em;">&nbsp;</div>
<?php } ?>
</div>
