<div class="box" id="widgetBlogComments">
	<div class="heading">
		<h3><a href="{$var|geturl:'comments':'blog'}">{$msgBlogLatestComments|ucfirst}</a></h3>
	</div>

	{option:numCommentsToModerate}
	<div class="moderate">
		<div class="oneLiner">
			<p>{$msgBlogCommentsToModerate|sprintf:{$numCommentsToModerate}}</p>
			<div class="buttonHolder">
				<a href="{$var|geturl:'comments':'blog'}#tabModeration" class="button"><span><span><span>{$lblModerate|ucfirst}</span></span></span></a>
			</div>
		</div>
	</div>
	{/option:numCommentsToModerate}

	{option:blogComments}
	<div class="datagridHolder">
		<table cellspacing="0" class="datagrid">
			<tbody>
				{iteration:blogComments}
				<tr class="{cycle:odd:even}">
					<td><a href="{$blogComments.full_url}">{$blogComments.title}</a></td>
					<td class="name">{$blogComments.author}</td>
				</tr>
				{/iteration:blogComments}
			</tbody>
		</table>
	</div>
	{/option:blogComments}
	<!-- @todo	@johan: style the no items message -->
	{option:!blogComments}<p>{$msgNoComments}</p>{/option:!blogComments}

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'comments':'blog'}" class="button"><span><span><span>{$lblAllComments|ucfirst}</span></span></span></a>
		</div>
	</div>
</div>