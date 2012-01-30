{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExtensions|ucfirst}: {$lblThemes}</h2>
	<div class="buttonHolderRight">
		{option:showExtensionsUploadTheme}
		<a href="{$var|geturl:'upload_theme'}" class="button icon iconImport" title="{$lblUploadTheme|ucfirst}">
			<span>{$lblUploadTheme|ucfirst}</span>
		</a>
		{/option:showExtensionsUploadTheme}

		<a href="http://www.fork-cms.com/extensions" class="button icon iconNext" title="{$lblFindThemes|ucfirst}">
			<span>{$lblFindThemes|ucfirst}</span>
		</a>
	</div>
</div>

{form:settingsThemes}
	{option:installableThemes}
	<div class="box">
		<div class="heading">
			<h3>{$lblInstallableThemes|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpInstallableThemes}</p>
			<ul id="installableThemes" class="selectThumbList clearfix">
				{iteration:installableThemes}
					<li>
						<label>
							<img src="{$installableThemes.thumbnail}" width="172" height="129" alt="{$installableThemes.label|ucfirst}" />
							<span>{$installableThemes.label|ucfirst}</span>
						</label>
						{option:showExtensionsInstallTheme}<a href="{$var|geturl:'install_theme'}&theme={$installableThemes.value}" data-message-id="confirmInstall" class="askConfirmation button icon iconNext linkButton" title="{$installableThemes.label|ucfirst}"><span>{$lblInstall|ucfirst}</span></a>{/option:showExtensionsInstallTheme}
						{option:showExtensionsDetailTheme}<a href="{$var|geturl:'detail_theme'}&theme={$installableThemes.value}" class="button icon iconDetail linkButton" title="{$installableThemes.label|ucfirst}"><span>{$lblDetails|ucfirst}</span></a>{/option:showExtensionsDetailTheme}
					</li>
				{/iteration:installableThemes}
			</ul>
		</div>
	</div>
	{/option:installableThemes}

	<div class="box">
		<div class="heading">
			<h3>{$lblInstalledThemes|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
		</div>
		<div class="options">
			<p>{$msgHelpThemes}</p>
			<ul id="installedThemes" class="selectThumbList clearfix">
				{iteration:installedThemes}
					<li{option:installedThemes.selected} class="selected"{/option:installedThemes.selected}>
						{$installedThemes.rbtInstalledThemes}
						<label for="{$installedThemes.id}">
							<img src="{$installedThemes.thumbnail}" width="172" height="129" alt="{$installedThemes.label|ucfirst}" />
							<span>{$installedThemes.label|ucfirst}</span>
						</label>
						{option:showExtensionsDetailTheme}<a href="{$var|geturl:'detail_theme'}&theme={$installedThemes.value}" class="button icon iconDetail linkButton" title="{$installedThemes.label|ucfirst}"><span>{$lblDetails|ucfirst}</span></a>{/option:showExtensionsDetailTheme}
					</li>
				{/iteration:installedThemes}
			</ul>
			{option:rbtInstalledThemesError}<p class="error">{$rbtThemesError}</p>{/option:rbtInstalledThemesError}
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settingsThemes}

<div id="confirmInstall" title="{$lblInstall|ucfirst}?" style="display: none;">
	<p>
		{$msgConfirmThemeInstall}
	</p>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}