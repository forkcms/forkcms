{*
	variables that are available:
	- {$blogArticles}: contains an array with all posts, each element contains data about the post
*}

<div id="blog" class="index">
	{option:!blogArticles}<div class="message warning"><p>{$msgBlogNoItems}</p></div>{/option:!blogArticles}
	{option:blogArticles}
		{iteration:blogArticles}
			<div class="article">
				<h2>
					<a href="{$blogArticles.full_url}" title="{$blogArticles.title}">
						{$blogArticles.title}
					</a>
				</h2>
				<p class="date">
					{$blogArticles.publish_on|date:'j F Y H:i:s':{$LANGUAGE}}
				</p>

				<div class="content">
					{option:!blogArticles.introduction}{$blogArticles.text}{/option:!blogArticles.introduction}
					{option:blogArticles.introduction}{$blogArticles.introduction}{/option:blogArticles.introduction}
				</div>

				<div class="meta">
					<ul>
						<!-- Permalink -->
						<li><a href="{$blogArticles['full_url']}" title="{$blogArticles['title']}">{$blogArticles['title']}</a> {$msgWrittenBy|sprintf:{$blogArticles['user_id']|usersetting:'nickname'}}</li>

						<!-- Category -->
						<li>{$lblCategory|ucfirst}: <a href="{$blogArticles['category_full_url']}" title="{$blogArticles['category_name']}">{$blogArticles['category_name']}</a></li>

						{option:blogArticles['tags']}
						<!-- Tags -->
						<li>{$lblTags|ucfirst}: {iteration:blogArticlesTags}<a href="{$blogArticlesTags.full_url}" rel="tag" title="{$blogArticlesTags.name}">{$blogArticlesTags.name}</a>{option:!blogArticlesTags.last}, {/option:!blogArticlesTags.last}{/iteration:blogArticlesTags}</li>
						{/option:blogArticles['tags']}

						<!-- Comments -->
						{option:!blogComments}<li class="lastChild"><a href="{$blogArticles['full_url']}#{$actComment}">{$msgBlogNoComments|ucfirst}</a></li>{/option:!blogComments}
						{option:blogComments}
							{option:blogCommentsMultiple}<li class="lastChild"><a href="{$blogArticles['full_url']}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogCommentsCount}}</a></li>{/option:blogCommentsMultiple}
							{option:!blogCommentsMultiple}<li class="lastChild"><a href="{$blogArticles['full_url']}#{$actComments}">{$msgBlogOneComment}</a></li>{/option:!blogCommentsMultiple}
						{/option:blogComments}
					</ul>
				</div>
			</div>
		{/iteration:blogArticles}

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
	{/option:blogArticles}
</div>