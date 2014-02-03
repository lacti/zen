<p class="pageTitle">Create your personal account</p>
<form name="userCreate" id="userCreate" method="post" action="<?php echo HOME_URL; ?>user/action/create">
<dl>
	<dt><label for="userCreateName">Username</label></dt>
	<dd><input name="name" id="userCreateName" size="30" /></dd>
	<dt><label for="userCreateEmail">User ID</label></dt>
	<dd><input name="email" id="userCreateEmail" size="30" /></dd>
	<dt><label for="userCreateEmail">Password</label></dt>
	<dd><input name="password" id="userCreatePassword" type="password" size="30" /></dd>
	<dt><label for="userCreateEmail">Confirm Password</label></dt>
	<dd><input name="password2" id="userCreatePassword2" type="password" size="30" /></dd>
</dl>
<div style="text-align: right; padding-right: 7em; margin-top: 2em;">
	<button type="submit" class="classy">
		<span>Create account</span>
	</button>
</div>
</form>
