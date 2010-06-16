{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				<div class="pageTitle">
					<h2>{$lblBlog|ucfirst}: {$msgEditWithItem|sprintf:{$blog['title']}}</h2>
				</div>

				{form:edit}
					{$txtTitle} {$txtTitleError}

					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$blogUrl}/{$blog['url']}">{$blogUrl}/<span id="generatedUrl">{$blog['url']}</span></a></span>
							</p>
						</div>
					</div>

					<div class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
							<li><a href="#tabRevisions">{$lblRevisions|ucfirst}</a></li>
							<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
							<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td id="leftColumn">
										<div class="box">
											<div class="heading headingRTE">
												<h3>{$lblMainContent|ucfirst}</h3>
											</div>
											<div class="optionsRTE">
												{$txtText} {$txtTextError}
											</div>
										</div>
										<br />
										<table border="0" cellspacing="0" cellpadding="0" id="advancedOptions">
											<tr>
												<td>
													<div class="collapseBox" id="summary">
														<div class="collapseBoxHeading">
															<div class="buttonHolderSingle">
																<a href="#summary" class="toggleDiv button icon iconExpanded iconOnly"><span>Expand</span></a>
															</div>
															<h4><a href="#summary" class="toggleDiv">{$lblSummary|ucfirst}</a></h4>
														</div>
														<div class="options hidden" style="display: none;">
															<p class="helpTxt">{$msgHelpSummary}</p>
															{$txtIntroduction} {$txtIntroductionError}
														</div>
													</div>
												</td>
											</tr>
										</table>
									</td>

									<td id="sidebar">
										<div id="publishOptions" class="box">
											<div class="heading">
												<h3>{$lblStatus|ucfirst}</h3>
											</div>

											{option:usingDraft}
											<div class="options">
												<div class="buttonHolder">
													<a href="{$blogUrl}/{$blog['url']}?draft={$draftId}" class="button icon iconZoom" target="_blank"><span>{$lblPreview|ucfirst}</span></a>
												</div>
											</div>
											{/option:usingDraft}

											<div class="options">
												<p class="status">{$lblStatus|ucfirst}: <strong>{$status}</strong></p>
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

											<div class="options">
												<dl>
													<dt><label for="publishOnDate">{$lblPublishOn|ucfirst}:</label></dt>
													<dd>{$txtPublishOnDate} <label for="publishOnTime">{$lblAt}</label> {$txtPublishOnTime}</dd>
												</dl>
											</div>

											<div class="footer">
												<table border="0" cellpadding="0" cellspacing="0">
													<tbody>
														<tr>
															<td><p>&nbsp;</p></td>
															<td>
																<div class="buttonHolderRight">
																	<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>

										<div id="category" class="box">
											<div class="heading">
												<h4>{$lblCategory|ucfirst}</h4>
												<div class="buttonHolderRight">
													<a href="#newCategory" class="toggleDiv button icon iconAdd iconOnly"><span>{$lblAddCategory|ucfirst}</span></a>
												</div>
											</div>
											<div class="options">
												{$ddmCategoryId} {$ddmCategoryIdError}
											</div>
											<div id="newCategory" class="options hidden">
												<div class="oneLiner">
													<p>
														<input id="newCategoryValue" class="inputTextfield dontSubmit" type="text" name="new_category" />
														<span id="newCategoryError" class="formError">{$errAddingCategoryFailed}</span>
													</p>
													<div class="buttonHolder">
														<a href="#" id="newCategoryButton" class="button icon iconAdd iconOnly"><span>{$lblAddCategory|ucfirst}</span></a>
													</div>
												</div>
											</div>
										</div>

										<div id="authors" class="box">
											<div class="heading">
												<h4>{$lblAuthor|ucfirst}</h4>
											</div>
											<div class="options">
												{$ddmUserId} {$ddmUserIdError}
											</div>
										</div>

										<div id="tagBox" class="box">
											<div class="heading">
												<h4>{$lblTags|ucfirst}</h4>
											</div>

											<div class="options">
												{$txtTags} {$txtTagsError}
											</div>
										</div>
									</td>
								</tr>
							</table>
						</div>

						<div id="tabPermissions">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td>
										{$chkAllowComments} <label for="allowComments">{$lblAllowComments|ucfirst}</label>
									</td>
								</tr>
							</table>
						</div>

						<div id="tabRevisions">
							{option:drafts}
							<div class="datagridHolder">
								<div class="tableHeading">
									<div class="oneLiner">
										<h3 class="floater">{$lblDrafts|ucfirst}</h3>
										<abbr class="help floater">(?)</abbr>
										<div class="tooltip" style="display: none;">
											<p>{$msgHelpDrafts}</p>
										</div>
									</div>
								</div>

								{$drafts}
							</div>
							{/option:drafts}

							{option:!drafts}
							<div class="datagridHolder">
								<div class="tableHeading">
									<div class="oneLiner">
										<h3 class="floater">{$lblRevisions|ucfirst}</h3>
										<abbr class="help floater">(?)</abbr>
										<div class="tooltip" style="display: none;">
											<p>{$msgHelpRevisions}</p>
										</div>
									</div>
								</div>

								{option:revisions}{$revisions}{/option:revisions}
								{option:!revisions}{$msgNoRevisions}{/option:!revisions}
							</div>
							{/option:!drafts}
						</div>

						<div id="tabSEO">
							<div id="seoMeta" class="subtleBox">
								<div class="heading">
									<h3>{$lblMetaInformation|ucfirst}</h3>
								</div>
								<div class="options">
									<p>
										<label for="metaDescriptionOverwrite">{$lblMetaDescription|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaDescription}</span>
									</p>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkMetaDescriptionOverwrite}
											{$txtMetaDescription} {$txtMetaDescriptionError}
										</li>
									</ul>
									<p>
										<label for="metaKeywordsOverwrite">{$lblMetaKeywords|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaKeywords}</span>
									</p>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkMetaKeywordsOverwrite}
											{$txtMetaKeywords} {$txtMetaKeywordsError}
										</li>
									</ul>
									<div class="textareaHolder">
										<label for="metaCustom">{$lblMetaCustom|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaCustom}</span>
										{$txtMetaCustom} {$txtMetaCustomError}
									</div>
								</div>
							</div>

							<div class="subtleBox">
								<div class="heading">
									<h3>{$lblURL|uppercase}</h3>
								</div>
								<div class="options">
									<label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
									<span class="helpTxt">{$msgHelpMetaURL}</span>
									<ul class="inputList checkboxTextFieldCombo">
										<li>
											{$chkUrlOverwrite}
											<span id="urlFirstPart">{$blogUrl}/</span>{$txtUrl} {$txtUrlError}
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="fullwidthOptions">
						<a href="{$var|geturl:'delete'}&amp;id={$blog['id']}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
							<span>{$lblDelete|ucfirst}</span>
						</a>
						<div class="buttonHolderRight">
							<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
						</div>
					</div>

					<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
						<p>
							{$msgConfirmDelete|sprintf:{$blog['title']}}
						</p>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}