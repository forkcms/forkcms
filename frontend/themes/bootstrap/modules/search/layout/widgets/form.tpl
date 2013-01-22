<section id="searchFormWidget" class="navbar-form pull-right form-inline search">
	{form:search}
		<label for="qWidget" class="hide">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
		{$txtQWidget}
		<input id="submit" class="btn" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
	{/form:search}
</section>