<p class="pageTitle">Request error</p>
<?php
if ($message) echo "<p class=\"error\">$message</p>\n";
?>
<div style="text-align: center;">
<button onclick="location.href='<?php echo $backURL; ?>'" class="classy">
	<span>go back</span>
</button>
</div>