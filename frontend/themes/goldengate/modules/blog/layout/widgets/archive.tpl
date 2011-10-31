{*
	variables that are available:
	- {$widgetBlogArchive}:
*}

{cache:{$LANGUAGE}_blogWidgetArchiveCache}
	{option:widgetBlogArchive}
		<section class="mod sideMod">
			<div class="inner">
				<header>
					<h4>{$lblArchive|ucfirst}</h4>
				</header>
				<div class="bd">
					<ul class="doubleLineList">
						{iteration:widgetBlogArchive}
							<li>
								{option:widgetBlogArchive.url}<a href="{$widgetBlogArchive.url}">{/option:widgetBlogArchive.url}
									{$widgetBlogArchive.label}
									{option:widgetBlogArchive.url}({$widgetBlogArchive.total}){/option:widgetBlogArchive.url}
								{option:widgetBlogArchive.url}</a>{/option:widgetBlogArchive.url}
							</li>
						{/iteration:widgetBlogArchive}
					</ul>
				</div>
			</div>
		</section>
	{/option:widgetBlogArchive}
{/cache:{$LANGUAGE}_blogWidgetArchiveCache}