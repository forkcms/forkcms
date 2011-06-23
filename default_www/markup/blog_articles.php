<?php require_once 'head.php'; ?>

<body id="blog" class="blogIndex blogAddEdit">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">Blog articles markup</a></h1>
					<table cellspacing="0" cellpadding="0" id="header">
						<tr>
							<td id="navigation">
								<ul>
									<li><a href="#">Dashboard</a></li>
									<li><a href="#">Pages</a></li>
									<li class="selected"><a href="#">Modules</a></li>
									<li><a href="#">Marketing</a></li>
									<li><a href="#">Mailmotor</a></li>
									<li><a href="#">Settings</a></li>
								</ul>
							</td>
							<td id="user">
								<ul>
									<li>
										<div id="debugnotify">Debug mode</div>
									</li>
									<li>
										now editing:
										<select id="workingLanguage">
											<option value="nl">Dutch</option>
											<option selected="selected" value="en">English</option>
											<option value="fr">French</option>
										</select>
									</li>
									<li id="account">
										<a href="#ddAccount" id="openAccountDropdown" class="fakeDropdown">
											<span class="avatar av24 block">
												<img src="../frontend/files/backend_users/avatars/32x32/god.jpg" width="24" height="24" alt="Fork CMS" />
											</span>
											<span class="nickname">Fork CMS</span>
											<span class="arrow">&#x25BC;</span>
										</a>
										<ul class="hidden" id="ddAccount">
											<li><a href="#">Edit profile</a></li>
											<li><a class="targetBlank" href="../docs">Developer</a></li>
											<li class="lastChild"><a href="#">Sign out</a></li>
										</ul>
									</li>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr> <!-- End of header -->

		<tr>
			<td id="container">
				<div id="main">
					<table border="0" cellspacing="0" cellpadding="0" id="mainHolder">
						<tr>
							<td id="subnavigation">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td id="moduleHolder">
											<ul>
												<li><a href="content_blocks.php">Content blocks</a></li>
												<li><a href="tags.php">Tags</a></li>
												<li class="selected">
													<a href="#">Blog</a>
													<ul>
														<li class="selected"><a href="#">Articles</a></li>
														<li><a href="blog_comments.php">Comments</a></li>
														<li><a href="blog_categories.php">Categories</a></li>
													</ul>
												</li>
												<li><a href="#">Search</a></li>
												<li><a href="location.php">Location</a></li>
												<li><a href="faq_questions.php">FAQ</a></li>
												<li><a href="#">Formbuilder</a></li>
											</ul>
										</td>
									</tr>
								</table>
							</td> <!-- End of subnavigation -->

							<td id="fullwidthSwitch">
								<a href="#close">&nbsp;</a>
							</td>

							<td id="contentHolder">
								<div class="inner">
									<div class="pageTitle">
										<h2>Blog: articles</h2>
										<div class="buttonHolderRight">
											<a href="/private/en/blog/add?token=true" class="button icon iconAdd" title="Add article">
												<span>Add article</span>
											</a>
										</div>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div class="dataGridHolder">
										<div class="tableHeading">
											<h3>Published articles</h3>
										</div>

										<table class="dataGrid" cellspacing="0" cellpadding="0" border="0">
											<thead>
												<tr>
													<th class="title">
														<a href="/private/en/blog/index?offset=0&amp;order=title&amp;sort=asc" title="sort ascending" class="sortable">
															Title
														</a>
													</th>
													<th class="publish_on">
														<a href="/private/en/blog/index?offset=0&amp;order=publish_on&amp;sort=asc" title="sorted descending" class="sortable sorted sortedDesc">
															Published on
														</a>
													</th>
													<th class="user_id">
														<a href="/private/en/blog/index?offset=0&amp;order=user_id&amp;sort=asc" title="sort ascending" class="sortable">
															Author
														</a>
													</th>
													<th class="comments">
														<a href="/private/en/blog/index?offset=0&amp;order=comments&amp;sort=asc" title="sort ascending" class="sortable">
															Comments
														</a>
													</th>
													<th class="edit">
														<span>&#160;</span>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr id="row-7" class="odd">
													<td class="title">
														<a href="/private/en/blog/edit?token=true&amp;id=1" title="">Nunc sediam est</a>
													</td>
													<td class="publish_on">
														16 March 2011 14:11
													</td>
													<td class="user_id">
														<div class="dataGridAvatar">
															<div class="avatar av24">
																<a href="/private/en/users/edit?token=true&amp;id=1">
																	<img src="/frontend/files/backend_users/avatars/32x32/god.jpg" width="24" height="24" alt="Fork CMS" />
																</a>
															</div>
															<p>
																<a href="/private/en/users/edit?token=true&amp;id=1">Fork CMS</a>
															</p>
														</div>
													</td>
													<td class="comments">
														3
													</td>
													<td class="action actionEdit">
														<a href="/private/en/blog/edit?token=true&amp;id=1" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
												<tr id="row-2" class="even">
													<td class="title">
														<a href="/private/en/blog/edit?token=true&amp;id=2" title="">Lorem ipsum</a>
													</td>
													<td class="publish_on">
														16 March 2011 14:10
													</td>
													<td class="user_id">
														<div class="dataGridAvatar">
															<div class="avatar av24">
																<a href="/private/en/users/edit?token=true&amp;id=1">
																	<img src="/frontend/files/backend_users/avatars/32x32/god.jpg" width="24" height="24" alt="Fork CMS" />
																</a>
															</div>
															<p>
																<a href="/private/en/users/edit?token=true&amp;id=1">Fork CMS</a>
															</p>
														</div>
													</td>
													<td class="comments">
														0
													</td>
													<td class="action actionEdit">
														<a href="/private/en/blog/edit?token=true&amp;id=2" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div> <!-- End of browse dataGrid -->

									<div class="pageTitle">
										<h2>Blog: add article</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/blog/add?token=true" method="post" id="add" class="forkForms submitWithLink">
										<input type="hidden" value="add" id="formAdd" name="form" />
										<input type="hidden" name="form_token" id="formTokenAdd" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<input value="" id="title" name="title" maxlength="255" type="text" class="inputText" />

										<div id="pageUrl">
											<div class="oneLiner">
												<p>
													<span>
														<a href="http://forkcms.local/en/blog/detail">
															http://forkcms.local/en/blog/detail/
															<span id="generatedUrl"></span>
														</a>
													</span>
												</p>
											</div>
										</div>

										<div class="tabs">
											<ul>
												<li><a href="#tabContent">Content</a></li>
												<li><a href="#tabPermissions">Comments</a></li>
												<li><a href="#tabSEO">SEO</a></li>
											</ul>

											<div id="tabContent">
												<table border="0" cellspacing="0" cellpadding="0" width="100%">
													<tr>
														<td id="leftColumn">
															<div class="box">
																<div class="heading">
																	<h3>Main content<abbr title="required field">*</abbr></h3>
																</div>
																<div class="optionsRTE">
																	<textarea id="text" name="text" cols="62" rows="5" class="inputEditor "></textarea>
																</div>
															</div>

															<div class="box">
																<div class="heading">
																	<div class="oneLiner">
																		<h3>Summary</h3>
																		<abbr class="help">(?)</abbr>
																		<div class="tooltip" style="display: none;">
																			<p>Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.</p>
																		</div>
																	</div>
																</div>

																<div class="optionsRTE">
																	<textarea id="introduction" name="introduction" cols="62" rows="5" class="inputEditor "></textarea>
																</div>
															</div>
														</td>

														<td id="sidebar">
															<div id="publishOptions" class="box">
																<div class="heading">
																	<h3>Status</h3>
																</div>

																<div class="options">
																	<ul class="inputList">
																		<li>
																			<input type="radio" name="hidden" value="Y" class="inputRadio" id="hiddenY" />
																			<label for="hiddenY">hidden</label>
																		</li>
																		<li>
																			<input type="radio" name="hidden" value="N" checked="checked" class="inputRadio" id="hiddenN" />
																			<label for="hiddenN">published</label>
																		</li>
																	</ul>
																</div>

																<div class="options">
																	<p class="p0">
																		<label for="publishOnDate">Publish on</label>
																	</p>

																	<div class="oneLiner">
																		<p>
																			<input type="text" value="17/03/2011" id="publishOnDate" name="publish_on_date" maxlength="10" class="inputText inputDate inputDatefieldNormal inputText" data-mask="dd/mm/yy" data-firstday="1" />
																		</p>
																		<p>
																			<label for="publishOnTime">at</label>
																		</p>
																		<p>
																			<input type="text" value="09:14" id="publishOnTime" name="publish_on_time" maxlength="5" class="inputText inputTime" />
																		</p>
																	</div>
																</div>
															</div>

															<div class="box" id="articleMeta">
																<div class="heading">
																	<h3>Metadata</h3>
																</div>
																<div class="options">
																	<label for="categoryId">Category</label>
																	<select id="categoryId" name="category_id" class="select" size="1">
																		<option value="1" selected="selected">Default</option>
																	</select>
									 							</div>
																<div class="options">
																	<label for="userId">Author</label>
																	<select id="userId" name="user_id" class="select" size="1">
																		<option value="1" selected="selected">Fork CMS</option>
																	</select>
									 							</div>
																<div class="options">
																	<label for="tags">Tags</label>
																	<input value="" id="tags" name="tags" type="text" class="inputText tagBox" />
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
															<input type="checkbox" value="Y" id="allowComments" name="allow_comments" class="inputCheckbox" checked="checked" />
															<label for="allowComments">Allow comments</label>
														</td>
													</tr>
												</table>
											</div>

											<div id="tabSEO">
												<div id="seoMeta" class="subtleBox">
													<div class="heading">
														<h3>Meta information</h3>
													</div>
													<div class="options">
														<p>
															<label for="pageTitleOverwrite">Pagetitle</label>
															<span class="helpTxt">The title in the browser window (<code>&lt;title&gt;</code>).</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="pageTitleOverwrite" name="page_title_overwrite" class="inputCheckbox" />
																<input value="" id="pageTitle" name="page_title" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<p>
															<label for="metaDescriptionOverwrite">Description</label>
															<span class="helpTxt">Briefly summarize the content. This summary is shown in the results of search engines.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="metaDescriptionOverwrite" name="meta_description_overwrite" class="inputCheckbox" />
																<input value="" id="metaDescription" name="meta_description" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<p>
															<label for="metaKeywordsOverwrite">Keywords</label>
															<span class="helpTxt">Choose a number of wellthought terms that describe the content.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="metaKeywordsOverwrite" name="meta_keywords_overwrite" class="inputCheckbox" />
																<input value="" id="metaKeywords" name="meta_keywords" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>

														<div class="textareaHolder">
															<p>
																<label for="metaCustom">Extra metatags</label>
																<span class="helpTxt">These custom metatags will be placed in the <code>&lt;head&gt;</code> section of the page.</span>
															</p>
															<textarea id="metaCustom" name="meta_custom" cols="62" rows="5" class="textarea"></textarea>
														</div>
													</div>
												</div>

												<div class="subtleBox">
													<div class="heading">
														<h3>URL</h3>
													</div>
													<div class="options">
														<p>
															<label for="urlOverwrite">Custom URL</label>
															<span class="helpTxt">Replace the automaticly generated URL by a custom one.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="urlOverwrite" name="url_overwrite" class="inputCheckbox" />
																<span id="urlFirstPart">http://forkcms.local/en/blog/detail/</span>
																<input value="" id="url" name="url" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="Publish" />
												<a href="#" id="saveAsDraft" class="inputButton button"><span>Save draft</span></a>
											</div>
										</div>
									</form> <!-- End of add form -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<div class="pageTitle">
										<h2>Blog: edit article "Nunc sediam est"</h2>
										<div class="buttonHolderRight">
											<a href="http://forkcms.local/en/blog/detail/nunc-sediam-est?revision=7" class="button icon iconZoom previewButton targetBlank">
												<span>View</span>
											</a>
										</div>
									</div>

									<form accept-charset="UTF-8" action="/private/en/blog/edit?token=true&amp;id=1" method="post" id="edit" class="forkForms submitWithLink">
										<input type="hidden" value="edit" id="formEdit" name="form" />
										<input type="hidden" name="form_token" id="formTokenEdit" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<input value="Nunc sediam est" id="title" name="title" maxlength="255" type="text" class="inputText" />

										<div id="pageUrl">
											<div class="oneLiner">
												<p>
													<span>
														<a href="http://forkcms.local/en/blog/detail/nunc-sediam-est">
															http://forkcms.local/en/blog/detail/
															<span id="generatedUrl">nunc-sediam-est</span>
														</a>
													</span>
												</p>
											</div>
										</div>

										<div class="tabs">
											<ul>
												<li><a href="#tabContent">Content</a></li>
												<li><a href="#tabRevisions">Previous versions</a></li>
												<li><a href="#tabPermissions">Comments</a></li>
												<li><a href="#tabSEO">SEO</a></li>
											</ul>

											<div id="tabContent">
												<table border="0" cellspacing="0" cellpadding="0" width="100%">
													<tr>
														<td id="leftColumn">
															<div class="box">
																<div class="heading">
																	<h3>Main content<abbr title="required field">*</abbr></h3>
																</div>

																<div class="optionsRTE">
																	<textarea id="text" name="text" cols="62" rows="5" class="inputEditor ">
																		&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;
																	</textarea>
																</div>
															</div>

															<div class="box">
																<div class="heading">
																	<div class="oneLiner">
																		<h3>Summary</h3>
																		<abbr class="help">(?)</abbr>
																		<div class="tooltip" style="display: none;">
																			<p>Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.</p>
																		</div>
																	</div>
																</div>

																<div class="optionsRTE">
																	<textarea id="introduction" name="introduction" cols="62" rows="5" class="inputEditor ">
																		&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;
																	</textarea>
																</div>
															</div>
														</td>

														<td id="sidebar">
															<div id="publishOptions" class="box">
																<div class="heading">
																	<h3>Status</h3>
																</div>

																<div class="options">
																	<ul class="inputList">
																		<li>
																			<input type="radio" name="hidden" value="Y" class="inputRadio" id="hiddenY" />
																			<label for="hiddenY">hidden</label>
																		</li>
																		<li>
																			<input type="radio" name="hidden" value="N" checked="checked" class="inputRadio" id="hiddenN" />
																			<label for="hiddenN">published</label>
																		</li>
																	</ul>
																</div>

																<div class="options">
																	<p class="p0">
																		<label for="publishOnDate">Publish on</label>
																	</p>

																	<div class="oneLiner">
																		<p>
																			<input type="text" value="16/03/2011" id="publishOnDate" name="publish_on_date" maxlength="10" class="inputText inputDate inputDatefieldNormal inputText" data-mask="dd/mm/yy" data-firstday="1" />
																		</p>
																		<p>
																			<label for="publishOnTime">at</label>
																		</p>
																		<p>
																			<input type="text" value="14:11" id="publishOnTime" name="publish_on_time" maxlength="5" class="inputText inputTime" />
																		</p>
																	</div>
																</div>
															</div>

															<div class="box" id="articleMeta">
																<div class="heading">
																	<h3>Metadata</h3>
																</div>

																<div class="options">
																	<label for="categoryId">Category</label>
																	<select id="categoryId" name="category_id" class="select" size="1">
																		<option value="1" selected="selected">Default</option>
																	</select>
									 							</div>
																<div class="options">
																	<label for="userId">Author</label>
																	<select id="userId" name="user_id" class="select" size="1">
																		<option value="1" selected="selected">Fork CMS</option>
																	</select>
									 							</div>
																<div class="options">
																	<label for="tags">Tags</label>
																	<input value="othertest,test" id="tags" name="tags" type="text" class="inputText tagBox" />
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
															<input type="checkbox" value="Y" id="allowComments" name="allow_comments" class="inputCheckbox" checked="checked" />
															<label for="allowComments">Allow comments</label>
														</td>
													</tr>
												</table>
											</div>

											<div id="tabRevisions">
												<div class="tableHeading">
													<div class="oneLiner">
														<h3 class="oneLinerElement">Previous versions</h3>
														<abbr class="help">(?)</abbr>
														<div class="tooltip" style="display: none;">
															<p>The last saved versions are kept here. The current version will only be overwritten when you save your changes.</p>
														</div>
													</div>
												</div>

												<div class="dataGridHolder">
													<table class="dataGrid" cellspacing="0" cellpadding="0" border="0">
														<thead>
															<tr>
																<th class="title">
																	<span>Title</span>
																</th>
																<th class="date">
																	<span>Last edited on</span>
																</th>
																<th class="user_id">
																	<span>By</span>
																</th>
																<th class="use_revision">
																	<span>&#160;</span>
																</th>
															</tr>
														</thead>
														<tbody>
															<tr id="row-1" class="odd">
																<td class="title">
																	<a href="/private/en/blog/edit?token=true&amp;id=1&amp;revision=1" title="">Nunc sediam est</a></td>
																<td class="date">
																	<abbr title="16 March 2011 14:11">19 hours ago</abbr>
																</td>
																<td class="user_id">
																	<div class="dataGridAvatar">
																		<div class="avatar av24">
																			<a href="/private/en/users/edit?token=true&amp;id=1">
																				<img src="/frontend/files/backend_users/avatars/32x32/god.jpg" width="24" height="24" alt="Fork CMS" />
																			</a>
																		</div>
																		<p>
																			<a href="/private/en/users/edit?token=true&amp;id=1">Fork CMS</a>
																		</p>
																	</div>
																</td>
																<td class="action actionUseRevision">
																	<a href="/private/en/blog/edit?token=true&amp;id=1&amp;revision=1" class="button iconUseRevision">
																		<span>Use this version</span>
																	</a>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>

											<div id="tabSEO">
												<div id="seoMeta" class="subtleBox">
													<div class="heading">
														<h3>Meta information</h3>
													</div>
													<div class="options">
														<p>
															<label for="pageTitleOverwrite">Pagetitle</label>
															<span class="helpTxt">The title in the browser window (<code>&lt;title&gt;</code>).</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="pageTitleOverwrite" name="page_title_overwrite" class="inputCheckbox" />
																<input value="Nunc sediam est" id="pageTitle" name="page_title" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<p>
															<label for="metaDescriptionOverwrite">Description</label>
															<span class="helpTxt">Briefly summarize the content. This summary is shown in the results of search engines.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="metaDescriptionOverwrite" name="meta_description_overwrite" class="inputCheckbox" />
																<input value="Nunc sediam est" id="metaDescription" name="meta_description" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<p>
															<label for="metaKeywordsOverwrite">Keywords</label>
															<span class="helpTxt">Choose a number of wellthought terms that describe the content.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="metaKeywordsOverwrite" name="meta_keywords_overwrite" class="inputCheckbox" />
																<input value="Nunc sediam est" id="metaKeywords" name="meta_keywords" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<div class="textareaHolder">
															<p>
																<label for="metaCustom">Extra metatags</label>
																<span class="helpTxt">These custom metatags will be placed in the <code>&lt;head&gt;</code> section of the page.</span>
															</p>
															<textarea id="metaCustom" name="meta_custom" cols="62" rows="5" class="textarea"></textarea>
														</div>
													</div>
												</div>

												<div class="subtleBox">
													<div class="heading">
														<h3>URL</h3>
													</div>
													<div class="options">
														<p>
															<label for="urlOverwrite">Custom URL</label>
															<span class="helpTxt">Replace the automaticly generated URL by a custom one.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="urlOverwrite" name="url_overwrite" class="inputCheckbox" />
																<span id="urlFirstPart">http://forkcms.local/en/blog/detail/</span>
																<input value="nunc-sediam-est" id="url" name="url" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>

										<div class="fullwidthOptions">
											<a href="/private/en/blog/delete?token=true&amp;id=1" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
												<span>Delete</span>
											</a>

											<div class="buttonHolderRight">
												<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="Publish" />
												<a href="#" id="saveAsDraft" class="inputButton button"><span>Save draft</span></a>
											</div>
										</div>

										<div id="confirmDelete" title="Delete?" style="display: none;">
											<p>
												Are your sure you want to delete the article "Nunc sediam est"?
											</p>
										</div>
									</form> <!-- End of edit form -->
								</div>
							</td>
						</tr>
					</table>
				</div> <!-- End of main -->
			</td> <!-- End of container -->
		</tr>
	</table>

	<div id="messaging">
		<noscript>
			<div class="formMessage errorMessage">
				<p>To use Fork CMS, javascript needs to be enabled. Activate javascript and refresh this page.</p>
			</div>
		</noscript>
		<div id="noCookies" class="formMessage errorMessage" style="display: none;">
			<p>You need to enable cookies in order to use Fork CMS. Activate cookies and refresh this page.</p>
		</div>
	</div>

	<div id="ajaxSpinner" style="position: fixed; top: 10px; right: 10px; display: none;">
		<img src="../backend/core/layout/images/spinner.gif" width="16" height="16" alt="loading" />
	</div>

</body>
</html>