<section id="searchFormWidget" class="navbar-form pull-right navbar-left search" role="search">
	{form:search}
		<label for="qWidget" class="hide">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
		<div class="form-group">
  		{$txtQWidget}
		</div>
		<input id="submit" class="btn btn-default" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
	{/form:search}
</section>