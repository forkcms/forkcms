{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
	<section id="blogCategoriesWidget" class="well blog">
		<header role="banner">
		    <h3>{$lblCategories|ucfirst}</h3>
		</header>
		<ul>
		    {iteration:widgetBlogCategories}
		    	<li>
		    		<a href="{$widgetBlogCategories.url}">
		    			{$widgetBlogCategories.label}</a>
		    		<span class="badge">{$widgetBlogCategories.total}</span>
		    	</li>
		    {/iteration:widgetBlogCategories}
		</ul>
	</section>
{/option:widgetBlogCategories}