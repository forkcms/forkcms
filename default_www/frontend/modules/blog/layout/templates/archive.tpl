{*
	variables that are available:
	- {$blogCategory}: contains data about the category
	- {$blogArticles}: contains an array with all posts, each element contains data about the post
*}

{option:blogArticles}
	<div id="blogArchive" class="mod">
		<div class="inner">
			<div class="bd content">
				<table class="datagrid" width="100%">
					<thead>
						<tr>
							<th class="date">{$lblDate|ucfirst}</th>
							<th class="title">{$lblTitle|ucfirst}</th>
							<th class="comments">{$lblComments|ucfirst}</th>
						</tr>
					</thead>
					<tbody>
						{iteration:blogArticles}
							<tr>
								<td class="date">{$blogArticles.publish_on|date:{$dateFormatShort}:{$LANGUAGE}}</td>
								<td class="title"><a href="{$blogArticles.full_url}" title="{$blogArticles.title}">{$blogArticles.title}</a></td>
								<td class="comments">
									{option:!blogArticles.comments}<a href="{$blogArticles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!blogArticles.comments}
									{option:blogArticles.comments}
										{option:blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogArticles.comments_count}}</a>{/option:blogArticles.comments_multiple}
										{option:!blogArticles.comments_multiple}<a href="{$blogArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogArticles.comments_multiple}
									{/option:blogArticles.comments}
								</td>
							</tr>
						{/iteration:blogArticles}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
{/option:blogArticles}
