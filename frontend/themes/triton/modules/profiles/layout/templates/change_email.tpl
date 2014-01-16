{* Success *}
{option:updateEmailSuccess}
	<div class="message success"><p>{$msgUpdateEmailIsSuccess}</p></div>
{/option:updateEmailSuccess}

{* Error *}
{option:updateEmailHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:updateEmailHasFormError}

<section id="updateEmailForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:updateEmail}
				<fieldset>
					<p{option:txtPasswordError} class="errorArea"{/option:txtPasswordError}>
						<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtPassword}{$txtPasswordError}
					</p>
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail}{$txtEmailError}
					</p>
					<p>
						<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
					</p>
				</fieldset>
			{/form:updateEmail}
		</div>
	</div>
</section>
