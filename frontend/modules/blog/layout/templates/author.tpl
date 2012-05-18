{*
	variables that are available:
	- {$authorId}: contains the author ID.
	- {$articles}: contains an array with all posts, each element contains data about the post.
*}

{option:articles}
	<header class="mainTitle">
	<h1>{$msgArticlesBy|ucfirst|sprintf:{$authorId|usersetting:'nickname'}}</h1>
	</header>
	<section id="blogCategory">
		{iteration:articles}
			<article class="mod article">
				<div class="inner">
					<header class="hd">
						<h3><a href="{$articles.full_url}" title="{$articles.title}">{$articles.title}</a></h3>
						<ul>
							<li>
								{* Written by *}
								{$msgWrittenBy|ucfirst|sprintf:{$articles.author_full_url}:{$articles.user_id|usersetting:'nickname'}}

								{* Written on *}
								{$lblOn} {$articles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

								{* Category*}
								{$lblIn} {$lblThe} {$lblCategory} <a href="{$articles.category_full_url}" title="{$articles.category_title}">{$articles.category_title}</a>{option:!articles.tags}.{/option:!articles.tags}

								{* Tags*}
								{option:articles.tags}
									{$lblWith} {$lblThe} {$lblTags}
									{iteration:articles.tags}
										<a href="{$articles.tags.full_url}" rel="tag" title="{$articles.tags.name}">{$articles.tags.name}</a>{option:!articles.tags.last}, {/option:!articles.tags.last}{option:articles.tags.last}.{/option:articles.tags.last}
									{/iteration:articles.tags}
								{/option:articles.tags}
							</li>
							<li>
								{* Comments *}
								{option:!articles.comments}<a href="{$articles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!articles.comments}
								{option:articles.comments}
									{option:articles.comments_multiple}<a href="{$articles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$articles.comments_count}}</a>{/option:articles.comments_multiple}
									{option:!articles.comments_multiple}<a href="{$articles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!articles.comments_multiple}
								{/option:articles.comments}
							</li>
						</ul>
					</header>
					<div class="bd content">
						{option:articles.image}<img src="{$FRONTEND_FILES_URL}/blog/images/source/{$articles.image}" alt="{$articles.title}" />{/option:articles.image}
						{option:!articles.introduction}{$articles.text}{/option:!articles.introduction}
						{option:articles.introduction}{$articles.introduction}{/option:articles.introduction}
					</div>
				</div>
			</article>
		{/iteration:articles}
	</section>
	{include:core/layout/templates/pagination.tpl}
{/option:articles}
