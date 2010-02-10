{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblAdd|ucfirst}</p>
			</div>

			<div class="inner">
				{form:add}
					<div class="box">
						<div class="heading">
							<h3>{$lblAddTranslation|ucfirst}</h3>
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
								<input id="add" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
							</div>
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}