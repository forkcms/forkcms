{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}

		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblBlog|ucfirst} &gt; {$msgEditWithItem|sprintf:{$blog['title']}}</p>
			</div>

			<div class="inner">
				{form:edit}
					{$txtTitle} {$txtTitleError}
					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$blogUrl}/{$blog['url']}">{$blogUrl}/{$blog['url']}</a></span>
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
							<!-- Content tab -->
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td>
										<table border="0" cellspacing="0" cellpadding="0" id="advancedOptions">
											<tr>
												<td>
													<div class="collapseBox" id="summary">
														<div class="collapseBoxHeading">
															<div class="buttonHolderSingle">
																<a href="#summary .options" class="toggleDiv button icon iconExpanded iconOnly"><span><span><span>Expand</span></span></span></a>
															</div>

															<h4><a href="#summary .options" class="toggleDiv">{$lblSummary|ucfirst}</a></h4>
														</div>

														<div class="options hidden"{option:oShowSummary} style="display: block"{/option:oShowSummary}>
															<p class="helpTxt">{$msgHelpSummary}</p>
															{$txtIntroduction} {$txtIntroductionError}
														</div>
													</div>
												</td>
											</tr>
										</table>
										<br />
										{$txtText} {$txtTextError}
									</td>

									<td id="pagesSide">
										<div id="publishOptions" class="box">
											<div class="heading">
												<h3>{$lblPublish|ucfirst}</h3>
											</div>

											<div class="options">
												<div class="buttonHolder">
													<!-- @todo style this @johan @yoni -->
													<!-- @todo label for save as draft -->

													<a href="{$blogUrl}/{$blog['url']}?draft={$draftId}" class="button icon iconZoom" target="_blank"><span><span><span>{$lblPreview|ucfirst}</span></span></span></a>
													<br /><br />
													<a href="#" id="saveAsDraft" class="button mainButton"><span><span><span>Kladversie opslaan</span></span></span></a>
												</div>
											</div>

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
																	<input id="save" class="inputButton button" type="submit" name="save" value="{$lblSave|ucfirst}" />
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
													<a href="#newCategory" class="toggleDiv button icon iconAdd iconOnly"><span><span><span>{$lblAddCategory|ucfirst}</span></span></span></a>
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
														<a href="#" id="newCategoryButton" class="button icon iconAdd iconOnly"><span><span><span>{$lblAddCategory|ucfirst}</span></span></span></a>
													</div>
												</div>
											</div>
										</div>

										<div id="authors" class="box">
											<div class="heading">
												<h4>{$lblAuthor|ucfirst}</h4>
												{*
													@later
													Johan, this is realy complicated, can't we find a beter way to do this?
												<div class="buttonHolderRight">
													<a href="#" id="editAuthor" class="button icon iconEdit iconOnly">
														<span><span><span>Edit</span></span></span>
													</a>
												</div>
												 *}
											</div>

											<div class="options">
												{$ddmUserId} {$ddmUserIdError}
												{*
												<ul>
													<li>
														<div class="avatarAndNickName">
															<a href="#">
																<img src="images/avatars/fun/mushimushi.png" width="24" height="24" alt="Mushimush2">
																<span>Bram_ (Bram Vanderhaeghe)</span>
															</a>
														</div>
													</li>
												</ul>
												*}
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
										<div class="balloon" style="display: none;">
											<p>{$msgHelpDrafts}</p>
										</div>
									</div>
								</div>

								{$drafts}
							</div>
							{/option:drafts}


							<div class="datagridHolder">
								<div class="tableHeading">
									<div class="oneLiner">
										<h3 class="floater">{$lblRevisions|ucfirst}</h3>
										<abbr class="help floater">(?)</abbr>
										<div class="balloon" style="display: none;">
											<p>{$msgHelpRevisions}</p>
										</div>
									</div>
								</div>

								{option:revisions}{$revisions}{/option:revisions}
								{option:!revisions}{$msgNoRevisions}{/option:!revisions}
							</div>
						</div>

						<div id="tabSEO">
							<div id="seoMeta" class="box boxLevel2">
								<div class="heading">
									<h3>{$lblMetaInformation|ucfirst}</h3>
								</div>
								<div class="options">
									<p>
										<label for="meta_description_overwrite">{$lblMetaDescription|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaDescription}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkMetaDescriptionOverwrite}
											{$txtMetaDescription} {$txtMetaDescriptionError}
										</li>
									</ul>

									<p>
										<label for="meta_keywords_overwrite">{$lblMetaKeywords|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaKeywords}</span>
									</p>

									<ul class="inputList">
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
									<h3>{$lblURL}</h3>
								</div>
								<div class="options">

									<label for="url_overwrite">{$lblCustomURL|ucfirst}</label>
									<span class="helpTxt">{$msgHelpMetaURL}</span>

									<ul class="inputList">
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
						<a href="{$var|geturl:'delete'}&amp;id={$id}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
							<span><span><span>{$lblDelete|ucfirst}</span></span></span>
						</a>
						<div class="buttonHolderRight">
							<input id="edit" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
						</div>
					</div>

					<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
						<p>
							<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
							{$msgConfirmDelete|sprintf:{$blog['title']}}
						</p>
					</div>
				{/form:edit}
			</div>

		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}