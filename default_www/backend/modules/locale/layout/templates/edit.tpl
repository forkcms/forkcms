{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Locale</p>
			</div>

			<div class="inner">
				{form:edit}
					<p>
						<label for="application">Applicatie</label>
						{$ddmApplication} {$ddmApplicationError}
					</p>

					<p>
						<label for="module">Module</label>
						{$ddmModule} {$ddmModuleError}
					</p>

					<p>
						<label for="type">Type</label>
						{$ddmType} {$ddmTypeError}
					</p>

					<p>
						<label for="name">Naam</label>
						{$txtName} {$txtNameError}
					</p>

					<p>
						<label for="value">Waarde</label>
						{$txtValue} {$txtValueError}
					</p>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							{$btnSave}
						</div>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}