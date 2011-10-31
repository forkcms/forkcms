{*
	variables that are available:
	- {$widgetBlogArchive}:
*}

{cache:{$LANGUAGE}_blogWidgetArchiveCache}
	{option:widgetBlogArchive}
		{option:widgetBlogArchive.months}
			<section id="blogArchiveWidget" class="mod">
				<div class="inner">
					<header class="hd">
						<h3>{$lblArchive|ucfirst}</h3>
					</header>
					<div class="bd content">
						<ul>
							{iteration:widgetBlogArchive.months}
								<li>
									{option:widgetBlogArchive.months.url}<a href="{$widgetBlogArchive.months.url}">{/option:widgetBlogArchive.months.url}
										{$widgetBlogArchive.months.label|date:'F':{$LANGUAGE}}
									{option:widgetBlogArchive.months.url}</a>{/option:widgetBlogArchive.months.url}
								</li>
							{/iteration:widgetBlogArchive.months}
						</ul>
					</div>
				</div>
			</section>
		{/option:widgetBlogArchive.months}
	{/option:widgetBlogArchive}
{/cache:{$LANGUAGE}_blogWidgetArchiveCache}