{*
	variables that are available:
	- {$blogArticle}: contains data about the post
	- {$blogComments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$blogCommentsCount}: contains a variable with the number of comments for this blog post.
	- {$blogNavigation}: contains an array with data for previous and next post
*}

<div id="blog" class="detail">
	<div class="article">
		<div class="heading">
			<h1>{$blogArticle['title']}</h1>
			<p class="date">
				{$blogArticle['publish_on']|date:{$dateFormatLong}:{$LANGUAGE}} - 
				{option:!blogComments}<a href="{$blogArticle['full_url']}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!blogComments}
				{option:blogComments}
					{option:blogCommentsMultiple}<a href="{$blogArticle['full_url']}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogCommentsCount}}</a>{/option:blogCommentsMultiple}
					{option:!blogCommentsMultiple}<a href="{$blogArticle['full_url']}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogCommentsMultiple}
				{/option:blogComments}
			</p>
		</div>
		<div class="content">
			{$blogArticle['text']}
		</div>
		<p class="meta">
			{$msgWrittenBy|ucfirst|sprintf:{$blogArticle['user_id']|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$blogArticle['category_full_url']}" title="{$blogArticle['category_name']">{$blogArticle['category_name']}</a>. {$lblTags|ucfirst}: {iteration:blogArticleTags}<a href="{$blogArticleTags.full_url}" rel="tag" title="{$blogArticleTags.name}">{$blogArticleTags.name}</a>{option:!blogArticleTags.last}, {/option:!blogArticleTags.last}{/iteration:blogArticleTags}
		</p>
	</div>

	<div class="navigation">
		<ul>
			{option:blogNavigation['previous']}
			<li class="previousLink">
				<a href="{$blogNavigation['previous']['url']}" rel="prev">Vorig bericht: <em>{$blogNavigation['previous']['title']}</em></a>
			</li>
			{/option:blogNavigation['previous']}
			{option:blogNavigation['next']}
			<li class="nextLink">
				<a href="{$blogNavigation['next']['url']}" rel="next">Volgend bericht: <em>{$blogNavigation['next']['title']}</em></a>
			</li>
			{/option:blogNavigation['next']}
		</ul>
	</div>

	{option:blogComments}
	<div id="comments">
		<h3 id="{$actComments}">{$lblComments|ucfirst}</h3>

		{iteration:blogComments}
		{* Remark: Do not alter the id! It is used as an anchor *}
		<div id="comment-{$blogComments.id}" class="comment">
			<div class="imageHolder">
				{option:blogComments.website}<a href="{$blogComments.website}">{/option:blogComments.website}
					<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$blogComments.author}" class="replaceWithGravatar" rel="{$blogComments.gravatar_id}" />
				{option:blogComments.website}</a>{/option:blogComments.website}
			</div>
			<div class="commentText">
				<p class="author">
					{option:blogComments.website}<a href="{$blogComments.website}">{/option:blogComments.website}{$blogComments.author}{option:blogComments.website}</a>{/option:blogComments.website}
					{$lblWrote}
					{$blogComments.created_on|timeago}
				</p>
				<div class="content">
					{$blogComments.text|cleanupplaintext}
				</div>
			</div>
		</div>
		{/iteration:blogComments}
	</div>
	{/option:blogComments}

	{option:blogArticle['allow_comments']}
		<div id="commentForm">
			{* Remark: Do not alter the id! It is used as anchor *}
			<h3 id="{$actComment}">{$msgComment|ucfirst}</h3>

			{option:commentIsInModeration}<div class="formMessage generalMessage"><p>{$msgBlogCommentInModeration}</p></div>{/option:commentIsInModeration}
			{option:commentIsSpam}<div class="formMessage errorMessage"><p>{$msgBlogCommentIsSpam}</p></div>{/option:commentIsSpam}
			{option:commentIsAdded}<div class="formMessage successMessage"><p>{$msgBlogCommentIsAdded}</p></div>{/option:commentIsAdded}

			{form:comment}
				<fieldset class="horizontal">
					<p>
						<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtAuthor} {$txtAuthorError}
					</p>
					<p>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<label for="website">{$lblWebsite|ucfirst}</label>
						{$txtWebsite} {$txtWebsiteError}
					</p>
					<p>
						<label for="text">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtText} {$txtTextError}
					</p>
					<p class="spacing">
						<input class="inputSubmit" type="submit" name="comment" value="{$msgComment|ucfirst}" />
					</p>
				</fieldset>
			{/form:comment}
		</div>
	{/option:blogArticle['allow_comments']}
</div>