<div id="searchFormWidget" class="mod">
	<div class="inner">
		<div class="hd">
			<h3>{$lblSearch|ucfirst}</h3>
		</div>
		<div class="bd">
			{form:search}
				<p>
					{$txtQ} <input id="submit" class="inputSubmit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
				</p>
			{/form:search}
		</div>
	</div>
</div>