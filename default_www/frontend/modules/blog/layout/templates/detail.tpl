{*
	variables that are available:
	- {$blogArticle}: contains data about the post
	- {$blogComments}: contains an array with the comments for the post, each element contains data about the comment.
*}

<div id="blog" class="detail">
	<div class="article">
		<div class="heading">
			<h1>{$blogArticle['title']}</h1>
			<p class="date">{$blogArticle['publish_on']|date:'j F Y':{$LANGUAGE}}</p>
		</div>
		<div class="content">
			{$blogArticle['text']}
		</div>

		<div class="meta">
			<ul>
				<!-- Permalink -->
				<li><a href="{$blogArticle['full_url']}" title="{$blogArticle['title']}">{$blogArticle['title']}</a> {$msgWroteBy|sprintf:{$blogArticle['user_id']|userSetting:'nickname'}}</li>

				<!-- Category -->
				<li>{$lblCategory|ucfirst}: <a href="{$blogArticle['category_full_url']}" title="{$blogArticle['category_name']}">{$blogArticle['category_name']}</a></li>

				{option:blogArticle['tags']}
				<!-- Tags -->
				<li>{$lblTags|ucfirst}: {iteration:blogArticleTags}<a href="{$blogArticleTags.full_url}" rel="tag" title="{$blogArticleTags.name}">{$blogArticleTags.name}</a>{option:!blogArticleTags.last}, {/option:!blogArticleTags.last}{/iteration:blogArticleTags}</li>
				{/option:blogArticle['tags']}

				<!-- Comments -->
				{option:!blogComments}<li><a href="{$blogArticle['full_url']}#{$actReact}">{$msgBlogNoComments|ucfirst}</a></li>{/option:!blogComments}
				{option:blogComments}
					{option:blogCommentsMultiple}<li><a href="{$blogArticle['full_url']}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogCommentsCount}}</a></li>{/option:blogCommentsMultiple}
					{option:!blogCommentsMultiple}<li><a href="{$blogArticle['full_url']}#{$actComments}">{$msgBlogOneComment}</a></li>{/option:!blogCommentsMultiple}
				{/option:blogComments}
			</ul>
		</div>
	</div>

	{option:blogComments}
	<div id="comments">
		<h3 id="{$actComments}">{$lblComments|ucfirst}</h3>

		{iteration:blogComments}
		<div id="{$actComment}-{$blogComments.id}" class="comment">
			<div class="commentAuthor">
				<p>
					{$lblBy|ucfirst}
					{option:blogComments.website}<a href="{$blogComments.website}">{/option:blogComments.website}
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="24" height="24" alt="{$blogComments.author}" class="replaceWithGravatar" rel="{$blogComments.gravatar_id}" />
						{$blogComments.author}
					{option:blogComments.website}</a>{/option:blogComments.website}
					{$blogComments.created_on|timeago}
				</p>
			</div>
			<div class="commentText">
				{$blogComments.text|cleanupplaintext}
			</div>
		</div>
		{/iteration:blogComments}
	</div>
	{/option:blogComments}

	{option:blogArticle['allow_comments']}
	<div id="commentForm">
		<h3 id="{$actReact}">{$lblReact|ucfirst}</h3>

		{option:commentIsInModeration}<div class="message notice"><p>{$msgBlogCommentInModeration}</p></div>{/option:commentIsInModeration}
		{option:commentIsSpam}<div class="message error"><p>{$msgBlogCommentIsSpam}</p></div>{/option:commentIsSpam}
		{option:commentIsAdded}<div class="message success"><p>{$msgBlogCommentIsAdded}</p></div>{/option:commentIsAdded}

		{form:react}
			<p>
				<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtAuthor} {$txtAuthorError}
			</p>
			<p>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail} {$txtEmailError}
			</p>
			<p>
				<label for="text">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtText} {$txtTextError}
			</p>

			<p>
				<input id="react" class="inputButton button mainButton" type="submit" name="react" value="{$lblReact|ucfirst}" />
			</p>
		{/form:react}
	</div>
	{/option:blogArticle['allow_comments']}

</div>