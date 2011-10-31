{*
	variables that are available:
	- {$item}: contains data about the post
	- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$commentsCount}: contains a variable with the number of comments for this blog post.
	- {$navigation}: contains an array with data for previous and next post
*}
<div id="blogDetail">
	<article class="mod"> 
		<header> 
			<h2><a href="{$item.full_url}" title="{$item.title}">{$item.title}</a></h2> 
			<ul class="meta">
				<li>
					<time datetime="{$item.publish_on|date:'c':{$LANGUAGE}}">
						<span class="day">{$item.publish_on|date:'d':{$LANGUAGE}}</span>
						<span class="month">{$item.publish_on|date:'m':{$LANGUAGE}}</span>
						<span class="year">{$item.publish_on|date:'Y':{$LANGUAGE}}</span>
					</time>
				</li> 
				<li class="comments">
					{* Comments *}
					{option:!comments}<a href="#{$actComment}" class="nocomments" title="{$msgBlogNoComments|ucfirst}"><span class="x">{$msgBlogNoComments|ucfirst}</span></a>{/option:!comments}
					{option:comments}<a href="#{$actComments}" title="{$msgBlogComments|ucfirst}">{$item.comments_count}</a>{/option:comments}
				</li>
			</ul>
		</header> 
		<div class="bd content"> 
			{$item.text}
		</div>
		<footer>
			<ul class="shareButtons"> 
					<li><div class="g-plusone" data-size="medium" data-count="false"></div></li>
					<li><a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a></li> 
					<li><iframe src="https://www.facebook.com/plugins/like.php?href={$SITE_URL}{$item.full_url}&amp;layout=button_count&amp;show_faces=false&amp;font=Arial&amp;locale=en_US" scrolling="no" frameborder="0" style="border:none; width:50px; height:20px"></iframe> 
					</li>
			</ul>
			<ul class="pageNavigation">
				{option:navigation.previous}
					<li class="previousLink">
						<a href="{$navigation.previous.url}" rel="prev">{$lblPreviousArticle|ucfirst}</a>
					</li>
				{/option:navigation.previous}
				{option:navigation.next}
					<li class="nextLink">
						<a href="{$navigation.next.url}" rel="next">{$lblNextArticle|ucfirst}</a>
					</li>
				{/option:navigation.next}
			</ul>
		</footer>
	</article>
	{option:comments}
	<section id="blogComments" class="mod">
		<div class="inner">
			<header class="hd">
				<h3 id="{$actComments}">{$lblComments|ucfirst}</h3>
			</header>
			<div class="bd content">
				{iteration:comments}
					{* Do not alter the id! It is used as an anchor *}
					<div id="comment-{$comments.id}" class="comment">
						<div class="imageHolder">
							{option:comments.website}<a href="{$comments.website}">{/option:comments.website}
								<img src="{$THEME_URL}/core/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$comments.author}" class="replaceWithGravatar" data-gravatar-id="{$comments.gravatar_id}" />
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
					<h3 id="{$actComment}">{$msgComment|ucfirst}</h3>
				</header>
				<div class="bd">
					{option:commentIsInModeration}<div class="message warning"><p>{$msgBlogCommentInModeration}</p></div>{/option:commentIsInModeration}
					{option:commentIsSpam}<div class="message error"><p>{$msgBlogCommentIsSpam}</p></div>{/option:commentIsSpam}
					{option:commentIsAdded}<div class="message success"><p>{$msgBlogCommentIsAdded}</p></div>{/option:commentIsAdded}
					{form:comment}
						<div class="alignBlocks">
							<p {option:txtAuthorError}class="errorArea"{/option:txtAuthorError}>
								<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtAuthor} {$txtAuthorError}
							</p>
							<p {option:txtEmailError}class="errorArea"{/option:txtEmailError}>
								<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtEmail} {$txtEmailError}
							</p>
						</div>
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
					{/form:comment}
				</div>
			</div>
		</section>
	{/option:item.allow_comments}
</div>