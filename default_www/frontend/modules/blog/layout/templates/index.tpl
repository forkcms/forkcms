{*
	variables that are available:
	- {$blogArticles}: contains an array with all posts, each element contains data about the post
*}

<div id="blog" class="index">
	{option:!blogArticles}<div class="message warning"><p>{$msgBlogNoItems}</p></div>{/option:!blogArticles}
	{option:blogArticles}
		{iteration:blogArticles}
			<div class="article">
				<div class="heading">
					<h2><a href="{$blogArticles.full_url}" title="{$blogArticles.title}">{$blogArticles.title}</a></h2>
					<p class="date">{$lblWrittenOn|ucfirst} {$blogArticles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblOn} {$blogArticles.publish_on|date:{$timeFormat}:{$LANGUAGE}}</p>
				</div>
				<div class="content">
					{option:!blogArticles.introduction}{$blogArticles.text}{/option:!blogArticles.introduction}
					{option:blogArticles.introduction}{$blogArticles.introduction}{/option:blogArticles.introduction}
				</div>
				<div class="meta">
					<ul>
						<!-- Permalink -->
						<li><a href="{$blogArticles.full_url}" title="{$blogArticles.title}">{$blogArticles.title}</a> {$msgWrittenBy|sprintf:{$blogArticles.user_id|usersetting:'nickname'}}</li>

						<!-- Category -->
						<li>{$lblCategory|ucfirst}: <a href="{$blogArticles.category_full_url}" title="{$blogArticles.category_name}">{$blogArticles.category_name}</a></li>

						{option:blogArticles.tags}
						<!-- Tags -->
						<li>{$lblTags|ucfirst}: {iteration:blogArticles.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:blogArticles.tags}</li>
						{/option:blogArticles.tags}

						<!-- Comments -->
						{option:!blogArticles.comments}<li><a href="{$blogArticles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a></li>{/option:!blogArticles.comments}
						{option:blogArticles.comments}
							{option:blogArticles.comments_multiple}<li><a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogArticles.comments_count}}</a></li>{/option:blogArticles.comments_multiple}
							{option:!blogArticles.comments_multiple}<li><a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a></li>{/option:!blogArticles.comments_multiple}
						{/option:blogArticles.comments}
					</ul>
				</div>
			</div>
		{/iteration:blogArticles}

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
	{/option:blogArticles}
</div>