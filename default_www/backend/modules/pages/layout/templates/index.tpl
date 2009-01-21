{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	<h2>{$lblPages|ucfirst}</h2>
	
	<a href="{$var|geturl:add}">{$lblAdd}</a>
	
	<div class="tree">
		<ul>
		</ul>
	</div>
	
	<div class="content">
		{form:edit}
		{/form:edit}
	</div>
	

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}