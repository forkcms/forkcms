{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	<h2>{$lblLogin|ucfirst}</h2>
	{form:authenticationIndex}
		<fieldset>
			{option:hasError}<div class="error"><strong>{$errInvalidUsernamePasswordCombination}</strong></div>{/option:hasError}
			<label for="backendUsername">{$lblUsername|ucfirst}</label>
			<p>{$txtBackendUsername} {$txtBackendUsernameError}</p>
				
			<label for="backendPassword">{$lblPassword|ucfirst}</label>
			<p>{$txtBackendPassword} {$txtBackendPasswordError}</p>
				
			<p>{$btnSubmit}</p>
		</fieldset>
	{/form:authenticationIndex}

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}