{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_MODULES_PATH}/dashboard/layout/templates/structure_start.tpl}

<div id="dashboardWidgets" class="clearfix">
	<div id="editDashboardMessage" class="generalMessage infoMessage" style="display:none; margin: 12px;">
		{$msgHelpEditDashboard}
		<a href="#" id="doneEditingDashboard">{$lblDone|ucfirst}</a>
	</div>

	<div class="leftColumn column">
		{iteration:leftColumn}
			<div class="sortableWidget{option:leftColumn.hidden} isRemoved{/option:leftColumn.hidden}" data-module="{$leftColumn.module}" data-widget="{$leftColumn.widget}" data-title="{$leftColumn.title}"{option:leftColumn.hidden} style="display: none;"{/option:leftColumn.hidden}>
				<a href="#" class="editDashboardClose ui-dialog-titlebar-close ui-corner-all" style="display: none;"><span class="ui-icon ui-icon-closethick">close</span></a>
				{option:leftColumn.hidden}
					<div id="widgetBlogComments" class="box">
						<div class="heading">
							<h3>{$leftColumn.title}</h3>
						</div>
						<div class="options" style="display: none;">
							{$msgWillBeEnabledOnSave}
						</div>
					</div>
				{/option:leftColumn.hidden}
				{option:!leftColumn.hidden}
					{include:{$leftColumn.template}}
				{/option:!leftColumn.hidden}
			</div>
		{/iteration:leftColumn}
		&#160;
	</div>

	<div class="middleColumn column">
		{iteration:middleColumn}
			<div class="sortableWidget{option:middleColumn.hidden} isRemoved{/option:middleColumn.hidden}" data-module="{$middleColumn.module}" data-widget="{$middleColumn.widget}" data-title="{$middleColumn.title}"{option:middleColumn.hidden} style="display: none;"{/option:middleColumn.hidden}>
				<a href="#" class="editDashboardClose ui-dialog-titlebar-close ui-corner-all" style="display: none;"><span class="ui-icon ui-icon-closethick">close</span></a>
				{option:middleColumn.hidden}
					<div id="widgetBlogComments" class="box">
						<div class="heading">
							<h3>{$middleColumn.title}</h3>
						</div>
						<div class="options" style="display: none;">
							{$msgWillBeEnabledOnSave}
						</div>
					</div>
				{/option:middleColumn.hidden}
				{option:!middleColumn.hidden}
					{include:{$middleColumn.template}}
				{/option:!middleColumn.hidden}
			</div>
		{/iteration:middleColumn}
		&#160;
	</div>

	<div class="rightColumn column">
		{iteration:rightColumn}
			<div class="sortableWidget{option:rightColumn.hidden} isRemoved{/option:rightColumn.hidden}" data-module="{$rightColumn.module}" data-widget="{$rightColumn.widget}" data-title="{$rightColumn.title}"{option:rightColumn.hidden} style="display: none;"{/option:rightColumn.hidden}>
				<a href="#" class="editDashboardClose ui-dialog-titlebar-close ui-corner-all" style="display: none;"><span class="ui-icon ui-icon-closethick">close</span></a>
				{option:rightColumn.hidden}
					<div id="widgetBlogComments" class="box">
						<div class="heading">
							<h3>{$rightColumn.title}</h3>
						</div>
						<div class="options" style="display: none;">
							{$msgWillBeEnabledOnSave}
						</div>
					</div>
				{/option:rightColumn.hidden}
				{option:!rightColumn.hidden}
					{include:{$rightColumn.template}}
				{/option:!rightColumn.hidden}
			</div>
		{/iteration:rightColumn}
		&#160;
	</div>
</div>

<p>
	<small>
		<a href="#" id="editDashboard">
			{$msgEditYourDashboard}
		</a>
	</small>
</p>

{include:{$BACKEND_MODULES_PATH}/dashboard/layout/templates/structure_end.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}