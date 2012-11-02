<div class="row">
	<div class="span12">
		<ul class="breadcrumb" itemprop="breadcrumb">
			{iteration:breadcrumb}
				<li>
					{option:breadcrumb.url}<a href="{$breadcrumb.url}" title="{$breadcrumb.title}">{/option:breadcrumb.url}
						{$breadcrumb.title}
					{option:breadcrumb.url}</a>{/option:breadcrumb.url}
					{option:!breadcrumb.last}<span class="divider"></span>{/option:!breadcrumb.last}
				</li>
			{/iteration:breadcrumb}
		</ul>
	</div>
</div>