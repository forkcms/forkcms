{*
	variables that are available:
	- {$widgetBlogArchive}:
*}

{cache:{$LANGUAGE}_blogWidgetArchiveCache}
	{option:widgetBlogArchive}
		<section id="blogArchiveWidget" class="well blog">
			<header role="banner">
			    <h3>{$lblArchive|ucfirst}</h3>
			</header>
			<ul>
				{iteration:widgetBlogArchive}
					<li>
						{option:widgetBlogArchive.url}<a href="{$widgetBlogArchive.url}">{/option:widgetBlogArchive.url}
							{$widgetBlogArchive.label}{option:widgetBlogArchive.url}</a>{/option:widgetBlogArchive.url}
							<span class="badge">{$widgetBlogArchive.total}</span>

						{option:widgetBlogArchive.months}
							<ul>
								{iteration:widgetBlogArchive.months}
									<li>
										{option:widgetBlogArchive.months.url}<a href="{$widgetBlogArchive.months.url}">{/option:widgetBlogArchive.months.url}
											{$widgetBlogArchive.months.label|date:'F':{$LANGUAGE}}{option:widgetBlogArchive.months.url}</a>{/option:widgetBlogArchive.months.url}
											<span class="badge">{$widgetBlogArchive.months.total}</span>
									</li>
								{/iteration:widgetBlogArchive.months}
							</ul>
						{/option:widgetBlogArchive.months}
					</li>
				{/iteration:widgetBlogArchive}
			</ul>
		</section>
	{/option:widgetBlogArchive}
{/cache:{$LANGUAGE}_blogWidgetArchiveCache}