{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExtensions|ucfirst}: {$lblThemes}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'upload_theme'}" class="button icon iconImport" title="{$lblUploadTheme|ucfirst}">
			<span>{$lblUploadTheme|ucfirst}</span>
		</a>
		<a href="http://www.fork-cms.com/extensions" class="button icon iconNext" title="{$lblFindThemes|ucfirst}">
			<span>{$lblFindThemes|ucfirst}</span>
		</a>
	</div>
</div>

{form:settingsThemes}
	<div class="box">
		<div class="heading">
			<h3>{$lblThemes|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
		</div>
		<div class="options">
			<p>{$msgHelpThemes}</p>
			<ul id="themeSelection" class="selectThumbList clearfix">
				{iteration:themes}
					<li{option:themes.selected} class="selected"{/option:themes.selected}>
						{$themes.rbtThemes}
						<label for="{$themes.id}">
							<img src="{$themes.thumbnail}" width="172" height="129" alt="{$themes.label|ucfirst}" />
							<span>{$themes.label|ucfirst}</span>
						</label>
					</li>
				{/iteration:themes}
			</ul>
			{option:rbtThemesError}<p class="error">{$rbtThemesError}</p>{/option:rbtThemesError}
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settingsThemes}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}