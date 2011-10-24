{*
	variables that are available:
	- {$item}: contains data about the post
	- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$commentsCount}: contains a variable with the number of comments for this blog post.
	- {$navigation}: contains an array with data for previous and next post
*}
<div id="blogDetail">
	<article class="mod">
		<div class="inner">
			<header class="hd">
				<h1>{$item.title}</h1>
				<p class="meta">
					{$msgWrittenBy|ucfirst|sprintf:{$item.user_id|usersetting:'nickname'}}
					{$lblOn} {$item.publish_on|date:"M d, Y":{$LANGUAGE}}
					{$lblIn} <a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>
					 - 
					{option:!comments}
						<a href="{$item.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>
					{/option:!comments}
					{option:comments}
						{option:blogCommentsMultiple}<a href="{$item.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$commentsCount}}</a>{/option:blogCommentsMultiple}
						{option:!blogCommentsMultiple}<a href="{$item.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!blogCommentsMultiple}
					{/option:comments}
				</p>
			</header>
			<div class="bd content">
				{$item.text}
			</div>
		</div>
	</article>

	{option:comments}
		<section id="blogComments" class="mod">
			<div class="inner">
				<header class="hd">
					<h2 id="{$actComments}">{$lblComments|ucfirst}</h2>
				</header>
				<div class="bd content">
					{iteration:comments}
						{* Do not alter the id! It is used as an anchor *}
						<div id="comment-{$comments.id}" class="comment">
							<div class="imageHolder">
								{option:comments.website}<a href="{$comments.website}">{/option:comments.website}
									<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$comments.author}" class="replaceWithGravatar" data-gravatar-id="{$comments.gravatar_id}" />
								{option:comments.website}</a>{/option:comments.website}
							</div>
							<div class="commentContent">
								<p class="commentAuthor">
									{option:comments.website}<a href="{$comments.website}">{/option:comments.website}{$comments.author}{option:comments.website}</a>{/option:comments.website}
									{$lblWrote}
									{$comments.created_on|timeago}
								</p>
								<div class="commentText content">
									{$comments.text|cleanupplaintext}
								</div>
							</div>
						</div>
					{/iteration:comments}
				</div>
			</div>
		</section>
	{/option:comments}
	{option:item.allow_comments}
		<section id="blogCommentForm" class="mod">
			<div class="inner">
				<header class="hd">
					<h2 id="{$actComment}">{$msgComment|ucfirst}</h2>
				</header>
				<div class="bd">
					{option:commentIsInModeration}<div class="message warning"><p>{$msgBlogCommentInModeration}</p></div>{/option:commentIsInModeration}
					{option:commentIsSpam}<div class="message error"><p>{$msgBlogCommentIsSpam}</p></div>{/option:commentIsSpam}
					{option:commentIsAdded}<div class="message success"><p>{$msgBlogCommentIsAdded}</p></div>{/option:commentIsAdded}
					{form:comment}
						<fieldset class="horizontal">
							<p class="bigInput{option:txtAuthorError} errorArea{/option:txtAuthorError}">
								<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtAuthor} {$txtAuthorError}
							</p>
							<p class="bigInput{option:txtEmailError} errorArea{/option:txtEmailError}">
								<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtEmail} {$txtEmailError}
							</p>
							<p class="bigInput{option:txtWebsiteError} errorArea{/option:txtWebsiteError}">
								<label for="website">{$lblWebsite|ucfirst}</label>
								{$txtWebsite} {$txtWebsiteError}
							</p>
							<p class="bigInput{option:txtMessageError} errorArea{/option:txtMessageError}">
								<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtMessage} {$txtMessageError}
							</p>
							<p>
								<input class="inputSubmit" type="submit" name="comment" value="{$msgComment|ucfirst}" />
							</p>
						</fieldset>
					{/form:comment}
				</div>
			</div>
		</section>
	{/option:item.allow_comments}
</div>