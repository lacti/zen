<div class="sideMain">
<p class="sideTitle">categories...</p>
<ul id="categories">
<?php
$statement = $database->createStatement ();
$statement->set ("SELECT * FROM `pzen_category_view`");
while ($data = $statement->fetchArray ()) {
	echo "
	<li>
		<div>
			<a href=\"". HOME_URL ."category/{$data['index']}\">{$data['name']}<span class=\"meta\">({$data['count']})</span></a>
		</div>
	</li>
";
}
?>
</ul>
<div style="clear: both;"></div>
</div>
