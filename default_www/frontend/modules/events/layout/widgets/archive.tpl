{*
	variables that are available:
	- {$widgetEventsArchive}:
*}

{cache:{$LANGUAGE}_eventsWidgetArchiveCache}
	{option:widgetEventsArchive}
		<div id="eventsArchiveWidget" class="mod">
			<div class="inner">
				<div class="hd">
					<h3>{$lblArchive|ucfirst}</h3>
				</div>
				<div class="bd">
					<ul>
						{iteration:widgetEventsArchive}
							<li>
								{option:widgetEventsArchive.url}<a href="{$widgetEventsArchive.url}">{/option:widgetEventsArchive.url}
									{$widgetEventsArchive.label}
									{option:widgetEventsArchive.url}({$widgetEventsArchive.total}){/option:widgetEventsArchive.url}
								{option:widgetEventsArchive.url}</a>{/option:widgetEventsArchive.url}

								{option:widgetEventsArchive.months}
									<ul>
										{iteration:widgetEventsArchive.months}
											<li>
												{option:widgetEventsArchive.months.url}<a href="{$widgetEventsArchive.months.url}">{/option:widgetEventsArchive.months.url}
													{$widgetEventsArchive.months.label|date:'F':{$LANGUAGE}}
													{option:widgetEventsArchive.months.url}({$widgetEventsArchive.months.total}){/option:widgetEventsArchive.months.url}
												{option:widgetEventsArchive.months.url}</a>{/option:widgetEventsArchive.months.url}
											</li>
										{/iteration:widgetEventsArchive.months}
									</ul>
								{/option:widgetEventsArchive.months}
							</li>
						{/iteration:widgetEventsArchive}
					</ul>
				</div>
			</div>
		</div>
	{/option:widgetEventsArchive}
{/cache:{$LANGUAGE}_eventsWidgetArchiveCache}