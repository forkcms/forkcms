{*
	variables that are available:
	- {$item}: contains data about the post
	- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$commentsCount}: contains a variable with the number of comments for this item.
	- @todo	{$subcriptions}: contains an array with the subscriptions for the post, each element contains data about the subscription.
	- @todo	{$subscriptionsCount}: contains a variable with the number of subscriptions for this item.
	- {$navigation}: contains an array with data for previous and next post
*}

<div id="eventDetail">
	<article class="mod article" itemscope itemtype="http://schema.org/Event">
		<div class="inner">
			<header class="hd">
				<h1 itemprop="name">{$item.title}</h1>
				<h3>
					<time itemprop="startDate" datetime="{$item.starts_on|date:'Y-m-d\TH:i:s'}">{$item.starts_on|date:{$dateFormatShort}:{$LANGUAGE}} {$item.starts_on|date:{$timeFormat}:{$LANGUAGE}}</time>
					{option:item.ends_on} - <time itemprop="endDate" datetime="{$item.end_on|date:'Y-m-d\TH:i:s'}">{$item.ends_on|date:{$dateFormatShort}:{$LANGUAGE}} {$item.ends_on|date:{$timeFormat}:{$LANGUAGE}}</time>{/option:item.ends_on}
				</h3>
				<ul>
					<li>
						{* Written by *}
						{$msgWrittenBy|ucfirst|sprintf:{$item.user_id|usersetting:'nickname'}}

						{* Written on *}
						{$lblOn} <time datetime="{$item.publish_on|date:'Y-m-d\TH:i:s'}" pubdate>{$item.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>

						{* Category*}
						{$lblIn} {$lblThe} {$lblCategory} <a title="{$item.category_title}">{$item.category_title}</a>{option:!item.tags}.{/option:!item.tags}

						{* Tags *}
						{option:item.tags}
							{$lblWith} {$lblThe} {$lblTags}
							<span>
								{iteration:item.tags}
									<a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
								{/iteration:item.tags}
							</span>
						{/option:item.tags}
					</li>
					<li>
						{* Comments *}
						{option:!comments}<a href="{$item.full_url}#{$actComment}">{$msgEventsNoComments}</a>{/option:!comments}
						{option:comments}
							{option:commentsMultiple}<a href="{$item.full_url}#{$actComments}">{$msgEventsNumberOfComments|sprintf:{$commentsCount}}</a>{/option:commentsMultiple}
							{option:!commentsMultiple}<a href="{$item.full_url}#{$actComments}">{$msgEventsOneComment}</a>{/option:!commentsMultiple}
						{/option:comments}
					</li>
					<li>
						{* Subscriptions *}
						{option:subscriptions}
							{option:subscriptionsMultiple}<a href="{$item.full_url}#{$actSubscriptions}">{$msgEventsNumberOfSubscriptions|sprintf:{$subscriptionsCount}}</a>{/option:subscriptionsMultiple}
							{option:!subscriptionsMultiple}<a href="{$item.full_url}#{$actSubscriptions}">{$msgEventsOneSubscription}</a>{/option:!subscriptionsMultiple}
						{/option:subscriptions}
					</li>
					{option:!item.in_past}
						<li><a href="{$item.ical_url}">{$msgEventsDownloadIcal}</a></li>
					{/option:!item.in_past}
				</ul>
			</header>
			<div class="bd content" itemprop="description">
				{option:item.image}<img src="{$FRONTEND_FILES_URL}/blog/images/source/{$item.image}" alt="{$item.title}" itemprop="image" />{/option:item.image}
				{$item.text}
			</div>
			<footer class="ft">
				<ul class="pageNavigation">
					{option:navigation.previous}
						<li class="previousLink">
							<a href="{$navigation.previous.url}" rel="prev">{$lblPreviousEvent|ucfirst}: {$navigation.previous.title}</a>
						</li>
					{/option:navigation.previous}
					{option:navigation.next}
						<li class="nextLink">
							<a href="{$navigation.next.url}" rel="next">{$lblNextEvent|ucfirst}: {$navigation.next.title}</a>
						</li>
					{/option:navigation.next}
				</ul>
			</footer>
		</div>
	</article>

	{option:subscriptions}
		<section id="eventSubscriptions" class="mod" itemprop="attendees">
			<div class="inner">
				<header class="hd">
					<h3 id="{$actSubscriptions}">{$lblSubscriptions|ucfirst}</h3>
				</header>
				<div class="bd content">
					{iteration:subscriptions}
						{* Do not alter the id! It is used as an anchor *}
						<div id="subscription-{$subscriptions.id}" class="comment" itemscope itemtype="http://schema.org/Person">
							<div class="imageHolder">
								<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$subscriptions.author}" class="replaceWithGravatar" data-gravatar-id="{$subscriptions.gravatar_id}" />
							</div>
							<div class="subscriptionContent">
								<p class="subscriptionAuthor" itemprop="name">
									{$subscriptions.author}
								</p>
							</div>
						</div>
					{/iteration:subscriptions}
				</div>
			</div>
		</div>
	{/option:subscriptions}
	{option:item.allow_subscriptions}
		<div id="eventsSubscribeForm" class="mod">
			<div class="inner">
				<div class="hd">
					<h3>{$msgSubscribe|ucfirst}</h3>
				</div>
				<div class="bd">
					{option:subscriptionIsInModeration}<div class="message warning"><p>{$msgEventsSubscriptionInModeration}</p></div>{/option:subscriptionIsInModeration}
					{option:subscriptionIsSpam}<div class="message error"><p>{$msgEventsSubscriptionIsSpam}</p></div>{/option:subscriptionIsSpam}
					{option:subscriptionIsAdded}<div class="message success"><p>{$msgEventsSubscriptionIsAdded}</p></div>{/option:subscriptionIsAdded}

					{option:subscriptionsComplete}<div class="message notice"><p>{$msgEventsSubscriptionsComplete}</p></div>{/option:subscriptionsComplete}
					{option:!subscriptionsComplete}
						{form:subscription}
							<div class="alignBlocks">
								<p {option:txtSubscriptionAuthorError}class="errorArea"{/option:txtSubscriptionAuthorError}>
									<label for="subscriptionAuthor">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									{$txtSubscriptionAuthor} {$txtSubscriptionAuthorError}
								</p>
								<p {option:txtSubscriptionEmailError}class="errorArea"{/option:txtSubscriptionEmailError}>
									<label for="subscriptionEmail">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									{$txtSubscriptionEmail} {$txtSubscriptionEmailError}
								</p>
							</div>
							<p>
								<input class="inputSubmit" type="submit" name="subscription" value="{$lblSubscribe|ucfirst}" />
							</p>
						{/form:subscription}
					{/option:!subscriptionsComplete}
				</div>
			</div>
		</div>
	{/option:item.allow_subscriptions}

	{option:comments}
		<section id="eventComments" class="mod" itemscope itemtype="http://schema.org/Article">
			<div class="inner">
				<header class="hd">
					<h3 id="{$actComments}">{$lblComments|ucfirst}</h3>
				</header>
				<div class="bd content">
					{iteration:comments}
						{* Do not alter the id! It is used as an anchor *}
						<div id="comment-{$comments.id}" class="comment" itemprop="comment" itemscope itemtype="http://schema.org/UserComments">
							<meta itemprop="discusses" content="{$item.title}" />  
							<div class="imageHolder">
								{option:comments.website}<a href="{$comments.website}">{/option:comments.website}
									<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$comments.author}" class="replaceWithGravatar" data-gravatar-id="{$comments.gravatar_id}" />
								{option:comments.website}</a>{/option:comments.website}
							</div>
							<div class="commentContent">
								<p class="commentAuthor" itemscope itemtype="http://schema.org/Person">
									{option:comments.website}<a href="{$comments.website}" itemprop="url">{/option:comments.website}
										<span itemprop="creator name">{$comments.author}</span>
									{option:comments.website}</a>{/option:comments.website}
									{$lblWrote}
									<time itemprop="commentTime" datetime="{$comments.created_on|date:'Y-m-d\TH:i:s'}">{$comments.created_on|timeago}</time>
								</p>
								<div class="commentText content" itemprop="commentText">
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
		<section id="eventsCommentForm" class="mod">
			<div class="inner">
				<header class="hd">
					<h3 id="{$actComment}">{$msgComment|ucfirst}</h3>
				</header>
				<div class="bd">
					{option:commentIsInModeration}<div class="message warning"><p>{$msgEventsCommentInModeration}</p></div>{/option:commentIsInModeration}
					{option:commentIsSpam}<div class="message error"><p>{$msgEventsCommentIsSpam}</p></div>{/option:commentIsSpam}
					{option:commentIsAdded}<div class="message success"><p>{$msgEventsCommentIsAdded}</p></div>{/option:commentIsAdded}
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