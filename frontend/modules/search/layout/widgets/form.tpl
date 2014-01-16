<section>
	{form:search}
		<p>
			<label for="qWidget">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtQWidget}
		</p>
		<p>
			<input id="submit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
		</p>
	{/form:search}
</section>
