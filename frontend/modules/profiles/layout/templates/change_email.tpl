{* Success *}
{option:updateEmailSuccess}
	<div class="alert-box success"><p>{$msgUpdateEmailIsSuccess}</p></div>
{/option:updateEmailSuccess}

{* Error *}
{option:updateEmailHasFormError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:updateEmailHasFormError}

<section>
	{form:updateEmail}
		<fieldset>
			<p{option:txtPasswordError} class="error-area"{/option:txtPasswordError}>
				<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtPassword}{$txtPasswordError}
			</p>
			<p{option:txtEmailError} class="error-area"{/option:txtEmailError}>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail}{$txtEmailError}
			</p>
			<p>
				<input type="submit" value="{$lblSave|ucfirst}" />
			</p>
		</fieldset>
	{/form:updateEmail}
</section>
