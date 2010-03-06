{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" width="264">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td id="treeHolder">
						<div id="treeOptions">
							<div class="buttonHolder">
								<a href="{$var|geturl:'index'}" class="button icon iconBack iconOnly"><span><span><span>{$lblBack|ucfirst}</span></span></span></a>
								<a href="{$var|geturl:'add'}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
							</div>
						</div>
						<div id="tree">
							{$tree}
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td id="fullwidthSwitch"><a href="#close">&nbsp;</a></td>
		<td id="contentHolder">
				<div class="inner">
				{form:edit}
					{$txtTitle} {$txtTitleError}
					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$SITE_URL}{$pageUrl}">{$SITE_URL}{$pageUrl}</a></span>
							</p>
						</div>
					</div>
					<div id="tabs" class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
							<li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
							<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
							<li><a href="#tabTemplate">{$lblTemplate|ucfirst}</a></li>
							<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td>
											<div id="editContent">
												{iteration:blocks}
												<div id="block-{$blocks.index}" class="contentBlock">
													<div class="contentTitle selected hover">
														<table border="0" cellpadding="0" cellspacing="0">
															<tbody><tr>
																<td class="numbering">{$blocks.index}</td>
																<td>
																	<div class="oneLiner">
																		<p><span class="blockName">{$blocks.name}</span></p>
																		<p>{$blocks.ddmExtraId}</p>
																	</div>
																</td>
															</tr>
														</tbody></table>
													</div>
													<div class="editContent">
														<fieldset id="blockContentHTML-{$blocks.index}">
															{$blocks.txtHTML}
														</fieldset>
														<fieldset id="blockContentExtra-{$blocks.index}">
															<p>&nbsp;</p>
														</fieldset>
													</div>
												</div>
												{/iteration:blocks}
											</div>
										</td>
										<td id="sidebar">
											<div id="publishOptions" class="box">
												<div class="heading">
													<h3>{$lblPublish|ucfirst}</h3>
												</div>
												<div class="options">
													<div class="buttonHolder">
														<a href="{$SITE_URL}{$pageUrl}" class="button icon iconZoom previewButton" target="_blank">
															<span><span><span>{$lblView|ucfirst}</span></span></span>
														</a>
													</div>
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
												<div class="footer">
													<table border="0" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td><p>{$lblLastSave|ucfirst}: {$recordedited_on|date:'H:i'}</p></td>
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
													<h4>{$lblTemplate|ucfirst}: {$templatelabel}</h4>
													<div class="buttonHolderRight">
														<a href="#tabTemplate" class="tabSelect button icon iconEdit iconOnly">
															<span><span><span>{$lblEdit|ucfirst}</span></span></span>
														</a>
													</div>
												</div>
												<div class="options">
													<!-- [A,B],[C,D,0],[E,E,0] -->
													<div id="templateVisual" class="templateVisual current">
														{$templatehtml}
													</div>

													<table id="templateDetails" class="infoGrid" border="0" cellpadding="0" cellspacing="0">
														<tbody>
														{iteration:blocks}
															<tr>
																<th class="numbering">{$blocks.index}</th>
																<td class="blockName">{$blocks.name}</td>
															</tr>
														{/iteration:blocks}
													</tbody>
												</table>
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
										<h3 class="floater">{$lblRevisions|ucfirst}</h3>
										<abbr class="help floater">(?)</abbr>
										<div class="balloon balloonAlt" style="display: none;">
											<p>{$msgHelpRevisions}</p>
										</div>
									</div>
								</div>
								{option:revisions}{$revisions}{/option:revisions}
								{option:!revisions}
								<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
									<tr>
										<td>
											{$msgNoRevisions}
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
										<label for="meta_pagetitle_overwrite">{$lblPageTitle|ucfirst}</label>
										<span class="helpTxt">{$msgHelpPageTitle}</span>
									</p>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkPageTitleOverwrite}
											{$txtPageTitle} {$txtPageTitleError}
										</li>
									</ul>
									<p>
										<label for="navigation_title_overwrite">{$lblNavigationTitle|ucfirst}</label>
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
												{$chkNoFollow}
												<label for="noFollow">{$msgActivateNoFollow|ucfirst}</label>
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
										<label for="meta_description_overwrite">{$lblMetaDescription|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaDescription}</span>
									</p>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkMetaDescriptionOverwrite}
											{$txtMetaDescription} {$txtMetaDescriptionError}
										</li>
									</ul>
									<p>
										<label for="meta_keywords_overwrite">{$lblMetaKeywords|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaKeywords}</span>
									</p>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkMetaKeywordsOverwrite}
											{$txtMetaKeywords} {$txtMetaKeywordsError}
										</li>
									</ul>
									<p>
										<label for="meta_custom">{$lblMetaCustom|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaCustom}</span>
										{$txtMetaCustom} {$txtMetaCustomError}
									</p>
								</div>
							</div>

							<div class="box boxLevel2">
								<div class="heading">
									<h3>{$lblURL|uppercase}</h3>
								</div>
								<div class="options">
									<label for="url_overwrite">{$lblCustomURL|ucfirst}</label>
									<span class="helpTxt">{$msgHelpMetaURL}</span>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkUrlOverwrite}
											<span id="urlFirstPart">{$SITE_URL}{$seoPageUrl}</span>{$txtUrl} {$txtUrlError}
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div id="tabTemplate">
							<ul class="inputList" id="templateList">
							{iteration:templates}
								<li>
									<input type="radio" id="template{$templates.id}" value="{$templates.id}" name="template_id" class="inputRadio"{option:templates.checked} checked="checked"{/option:templates.checked} />
									<label for="template{$templates.id}">{$templates.label}</label>
									<div class="templateVisual current">
										{$templates.html}
									</div>
								</li>
							{/iteration:templates}
							</ul>
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
						<a href="{$var|geturl:'delete'}&id={$recordid}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
							<span><span><span>{$lblDelete|ucfirst}</span></span></span>
						</a>
						<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
							<p>
								<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
								{$msgConfirmDelete|sprintf:{$recordtitle}}
							</p>
						</div>
						{/option:showDelete}
						<div class="buttonHolderRight">
							<input id="edit" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
						</div>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var pageID = {$recordid};
	var templates = {};
	{iteration:templates}templates[{$templates.id}] = {$templates.json};{/iteration:templates}

	var extraData = {};
	{iteration:extras}extraData[{$extras.id}] = {$extras.json}; {/iteration:extras}

	$('#page-'+ pageID).addClass('selected');
</script>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}