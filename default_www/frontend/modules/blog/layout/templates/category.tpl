{*
	variables that are available:
	- {$blogCategory}: contains data about the category
	- {$blogArticles}: contains an array with all posts, each element contains data about the post
*}

{option:blogArticles}
	<div id="blogCategory">
		{iteration:blogArticles}
			<div class="mod article">
				<div class="inner">
					<div class="hd">
						<h2><a href="{$blogArticles.full_url}" title="{$blogArticles.title}">{$blogArticles.title}</a></h2>
						<p>{$blogArticles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}|ucfirst} -
						{option:!blogArticles.comments}<a href="{$blogArticles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!blogArticles.comments}
						{option:blogArticles.comments}
							{option:blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogArticles.comments_count}}</a>{/option:blogArticles.comments_multiple}
							{option:!blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogArticles.comments_multiple}
						{/option:blogArticles.comments}
						</p>
					</div>
					<div class="bd content">
						{option:!blogArticles.introduction}{$blogArticles.text}{/option:!blogArticles.introduction}
						{option:blogArticles.introduction}{$blogArticles.introduction}{/option:blogArticles.introduction}
					</div>
					<div class="ft">
						<p>
							{$msgWrittenBy|ucfirst|sprintf:{$blogArticles.user_id|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$blogArticles.category_full_url}" title="{$blogArticles.category_name}">{$blogArticles.category_name}</a>. {option:blogArticles.tags}{$lblTags|ucfirst}: {iteration:blogArticles.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:blogArticles.tags}{/option:blogArticles.tags}
						</p>
					</div>
				</div>
			</div>
		{/iteration:blogArticles}
	</div>
	{include:{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl}
{/option:blogArticles}
