{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblSearch}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblPagination|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="overviewNumItems">{$lblItemsPerPage|ucfirst}</label>
				{$ddmOverviewNumItems} {$ddmOverviewNumItemsError}
			</p>
			<p>
				<label for="autocompleteNumItems">{$lblItemsForAutocomplete|ucfirst}</label>
				{$ddmAutocompleteNumItems} {$ddmAutocompleteNumItemsError}
			</p>
			<p>
				<label for="autosuggestNumItems">{$lblItemsForAutosuggest|ucfirst}</label>
				{$ddmAutosuggestNumItems} {$ddmAutosuggestNumItemsError}
			</p>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblModuleWeight|ucfirst}</h3>
		</div>

		<div class="options" id="searchModules">

			<p>{$msgHelpWeightGeneral}</p>
			<div class="dataGridHolder">
				<table class="dataGrid">
					<tr>
						<th style="width: 30%;"><span>{$msgIncludeInSearch}</span></th>
						<th><span>{$lblModule|ucfirst}</span></th>
						<th>
							<span>
								<div class="oneLiner">
									<p>{$lblWeight|ucfirst}</p>
									<abbr class="help">(?)</abbr>
									<div class="tooltip" style="display: none;">
										<p>{$msgHelpWeight}</p>
									</div>
								</div>
							</span>
						</th>
					</tr>
					{iteration:modules}
						<tr class="{cycle:odd:even}">
							<td><span class="checkboxHolder">{$modules.chk}</span></td>
							<td><label for="{$modules.id}">{$modules.label}</label></td>
							<td><label for="{$modules.id}Weight" class="visuallyHidden">{$lblWeight|ucfirst}</label>{$modules.txt} {option:modules.txtError}<span class="formError">{$modules.txtError}</span>{/option:modules.txtError}</td>
						</tr>
					{/iteration:modules}
				</table>
			</div>

		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}