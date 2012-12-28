{*
	variables that are available:
	- {$item}: contains data about the post
	- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$commentsCount}: contains a variable with the number of comments for this blog post.
	- {$navigation}: contains an array with data for previous and next post
*}
<div id="blogDetail">
	<article class="mod article" itemscope itemtype="http://schema.org/Blog">
		<div class="inner">
			<meta itemprop="interactionCount" content="UserComments:{$commentsCount}">
			<meta itemprop="author" content="{$item.user_id|usersetting:'nickname'}">
			<header class="hd">
				<h1 itemprop="name">{$item.title}</h1>
				<ul>
					<li>
						{* Written by *}
						{$msgWrittenBy|ucfirst|sprintf:{$item.user_id|usersetting:'nickname'}}

						{* Written on *}
						{$lblOn} <time itemprop="datePublished" datetime="{$item.publish_on|date:'Y-m-d\TH:i:s'}">{$item.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>

						{* Category*}
						{$lblIn} {$lblThe} {$lblCategory} <a itemprop="articleSection" href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>{option:!item.tags}.{/option:!item.tags}

						{* Tags *}
						{option:item.tags}
							{$lblWith} {$lblThe} {$lblTags}
							<span itemprop="keywords">
								{iteration:item.tags}
									<a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
								{/iteration:item.tags}
							</span>
						{/option:item.tags}
					</li>
					<li>
						{* Comments *}
						{option:!comments}<a href="{$item.full_url}#{$actComment}" itemprop="discussionUrl">{$msgBlogNoComments|ucfirst}</a>{/option:!comments}
						{option:comments}
							{option:blogCommentsMultiple}<a href="{$item.full_url}#{$actComments}" itemprop="discussionUrl">{$msgBlogNumberOfComments|sprintf:{$commentsCount}}</a>{/option:blogCommentsMultiple}
							{option:!blogCommentsMultiple}<a href="{$item.full_url}#{$actComments}" itemprop="discussionUrl">{$msgBlogOneComment}</a>{/option:!blogCommentsMultiple}
						{/option:comments}
					</li>
					<li>
						<a href="{$item.full_url}" class="share">{$lblShare|ucfirst}</a>
					</li>
				</ul>
			</header>
			<div class="bd content" itemprop="articleBody">
				{option:item.image}<img src="{$FRONTEND_FILES_URL}/blog/images/source/{$item.image}" alt="{$item.title}" itemprop="image" />{/option:item.image}
				{$item.text}
			</div>
			<footer class="ft">
				<ul class="pageNavigation">
					{option:navigation.previous}
						<li class="previousLink">
							<a href="{$navigation.previous.url}" rel="prev">{$lblPreviousArticle|ucfirst}: {$navigation.previous.title}</a>
						</li>
					{/option:navigation.previous}
					{option:navigation.next}
						<li class="nextLink">
							<a href="{$navigation.next.url}" rel="next">{$lblNextArticle|ucfirst}: {$navigation.next.title}</a>
						</li>
					{/option:navigation.next}
				</ul>
			</footer>
		</div>
	</article>

	{option:comments}
		<section id="{$actComments}" class="comments" itemscope itemtype="http://schema.org/Article">
				<header>
				    <h3>{$lblComments|ucfirst}</h3>
				</header>
				{iteration:comments}
				    {* Do not alter the id! It is used as an anchor *}
				    <div id="comment-{$comments.id}" class="comment row-fluid {option:comments.last}lastChild{/option:comments.last}" itemprop="comment" itemscope itemtype="http://schema.org/UserComments">
				    	<div class="span2">
				    		<meta itemprop="discusses" content="{$item.title}" />
				    		{option:comments.website}<a href="{$comments.website}">{/option:comments.website}
				    			<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$comments.author}" class="replaceWithGravatar img-circle" data-gravatar-id="{$comments.gravatar_id}" />
				    		{option:comments.website}</a>{/option:comments.website}
				    	</div>
				    	<div class="span7">
				    		<div class="meta" itemscope itemtype="http://schema.org/Person">
				    			{option:comments.website}<a href="{$comments.website}" itemprop="url">{/option:comments.website}
				    				<span itemprop="creator name">{$comments.author}</span>{option:comments.website}</a>{/option:comments.website}
				    			<span class="hideText">{$lblWrote}</span><time class="muted" itemprop="commentTime" datetime="{$comments.created_on|date:'Y-m-d\TH:i:s'}">{$comments.created_on|timeago}</time>
				    		</div>
				    		<div class="commentText content" itemprop="commentText">
				    			{$comments.text|cleanupplaintext}
				    		</div>
				    	</div>
				    </div>
				{/iteration:comments}
		</section>
	{/option:comments}
	{option:item.allow_comments}
		<section id="{$actComment}" class="commentForm">
			<header>
			    <h3>{$msgComment|ucfirst}</h3>
			</header>
			<div class="well">
			    {option:commentIsInModeration}<div class="alert">{$msgBlogCommentInModeration}</div>{/option:commentIsInModeration}
			    {option:commentIsSpam}<div class="alert alert-error">{$msgBlogCommentIsSpam}</div>{/option:commentIsSpam}
			    {option:commentIsAdded}<div class="alert alert-success">{$msgBlogCommentIsAdded}</div>{/option:commentIsAdded}
			    {form:commentsForm}
			    	<div class="row-fluid">
			    		<div class="span7">
			    			<div class="control-group {option:txtMessageError}error{/option:txtMessageError}">
			    				<label class="control-label" for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			    				<div class="controls">
			    					{$txtMessage} {$txtMessageError}
			    				</div>
			    			</div>
			    		</div>
			    		<div class="span5 authorInfo">
			    			<div class="control-group {option:txtAuthorError}error{/option:txtAuthorError}">
			    				<label class="control-label" for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			    				<div class="controls">
			    				    {$txtAuthor} {$txtAuthorError}
			    				</div>
			    			</div>
			    			<div class="control-group {option:txtEmailError}error{/option:txtEmailError}">
			    				<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			    				<div class="controls">
			    					{$txtEmail} {$txtEmailError}
			    				</div>
			    			</div>
			    			<div class="control-group {option:txtWebsiteError}error{/option:txtWebsiteError}">
			    				<label class="control-label" for="website">{$lblWebsite|ucfirst}</label>
			    				<div class="controls">
			    					{$txtWebsite} {$txtWebsiteError}
			    				</div>
			    			</div>
			    		</div>
			    	</div>
			    	<div class="form-actions">
			    		<input class="btn-primary btn" type="submit" name="comment" value="{$msgComment|ucfirst}" />
			    	</div>
			    {/form:commentsForm}
			</div>
		</section>
	{/option:item.allow_comments}
</div>