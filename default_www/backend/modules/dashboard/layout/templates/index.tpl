{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_MODULES_PATH}/dashboard/layout/templates/structure_start.tpl'}

<div id="dashboardWidgets">

	<div class="leftColumn">
	{iteration:leftColumn}
		{include:file='{$leftColumn.template}'}
	{/iteration:leftColumn}
	</div>

	<div class="middleColumn">
	{iteration:middleColumn}
		{include:file='{$middleColumn.template}'}
	{/iteration:middleColumn}
	</div>

	<div class="rightColumn">
	{iteration:rightColumn}
		{include:file='{$rightColumn.template}'}
	{/iteration:rightColumn}
	</div>
</div>

{include:file='{$BACKEND_MODULES_PATH}/dashboard/layout/templates/structure_end.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}