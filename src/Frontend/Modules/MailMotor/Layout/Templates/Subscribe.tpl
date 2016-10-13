{*
	The variables that are available to use:
	- $mailMotorSubscribeHasFormError
	- $mailMotorSubscribeHasError
	- $mailMotorSubscribeIsSuccess
	- $mailMotorSubscribeHideForm
*}

<div id="mailMotorSubscribeIndex" class="mod">
	<div class="inner">
		<div class="hd">
			<h3>{$lblSubscribeToNewsletter|ucfirst}</h3>
		</div>
		<div class="bd">
			{option:mailMotorSubscribeHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:mailMotorSubscribeHasFormError}
			{option:mailMotorSubscribeHasError}<div class="message error"><p>{$errSubscribeFailed}</p></div>{/option:mailMotorSubscribeHasError}
			{option:mailMotorSubscribeIsSuccess}
				<div class="message success">
					<p>
						{option:mailMotorSubscribeHasDoubleOptIn}{$msgSubscribeSuccessForDoubleOptIn}{/option:mailMotorSubscribeHasDoubleOptIn}
						{option:!mailMotorSubscribeHasDoubleOptIn}{$msgSubscribeSuccess}{/option:!mailMotorSubscribeHasDoubleOptIn}
					</p>
				</div>
			{/option:mailMotorSubscribeIsSuccess}

			{option:!mailMotorSubscribeHideForm}
				{form:mailMotorSubscribeForm}
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					{option:interests}
					<p{option:chkInterestsError} class="errorArea"{/option:chkInterestsError}>
						<label>{$lblInterests|ucfirst}</label>
						<ul class="inputList">
							{iteration:interests}<li>{$interests.chkInterests} <label for="{$interests.id}">{$interests.label|ucfirst}</label></li>{/iteration:interests}
						</ul>
						{$chkInterestsError}
					</p>
					{/option:interests}
					<p>
						<input id="send" class="inputSubmit btn" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
					</p>
				{/form:mailMotorSubscribeForm}
			{/option:!mailMotorSubscribeHideForm}
		</div>
	</div>
</div>
