<p itemprop="breadcrumb">
	{$lblYouAreHere|ucfirst}:
	{iteration:breadcrumb}
		{option:!breadcrumb.last}<a href="{$breadcrumb.url}" title="{$breadcrumb.title}">{/option:!breadcrumb.last}{$breadcrumb.title}{option:!breadcrumb.last}</a> › {/option:!breadcrumb.last}
	{/iteration:breadcrumb}
</p>