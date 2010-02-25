{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{option:resetSuccess}
<div id="report">
	<div class="singleMessage successMessage">
		<p>{$msgCoreResetSuccess}</p>
	</div>
</div>
{/option:resetSuccess}

<div id="dashboardWidgets">
	<div id="wip" class="content">
		<p>Welcome to Fork NG. What you are seeing is a work in progress. Report any bugs in the <a href="http://projects.netlash.com/public/index.php/projects/369">Fork NG</a> project in ActiveCollab.</p>
	</div>

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
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}