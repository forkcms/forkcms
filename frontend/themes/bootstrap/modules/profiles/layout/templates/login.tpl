{* Error *}
{option:formError}
	<div class="alert alert-danger" role="alert">
		{option:loginError}{$loginError}{/option:loginError}
		{option:!loginError}{$errFormError}{/option:!loginError}
	</div>
{/option:formError}

<section id="loginForm" class="profiles">
	{form:login}
		<fieldset class="form-horizontal">
			<div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
				<label class="control-label col-sm-2" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="col-sm-6">
  				{$txtEmail}
  				{$txtEmailError}
				</div>
			</div>
			<div class="form-group{option:txtPasswordError} has-error{/option:txtPasswordError}">
				<label class="control-label col-sm-2" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="col-sm-6">
  				{$txtPassword}{$txtPasswordError}
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-6">
				  <div class="checkbox">
  					<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
  					{$chkRememberError}
				  </div>
				</div>
			</div>
			<div class="form-group">
			  <div class="col-sm-offset-2 col-sm-6">
    			<input class="btn btn-primary" type="submit" value="{$lblLogin|ucfirst}" />
    			<small><a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a></small>
			  </div>
			</div>
		</fieldset>
	{/form:login}
</section>
