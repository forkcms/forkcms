{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Installation complete</h2>
<p>Fork CMS is installed! You can now log in using:</p>

<table class="infoGrid">
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
	<a class="button" href="../">View your new website</a>
	<a class="button" href="../private">Log in to Fork CMS</a>
</div>

{option:warnings}
	<div class="generalMessage infoMessage">
		<p><strong>There are some warnings for following module(s):</strong></p>
		{iteration:warnings}
			<ul>
				<li>
					<strong>{$warnings.module}</strong>
					<ul>
						{iteration:warnings.warnings}
							<li>- {$warnings.warnings.message}</li>
						{/iteration:warnings.warnings}
					</ul>
				</li>
			</ul>
		{/iteration:warnings}
	</div>
{/option:warnings}

{include:{$PATH_WWW}/install/layout/templates/foot.tpl}