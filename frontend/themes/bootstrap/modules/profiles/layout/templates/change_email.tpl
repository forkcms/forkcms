{* Success *}
{option:updateEmailSuccess}
	<div class="alert alert-success"><p>{$msgUpdateEmailIsSuccess}</p></div>
{/option:updateEmailSuccess}

{* Error *}
{option:updateEmailHasFormError}
	<div class="alert alert-error"><p>{$errFormError}</p></div>
{/option:updateEmailHasFormError}

<section id="updateEmailForm" class="profiles">
	<div class="bd">
		{form:updateEmail}
			<fieldset class="form-horizontal">
				<div class="control-group{option:txtPasswordError} error{/option:txtPasswordError}">
					<label class="control-label" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtPassword}{$txtPasswordError}
					</div>
				</div>
				<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
					<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtEmail}{$txtEmailError}
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
					</div>
				</div>
			</fieldset>
		{/form:updateEmail}
	</div>
</section>
