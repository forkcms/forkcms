<div id="blog" class="index">
	<h2>{$blogCategoryName}</h2>

	{option:!blogArticles}<div class="message warning"><p>{$msgBlogNoItemsInCategory|sprintf:{$blogCategoryName}}</p></div>{/option:!blogArticles}
	{option:blogArticles}
	<table class="datagrid" width="100%">
		<thead>
			<tr>
				<th width="15%">{$lblDate|ucfirst}</th>
				<th width="65%">{$lblTitle|ucfirst}</th>
				<th width="20%">{$lblComments|ucfirst}</th>
			</tr>
		</thead>
		<tbody>
			{iteration:blogArticles}
			<tr>
				<td class="date">{$blogArticles.publish_on|date:'j F Y':{$LANGUAGE}}</td>
				<td class="title"><a href="{$blogArticles.full_url}" title="{$blogArticles.title}">{$blogArticles.title}</a></td>
				<td class="comments">
				<!-- Comments -->
					{option:!blogArticles.comments}<a href="{$blogArticles.full_url}#{$actReact}">{$msgBlogNoComments|ucfirst}</a>{/option:!blogArticles.comments}
					{option:blogArticles.comments}
						{option:blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogArticles.comments_count}}</a>{/option:blogArticles.comments_multiple}
						{option:!blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogArticles.comments_multiple}
					{/option:blogArticles.comments}
				</td>
			</tr>
			{/iteration:blogArticles}
		</tbody>
	</table>

	{include:file="{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl"}
	{/option:blogArticles}
</div>