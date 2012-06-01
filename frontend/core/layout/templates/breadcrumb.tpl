<p itemprop="breadcrumb">
	{$lblYouAreHere|ucfirst}:
	{iteration:breadcrumb}
		{option:breadcrumb.url}<a href="{$breadcrumb.url}" title="{$breadcrumb.title}">{/option:breadcrumb.url}{$breadcrumb.title}{option:breadcrumb.url}</a>{/option:breadcrumb.url}
		{option:!breadcrumb.last} › {/option:!breadcrumb.last}
	{/iteration:breadcrumb}
</p>