<section id="subscribeFormWidget" class="mod">
	<div class="inner">
		<div class="bd">
			{form:subscribe}
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
