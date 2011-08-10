<div id="headerSearch">
	<h4>{$lblSearch|ucfirst}</h4>

	{form:search}
		<div class="oneLiner">
			<p>{$txtQWidget}</p>
			<p><input id="submit" class="inputSubmit" type="submit" name="submit" value="{$lblSearch|ucfirst}" /></p>
		</div>
	{/form:search}
</div>