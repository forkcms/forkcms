{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblBlog|ucfirst}: {$lblArticles}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
</div>

{option:dgRecent}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblRecentlyEdited|ucfirst}</h3>
		</div>
		{$dgRecent}
	</div>
{/option:dgRecent}

{option:dgDrafts}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblDrafts|ucfirst}</h3>
		</div>
		{$dgDrafts}
	</div>
{/option:dgDrafts}

{option:dgPosts}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblPublishedArticles|ucfirst}</h3>
		</div>
		{$dgPosts}
	</div>
{/option:dgPosts}

{option:!dgPosts}<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>{/option:!dgPosts}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}