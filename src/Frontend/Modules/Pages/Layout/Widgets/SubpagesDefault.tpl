{*
	variables that are available:
	- {$widgetSubpages}: An array with all the subpages that should be shown as a block on the current page.
*}

{option:widgetSubpages}
	<div id="subpagesBlocks">
		{iteration:widgetSubpages}
			<div class="subpagesBlock">
				<h3><a href="{$widgetSubpages.full_url}" title="{$widgetSubpages.title}">{$widgetSubpages.title}</a></h3>
				{$widgetSubpages.description}
			</div>
		{/iteration:widgetSubpages}
	</div>
{/option:widgetSubpages}