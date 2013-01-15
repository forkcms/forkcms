{*
	variables that are available:
	- {$item}: contains data about the question
	- {$related}: the related items
*}
<div id="faqDetail" class="faq">
	<article class="article">
		<header>
			<h1>{$item.question}</h1>
            {option:settings.allow_multiple_categories}
					{* Category*}
					<span class="hideText">{$lblIn|ucfirst} {$lblThe} </span>{$lblCategory}: <a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>

					{* Tags*}
					{option:item.tags}
						<span class="hideText">{$lblWith} {$lblThe} </span>{$lblTags}:
						{iteration:item.tags}
							<a class="tag" href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>
						{/iteration:item.tags}
					{/option:item.tags}
            {/option:settings.allow_multiple_categories}

			{* Tags *}
            {option:!settings.allow_multiple_categories}
                {option:item.tags}
                <ul>
                    <li>
                        {$lblWith} {$lblThe} {$lblTags}
                        {iteration:item.tags}
                            <a href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>{option:!item.tags.last}, {/option:!item.tags.last}{option:item.tags.last}.{/option:item.tags.last}
                        {/iteration:item.tags}
                    </li>
                </ul>
                {/option:item.tags}
            {/option:!settings.allow_multiple_categories}
		</header>
		<div class="bd content">
			{$item.answer}
		</div>
	</article>

	{option:inSameCategory}
		<section id="faqRelatedItems">
			<header>
            	{option:settings.allow_multiple_categories}<h3>{$msgQuestionsInSameCategory|ucfirst}</h3>{/option:settings.allow_multiple_categories}
            	{option:!settings.allow_multiple_categories}<h3>{$msgOtherQuestions|ucfirst}</h3>{/option:!settings.allow_multiple_categories}
			</header>
			<div class="bd content">
				<ul>
					{iteration:inSameCategory}
						<li><a href="{$inSameCategory.full_url}" title="{$inSameCategory.question}">{$inSameCategory.question}</a></li>
					{/iteration:inSameCategory}
				</ul>
			</div>
		</section>
	{/option:inSameCategory}

	{option:related}
		<section id="faqRelatedItems">
			<header>
				<h3>{$msgRelatedQuestions|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:related}
						<li><a href="{$related.full_url}" title="{$related.question}">{$related.question}</a></li>
					{/iteration:related}
				</ul>
			</div>
		</section>
	{/option:related}
	
	<ul class="pager">
		<li class="previous">
			<a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">&larr; {$lblToFaqOverview|ucfirst}</a>
		</li>
	</ul>

	{option:settings.allow_feedback}
		<section id="faqFeedbackForm" class="mod">
			<header>
				<h3 id="{$actFeedback}">{$msgFeedback|ucfirst}</h3>
			</header>
			<div class="bd">
				{option:success}<div class="alert alert-success">{$msgFeedbackSuccess}</div>{/option:success}
				{option:spam}<div class="alert alert-error">{$errFeedbackSpam}</div>{/option:spam}

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
		</section>
	{/option:settings.allow_feedback}
</div>