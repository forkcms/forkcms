{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_start.tpl}

{form:edit}
	{$hidTemplateId}

	<div class="pageTitle">
		<h2>{$lblPages|ucfirst}: {$lblEdit}</h2>
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}" class="button icon iconAdd">
				<span>{$lblAdd|ucfirst}</span>
			</a>
			{option:!item.is_hidden}
				<a href="{$SITE_URL}{$item.full_url}{option:appendRevision}?page_revision={$item.revision_id}{/option:appendRevision}" class="button icon iconZoom previewButton targetBlank">
					<span>{$lblView|ucfirst}</span>
				</a>
			{/option:!item.is_hidden}
			<a href="{$var|geturl:'index'}" class="button icon iconBack">
				<span>{$lblOverview|ucfirst}</span>
			</a>
		</div>
	</div>

	<p id="pagesPageTitle">
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
		<span class="oneLiner">
			<span><a href="{$SITE_URL}{$prefixURL}/{$item.url}{option:appendRevision}?page_revision={$item.revision_id}{/option:appendRevision}">{$SITE_URL}{$prefixURL}/<span id="generatedUrl">{$item.url}</span></a></span>
		</span>
	</p>

	<div id="tabs" class="tabs">
		<ul>
			<li style="float: left;"><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li style="float: left;"><a href="#tabRedirect">{$lblRedirect|ucfirst}</a></li>
			<!-- Reverse order after content tab [floatRight] -->
			<li><a href="#tabSettings">{$lblSettings|ucfirst}</a></li>
			<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
			<li><a href="#tabTemplate">{$lblTemplate|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
			<li><a href="#tabVersions">{$lblPreviousVersions|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<div id="editContent">
				{iteration:blocks}
					<div id="block-{$blocks.index}" class="box contentBlock">
						<div class="heading">
							<table border="0" cellpadding="0" cellspaciong="0">
								<tbody>
									<tr>
										<td>
											<div class="oneLiner">
												<h3><span class="blockName">{$blocks.name}</span></h3>
												{* don't remove this class *}
												<p class="linkedExtra">
													{* this will store the selected extra *}
													{$blocks.hidExtraId}
												</p>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="blockContentModule-{$blocks.index}" class="options">
							{* do not alter markup *}
							<div class="oneLiner">
								<span class="oneLinerElement"></span>
								<a href="#" class="button targetBlank">{$lblEditModuleContent|ucfirst}</a>
								{$blocks.txtHTMLError}
							</div>
						</div>
						<div id="blockContentWidget-{$blocks.index}" class="options">
							{* do not alter markup *}
							<div class="oneLiner">
								<span class="oneLinerElement"></span>
								<a href="#" class="button targetBlank">{$lblEdit|ucfirst}</a>
								{$blocks.txtHTMLError}
							</div>
						</div>
						<div id="blockContentHTML-{$blocks.index}" class="optionsRTE">
							<fieldset>
								{$blocks.txtHTML}
								{$blocks.txtHTMLError}
							</fieldset>
						</div>

					</div>
				{/iteration:blocks}
			</div>
		</div>

		<div id="tabRedirect">
			<div class="subtleBox">
				<div class="options">
					{$rbtRedirectError}
					<ul class="inputList radiobuttonFieldCombo">
						{iteration:redirect}
							<li>
								<label for="{$redirect.id}">{$redirect.rbtRedirect} {$redirect.label}</label>
								{option:redirect.isInternal}
										{$ddmInternalRedirect} {$ddmInternalRedirectError}
										<span class="helpTxt">{$msgHelpInternalRedirect}</span>
								{/option:redirect.isInternal}

								{option:redirect.isExternal}
										{$txtExternalRedirect} {$txtExternalRedirectError}
										<span class="helpTxt">{$msgHelpExternalRedirect}</span>
								{/option:redirect.isExternal}
							</li>
						{/iteration:redirect}
					</ul>
				</div>
			</div>
		</div>

		<div id="tabVersions">
			<div class="tableHeading">
				<div class="oneLiner">
					<h3 class="oneLinerElement">{$lblPreviousVersions|ucfirst}</h3>
					<abbr class="help">(?)</abbr>
					<div class="tooltip" style="display: none;">
						<p>{$msgHelpRevisions}</p>
					</div>
				</div>
			</div>
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
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblTitles|ucfirst}</h3>
				</div>
				<div class="options">
					<p>
						<label for="pageTitleOverwrite">{$lblPageTitle|ucfirst}</label>
						<span class="helpTxt">{$msgHelpPageTitle}</span>
					</p>
					<ul class="inputList checkboxTextFieldCombo">
						<li>
							{$chkPageTitleOverwrite}
							{$txtPageTitle} {$txtPageTitleError}
						</li>
					</ul>
					<p>
						<label for="navigationTitleOverwrite">{$lblNavigationTitle|ucfirst}</label>
						<span class="helpTxt">{$msgHelpNavigationTitle}</span>
					</p>
					<ul class="inputList checkboxTextFieldCombo">
						<li>
							{$chkNavigationTitleOverwrite}
							{$txtNavigationTitle} {$txtNavigationTitleError}
						</li>
					</ul>
				</div>
			</div>

			<div id="seoNofollow" class="subtleBox">
				<div class="heading">
					<h3>Nofollow</h3>
				</div>
				<div class="options">
					<fieldset>
						<p class="helpTxt">{$msgHelpNoFollow}</p>
						<ul class="inputList">
							<li>
								{$chkNoFollow} <label for="noFollow">{$msgActivateNoFollow|ucfirst}</label>
							</li>
						</ul>
					</fieldset>
				</div>
			</div>

			<div id="seoMeta" class="subtleBox">
				<div class="heading">
					<h3>{$lblMetaInformation|ucfirst}</h3>
				</div>
				<div class="options">
					<p>
						<label for="metaDescriptionOverwrite">{$lblDescription|ucfirst}</label>
						<span class="helpTxt">{$msgHelpMetaDescription}</span>
					</p>
					<ul class="inputList checkboxTextFieldCombo">
						<li>
							{$chkMetaDescriptionOverwrite}
							{$txtMetaDescription} {$txtMetaDescriptionError}
						</li>
					</ul>
					<p>
						<label for="metaKeywordsOverwrite">{$lblKeywords|ucfirst}</label>
						<span class="helpTxt">{$msgHelpMetaKeywords}</span>
					</p>
					<ul class="inputList checkboxTextFieldCombo">
						<li>
							{$chkMetaKeywordsOverwrite}
							{$txtMetaKeywords} {$txtMetaKeywordsError}
						</li>
					</ul>
					<div class="textareaHolder">
						<p>
							<label for="metaCustom">{$lblExtraMetaTags|ucfirst}</label>
							<span class="helpTxt">{$msgHelpMetaCustom}</span>
						</p>
						{$txtMetaCustom} {$txtMetaCustomError}
					</div>
				</div>
			</div>

			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblURL}</h3>
				</div>
				<div class="options">
					<p>
						<label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
						<span class="helpTxt">{$msgHelpMetaURL}</span>
					</p>
					<ul class="inputList checkboxTextFieldCombo">
						<li>
							{$chkUrlOverwrite}
							<span id="urlFirstPart">{$SITE_URL}{$prefixURL}/</span>{$txtUrl} {$txtUrlError}
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div id="tabTemplate">
			<div class="pageTitle">
				{* Do not change the ID! *}
				<h2>{$lblTemplate|ucfirst}: <span id="tabTemplateLabel">&nbsp;</span></h2>
				<div class="buttonHolderRight">
					<a id="changeTemplate" href="#" class="button icon iconEdit">
						<span>{$lblEditTemplate|ucfirst}</span>
					</a>
				</div>
			</div>

			<div id="templateVisualLarge">
				&nbsp;
			</div>

			{*
				Dialog to select the content (editor, module or widget).
				Do not change the ID!
			 *}
			<div id="chooseExtra" title="{$lblChooseContent|ucfirst}" style="display: none;" class="forkForms">
				<input type="hidden" id="extraForBlock" name="extraForBlock" value="" />
				<div class="options horizontal">
					<p>{$msgHelpBlockContent}</p>
					<p id="extraWarningAlreadyBlock">
						<span class="infoMessage">{$msgModuleBlockAlreadyLinked}</span>
					</p>
					<p>
						<label for="extraType">{$lblType|ucfirst}</label>
						{$ddmExtraType}
					</p>
					<p id="extraModuleHolder" style="display: none;">
						<label for="extraModule">{$lblWhichModule|ucfirst}</label>
						<select id="extraModule">
							<option value="-1">-</option>
						</select>
					</p>
					<p id="extraExtraIdHolder" style="display: none;">
						<label for="extraExtraId">{$lblWhichWidget|ucfirst}</label>
						<select id="extraExtraId">
							<option value="-1">-</option>
						</select>
					</p>
				</div>
			</div>

			{*
				Dialog to select another template.
				Do not change the ID!
			 *}
			<div id="chooseTemplate" class="forkForms" title="{$lblChooseATemplate|ucfirst}" style="display: none;">
				<div class="generalMessage singleMessage infoMessage">
					<p>{$msgTemplateChangeWarning}</p>
				</div>
				<div id="templateList">
					<ul>
						{iteration:templates}
					{option:templates.break}
					</ul>
					<ul class="lastChild">
					{/option:templates.break}
							<li{option:templates.disabled} class="disabled"{/option:templates.disabled}>
								<label for="template{$templates.id}"><input type="radio" id="template{$templates.id}" value="{$templates.id}" name="template_id_chooser" class="inputRadio"{option:templates.checked} checked="checked"{/option:templates.checked}{option:templates.disabled} disabled="disabled"{/option:templates.disabled} />{$templates.label}</label>
								<div class="templateVisual current">
									{$templates.html}
								</div>
							</li>
						{/iteration:templates}
					</ul>
				</div>
			</div>
		</div>

		<div id="tabTags">
			<div class="subtleBox">
				<div class="heading">
					<h3>Tags</h3>
				</div>
				<div class="options">
					{$txtTags} {$txtTagsError}
				</div>
			</div>
		</div>

		<div id="tabSettings">
			<ul class="inputList">
				{iteration:hidden}
				<li>
					{$hidden.rbtHidden} <label for="{$hidden.id}">{$hidden.label|ucfirst}</label>
				</li>
				{/iteration:hidden}
			</ul>
			<p>
				<label for="isAction">{$chkIsAction} {$msgIsAction}</label>
			</p>
		</div>
	</div>
	<div class="fullwidthOptions">
		{option:showDelete}
			<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
			<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
				<p>
					{$msgConfirmDelete|sprintf:{$item.title}}
				</p>
			</div>
		{/option:showDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
			<a href="#" id="saveAsDraft" class="inputButton button"><span>{$lblSaveDraft|ucfirst}</span></a>
		</div>
	</div>
{/form:edit}

<script type="text/javascript">
	//<![CDATA[
		// the ID of the page
		var pageID = {$item.id};

		// all the possible templates
		var templates = {};
		{iteration:templates}templates[{$templates.id}] = {$templates.json};{/iteration:templates}

		// the data for the extra's
		var extrasData = {};
		{option:extrasData}extrasData = {$extrasData};{/option:extrasData}

		// the extra's, but in a way we can access them based on their ID
		var extrasById = {};
		{option:extrasById}extrasById = {$extrasById};{/option:extrasById}

		// fix selected state in the tree
		$('#page-'+ pageID).addClass('selected');
	//]]>
</script>

{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_end.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
