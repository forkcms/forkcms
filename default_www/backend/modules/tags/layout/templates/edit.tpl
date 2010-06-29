{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:edit}
	<div class="pageTitle">
		<h2>{$lblTags|ucfirst}: {$msgEditTag|sprintf:{$name}}</h2>
	</div>
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li class="notImportant"><a href="#tabUsedIn">{$lblUsedIn|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<fieldset>
				<p>
					<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtName} {$txtNameError}
				</p>
			</fieldset>
		</div>

		<div id="tabUsedIn">
			<div class="datagridHolder">
				<div class="tableHeading">
					<div class="oneLiner">
						<h3>{$lblUsedIn|ucfirst}</h3>
					</div>
				</div>

				{option:usage}{$usage}{/option:usage}
				{option:!usage}
					<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
						<tr>
							<td>
								<p>{$msgNoUsage}</p>
							</td>
						</tr>
					</table>
				{/option:!usage}
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}