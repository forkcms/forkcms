{option:updateEmailSuccess}<div class="alert alert-success" role="alert">{$msgUpdateEmailIsSuccess}</div>{/option:updateEmailSuccess}
{option:updateEmailHasFormError}<div class="alert alert-danger" role="alert">{$errFormError}</div>{/option:updateEmailHasFormError}

<section id="updateEmailForm" class="profiles">
	<div class="bd">
		{form:updateEmail}
			<fieldset class="form-horizontal">
				<div class="form-group{option:txtPasswordError} has-error{/option:txtPasswordError}">
					<label class="control-label col-sm-2" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="col-sm-6">
						{$txtPassword}{$txtPasswordError}
					</div>
				</div>
				<div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
					<label class="control-label col-sm-2" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="col-sm-6">
						{$txtEmail}{$txtEmailError}
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-6">
						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
					</div>
				</div>
			</fieldset>
		{/form:updateEmail}
	</div>
</section>
