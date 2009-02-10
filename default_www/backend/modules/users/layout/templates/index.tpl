{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
	{option:reportAdd}
		<div class="report fadeOutAfterMouseMove">{$msgAdded|sprintf:{$var}}</div>
		{option:hilight}
		<script type="text/javascript">
			var hilightId = '#{$hilight}';
		</script>
		{/option:hilight}
	{/option:reportAdd}

	{option:reportDelete}
		<div class="report fadeOutAfterMouseMove">{$msgDeleted|sprintf:{$var}}</div>
	{/option:reportDelete}

	{option:reportEdit}
		<div class="report fadeOutAfterMouseMove">{$msgSaved|sprintf:{$var}}</div>
		{option:hilight}
		<script type="text/javascript">
			var hilightId = '#{$hilight}';
		</script>
		{/option:hilight}
	{/option:reportEdit}

	<h2>{$msgHeaderIndex|ucfirst}</h2>
	<a href="{$var|geturl:add:users}" title="{$lblAdd}">{$lblAdd}</a>
	
	<div>
	{option:dgUsers}{$dgUsers}{/option:dgUsers}
	{option:!dgUsers}{$msgNoUsers}{/option:!dgUsers}
	</div>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}