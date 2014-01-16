{*
	variables that are available:
	- {$item}: contains data about the question
	- {$related}: the related items
*}
<article>
	<header>
		<h1>{$item.question}</h1>
		<p>
		{option:settings.allow_multiple_categories}
			{* Category*}
			{$lblIn}	<a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>
			{option:!item.tags}{/option:!item.tags}

			{* Tags*}
			{option:item.tags}
				{$lblWith} {$lblThe} {$lblTags}
				{iteration:item.tags}
					<a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
				{/iteration:item.tags}
			{/option:item.tags}
		{/option:settings.allow_multiple_categories}

		{* Tags *}
		{option:!settings.allow_multiple_categories}
			{option:item.tags}
				{$lblWith} {$lblThe} {$lblTags}
				{iteration:item.tags}
				<a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
				{/iteration:item.tags}
			{/option:item.tags}
		{/option:!settings.allow_multiple_categories}
	</header>
	{$item.answer}
	<footer>
		<p><a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">{$lblToFaqOverview|ucfirst}</a></p>
	</footer>
</article>

{option:inSameCategory}
<section>
	<header>
		{option:settings.allow_multiple_categories}<h2>{$msgQuestionsInSameCategory|ucfirst}</h2>{/option:settings.allow_multiple_categories}
		{option:!settings.allow_multiple_categories}<h2>{$msgOtherQuestions|ucfirst}</h2>{/option:!settings.allow_multiple_categories}
	</header>
	<ul>
		{iteration:inSameCategory}
		<li><a href="{$inSameCategory.full_url}" title="{$inSameCategory.question}">{$inSameCategory.question}</a></li>
		{/iteration:inSameCategory}
	</ul>
</section>
{/option:inSameCategory}

{option:related}
<section>
	<header>
		<h2>{$msgRelatedQuestions|ucfirst}</h2>
	</header>
	<ul>
		{iteration:related}
		<li><a href="{$related.full_url}" title="{$related.question}">{$related.question}</a></li>
		{/iteration:related}
	</ul>
</section>
{/option:related}

{option:settings.allow_feedback}
<section id="faqFeedbackForm">
	<header>
		<h2 id="{$actFeedback}">{$msgFeedback|ucfirst}</h2>
	</header>
	{option:success}<div class="alert-box success"><p>{$msgFeedbackSuccess}</p></div>{/option:success}
	{option:spam}<div class="alert-box error"><p>{$errFeedbackSpam}</p></div>{/option:spam}

	{form:feedback}
	{$hidQuestionId}
	<ul>
		{iteration:useful}
		<li>
			{$useful.rbtUseful}
			<label for="{$useful.id}">{$useful.label|ucfirst}</label>
		</li>
		{/iteration:useful}
	</ul>

	<div id="feedbackNoInfo"{option:hideFeedbackNoInfo} style="display: none;"{/option:hideFeedbackNoInfo}>
		<p{option:txtMessageError} class="form-error"{/option:txtMessageError}>
			<label for="message">{$msgHowToImprove|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtMessage} {$txtMessageError}
		</p>
		<p>
			<input type="submit" name="comment" value="{$lblSend|ucfirst}" />
		</p>
	</div>
	{/form:feedback}
</section>
{/option:settings.allow_feedback}