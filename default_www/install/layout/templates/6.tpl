{include:file='{$PATH_WWW}/install/layout/templates/head.tpl'}

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

<p>The CMS can be accessed via <a href="http://{$url}/private/">http://{$url}/private/</a>. We recommend bookmarking this link.</p>

<div class="buttonHolder">
	<a class="button buttonMain" href="../private">Log in</a>
</div>

{include:file='{$PATH_WWW}/install/layout/templates/foot.tpl'}