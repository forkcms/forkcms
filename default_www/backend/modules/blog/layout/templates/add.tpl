{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Blog &gt; {$msgHeaderAddCategory|ucfirst}</p>
			</div>

			<div class="inner">
				{form:add}
					{$txtTitle} {$txtTitleError}

					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="#">http://www.abconcerts.be/nl/blog/the-state-of-japanoidism</a></span>
							</p>
						</div>
					</div>

					<div class="tabs">
						<ul>
							<li><a href="#tabContent">Content</a></li>
							<li><a href="#tabSEO">SEO</a></li>
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
												<h3>Publish</h3>
											</div>

											<div class="options">
												<div class="buttonHolder">
													<a href="#" class="button icon iconZoom"><span><span><span>Preview</span></span></span></a>
													<a href="#" class="button"><span><span><span>Save</span></span></span></a>
												</div>
											</div>

											<div class="options">
												<p class="status">Status: <strong>draft</strong></p>
											</div>

											<div class="options last">
												<ul class="inputList">
													<li>
														<input type="checkbox" class="inputCheckbox" value="Y" checked="checked" name="article_visibility" id="article_visibility"/>
														<label for="article_visibility">Article is visible</label>
													</li>
												</ul>
											</div>

											<div class="footer">
												<table border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td>
															<p>Last save: 15:43</p>
														</td>
														<td>
															<div class="buttonHolderRight">
																<a href="#" class="button mainButton" id="publish"><span><span><span>Publish</span></span></span></a>
															</div>
														</td>
													</tr>
												</table>
											</div>
										</div>

										<div id="category" class="box">
											<div class="heading">
												<h4>Category</h4>
												<div class="buttonHolderRight">
													<a href="#" class="button icon iconAdd iconOnly"><span><span><span>add</span></span></span></a>
												</div>
											</div>

											<div class="options">
												<select>
													<optgroup label="Existing categories">
														<option>Japan</option>
														<option>Women</option>
														<option>Motorbikes</option>
														<option>Katanas</option>
														<option>Bo</option>
													</optgroup>
													<optgroup label="New">
														<option class="addOption">Add new category</option>
													</optgroup>
												</select>
											</div>

											<div class="options" id="newCategory" style="display: none;">
												<p>
													<label for="newCategory">Category name:</label>
													<input type="text" class="inputText" />
												</p>
											</div>
										</div>

										<div id="authors" class="box">
											<div class="heading">
												<h4>Author</h4>
												<div class="buttonHolderRight">
													<a href="#" id="editAuthor" class="button icon iconEdit iconOnly">
														<span><span><span>Edit</span></span></span>
													</a>
												</div>
											</div>

											<div class="options">
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
											</div>
										</div>

										<div id="tags" class="box">
											<div class="heading">
												<h4>Tags</h4>
											</div>

											<div class="options">
												<div class="oneLiner">
													<p><input type="text" class="inputText" id="addTag" /></p>
													<div class="buttonHolder">
														<a href="#" class="button icon iconAdd"><span><span><span>Add</span></span></span></a>
													</div>
												</div>

												<ul id="tagsList">
													<li><span><strong>Music</strong> <a href="#deleteTag" title="Delete tag">X</a></span></li>
													<li><span><strong>Concerts</strong> <a href="#deleteTag" title="Delete tag">X</a></span></li>
												</ul>
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