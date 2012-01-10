{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblSitemap}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblSitemap|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="sitemapPagesItems">{$msgHelpSitemapPagesItems|ucfirst}</label>
				{$ddmSitemapPagesItems} {$ddmSitemapPagesItemsError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="sitemapImagesItems">{$msgHelpSitemapImagesItems|ucfirst}</label>
				{$ddmSitemapImagesItems} {$ddmSitemapImagesItemsError}
			</p>
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