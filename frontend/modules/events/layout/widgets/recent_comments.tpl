{*
	variables that are available:
	- {$widgetEventsRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetEventsRecentComments}
	<div id="eventsRecentCommentsWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblRecentComments|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
					{iteration:widgetEventsRecentComments}
					<li>
						{option:widgetEventsRecentComments.website}<a href="{$widgetEventsRecentComments.website}" rel="nofollow">{/option:widgetEventsRecentComments.website}
							{$widgetEventsRecentComments.author}
						{option:widgetEventsRecentComments.website}</a>{/option:widgetEventsRecentComments.website}
						{$lblCommentedOn} <a href="{$widgetEventsRecentComments.full_url}">{$widgetEventsRecentComments.event_title}</a>
					</li>
					{/iteration:widgetEventsRecentComments}
				</ul>
			</div>
		</div>
	</div>
{/option:widgetEventsRecentComments}