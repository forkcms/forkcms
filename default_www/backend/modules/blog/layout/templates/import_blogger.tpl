{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblBlog|ucfirst}: {$lblImport}</h2>
</div>

{form:import}
	<div class="box">
		<div class="heading">
			<h3>{$lblFile|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="blogger">{$lblFile|ucfirst}</label>
				{$fileBlogger} {$fileBloggerError}
				<span class="helpTxt">{$msgHelpBlogger}</span>
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:import}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}