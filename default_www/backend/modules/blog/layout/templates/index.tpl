{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

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
<form action="{$var|geturl:'mass_post_action'}" method="get" class="forkForms submitWithLink" id="posts">
	<div class="datagridHolder">
		<input type="hidden" name="from" value="posts" />

		<div class="tableHeading">
			<h3>{$lblPublishedPosts|ucfirst}</h3>
		</div>
		{$dgPosts}
	</div>
</form>
{/option:dgPosts}

{option:!dgPosts}<p>{$msgNoItems}</p>{/option:!dgPosts}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}