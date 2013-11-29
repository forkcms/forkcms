{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblSitemap|ucfirst}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblDescription|ucfirst}</h3>
		</div>
		<div class="options">
			{$msgSitemapExplanation}
		</div>
	</div>

	{option:nonImplementedModules}
	<div class="box">
		<div class="heading">
			<h3 style="color: red;">{$lblNotifications|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgNonImplementedModules}</p>
			<strong>{$lblModules|ucfirst}</strong>
			<ul>
				{iteration:nonImplementedModules}
					<li>
						- {$nonImplementedModules.name}
					</li>
				{/iteration:nonImplementedModules}
			</ul>
		</div>
	</div>
	{/option:nonImplementedModules}

	<div class="box">
		<div class="heading">
			<h3>{$lblSitemap|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgSitemapsWillCreated}</p>
			<strong>{$lblFiles|ucfirst}</strong>
			<ul>
				{iteration:indexes}
					<li>
						- {$indexes.name}
					</li>
				{/iteration:indexes}
			</ul>
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