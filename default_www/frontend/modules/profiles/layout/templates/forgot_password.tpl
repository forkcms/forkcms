{* Success *}
{option:forgotPasswordSuccess}
	<div class="message success"><p>{$msgForgotPasswordIsSuccess}</p></div>
{/option:forgotPasswordSuccess}

{* Error *}
{option:forgotPasswordHasError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:forgotPasswordHasError}

{option:!forgotPasswordHideForm}
	<section id="forgotPasswordForm" class="mod">
		<div class="inner">
			<div class="bd">
				{form:forgotPassword}
					<fieldset>
						<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
							<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtEmail}{$txtEmailError}
						</p>
						<p>
							<input class="inputSubmit" type="submit" value="{$lblSend|ucfirst}" />
						</p>
					</fieldset>
				{/form:forgotPassword}
			</div>
		</div>
	</section>
{/option:!forgotPasswordHideForm}