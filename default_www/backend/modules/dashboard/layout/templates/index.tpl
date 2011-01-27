{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_MODULES_PATH}/dashboard/layout/templates/structure_start.tpl}

<div id="dashboardWidgets">

	<div class="leftColumn">
	{iteration:leftColumn}
		{include:{$leftColumn.template}}
	{/iteration:leftColumn}
	</div>

	<div class="middleColumn">
	{iteration:middleColumn}
		{include:{$middleColumn.template}}
	{/iteration:middleColumn}
	</div>

	<div class="rightColumn">
	{iteration:rightColumn}
		{include:{$rightColumn.template}}
	{/iteration:rightColumn}
	</div>
</div>

{include:{$BACKEND_MODULES_PATH}/dashboard/layout/templates/structure_end.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}