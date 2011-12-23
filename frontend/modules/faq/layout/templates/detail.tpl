{*
	variables that are available:
	- {$item}: contains data about the question
	- {$related}: the related items
*}
<div id="blogDetail">
	<article class="mod article">
		<div class="inner">
			<header class="hd">
				<h1>{$item.question}</h1>
				<ul>
					<li>
						{* Category*}
						{$lblIn|ucfirst} {$lblThe} {$lblCategory} <a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>{option:!item.tags}.{/option:!item.tags}

						{* Tags*}
						{option:item.tags}
							{$lblWith} {$lblThe} {$lblTags}
							{iteration:item.tags}
								<a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
							{/iteration:item.tags}
						{/option:item.tags}
					</li>
				</ul>
			</header>
			<div class="bd content">
				{$item.answer}
			</div>
		</div>
	</article>

	{option:inSameCategory}
		<section id="blogRelatedItems" class="mod">
			<div class="inner">
				<header class="hd">
					<h3>{$msgQuestionsInSameCategory|ucfirst}</h3>
				</header>
				<div class="bd content">
					<ul>
						{iteration:inSameCategory}
							<li><a href="{$inSameCategory.full_url}" title="{$inSameCategory.question}">{$inSameCategory.question}</a></li>
						{/iteration:inSameCategory}
					</ul>
				</div>
			</div>
		</section>
	{/option:inSameCategory}

	{option:related}
		<section id="blogRelatedItems" class="mod">
			<div class="inner">
				<header class="hd">
					<h3>{$msgRelatedQuestions|ucfirst}</h3>
				</header>
				<div class="bd content">
					<ul>
						{iteration:related}
							<li><a href="{$related.full_url}" title="{$related.question}">{$related.question}</a></li>
						{/iteration:related}
					</ul>
				</div>
			</div>
		</section>
	{/option:related}

	<p><a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">{$lblToFaqOverview|ucfirst}</a></p>

	{option:settings.allow_feedback}
		<section id="faqFeedbackForm" class="mod">
			<div class="inner">
				<header class="hd">
					<h3 id="{$actFeedback}">{$msgFeedback|ucfirst}</h3>
				</header>
				<div class="bd">
					{option:success}<div class="message success"><p>{$msgFeedbackSuccess}</p></div>{/option:success}
					{option:spam}<div class="message error"><p>{$errFeedbackSpam}</p></div>{/option:spam}

					{form:feedback}
						{$hidQuestionId}
						<div class="options">
							<ul class="inputList">
								{iteration:useful}
									<li>
										{$useful.rbtUseful}
										<label for="{$useful.id}">{$useful.label|ucfirst}</label>
									</li>
								{/iteration:useful}
							</ul>
						</div>

						<div id="feedbackNoInfo"{option:hideFeedbackNoInfo} style="display: none;"{/option:hideFeedbackNoInfo}>
							<p class="bigInput{option:txtMessageError} errorArea{/option:txtMessageError}">
								<label for="message">{$msgHowToImprove|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								{$txtMessage} {$txtMessageError}
							</p>
							<p>
								<input class="inputSubmit" type="submit" name="comment" value="{$lblSend|ucfirst}" />
							</p>
						</div>
					{/form:feedback}
				</div>
			</div>
		</section>
	{/option:settings.allow_feedback}
</div>