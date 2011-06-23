{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabName">{$lblName|ucfirst}</a></li>
			<li><a href="#tabPresets">{$lblPresets|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
		</ul>

		<div id="tabName">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblName|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtName} {$txtNameError}
					</p>
				</div>
			</div>
		</div>

		<div id="tabPresets">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblPresets|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<label for="widgetList">{$lblDisplayWidgets|ucfirst}</label>
					<div class="dataGridHolder groupHolder">
						{option:widgets}
							{$widgets}
						{/option:widgets}
						{option:!widgets}
							{$msgNoWidgets|ucfirst}
						{/option:!widgets}
					</div>
				</div>
			</div>
		</div>

		<div id="tabPermissions">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblModules|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<label for="moduleList">{$lblSetPermissions|ucfirst}</label>
					<ul id="moduleList" class="inputList">
						{iteration:permissions}
							<li class="module">
								{$permissions.chk}<a href="#" class="icon iconCollapsed container" title="open"><span><label for="modules{$permissions.label}">{$permissions.label}</label></span></a>
								<ul class="dataGridHolder hide">
									{$permissions.actions.dataGrid}
								</ul>
							</li>
						{/iteration:permissions}
					</ul>
				</div>
			</div>
		</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}