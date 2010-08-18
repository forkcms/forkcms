{*
	variables that are available:
	- {$widgetBlogLatestArticles}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogLatestArticles}
	<div class="widget widgetBlogLatestArticles">
		<ul>
			{iteration:widgetBlogLatestArticles}
				<li>
					<h4><a href="{$widgetBlogLatestArticles.full_url}" title="{$widgetBlogLatestArticles.title}">{$widgetBlogLatestArticles.title}</a></h4>
					<p class="date">{$lblWrittenOn|ucfirst} {$widgetBlogLatestArticles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblOn} {$widgetBlogLatestArticles.publish_on|date:{$timeFormat}:{$LANGUAGE}}</p>
				</li>
			{/iteration:widgetBlogLatestArticles}
		</ul>
	</div>
{/option:widgetBlogLatestArticles}