{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_start.tpl'}

{form:edit}
	{$hidTemplateId}

	<div class="pageTitle">
		<h2>{$lblPages|ucfirst}: {$lblEdit}</h2>
	</div>

	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabVersions">{$lblPreviousVersions|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
			<li><a href="#tabTemplate">{$lblTemplate|ucfirst}</a></li>
			<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
		</ul>

		<div id="tabContent">

			<p id="pagesPageTitle">
				<label for="title">{$lblTitle|ucfirst}</label>
				{$txtTitle} {$txtTitleError}
				<span class="oneLiner">
					<span><a href="{$SITE_URL}{$itemURL}">{$SITE_URL}{$URL}<span id="generatedUrl">{$itemURL}</span></a></span>
				</span>
			</p>

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td>
							<div id="editContent">
								{iteration:blocks}
									<div id="block-{$blocks.index}" class="box contentBlock">
										<div class="heading">
											<table border="0" cellpadding="0" cellspacing="0">
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
										<div class="optionsRTE">
											<fieldset id="blockContentHTML-{$blocks.index}">
												{$blocks.txtHTML}
											</fieldset>
										</div>
									</div>
								{/iteration:blocks}
							</div>
						</td>
						<td id="sidebar">
							<div id="publishOptions" class="box">
								<div class="heading">
									<h3>{$lblStatus|ucfirst}</h3>
								</div>
								<!-- @later
								<div class="options">
									<div class="buttonHolder">
										<a href="#" class="button icon iconZoom previewButton" target="_blank">
											<span>{$lblPreview|ucfirst}</span>
										</a>
									</div>
								</div>
								 -->
								<div class="options">
									<div class="buttonHolder">
										<a href="{$SITE_URL}{$itemURL}" class="button icon iconZoom previewButton" target="_blank">
											<span>{$lblView|ucfirst}</span>
										</a>
									</div>
								</div>
								 <div class="options">
										<ul class="inputList">
											{iteration:hidden}
											<li>
												{$hidden.rbtHidden} <label for="{$hidden.id}">{$hidden.label}</label>
											</li>
											{/iteration:hidden}
										</ul>
									</div>
								<div class="footer">
									<table border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td><p>{$lblLastSaved|ucfirst}: <abbr title="{$recordedited_on|date:'j F Y H:i'}">{$recordedited_on|date:'H:i'}</abbr></p></td>
												<td>
													<div class="buttonHolderRight">
														<input id="save" class="inputButton button" type="submit" name="save" value="{$lblSave|ucfirst}" />
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="box" id="template">
								<div class="heading">
									{* Do not change the ID! *}
									<h4>{$lblTemplate|ucfirst}: <span id="templateLabel">&nbsp;</span></h4>
									<div class="buttonHolderRight">
										<a href="#tabTemplate" class="tabSelect button icon iconEdit iconOnly">
											<span>{$lblEdit|ucfirst}</span>
										</a>
									</div>
								</div>
								<div class="options">
									<div id="templateVisual" class="templateVisual current">
										&nbsp;
									</div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="tabVersions">
			<div class="datagridHolder">
				<div class="tableHeading">
					<div class="oneLiner">
						<h3 class="floater">{$lblPreviousVersions|ucfirst}</h3>
						<abbr class="help">(?)</abbr>
						<div class="tooltip" style="display: none;">
							<p>{$msgHelpRevisions}</p>
						</div>
					</div>
				</div>
				{option:revisions}{$revisions}{/option:revisions}
				{option:!revisions}
				<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
					<tr>
						<td>
							<p>{$msgNoRevisions}</p>
						</td>
					</tr>
				</table>
				{/option:!revisions}
			</div>
		</div>
		<div id="tabSEO">
			<div class="box boxLevel2">
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

			<div id="seoNofollow" class="box boxLevel2">
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

			<div id="seoMeta" class="box boxLevel2">
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
					<p>
						<label for="metaCustom">{$lblExtraMetaTags|ucfirst}</label>
						<span class="helpTxt">{$msgHelpMetaCustom}</span>
						{$txtMetaCustom} {$txtMetaCustomError}
					</p>
				</div>
			</div>

			<div class="box boxLevel2">
				<div class="heading">
					<h3>{$lblURL}</h3>
				</div>
				<div class="options">
					<label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
					<span class="helpTxt">{$msgHelpMetaURL}</span>

					<ul class="inputList checkboxTextFieldCombo">
						<li>
							{$chkUrlOverwrite}
							<span id="urlFirstPart">{$SITE_URL}{$URL}</span>{$txtUrl} {$txtUrlError}
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
			<div id="chooseExtra" title="{$lblChooseContent|ucfirst}" style="display: none;">
				<input type="hidden" id="extraForBlock" name="extraForBlock" value="" />
				<div class="options">
					<p>{$msgHelpBlockContent}</p>
					<p id="extraWarningAlreadyBlock" class="warning">
						{$msgModuleBlockAlreadyLinked}
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
			<div id="chooseTemplate" title="{$lblChooseATemplate|ucfirst}" style="display: none;">
				<ul class="inputList" id="templateList">
				{iteration:templates}
					<li style="float: left; width: 155px;">
						<input type="radio" id="template{$templates.id}" value="{$templates.id}" name="template_id_chooser" class="inputRadio"{option:templates.checked} checked="checked"{/option:templates.checked} />
						<label for="template{$templates.id}">{$templates.label}</label>
						<div class="templateVisual current">
							{$templates.html}
						</div>
					</li>
				{/iteration:templates}
				</ul>
			</div>

		</div>
		<div id="tabTags">
			<div class="box boxLevel2">
				<div class="heading">
					<h3>Tags</h3>
				</div>
				<div class="options">
					{$txtTags} {$txtTagsError}
				</div>
			</div>
		</div>
	</div>
	<div class="fullwidthOptions">
		{option:showDelete}
			<a href="{$var|geturl:'delete'}&amp;id={$recordid}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
			<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
				<p>
					{$msgConfirmDelete|sprintf:{$recordtitle}}
				</p>
			</div>
		{/option:showDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

<script type="text/javascript">
	//<![CDATA[
		// the ID of the page
		var pageID = {$recordid};

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

{include:file='{$BACKEND_MODULES_PATH}/pages/layout/templates/structure_end.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}