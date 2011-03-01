{*
	variables that are available:
	- {$widgetTagsRelated}: contains an array with all related items
*}

{option:widgetTagsRelated}
	<section id="tagRelatedWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRelated|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetTagsRelated}
						<li>
							<a href="{$widgetTagsRelated.full_url}" title="{$widgetTagsRelated.title}">
								{$widgetTagsRelated.title}
							</a>
						</li>
					{/iteration:widgetTagsRelated}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetTagsRelated}