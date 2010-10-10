{*
	variables that are available:
	- {$blogArticles}: contains an array with all posts, each element contains data about the post
*}

<div id="blog" class="index">
	{option:!blogArticles}<p>{$msgBlogNoItems}</p>{/option:!blogArticles}
	{option:blogArticles}
		{iteration:blogArticles}
			<div class="article">
				<div class="heading">
					<h2><a href="{$blogArticles.full_url}" title="{$blogArticles.title}">{$blogArticles.title}</a></h2>
					<p class="date">{$blogArticles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}|ucfirst} -
					{option:!blogArticles.comments}<a href="{$blogArticles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!blogArticles.comments}
					{option:blogArticles.comments}
						{option:blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogArticles.comments_count}}</a>{/option:blogArticles.comments_multiple}
						{option:!blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogArticles.comments_multiple}
					{/option:blogArticles.comments}
					</p>
				</div>
				<div class="content">
					{option:!blogArticles.introduction}{$blogArticles.text}{/option:!blogArticles.introduction}
					{option:blogArticles.introduction}{$blogArticles.introduction}{/option:blogArticles.introduction}
				</div>
				<p class="meta">
					{$msgWrittenBy|ucfirst|sprintf:{$blogArticles.user_id|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$blogArticles.category_full_url}" title="{$blogArticles.category_name}">{$blogArticles.category_name}</a>. {option:blogArticles.tags}{$lblTags|ucfirst}: {iteration:blogArticles.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:blogArticles.tags}{/option:blogArticles.tags}
				</p>
			</div>
		{/iteration:blogArticles}
		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
	{/option:blogArticles}
</div>