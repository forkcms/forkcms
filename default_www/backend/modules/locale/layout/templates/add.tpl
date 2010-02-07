{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblAdd|ucfirst}</p>
			</div>

			{option:formError}
			<div id="report">
				<div class="singleMessage errorMessage">
					<p>{$errFormError}</p>
				</div>
			</div>
			{/option:formError}

			<div class="inner">
				{form:add}
				<div class="box">
					<div class="heading">
						&nbsp;
					</div>
					<div class="options">
						<div class="horizontal">
							<p>
								<label for="name">{$lblName|ucfirst}</label>
								{$txtName} {$txtNameError}
							</p>

							<p>
								<label for="value">{$lblValue|ucfirst}</label>
								{$txtValue} {$txtValueError}
							</p>

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

						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							{$btnSave}
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}