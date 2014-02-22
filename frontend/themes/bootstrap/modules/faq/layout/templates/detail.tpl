{*
	variables that are available:
	- {$item}: contains data about the question
	- {$related}: the related items
*}
<div id="faqDetail" class="faq">
	<article class="article" role="main">
		<header role="banner">
			<h1>{$item.question}</h1>
            {option:settings.allow_multiple_categories}
					{* Category*}
					<span class="hideText">{$lblIn|ucfirst} {$lblThe} </span>{$lblCategory}: <a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>

					{* Tags*}
					{option:item.tags}
						<span class="hideText">{$lblWith} {$lblThe} </span>{$lblTags}:
						{iteration:item.tags}
							<a class="label label-default" href="{$item.tags.full_url}" rel="tag" title="{$item.tags.name}">{$item.tags.name}</a>
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


		{option:settings.allow_feedback}
			<aside id="faqFeedbackForm" role="complementary">
				{option:success}<div class="alert alert-success" role="alert">{$msgFeedbackSuccess}</div>{/option:success}
				{option:spam}<div class="alert alert-danger" role="alert">{$errFeedbackSpam}</div>{/option:spam}
				{form:feedback}
					<div class="row">
						<div class="col-xs-12 well">
							{$hidQuestionId}
							<div class="options form-inline">
								<div class="col-md-3">
									{$msgFeedback|ucfirst}
								</div>
								<div class="col-md-9">
									{iteration:useful}
										<div class="col-md-2">
											<label for="{$useful.id}">{$useful.rbtUseful} {$useful.label|ucfirst}</label>
										</div>
									{/iteration:useful}
								</div>
							</div>

							<div id="feedbackNoInfo"{option:hideFeedbackNoInfo} style="display: none;"{/option:hideFeedbackNoInfo}>
								<div class="form-group{option:txtMessageError} has-error{/option:txtMessageError}">
									<label class="control-label" for="message">{$msgHowToImprove|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
										{$txtMessage} {$txtMessageError}
								</div>
								<input class="btn btn-default" type="submit" name="comment" value="{$lblSend|ucfirst}" />
							</div>
						</div>
					</div>
				{/form:feedback}
			</aside>
		{/option:settings.allow_feedback}
	</article>

	{option:inSameCategory}
		<section id="faqOtherQuestions" class="faqOtherQuestions">
			<header role="banner">
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
		<section id="faqAlsoRead" class="faqAlsoRead">
			<header role="banner">
				<h2>{$msgRelatedQuestions|ucfirst}</h2>
			</header>
			<ul>
				{iteration:related}
					<li><a href="{$related.full_url}" title="{$related.question}">{$related.question}</a></li>
				{/iteration:related}
			</ul>
		</section>
	{/option:related}

	<ul class="pager">
		<li class="previous">
			<a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">&larr; {$lblToFaqOverview|ucfirst}</a>
		</li>
	</ul>

</div>
