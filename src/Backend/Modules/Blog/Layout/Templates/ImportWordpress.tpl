{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

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
				<label for="wordpress">{$lblFile|ucfirst}</label>
				{$fileWordpress} {$fileWordpressError}
				<span class="helpTxt">{$msgHelpWordpress}</span>
			</p>
			<p>
				<label for="filter">{$lblWordpressFilter|ucfirst}</label>
				{$txtFilter} {$txtFilterError}
				<span class="helpTxt">{$msgHelpWordpressFilter}</span>
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:import}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
