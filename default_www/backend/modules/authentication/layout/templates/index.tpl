{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	<h2>{$lblLogin|ucfirst}</h2>
	{form:login}
		<fieldset>
			{option:hasError}<div class="error"><strong>{$errInvalidUsernamePasswordCombination}</strong></div>{/option:hasError}
			<dl>
				<dt><label for="backend_username">{$lblUsername|ucfirst}</label></dt>
				<dd>{$txtBackendUsername} {$txtBackendUsernameError}</dd>
				<dt><label for="backend_password">{$lblPassword|ucfirst}</label></dt>
				<dd>{$txtBackendPassword} {$txtBackendPasswordError}</dd>
				<dd><input type="submit" value="{$lblLogin}" />
			</dl>
		</fieldset>
	{/form:login}

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}