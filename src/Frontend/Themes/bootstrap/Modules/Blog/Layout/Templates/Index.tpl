{*
	variables that are available:
	- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:!items}
	<div id="blogIndex">
		<p>{$msgBlogNoItems}</p>
	</div>
{/option:!items}

{option:items}
	<div id="blogIndex">
		{iteration:items}
			<article>
					<header>
						<h1><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h1>
						<p class="lead">
							{* Written by *}
							{$msgWrittenBy|ucfirst|sprintf:{$items.user_id|usersetting:'nickname'}}

							{* Category*}
							{$lblIn} {$lblThe} {$lblCategory} <a href="{$items.category_full_url}" title="{$items.category_title}">{$items.category_title}</a>{option:!items.tags}.{/option:!items.tags}
						</p>
						<hr>
						<p class="pull-left"><span class="glyphicon glyphicon-time"></span>
							{* Written on *}
							{$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}
						</p>
							{* Comments *}
							{option:items.allow_comments}
							<div class="text-right">
								{option:!items.comments}<a href="{$items.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!items.comments}
								{option:items.comments}
									{option:items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
									{option:!items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
								{/option:items.comments}
							</div>
							{/option:items.allow_comments}
						<hr>
							{* Tags *}
							{option:items.tags}
							<p>
								{$lblWith} {$lblThe} {$lblTags}
								{iteration:items.tags}
									<a href="{$items.tags.full_url}" rel="tag" title="{$items.tags.name}">{$items.tags.name}</a>{option:!items.tags.last}, {/option:!items.tags.last}{option:items.tags.last}.{/option:items.tags.last}
								{/iteration:items.tags}
							</p>
							<hr>
							{/option:items.tags}
					</header>

					{option:items.image}<img class="img-responsive" src="{$FRONTEND_FILES_URL}/blog/images/source/{$items.image}" alt="{$items.title}" />{/option:items.image}
					{option:!items.introduction}{$items.text}{/option:!items.introduction}
					{option:items.introduction}{$items.introduction}{/option:items.introduction}

	                <a class="btn btn-primary" href="{$items.full_url}">{$lblMore|ucfirst} <span class="glyphicon glyphicon-chevron-right"></span></a>
					<hr>
			</article>
		{/iteration:items}
	</div>
	{include:Core/Layout/Templates/Pagination.tpl}
{/option:items}
