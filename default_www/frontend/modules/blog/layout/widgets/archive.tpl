{*
	variables that are available:
	- {$widgetBlogArchive}:
*}

{cache:{$LANGUAGE}_blogWidgetArchiveCache}
	{option:widgetBlogArchive}
		<div id="blogArchiveWidget" class="mod">
			<div class="inner">
				<div class="hd">
					<h3>{$lblArchive|ucfirst}</h3>
				</div>
				<div class="bd">
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
												{option:months.url}<a href="{$months.url}">{/option:months.url}
													{$months.label|date:'F':{$LANGUAGE}}
													{option:months.url}({$months.total}){/option:months.url}
												{option:months.url}</a>{/option:months.url}
											</li>
										{/iteration:widgetBlogArchive.months}
									</ul>
								{/option:widgetBlogArchive.months}
							</li>
						{/iteration:widgetBlogArchive}
					</ul>
				</div>
			</div>
		</div>
	{/option:widgetBlogArchive}
{/cache:{$LANGUAGE}_blogWidgetArchiveCache}