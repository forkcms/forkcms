{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	{option:report}
		<div class="report fadeOutAfterMouseMove">{$reportMessage}</div>
		{option:hilight}
			<script type="text/javascript">
				var hilightId = '#{$hilight}';
			</script>
		{/option:hilight}
	{/option:report}

	<h2>{$msgHeaderIndex|ucfirst}</h2>
	<a href="{$var|geturl:add}" title="{$lblAdd}">{$lblAdd}</a>
	
	<div id="pages">
		<div id="tree">
			TREE
		</div>
		<div id="form">
			
		</div>
	</div>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}