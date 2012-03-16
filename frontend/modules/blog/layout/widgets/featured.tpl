{*
	Variables that are available:
		{$widgetBlogFeatured}
			{$widgetBlogFeatured.title}
			{$widgetBlogFeatured.url}
			{$widgetBlogFeatured.publish_on}
*}

{option:blogWidgetFeatured}
	<section id="blogFeaturedWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblFeaturedArticles|ucfirst}</h3>
			</header>
			<div class="bd">
				{iteration:blogWidgetFeatured}
					<p>
						{$blogWidgetFeatured.publish_on|date:{$dateFormatShort}:{$LANGUAGE}} ({$blogWidgetFeatured.publish_on|timeago})<br />
						<a href="{$var|geturlforblock:'blog':'detail'}/{$blogWidgetFeatured.url}" title="{$blogWidgetFeatured.url}">{$blogWidgetFeatured.title}</a>
					</p>
				{/iteration:blogWidgetFeatured}
			</div>
		</div>
	</section>
{/option:blogWidgetFeatured}
