{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Installation complete</h2>
<p>Fork CMS is installed! You can now log in using:</p>

<table border="0" cellspacing="0" cellpadding="0" class="infoGrid">
	<tr>
		<th>Your e-mail</th>
		<td>{$email}</td>
	</tr>
	<tr>
		<th>Your password</th>
		<td>
			<span id="plainPassword" class="hidden">{$password}</span>
			<span id="fakePassword">••••••••••••</span>
			<input type="checkbox" id="showPassword" name="showPassword" /> <label for="showPassword">show password</label>
		</td>
	</tr>
</table>

<div class="buttonHolder">
	<a class="button" href="/">View your new website</a>
	<a class="button" href="../private">Log in to Fork CMS</a>
</div>

{include:{$PATH_WWW}/install/layout/templates/foot.tpl}