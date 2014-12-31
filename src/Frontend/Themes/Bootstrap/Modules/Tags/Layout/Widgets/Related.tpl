{*
	variables that are available:
	- {$widgetTagsRelated}: contains an array with all related items
*}
{option:widgetTagsRelated}
	<section id="tagRelatedWidget" class="tags">
			<header role="banner">
				<h3>{$lblRelated|ucfirst}</h3>
			</header>
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
	</section>
{/option:widgetTagsRelated}