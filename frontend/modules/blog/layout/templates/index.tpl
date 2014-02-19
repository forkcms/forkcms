{*
	variables that are available:
	- {$items}: contains an array with all posts, each element contains data about the post
*}

<section >
	{option:!items}
		<p>{$msgBlogNoItems}</p>
	{/option:!items}

	{option:items}
		{iteration:items}
			<article>
				<header>
					<h2><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h2>
					<p>
						{$msgWrittenBy|ucfirst|sprintf:{$items.user_id|usersetting:'nickname'}} {$lblOn} {$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblIn} {$lblThe} {$lblCategory} <a href="{$items.category_full_url}" title="{$items.category_title}">{$items.category_title}</a>
						{option:items.allow_comments}
							{option:!items.comments}- <a href="{$items.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!items.comments}
							{option:items.comments}
								{option:items.comments_multiple}- <a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
								{option:!items.comments_multiple}- <a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
							{/option:items.comments}
						{/option:items.allow_comments}
					</p>
				</header>
				{option:items.image}<img src="{$FRONTEND_FILES_URL}/blog/images/128x128/{$items.image}" alt="{$items.title}" />{/option:items.image}
				{option:!items.introduction}{$items.text}{/option:!items.introduction}
				{option:items.introduction}{$items.introduction}{/option:items.introduction}
			</article>
		{/iteration:items}
	{/option:items}

	{include:core/layout/templates/pagination.tpl}
</section>
