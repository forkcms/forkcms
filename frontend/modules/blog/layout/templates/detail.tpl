{*
variables that are available:
- {$item}: contains data about the post
- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
- {$commentsCount}: contains a variable with the number of comments for this blog post.
- {$navigation}: contains an array with data for previous and next post
*}

<article itemscope itemtype="http://schema.org/Blog">
	<meta itemprop="interactionCount" content="UserComments:{$commentsCount}">
	<meta itemprop="author" content="{$item.user_id|usersetting:'nickname'}">
	<header>
		<h1 itemprop="articletitle">{$item.title}</h1>
		<p>
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
		</p>
	</header>
	<div itemprop="articlecontent">
		{option:item.image}
			<img src="{$FRONTEND_FILES_URL}/blog/images/128x128/{$item.image}" alt="{$item.title}" itemprop="image" />
		{/option:item.image}
		{$item.text}
	</div>
	<footer>
		<p>
			<a href="{$item.full_url}">{$lblShare|ucfirst}</a>
		</p>

		{option:navigation}
		<ul>
			{option:navigation.previous}
			<li>
				<a href="{$navigation.previous.url}" rel="prev">{$lblPreviousArticle|ucfirst}: {$navigation.previous.title}</a>
			</li>
			{/option:navigation.previous}
			{option:navigation.next}
			<li>
				<a href="{$navigation.next.url}" rel="next">{$lblNextArticle|ucfirst}: {$navigation.next.title}</a>
			</li>
			{/option:navigation.next}
		</ul>
		{/option:navigation}
	</footer>
</article>

{option:comments}
	{option:item.allow_comments}
		<section itemscope itemtype="http://schema.org/Article">
			<header>
				<h1 id="{$actComments}">{$lblComments|ucfirst}</h1>
			</header>
			{iteration:comments}
				{* Do not alter the id! It is used as an anchor *}
				<article id="comment-{$comments.id}" itemprop="comment" itemscope itemtype="http://schema.org/UserComments">
					<meta itemprop="discusses" content="{$item.title}" />
					{option:comments.website}<a href="{$comments.website}">{/option:comments.website}
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$comments.author}" class="replaceWithGravatar" data-gravatar-id="{$comments.gravatar_id}" />
					{option:comments.website}</a>{/option:comments.website}
					<p itemscope itemtype="http://schema.org/Person">
						{option:comments.website}<a href="{$comments.website}" itemprop="url">{/option:comments.website}
						<span itemprop="creator name">{$comments.author}</span>
						{option:comments.website}</a>{/option:comments.website}
						{$lblWrote}
						<time itemprop="commentTime" datetime="{$comments.created_on|date:'Y-m-d\TH:i:s'}">{$comments.created_on|timeago}</time>
					</p>
					<div itemprop="commentText">
						{$comments.text|cleanupplaintext}
					</div>
				</article>
			{/iteration:comments}
		</section>
	{/option:item.allow_comments}
{/option:comments}
{option:item.allow_comments}
	<section id="{$actComment}">
		<header>
			<h1 >{$msgComment|ucfirst}</h1>
		</header>
		{option:commentIsInModeration}<div class="alert-box notice"><p>{$msgBlogCommentInModeration}</p></div>{/option:commentIsInModeration}
		{option:commentIsSpam}<div class="alert-box error"><p>{$msgBlogCommentIsSpam}</p></div>{/option:commentIsSpam}
		{option:commentIsAdded}<div class="alert-box success"><p>{$msgBlogCommentIsAdded}</p></div>{/option:commentIsAdded}
		{form:commentsForm}
			<p {option:txtAuthorError}class="errorArea"{/option:txtAuthorError}>
				<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtAuthor} {$txtAuthorError}
			</p>
			<p {option:txtEmailError}class="errorArea"{/option:txtEmailError}>
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
				<input type="submit" name="comment" value="{$msgComment|ucfirst}" />
			</p>
		{/form:commentsForm}
	</section>
{/option:item.allow_comments}