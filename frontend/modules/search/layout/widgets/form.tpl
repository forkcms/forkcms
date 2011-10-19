<section id="searchFormWidget" class="mod">
	<div class="inner">
		<header class="hd">
			<h3>{$lblSearch|ucfirst}</h3>
		</header>
		<div class="bd">
			{form:search}
				<p>
					<label for="q_widget">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtQWidget}
				</p>
				<p>
					<input id="submit" class="inputSubmit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
				</p>
			{/form:search}
		</div>
	</div>
</section>