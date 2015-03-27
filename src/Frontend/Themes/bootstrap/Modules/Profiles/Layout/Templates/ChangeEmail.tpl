{* Success *}
{option:updateEmailSuccess}
	<div class="alert alert-success"><p>{$msgUpdateEmailIsSuccess}</p></div>
{/option:updateEmailSuccess}

{* Error *}
{option:updateEmailHasFormError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:updateEmailHasFormError}

<section id="updateEmailForm">
	{form:updateEmail}
		<fieldset>
			<p{option:txtPasswordError} class="alert alert-danger"{/option:txtPasswordError}>
				<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtPassword}{$txtPasswordError}
			</p>
			<p{option:txtEmailError} class="alert alert-danger"{/option:txtEmailError}>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail}{$txtEmailError}
			</p>
			<p>
				<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
			</p>
		</fieldset>
	{/form:updateEmail}
</section>