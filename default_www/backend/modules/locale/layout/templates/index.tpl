{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblIndex|ucfirst}</p>
			</div>

			{option:report}
			<div id="report">
				<div class="singleMessage successMessage">
					<p>{$reportMessage}</p>
				</div>
			</div>
			{/option:report}

			<div class="inner">
				{form:filter}
					<p>
						<label for="language">{$lblLanguage|ucfirst}</label>
						{$ddmLanguage} {$ddmLanguageError}
					</p>

					<p>
						<label for="application">{$lblApplication|ucfirst}</label>
						{$ddmApplication} {$ddmApplicationError}
					</p>

					<p>
						<label for="module">{$lblModule|ucfirst}</label>
						{$ddmModule} {$ddmModuleError}
					</p>

					<p>
						<label for="type">{$lblType|ucfirst}</label>
						{$ddmType} {$ddmTypeError}
					</p>

					<p>
						<label for="name">{$lblName|ucfirst}</label>
						{$txtName} {$txtNameError}
					</p>

					<p>
						<label for="value">{$lblValue|ucfirst}</label>
						{$txtValue} {$txtValueError}
					</p>

					{$btnSearch}
					<br /><br /><br />
				{/form:filter}

				{option:datagrid}
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblLocale|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add'}&language={$language}&application={$application}&module={$module}&type={$type}&name={$name}&value={$value}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
							</div>
						</div>

						<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
							<input type="hidden" name="offset" value="{$offset}" />
							<input type="hidden" name="order" value="{$order}" />
							<input type="hidden" name="sort" value="{$sort}" />
							<input type="hidden" name="language" value="{$language}" />
							<input type="hidden" name="application" value="{$application}" />
							<input type="hidden" name="module" value="{$module}" />
							<input type="hidden" name="type" value="{$type}" />
							<input type="hidden" name="name" value="{$name}" />
							<input type="hidden" name="value" value="{$value}" />
							<div class="datagridHolder">
								{$datagrid}
							</div>
						</form>
					</div>
				{/option:datagrid}
				{option:!datagrid}{$msgNoItems}{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}