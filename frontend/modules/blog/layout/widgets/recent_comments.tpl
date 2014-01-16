{*
	variables that are available:
	- {$widgetBlogRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetBlogRecentComments}
	<section>
		<header>
			<h2>{$lblRecentComments|ucfirst}</h2>
		</header>
		{iteration:widgetBlogRecentComments}
			<article>
				<p>
					{option:widgetBlogRecentComments.website}<a href="{$widgetBlogRecentComments.website}" rel="nofollow">{/option:widgetBlogRecentComments.website}
						{$widgetBlogRecentComments.author}
					{option:widgetBlogRecentComments.website}</a>{/option:widgetBlogRecentComments.website}
					{$lblCommentedOn} <a href="{$widgetBlogRecentComments.full_url}">{$widgetBlogRecentComments.post_title}</a>
				</p>
				<p>
					{$widgetBlogRecentComments.text}
				</p>
			</article>
		{/iteration:widgetBlogRecentComments}
	</section>
{/option:widgetBlogRecentComments}
