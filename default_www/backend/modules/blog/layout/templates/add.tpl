{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblBlog|ucfirst} &gt; {$lblAdd|ucfirst}</p>
			</div>

			{option:formError}
			<div id="report">
				<div class="singleMessage errorMessage">
					<p>{$errFormError}</p>
				</div>
			</div>
			{/option:formError}

			<div class="inner">
				{form:add}
					{$txtTitle} {$txtTitleError}

					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$blogUrl}">{$blogUrl}</a></span>
							</p>
						</div>
					</div>

					<div class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
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

														<div class="options hidden" style="display: none;">
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

											<!-- @later
											<div class="options">
												<div class="buttonHolder">
													<a href="#" class="button icon iconZoom"><span><span><span>Preview</span></span></span></a>
													<a href="#" class="button"><span><span><span>Save</span></span></span></a>
												</div>
											</div>

											<div class="options">
												<p class="status">Status: <strong>draft</strong></p>
											</div>
											-->

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
																	{$btnAdd}
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
														<input id="newCategoryValue" class="inputTextfield" type="text" name="new_category" />
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
						<div class="buttonHolderRight">
							{$btnAdd}
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}