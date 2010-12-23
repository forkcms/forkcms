{*
	variables that are available:
	- {$blogArticle}: contains data about the post
	- {$blogComments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$blogCommentsCount}: contains a variable with the number of comments for this blog post.
	- {$blogNavigation}: contains an array with data for previous and next post
*}

<div id="blogDetail">
	<div class="mod article">
		<div class="inner">
			<div class="hd">
				<h1>{$blogArticle['title']}</h1>
				<p>
					{$blogArticle['publish_on']|date:{$dateFormatLong}:{$LANGUAGE}} -
					{option:!blogComments}<a href="{$blogArticle['full_url']}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!blogComments}
					{option:blogComments}
						{option:blogCommentsMultiple}<a href="{$blogArticle['full_url']}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogCommentsCount}}</a>{/option:blogCommentsMultiple}
						{option:!blogCommentsMultiple}<a href="{$blogArticle['full_url']}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogCommentsMultiple}
					{/option:blogComments}
				</p>
			</div>
			<div class="bd content">
				{$blogArticle['text']}
			</div>
			<div class="ft">
				{$msgWrittenBy|ucfirst|sprintf:{$blogArticle['user_id']|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$blogArticle['category_full_url']}" title="{$blogArticle['category_name']">{$blogArticle['category_name']}</a>. {$lblTags|ucfirst}: {iteration:blogArticleTags}<a href="{$blogArticleTags.full_url}" rel="tag" title="{$blogArticleTags.name}">{$blogArticleTags.name}</a>{option:!blogArticleTags.last}, {/option:!blogArticleTags.last}{/iteration:blogArticleTags}
			</div>
		</div>
	</div>
	<div id="blogNavigation" class="mod">
		<div class="inner">
			<div class="bd">
				<ul>
					{option:blogNavigation['previous']}
					<li class="previousLink">
						<a href="{$blogNavigation['previous']['url']}" rel="prev">{$lblPreviousArticle|ucfirst}: <em>{$blogNavigation['previous']['title']}</em></a>
					</li>
					{/option:blogNavigation['previous']}
					{option:blogNavigation['next']}
					<li class="nextLink">
						<a href="{$blogNavigation['next']['url']}" rel="next">{$lblNextArticle|ucfirst}: <em>{$blogNavigation['next']['title']}</em></a>
					</li>
					{/option:blogNavigation['next']}
				</ul>
			</div>
		</div>
	</div>

	{option:blogComments}
	<div id="blogComments" class="mod">
		<div class="inner">
			<div class="hd">
				<h3 id="{$actComments}">{$lblComments|ucfirst}</h3>
			</div>
			{iteration:blogComments}
				{* Do not alter the id! It is used as an anchor *}
				<div id="comment-{$blogComments.id}" class="bd comment">
					<div class="imageHolder">
						{option:blogComments.website}<a href="{$blogComments.website}">{/option:blogComments.website}
							<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$blogComments.author}" class="replaceWithGravatar" data-gravatar-id="{$blogComments.gravatar_id}" />
						{option:blogComments.website}</a>{/option:blogComments.website}
					</div>
					<div class="commentContent">
						<p class="commentAuthor">
							{option:blogComments.website}<a href="{$blogComments.website}">{/option:blogComments.website}{$blogComments.author}{option:blogComments.website}</a>{/option:blogComments.website}
							{$lblWrote}
							{$blogComments.created_on|timeago}
						</p>
						<div class="commentText content">
							{$blogComments.text|cleanupplaintext}
						</div>
					</div>
				</div>
			{/iteration:blogComments}
		</div>
	</div>
	{/option:blogComments}
	{option:blogArticle['allow_comments']}
		<div id="blogCommentForm" class="mod">
			<div class="inner">
				<div class="hd">
					<h3>{$msgComment|ucfirst}</h3>
				</div>
				<div class="bd">
					{option:commentIsInModeration}<div class="message warning"><p>{$msgBlogCommentInModeration}</p></div>{/option:commentIsInModeration}
					{option:commentIsSpam}<div class="message error"><p>{$msgBlogCommentIsSpam}</p></div>{/option:commentIsSpam}
					{option:commentIsAdded}<div class="message success"><p>{$msgBlogCommentIsAdded}</p></div>{/option:commentIsAdded}
					{form:comment}
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
							<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtMessage} {$txtMessageError}
						</p>
						<p>
							<input class="inputSubmit" type="submit" name="comment" value="{$msgComment|ucfirst}" />
						</p>
					{/form:comment}
				</div>
			</div>
		</div>
	{/option:blogArticle['allow_comments']}
</div>