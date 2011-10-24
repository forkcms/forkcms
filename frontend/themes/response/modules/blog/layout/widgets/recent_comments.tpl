{*
	variables that are available:
	- {$widgetBlogRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetBlogRecentComments}
	<section id="blogRecentCommentsWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h4>{$lblRecentComments|ucfirst}</h4>
			</header>
			<div class="bd content">
				{iteration:widgetBlogRecentComments}
					<p>
						<span class="date">{$widgetBlogRecentComments.created_on|date:"d M Y"}</span>
						{option:widgetBlogRecentComments.website}<a href="{$widgetBlogRecentComments.website}" rel="nofollow">{/option:widgetBlogRecentComments.website}
							{$widgetBlogRecentComments.author}{option:widgetBlogRecentComments.website}</a>{/option:widgetBlogRecentComments.website}:
						{$widgetBlogRecentComments.text}
					</p>
				{/iteration:widgetBlogRecentComments}
			</div>
		</div>
	</section>
{/option:widgetBlogRecentComments}