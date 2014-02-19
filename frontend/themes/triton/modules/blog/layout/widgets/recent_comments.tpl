{*
	variables that are available:
	- {$widgetBlogRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetBlogRecentComments}
	<section id="blogRecentCommentsWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentComments|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetBlogRecentComments}
						<li>
							{option:widgetBlogRecentComments.website}<a href="{$widgetBlogRecentComments.website}" rel="nofollow">{/option:widgetBlogRecentComments.website}
								{$widgetBlogRecentComments.author}
							{option:widgetBlogRecentComments.website}</a>{/option:widgetBlogRecentComments.website}
							{$lblCommentedOn} <a href="{$widgetBlogRecentComments.full_url}">{$widgetBlogRecentComments.post_title}</a>
						</li>
					{/iteration:widgetBlogRecentComments}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetBlogRecentComments}