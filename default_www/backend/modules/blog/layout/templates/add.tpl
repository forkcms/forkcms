{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Blog &gt; {$msgHeaderAdd|ucfirst}</p>
			</div>

			<div class="inner">
				{form:add}
					{$txtTitle} {$txtTitleError}

					<!-- @todo does this have to be here? Answer: yes -->
					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$SITE_URL}">{$SITE_URL}</a></span>
							</p>
						</div>
					</div>
					
					<div class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
							<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							<!-- Content tab -->
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td>
										{$txtText} {$txtTextError}
										<br />
										<table border="0" cellspacing="0" cellpadding="0" id="advancedOptions">
											<tr>
												<td>
													<div class="collapseBox" id="summary">
														<div class="collapseBoxHeading">
															<div class="buttonHolderSingle">
																<a href="#" class="button icon iconExpanded iconOnly"><span><span><span>Expand</span></span></span></a>
															</div>

															<h4><a href="#">Summary</a></h4>
														</div>

														<div class="options">
															<p class="helpTxt">If you enter a summary, the summary will be shown on "list" pages (e.g. the blog overview). If you don't, the full blog post will be shown in the overview.</p>
															{$txtIntroduction} {$txtIntroductionError}
														</div>
													</div>
												</td>
											</tr>
										</table>
									</td>

									<td id="pagesSide">
										<div id="publishOptions" class="box">
											<div class="heading">
												<h3>{$lblPublish|ucfirst}</h3>
											</div>

											<!-- @todo
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
												<!--  @todo
												<div class="buttonHolderRight">
													<a href="#" class="button icon iconAdd iconOnly"><span><span><span>add</span></span></span></a>
												</div>
												 -->
											</div>

											<div class="options">
												{$ddmCategoryId} {$ddmCategoryIdError}
											</div>
										</div>

										<div id="authors" class="box">
											<div class="heading">
												<h4>Author</h4>
												<!--  @todo
												<div class="buttonHolderRight">
													<a href="#" id="editAuthor" class="button icon iconEdit iconOnly">
														<span><span><span>Edit</span></span></span>
													</a>
												</div>
												 -->
											</div>
											
											<div class="options">
												{$ddmUserId} {$ddmUserIdError}
												
												<!--  @todo ?
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
												 -->
											</div>
										</div>

										<div id="tags" class="box">
											<div class="heading">
												<h4>Tags</h4>
											</div>

											<div class="options">
												{$txtTags} {$txtTagsError}
											</div>
										</div>
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
											<span id="urlFirstPart">{$SITE_URL}</span>{$txtUrl} {$txtUrlError}
										</li>
									</ul>

								</div>
							</div>
						</div>
					</div>

					<div class="fullwidthOptions">
						<a href="#" class="button linkButton icon iconDelete"><span><span><span>Delete</span></span></span></a>
						<div class="buttonHolderRight">
							<a href="#" class="button mainButton"><span><span><span>Publish</span></span></span></a>
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}