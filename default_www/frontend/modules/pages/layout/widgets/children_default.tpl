{*
	variables that are available:
	- {$widgetChildren}: An array with all the children that should be shown as a block on the current page.

	How to make a new template:
	1. Create a new widget template.
	2. Create a new extra (in the database, pages_extras table) based on the extra for module 'pages' and with action 'children':
		- Change the label. Also create the label in the translations module and make sure the description describes what info will be shown and how.
		- Change the template info in the data field of the extra. After 's:' has to be the length of the filename of the new tempalte (eg. s:8:"test.tpl";).
	3. The new template will now be used on pages where the new extra has been linked to a block.
*}

{option:widgetChildren}
	<div id="childrenBlocks">
		{iteration:widgetChildren}
			<div class="childrenBlock">
				<h3><a href="{$widgetChildren.full_url}" title="{$widgetChildren.page_title}">{$widgetChildren.widget_title}</a></h3>

				{option:widgetChildren.widget_image}
					<p>
						<a href="{$widgetChildren.full_url}" title="{$widgetChildren.page_title}">
							<img src="{$FRONTEND_FILES_URL}/pages/widget_images/256x256/{$widgetChildren.widget_image}" alt="{$widgetChildren.widget_title}" />
						</a>
					</p>
				{/option:widgetChildren.widget_image}

				{$widgetChildren.widget_text}
			</div>
		{/iteration:widgetChildren}
	</div>
{/option:widgetChildren}