{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabName">{$lblName|ucfirst}</a></li>
			<li><a href="#tabDashboard">{$lblDashboard|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
			<li><a href="#tabUsers">{$lblUsers|ucfirst}</a></li>
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

		<div id="tabDashboard">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblDashboard|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<label for="toggleChecksWidgets">{$lblDisplayWidgets|ucfirst}</label>
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
					<label>{$lblSetPermissions|ucfirst}</label>
					<ul id="moduleList" class="inputList">
						{iteration:permissions}
							<li class="module">
								{$permissions.chk}
								<a href="#" class="icon iconCollapsed container" title="open">
									<span>
										<label for="{$permissions.id}">{$permissions.label}</label>
									</span>
								</a>
								<div class="datagridHolder hide clearfix">
									{$permissions.actions.dataGrid}
								</div>
							</li>
						{/iteration:permissions}
					</ul>
				</div>
			</div>
		</div>
		<div id="tabUsers">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblUsers|ucfirst} in {$groupName|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<div class="dataGridHolder">
						{option:dataGridUsers}{$dataGridUsers}{/option:dataGridUsers}
						{option:!dataGridUsers}{$msgNoUsers|ucfirst}{/option:!dataGridUsers}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showGroupsDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.name}}
			</p>
		</div>
		{/option:showGroupsDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}