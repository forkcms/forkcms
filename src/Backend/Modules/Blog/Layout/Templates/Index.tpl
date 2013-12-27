{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>
		{$lblBlog|ucfirst}:

		{option:!filterCategory}{$lblArticles}{/option:!filterCategory}
		{option:filterCategory}{$msgArticlesFor|sprintf:{$filterCategory.title}}{/option:filterCategory}
	</h2>

	{option:showBlogAdd}
	<div class="buttonHolderRight">
		{option:filterCategory}<a href="{$var|geturl:'add':null:'&category={$filterCategory.id}'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">{/option:filterCategory}
		{option:!filterCategory}<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">{/option:!filterCategory}
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
	{/option:showBlogAdd}
</div>

{form:filter}
	<p class="oneLiner">
		<label for="category">{$msgShowOnlyItemsInCategory}</label>
		&nbsp;{$ddmCategory} {$ddmCategoryError}
	</p>
{/form:filter}

{option:dgRecent}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblRecentlyEdited|ucfirst}</h3>
		</div>
		{$dgRecent}
	</div>
{/option:dgRecent}

{option:dgDrafts}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblDrafts|ucfirst}</h3>
		</div>
		{$dgDrafts}
	</div>
{/option:dgDrafts}

{option:dgPosts}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblPublishedArticles|ucfirst}</h3>
		</div>
		{$dgPosts}
	</div>
{/option:dgPosts}

{option:!dgPosts}
	{option:filterCategory}<p>{$msgNoItems|sprintf:{$var|geturl:'add':null:'&category={$filterCategory.id}'}}</p>{/option:filterCategory}
	{option:!filterCategory}<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>{/option:!filterCategory}
{/option:!dgPosts}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}