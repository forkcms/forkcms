{*
	variables that are available:
	- {$archive}: contains an array with some dates
	- {$items}: contains an array with all items, each element contains data about the items
*}

{option:!items}
	<section id="blogArchive">
		<div class="bd content">
			<p>{$msgBlogNoItems}</p>
		</div>
	</section>
{/option:!items}
{option:items}
	<section id="blogArchive">
		<header>
			<h3>
				{option:archive.month}
					{$msgArticlesFor|ucfirst|sprintf:{$archive.start_date|date:'F Y':{$LANGUAGE}}}
				{/option:archive.month}
				{option:!archive.month}
					{$msgArticlesFor|ucfirst|sprintf:{$archive.start_date|date:'Y'}}
				{/option:!archive.month}
			</h3>
		</header>
		<div class="bd content">
			<table class="dataGrid table table-hover" width="100%" itemscope itemtype="http://schema.org/Blog">
				<thead class="hide">
					<tr>
						<th class="date">{$lblDate|ucfirst}</th>
						<th class="title">{$lblTitle|ucfirst}</th>
						<th class="comments">{$lblComments|ucfirst}</th>
					</tr>
				</thead>
				<tbody>
					{iteration:items}
						<tr {option:items.first}class="firstChild"{/option:items.first}>
							<td class="date muted"><time itemprop="datePublished" datetime="{$items.publish_on|date:'Y-m-d\TH:i:s'}">{$items.publish_on|date:{$dateFormatShort}:{$LANGUAGE}}</time></td>
							<td class="title"><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></td>
							<td class="comments">
								{option:items.comments}
									<i class="icon-comment"></i>
									{option:items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
									{option:!items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
								{/option:items.comments}
							</td>
						</tr>
					{/iteration:items}
				</tbody>
			</table>
		</div>
	</section>
	{include:core/layout/templates/pagination.tpl}
{/option:items}
