<section>
	{form:subscribe}
		<input type="hidden" name="form_token" id="formToken" value="{$formToken}" />
		<p>
			<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtEmail} {$txtEmailError}
		</p>
		<p>
			<input id="send" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
		</p>
	{/form:subscribe}
</section>
