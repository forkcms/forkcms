{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_start.tpl}

{form:add}
	{$hidTemplateId}

	<div class="pageTitle">
		<h2>{$lblPages|ucfirst}: {$lblAdd}</h2>
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'index'}" class="button icon iconBack"><span>{$lblOverview|ucfirst}</span></a>
		</div>
	</div>

	<p id="pagesPageTitle">
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
		<span class="oneLiner">
			<span><a href="{$SITE_URL}">{$SITE_URL}{$prefixURL}/<span id="generatedUrl"></span></a></span>
		</span>
	</p>

	<div id="tabs" class="tabs">
		<ul>
			<li style="float: left;"><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li style="float: left;"><a href="#tabRedirect">{$lblRedirect|ucfirst}</a></li>
			<!-- Reverse order after content tab [floatRight] -->
			<li><a href="#tabSettings">{$lblSettings|ucfirst}</a></li>
			<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<div id="editTemplate">
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
			</div>

			{* This is the HTML content, hidden *}
			<div id="editContent">
				{iteration:positions}
					{iteration:positions.blocks}
						<div id="block-{$positions.blocks.index}" class="box contentBlock" style="display: none;">
							<div class="pageTitle" style="margin-bottom: 0;">
								<h2>{$positions.blocks.name}</h2>
							</div>

							<div class="blockContentHTML optionsRTE">
								<fieldset>
									{$positions.blocks.txtHTML}
									{$positions.blocks.txtHTMLError}
								</fieldset>
							</div>

							<p class="linkedExtra" style="display: none;">
								{* this will store the selected extra *}
								{$positions.blocks.hidExtraId}
							</p>
							<p class="linkedPosition" style="display: none;">
								{$positions.blocks.hidPosition}
							</p>
						</div>
					{/iteration:positions.blocks}
				{/iteration:positions}
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
							<span id="urlFirstPart">{$SITE_URL}{$prefixURL}</span>{$txtUrl} {$txtUrlError}
						</li>
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

	{*
		Dialog to select the content (editor, module or widget).
		Do not change the ID!
	 *}
	<div id="addBlock" class="forkForms" title="{$lblChooseContent|ucfirst}" style="display: none;">
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

	{* Buttons to save page *}
	<div id="pageButtons" class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
			<a href="#" id="saveAsDraft" class="inputButton button"><span>{$lblSaveDraft|ucfirst}</span></a>
		</div>
	</div>

	{* Button to indicate writing content is done *}
	<div id="editorButtons" class="fullwidthOptions" style="display: none">
		<div class="buttonHolderRight">
			<a href="#" id="okButton" class="button mainButton"><span>{$lblOK|ucfirst}</span></a>
			<a href="#" id="cancelButton" class="inputButton button"><span>{$lblCancel|ucfirst}</span></a>
		</div>
	</div>
{/form:add}

<script type="text/javascript">
	//<![CDATA[
		// all the possible templates
		var templates = {};
		{iteration:templates}templates[{$templates.id}] = {$templates.json};{/iteration:templates}

		// the data for the extra's
		var extrasData = {};
		{option:extrasData}extrasData = {$extrasData};{/option:extrasData}

		// the extra's, but in a way we can access them based on their ID
		var extrasById = {};
		{option:extrasById}extrasById = {$extrasById};{/option:extrasById}
	//]]>
</script>

{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_end.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
