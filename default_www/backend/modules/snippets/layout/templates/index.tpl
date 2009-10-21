{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

{option:report}
	<div class="report fadeOutAfterMouseMove">{$reportMessage}</div>
	{option:highlight}
		<script type="text/javascript">
			var highlightId = '#{$highlight}';
		</script>
	{/option:highlight}
{/option:report}

<h2>{$msgHeaderIndex}</h2>
<a href="{$var|geturl:add}" title="{$lblAdd}">{$lblAdd}</a>

<div>
	{option:datagrid}{$datagrid}{/option:datagrid}
	{option:!datagrid}{$msgNoItems}{/option:!datagrid}
</div>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}