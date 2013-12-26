{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Database configuration</h2>
{form:step5}
	{option:formError}<div class="formMessage errorMessage"><p>{$formError}</p></div>{/option:formError}
	<div id="javascriptDisabled" class="formMessage errorMessage"><p>Javascript should be enabled.</p></div>
	<div class="horizontal">
		<p>Enter your database details. Make sure this database already exists.</p>
		<p>
			<label for="hostname">Hostname<abbr title="Required field">*</abbr></label>
			{$txtHostname} : {$txtPort} {$txtHostnameError} {$txtPortError}
			<span class="helpTxt">If you are working locally, your hostname is probably <strong>localhost</strong>.</span>
		</p>
		<p>
			<label for="database">Database<abbr title="Required field">*</abbr></label>
			{$txtDatabase} {$txtDatabaseError}
			<span class="helpTxt">Make sure this database is empty!</span>
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
		<a href="index.php?step=4" class="button">Previous</a>
		<input id="installerButton" class="button inputButton mainButton" type="submit" name="installer" value="Next" disabled="disabled" />
	</p>
{/form:step5}

{include:{$PATH_WWW}/install/layout/templates/foot.tpl}