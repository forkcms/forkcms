{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblContentBlocks|ucfirst}: {$msgEditContentBlock|sprintf:{$title}}</h2>
</div>

{form:edit}
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<fieldset>
				<p>
					<label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtTitle} {$txtTitleError}
				</p>

				<div class="box">
					<div class="heading">
						<h3>
							<label for="text">{$lblContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						</h3>
					</div>
					<div class="optionsRTE">
						{$txtText} {$txtTextError}
					</div>
				</div>
				{option:ddmTemplate}<p>{$lblTemplate|ucfirst} <label for="template">{$ddmTemplate} {$ddmTemplateError}</label></p>{/option:ddmTemplate}
				<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite|ucfirst}</label></p>
			</fieldset>
		</div>

		<div id="tabVersions">
			<div class="tableHeading">
				<div class="oneLiner">
					<h3 class="oneLinerElement">{$lblPreviousVersions|ucfirst}</h3>
					<abbr class="help">(?)</abbr>
					<div class="tooltip" style="display: none;">
						<p>{$msgHelpRevisions}</p>
					</div>
				</div>
			</div>

			{option:revisions}
			<div class="dataGridHolder">
				{$revisions}
			</div>
			{/option:revisions}

			{option:!revisions}
				<p>{$msgNoRevisions}</p>
			{/option:!revisions}
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showContentBlocksDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		{/option:showContentBlocksDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$title}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}