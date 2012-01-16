{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblFaq|ucfirst}: {$msgEditQuestion|sprintf:{$item.question}}</h2>
</div>

{form:edit}
	<label for="title">{$lblQuestion|ucfirst}</label>
	{$txtTitle} {$txtTitleError}

	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>

	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
			<li><a href="#tabFeedback">{$lblFeedback|ucfirst} <span class="positiveFeedback">({$item.num_usefull_yes})</span> <span class="negativeFeedback">({$item.num_usefull_no})</span></a>
		</ul>

		<div id="tabContent">
			<table width="100%">
				<tr>
					<td id="leftColumn">

						<div class="box">
							<div class="heading">
								<h3>
									<label for="answer">{$lblAnswer|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								</h3>
							</div>
							<div class="optionsRTE">
								{$txtAnswer} {$txtAnswerError}
							</div>
						</div>

					</td>

					<td id="sidebar">
						<div id="publishOptions" class="box">
							<div class="heading">
								<h3>{$lblStatus|ucfirst}</h3>
							</div>

							<div class="options">
								<ul class="inputList">
									{iteration:hidden}
									<li>
										{$hidden.rbtHidden}
										<label for="{$hidden.id}">{$hidden.label}</label>
									</li>
									{/iteration:hidden}
								</ul>
							</div>
						</div>

						<div class="box" id="articleMeta">
							<div class="heading">
								<h3>{$lblMetaData|ucfirst}</h3>
							</div>
							<div class="options">
								<label for="categoryId">{$lblCategory|ucfirst}</label>
								{$ddmCategoryId} {$ddmCategoryIdError}
							</div>
							<div class="options">
								<label for="tags">{$lblTags|ucfirst}</label>
								{$txtTags} {$txtTagsError}
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>

		<div id="tabFeedback">
		{option:feedback}
			<p>
				{$msgFeedbackInfo}
			</p>
			{iteration:feedback}
			<div class="box feedback">
				<div class="heading shortFeedback container">
					<a href="#" class="icon iconCollapsed" title="open">
						<span>
							<label for="modules{$modules.label}">{$feedback.text|truncate:150}</label>
						</span>
					</a>
				</div>
				<div class="longFeedback options">
					<p>
						{$feedback.text}
					</p>

					{option:showFaqDeleteFeedback}
					<p>
						<a href="{$var|geturl:'delete_feedback'}&amp;id={$feedback.id}" class="button linkButton icon iconDelete">
							<span>{$lblDelete|ucfirst}</span>
						</a>
					</p>
					{/option:showFaqDeleteFeedback}
				</div>
			</div>
			{/iteration:feedback}
		{/option:feedback}

		{option:!feedback}
			<p>
				{$msgNoFeedbackItems}
			</p>
		{/option:!feedback}
		</div>

		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showFaqDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		{/option:showFaqDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblPublish|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.question}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}