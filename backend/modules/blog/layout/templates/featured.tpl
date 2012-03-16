{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblBlog|ucfirst}: {$lblFeaturedArticles}</h2>
</div>

{option:dgFeatured}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblFeaturedArticles|ucfirst}</h3>
		</div>
		{$dgFeatured}
	</div>
{/option:dgFeatured}

{option:!dgFeatured}
	<p>{$msgNoFeaturedItems}</p>
{/option:!dgFeatured}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
