<div class="box" id="widgetFaqFeedback">
	<div class="heading">
		<h3><a href="{$var|geturl:'index':'faq'}">{$lblFaq|ucfirst}: {$lblFeedback|ucfirst}</a></h3>
	</div>

	{option:faqFeedback}
		<div class="dataGridHolder">
			<table class="dataGrid">
				<tbody>
					{iteration:faqFeedback}
					<tr class="{cycle:'odd':'even'}">
						<td><a href="{$faqFeedback.full_url}">{$faqFeedback.text|truncate:150}</a></td>
					</tr>
					{/iteration:faqFeedback}
				</tbody>
			</table>
		</div>
	{/option:faqFeedback}

	{option:!faqFeedback}
		<div class="options content">
			<p>{$msgNoFeedback}</p>
		</div>
	{/option:!faqFeedback}

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'index':'faq'}" class="button"><span>{$lblAllQuestions|ucfirst}</span></a>
		</div>
	</div>
</div>