{include:file='{$PATH_WWW}/install/layout/templates/head.tpl'}
<body id="installer">
	<div id="installHolder" class="step3">
		<h2>Install Fork CMS</h2>
		<div class="horizontal">
			<div>
				<p>Hooray! Your server meets the requirements to run Fork CMS.</p>
			</div>
		</div>
		<h3>Database configuration</h3>
		{form:step3}
			{option:formError}<div class="formMessage errorMessage"><p>{$formError}</p></div>{/option:formError}
			<div class="horizontal">
				<p>Enter your database details. Make sure this database already exists.</p>
				<p>
					<label for="hostname">Hostname<abbr title="Required field">*</abbr></label>
					{$txtHostname} {$txtHostnameError}
					<span class="helpTxt">If you are working locally, your hostname is probably <strong>localhost</strong>.</span>
				</p>
				<p>
					<label for="database">Database<abbr title="Required field">*</abbr></label>
					{$txtDatabase} {$txtDatabaseError}
				</p>
				<p>
					<label for="username">Username<abbr title="Required field">*</abbr></label>
					{$txtUsername} {$txtUsernameError}
				</p>
				<p>
					<label for="password">Password<abbr title="Required field">*</abbr></label>
					{$txtPassword} {$txtPasswordError}
				</p>
			</div>
			<p class="buttonHolder spacing">
				<input id="installerButton" class="button inputButton mainButton" type="submit" name="installer" value="Next" />
			</p>
		{/form:step3}
	</div>
</body>
</html>