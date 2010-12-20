{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:edit}
	<div class="pageTitle">
		<h2>{$lblContentBlocks|ucfirst}: {$msgEditContentBlock|sprintf:{$title}}</h2>
	</div>

	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li class="notImportant"><a href="#tabRevisions">{$lblPreviousVersions|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<fieldset>
				<p>
					<label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtTitle} {$txtTitleError}
				</p>
				<p>
					<label for="text">{$lblContent|ucfirst}</label>
					{$txtText} {$txtTextError}
				</p>
				<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite}</label></p>
			</fieldset>
		</div>

		<div id="tabRevisions">
			<div class="datagridHolder">
				<div class="tableHeading">
					<div class="oneLiner">
						<h3 class="floater">{$lblPreviousVersions|ucfirst}</h3>
						<abbr class="help">(?)</abbr>
						<div class="balloon balloonAlt" style="display: none;">
							<p>{$msgHelpRevisions}</p>
						</div>
					</div>
				</div>

				{option:revisions}{$revisions}{/option:revisions}
				{option:!revisions}
					<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
						<tr>
							<td>
								<p>{$msgNoRevisions}</p>
							</td>
						</tr>
					</table>
				{/option:!revisions}
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete'}&amp;id={$id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>

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

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}