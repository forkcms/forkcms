{*
	variables that are available:
	- {$archive}: contains an array with some dates
	- {$items}: contains an array with all items, each element contains data about the items
	- {$allowComments}: boolean to indicate that the archive may display comment info
*}
<section>
	<header>
		<h1>{$lblArchive|ucfirst}</h1>
	</header>
	{option:!items}
		<p>{$msgBlogNoItems}</p>
	{/option:!items}
	{option:items}
		<ul>
			{iteration:items}
				<li>
					<a href="{$items.full_url}" title="{$items.title}">{$items.title}</a> - {$items.publish_on|date:{$dateFormatShort}:{$LANGUAGE}}
					{option:!items.comments}- <a href="{$items.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!items.comments}
					{option:items.comments}
						{option:items.comments_multiple}- <a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
						{option:!items.comments_multiple}- <a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
					{/option:items.comments}
				</li>
			{/iteration:items}
		</ul>
		{include:core/layout/templates/pagination.tpl}
	{/option:items}
</section>