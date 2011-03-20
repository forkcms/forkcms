{*
	variables that are available:
	- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:!items}
	<div id="blogIndex">
		<section class="mod">
			<div class="inner">
				<div class="bd content">
					<p>{$msgBlogNoItems}</p>
				</div>
			</div>
		</section>
	</div>
{/option:!items}
{option:items}
	<div id="blogIndex">
		{iteration:items}
			<article class="mod">
				<div class="inner">
					<header class="hd">
						<h3><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h3>
						<ul>
							<li>{$msgWrittenBy|ucfirst|sprintf:{$items.user_id|usersetting:'nickname'}} {$lblOn} {$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</li>
							<li>
								{option:!items.comments}<a href="{$items.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!items.comments}
								{option:items.comments}
									{option:items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
									{option:!items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
								{/option:items.comments}
							</li>
							<li><a href="{$items.category_full_url}" title="{$items.category_title}">{$items.category_title}</a></li>
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
