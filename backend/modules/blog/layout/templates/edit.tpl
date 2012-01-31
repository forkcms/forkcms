{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblBlog|ucfirst}: {$msgEditArticle|sprintf:{$item.title}}</h2>
	<div class="buttonHolderRight">
		<a href="{$detailURL}/{$item.url}{option:item.revision_id}?revision={$item.revision_id}{/option:item.revision_id}" class="button icon iconZoom previewButton targetBlank">
			<span>{$lblView|ucfirst}</span>
		</a>
	</div>
</div>

{form:edit}
	<label for="title">{$lblTitle|ucfirst}</label>
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
			<li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblComments|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table width="100%">
				<tr>
					<td id="leftColumn">

						{* Main content *}
						<div class="box">
							<div class="heading">
								<h3>
									<label for="text">{$lblMainContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								</h3>
							</div>
							<div class="optionsRTE">
								{$txtText} {$txtTextError}
							</div>
						</div>

						{* Image *}
						{option:imageIsAllowed}
						<div class="box">
							<div class="heading">
								<h3>{$lblImage|ucfirst}</h3>
							</div>
							<div class="options">
								{option:item.image}
								<p>
									<img src="{$FRONTEND_FILES_URL}/blog/images/source/{$item.image}" width="500" alt="{$lblImage|ucfirst}" />
								</p>
								<p>
									<label for="deleteImage">{$chkDeleteImage} {$lblDelete|ucfirst}</label>
									{$chkDeleteImageError}
								</p>
								{/option:item.image}
								<p>
									<label for="image">{$lblImage|ucfirst}</label>
									{$fileImage} {$fileImageError}
								</p>
							</div>
						</div>
						{/option:imageIsAllowed}

						{* Summary *}
						<div class="box">
							<div class="heading">
								<div class="oneLiner">
									<h3>
										<label for="introduction">{$lblSummary|ucfirst}</label>
									</h3>
									<abbr class="help">(?)</abbr>
									<div class="tooltip" style="display: none;">
										<p>{$msgHelpSummary}</p>
									</div>
								</div>
							</div>
							<div class="optionsRTE">
								{$txtIntroduction} {$txtIntroductionError}
							</div>
						</div>

					</td>

					<td id="sidebar">
						<div id="publishOptions" class="box">
							<div class="heading">
								<h3>{$lblStatus|ucfirst}</h3>
							</div>

							{option:usingDraft}
							<div class="options">
								<div class="buttonHolder">
									<a href="{$detailURL}/{$item.url}?revision={$draftId}" class="button icon iconZoom targetBlank"><span>{$lblPreview|ucfirst}</span></a>
								</div>
							</div>
							{/option:usingDraft}

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

							<div class="options">
								<p class="p0"><label for="publishOnDate">{$lblPublishOn|ucfirst}</label></p>
								<div class="oneLiner">
									<p>
										{$txtPublishOnDate} {$txtPublishOnDateError}
									</p>
									<p>
										<label for="publishOnTime">{$lblAt}</label>
									</p>
									<p>
										{$txtPublishOnTime} {$txtPublishOnTimeError}
									</p>
								</div>
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
								<label for="userId">{$lblAuthor|ucfirst}</label>
								{$ddmUserId} {$ddmUserIdError}
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

		<div id="tabPermissions">
			<table width="100%">
				<tr>
					<td>
						{$chkAllowComments} <label for="allowComments">{$lblAllowComments|ucfirst}</label>
					</td>
				</tr>
			</table>
		</div>

		<div id="tabVersions">
			{option:drafts}
				<div class="tableHeading">
					<div class="oneLiner">
						<h3 class="oneLinerElement">{$lblDrafts|ucfirst}</h3>
						<abbr class="help">(?)</abbr>
						<div class="tooltip" style="display: none;">
							<p>{$msgHelpDrafts}</p>
						</div>
					</div>
				</div>

				<div class="dataGridHolder">
					{$drafts}
				</div>
			{/option:drafts}

			<div class="tableHeading">
				<div class="oneLiner">
					<h3 class="oneLinerElement">{$lblPreviousVersions|ucfirst}</h3>
					<abbr class="help">(?)</abbr>
					<div class="tooltip" style="display: none;">
						<p>{$msgHelpRevisions}</p>
					</div>
				</div>
			</div>

			{option:revisions}
			<div class="dataGridHolder">
				{$revisions}
			</div>
			{/option:revisions}

			{option:!revisions}
				<p>{$msgNoRevisions}</p>
			{/option:!revisions}
		</div>

		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showBlogDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}{option:categoryId}&amp;category={$categoryId}{/option:categoryId}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>

		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.title}}
			</p>
		</div>
		{/option:showBlogDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblPublish|ucfirst}" />
			<a href="#" id="saveAsDraft" class="inputButton button"><span>{$lblSaveDraft|ucfirst}</span></a>
		</div>
	</div>

	<div id="addCategoryDialog" class="forkForms" title="{$lblAddCategory|ucfirst}" style="display: none;">
		<div id="templateList">
			<p>
				<label for="categoryTitle">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<input type="text" name="categoryTitle" id="categoryTitle" class="inputText" maxlength="255" />
				<span class="formError" id="categoryTitleError" style="display: none;">{$errFieldIsRequired|ucfirst}</span>
			</p>
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}