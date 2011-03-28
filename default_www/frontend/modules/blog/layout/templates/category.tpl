{*
	variables that are available:
	- {$category}: contains data about the category
	- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:items}
	<section id="blogCategory">
		{iteration:items}
			<article class="mod article">
				<div class="inner">
					<header class="hd">
						<h3><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h3>
						<ul>
							<li>
								{* Written by *}
								{$msgWrittenBy|ucfirst|sprintf:{$items.user_id|usersetting:'nickname'}}

								{* Written on *}
								{$lblOn} {$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

								{* Category*}
								{$lblIn} {$lblThe} {$lblCategory} <a href="{$items.category_full_url}" title="{$items.category_title}">{$items.category_title}</a>{option:!items.tags}.{/option:!items.tags}

								{* Tags*}
								{option:items.tags}
									{$lblWith} {$lblThe} {$lblTags} 
									{iteration:items.tags}
										<a href="{$items.tags.full_url}" rel="tag" title="{$items.tags.name}">{$items.tags.name}</a>{option:!items.tags.last}, {/option:!items.tags.last}{option:items.tags.last}.{/option:items.tags.last}
									{/iteration:items.tags}
								{/option:items.tags}
							</li>
							<li>
								{* Comments *}
								{option:!comments}<a href="{$items.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!comments}
								{option:comments}
									{option:blogCommentsMultiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$commentsCount}}</a>{/option:blogCommentsMultiple}
									{option:!blogCommentsMultiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogCommentsMultiple}
								{/option:comments}
							</li>
						</ul>
					</header>
					<div class="bd content">
						{option:!items.introduction}{$items.text}{/option:!items.introduction}
						{option:items.introduction}{$items.introduction}{/option:items.introduction}
					</div>
				</div>
			</article>
		{/iteration:items}
	</div>
	{include:{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl}
{/option:items}
