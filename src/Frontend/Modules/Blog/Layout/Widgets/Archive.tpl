{*
	variables that are available:
	- {$widgetBlogArchive}:
*}

{cache:{$LANGUAGE}_blogWidgetArchiveCache}
	{option:widgetBlogArchive}
		<section id="blogArchiveWidget" class="mod">
			<div class="inner">
				<header class="hd">
					<h3>{$lblArchive|ucfirst}</h3>
				</header>
				<div class="bd content">
					<ul>
						{iteration:widgetBlogArchive}
							<li>
								{option:widgetBlogArchive.url}<a href="{$widgetBlogArchive.url}">{/option:widgetBlogArchive.url}
									{$widgetBlogArchive.label}
									{option:widgetBlogArchive.url}({$widgetBlogArchive.total}){/option:widgetBlogArchive.url}
								{option:widgetBlogArchive.url}</a>{/option:widgetBlogArchive.url}

								{option:widgetBlogArchive.months}
									<ul>
										{iteration:widgetBlogArchive.months}
											<li>
												{option:widgetBlogArchive.months.url}<a href="{$widgetBlogArchive.months.url}">{/option:widgetBlogArchive.months.url}
													{$widgetBlogArchive.months.label|date:'F':{$LANGUAGE}}
													{option:widgetBlogArchive.months.url}({$widgetBlogArchive.months.total}){/option:widgetBlogArchive.months.url}
												{option:widgetBlogArchive.months.url}</a>{/option:widgetBlogArchive.months.url}
											</li>
										{/iteration:widgetBlogArchive.months}
									</ul>
								{/option:widgetBlogArchive.months}
							</li>
						{/iteration:widgetBlogArchive}
					</ul>
				</div>
			</div>
		</section>
	{/option:widgetBlogArchive}
{/cache:{$LANGUAGE}_blogWidgetArchiveCache}