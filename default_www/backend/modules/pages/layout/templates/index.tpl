{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	{option:report}
		<div class="report fadeOutAfterMouseMove">{$reportMessage}</div>
		{option:highlight}
			<script type="text/javascript">
				var highlightId = '#{$highlight}';
			</script>
		{/option:highlight}
	{/option:report}

	<h2>{$msgHeaderIndex|ucfirst}</h2>
	<a href="{$var|geturl:"add"}" title="{$lblAdd}">{$lblAdd}</a>
	
	<div id="pages">
		<div id="tree">
			{$tree}
		</div>
		<div id="form">
			
		</div>
	</div>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}