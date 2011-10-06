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
	<div class="mod article">
		<div class="inner">
			<div class="hd">
				<h1>
					{$item.title}
				</h1>
				<h3>
					{$item.starts_on|date:{$dateFormatShort}:{$LANGUAGE}} {$item.starts_on|date:{$timeFormat}:{$LANGUAGE}}
					{option:item.ends_on} - {$item.ends_on|date:{$dateFormatShort}:{$LANGUAGE}} {$item.ends_on|date:{$timeFormat}:{$LANGUAGE}}{/option:item.ends_on}
				</h3>
				<p>
					{$item.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} -
					{option:!comments}<a href="{$item.full_url}#{$actComment}">{$msgEventsNoComments}</a>{/option:!comments}
					{option:comments}
						{option:commentsMultiple}<a href="{$item.full_url}#{$actComments}">{$msgEventsNumberOfComments|sprintf:{$commentsCount}}</a>{/option:commentsMultiple}
						{option:!commentsMultiple}<a href="{$item.full_url}#{$actComments}">{$msgEventsOneComment}</a>{/option:!commentsMultiple}
					{/option:comments}

					{option:subscriptions}
						{option:subscriptionsMultiple}<a href="{$item.full_url}#{$actSubscriptions}">{$msgEventsNumberOfSubscriptions|sprintf:{$subscriptionsCount}}</a>{/option:subscriptionsMultiple}
						{option:!subscriptionsMultiple}<a href="{$item.full_url}#{$actSubscriptions}">{$msgEventsOneSubscription}</a>{/option:!subscriptionsMultiple}
					{/option:subscriptions}
					{option:!item.in_past}
						{* @todo : implement iCal *}
						{* <a href="#">{$msgEventsDownloadIcal}</a> *}
					{/option:!item.in_past}
				</p>
			</div>
			<div class="bd content">
				{option:item.imageURL}
					<img src="{$item.imageURL}" alt="{$item.title}" width="300" class="eventImage"/>
				{/option:item.imageURL}
				{$item.text}
			</div>
			<div class="ft">
				{$msgWrittenBy|ucfirst|sprintf:{$item.user_id|usersetting:'nickname'}}
				{$lblInTheCategory}: <a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>.
				{option:item.tags}
					{$lblTags|ucfirst}:
					{iteration:item.tags}
						<a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
					{/iteration:item.tags}
				{/option:item.tags}
			</div>
		</div>
	</div>
	<div id="navigation" class="mod">
		<div class="inner">
			<div class="bd">
				<ul>
					{option:navigation.previous}
						<li class="previousLink">
							<a href="{$navigation.previous.url}" rel="prev">{$lblPreviousEvent|ucfirst}: <em>{$navigation.previous.title}</em></a>
						</li>
					{/option:navigation.previous}
					{option:navigation.next}
						<li class="nextLink">
							<a href="{$navigation.next.url}" rel="next">{$lblNextEvent|ucfirst}: <em>{$navigation.next.title}</em></a>
						</li>
					{/option:navigation.next}
				</ul>
			</div>
		</div>
	</div>

	{option:subscriptions}
	<div id="subscriptions" class="mod">
		<div class="inner">
			<div class="hd">
				<h3 id="{$actSubscriptions}">{$lblSubscriptions|ucfirst}</h3>
			</div>
			{iteration:subscriptions}
				{* Do not alter the id! It is used as an anchor *}
				<div id="subscription-{$subscriptions.id}" class="bd">
					<div class="imageHolder">
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$subscriptions.author}" class="replaceWithGravatar" data-gravatar-id="{$subscriptions.gravatar_id}" />
					</div>
					<div class="subscriptionContent">
						<p class="subscriptionAuthor">
							{$subscriptions.author}
						</p>
					</div>
				</div>
			{/iteration:subscriptions}
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
							<p>
								<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtAuthor} {$txtAuthorError}
							</p>
							<p>
								<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtEmail} {$txtEmailError}
							</p>
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
	<div id="comments" class="mod">
		<div class="inner">
			<div class="hd">
				<h3 id="{$actComments}">{$lblComments|ucfirst}</h3>
			</div>
			{iteration:comments}
				{* Do not alter the id! It is used as an anchor *}
				<div id="comment-{$comments.id}" class="bd comment">
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
	{/option:comments}
	{option:item.allow_comments}
		<div id="eventsCommentForm" class="mod">
			<div class="inner">
				<div class="hd">
					<h3>{$msgComment|ucfirst}</h3>
				</div>
				<div class="bd">
					{option:commentIsInModeration}<div class="message warning"><p>{$msgEventsCommentInModeration}</p></div>{/option:commentIsInModeration}
					{option:commentIsSpam}<div class="message error"><p>{$msgEventsCommentIsSpam}</p></div>{/option:commentIsSpam}
					{option:commentIsAdded}<div class="message success"><p>{$msgEventsCommentIsAdded}</p></div>{/option:commentIsAdded}
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
	{/option:item.allow_comments}
</div>