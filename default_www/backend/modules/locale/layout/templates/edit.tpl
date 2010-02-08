{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblEdit|ucfirst}</p>
			</div>

			<div class="inner">
				{form:edit}
				<div class="box">
					<div class="heading">
						&nbsp;
					</div>
					<div class="options">
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
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="edit" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
						</div>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}