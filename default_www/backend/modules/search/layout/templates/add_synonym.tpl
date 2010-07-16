{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblSearch|ucfirst}: {$lblAddSynonym}</h2>
</div>

{form:addItem}
	<div id="tabContent">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td id="leftColumn">
					<div class="box">
						<div class="heading">
							<h3>{$lblTerm|ucfirst}</h3>
						</div>
						<div class="options">
							<label for="term">{$lblTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTerm} {$txtTermError}
						</div>
					</div>
				</td>

				<td id="sidebar">
					<div id="synonymBox" class="box">
						<div class="heading">
							<h4>{$lblSynonyms|ucfirst}</h4>
						</div>

						<div class="options">
							{$txtSynonym} {$txtSynonymError}
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddSynonym|ucfirst}" />
		</div>
	</div>
{/form:addItem}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}