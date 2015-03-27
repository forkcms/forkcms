<p itemprop="breadcrumb">
	<ol class="breadcrumb">	
	{iteration:breadcrumb}
		<li {option:!breadcrumb.last}class="active"{/option:!breadcrumb.last}>{option:breadcrumb.url}<a href="{$breadcrumb.url}" title="{$breadcrumb.title}">{/option:breadcrumb.url}{$breadcrumb.title}{option:breadcrumb.url}</a>{/option:breadcrumb.url}</li>
	{/iteration:breadcrumb}
	</ol>
</p>