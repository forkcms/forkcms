{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h3>{$lblFaq|ucfirst}: {$msgEditQuestion|sprintf:{$item['question']}}</h3>
</div>

{form:edit}
	{option:categories}
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td id="leftColumn">

					<div class="content">
						<div class="options">
							<p>
								<label for="question">{$lblQuestion|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
								{$txtQuestion} {$txtQuestionError}
							</p>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblAnswer|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
						</div>
						<div class="optionsRTE">
							{$txtAnswer} {$txtAnswerError}
						</div>
					</div>

				</td>

				<td id="sidebar">

					<div id="questionCategory" class="box">
						<div class="heading">
							<h3>{$lblCategory|ucfirst}</h3>
						</div>

						<div class="options">
							<p>
								{$ddmCategories} {$ddmCategoriesError}
							</p>
						</div>
					</div>

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

				</td>
			</tr>
		</table>

		<div class="fullwidthOptions">
			<a href="{$var|geturl:'delete'}&amp;id={$item['id']}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
			<div class="buttonHolderRight">
				<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblPublish|ucfirst}" />
			</div>
		</div>

		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item['question']}}
			</p>
		</div>
	{/option:categories}

	{option:!categories}
		{$msgNoCategories|sprintf:{$var|geturl:'add_category'}}
	{/option:!categories}
{/form:edit}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}