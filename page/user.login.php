<?php
if (!$user) {
?>
<form name="userLogin" id="userLogin" method="post" action="<?php echo HOME_URL; ?>user/action/login">
	<input name="email" type="text" placeholder="id" />
	<input name="password" type="password" placeholder="password" />
	<button type="submit" class="hidden"></button>
	<a href="#" onclick="document.getElementById('userLogin').submit();" style="color: white">login</a>
	<input type="checkbox" name="autologin" style="position: relative; top: 3px;" title="autologin" />
	<span style="color: yellow">|</span>
	<a href="<?php echo HOME_URL; ?>user/create/" style="color: yellow">join</a>
	<span>&nbsp;</span>
</form>
<?php
} else {
?>
	<a href="<?php echo HOME_URL; ?>user/edit/" style="color: white"><?php echo $user['name']; ?></a>
	<span style="color: yellow">|</span>
	<a href="<?php echo HOME_URL; ?>thread/create/" style="color: white">post</a>
	<span style="color: yellow">|</span>
	<a href="<?php echo HOME_URL; ?>user/action/logout/" style="color: yellow">logout</a>
	<span>&nbsp;</span>
<?php
}
?>
