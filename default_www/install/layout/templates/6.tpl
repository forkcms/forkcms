{include:file='{$PATH_WWW}/install/layout/templates/head.tpl'}
<body id="installer">
	<div id="installHolder" class="step6">
		<h2>Installation complete</h2>
		<p>Fork CMS is installed! You can now <a href="../private/">log in</a> using these credentials.</p>
		<p>Remember your login details for future reference:</p>
		<table border="0" cellspacing="0" cellpadding="0" class="infoGrid">
			<tr>
				<th>CMS</th>
				<td><a href="../private/">http://{$url}/private/</a></td>
			</tr>
			<tr>
				<th>Email</th>
				<td>{$email}</td>
			</tr>
			<tr>
				<th>Password</th>
				<td>
					<span id="plainPassword" class="hidden">{$password}</span>
					<span id="fakePassword">********</span>
					<input type="checkbox" id="showPassword" name="showPassword" /> <label for="showPassword">show password</label>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>