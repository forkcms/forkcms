<section id="subscribeFormWidget" class="mod">
	<div class="inner">
		<div class="bd">
			{form:subscribe}
				<input type="hidden" name="form_token" id="formToken" value="{$formToken}" />
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
				<p>
					<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSubscribe|ucfirst}" />
				</p>
			{/form:subscribe}
		</div>
	</div>
</section>
