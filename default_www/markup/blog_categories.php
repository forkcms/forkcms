<?php require_once 'head.php'; ?>

<body id="blog" class="blogCategories blogAddCategory blogEditCategory">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">Blog categories markup</a></h1>
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
													<a href="blog_articles.php">Blog</a>
													<ul>
														<li><a href="blog_articles.php">Articles</a></li>
														<li><a href="blog_comments.php">Comments</a></li>
														<li class="selected"><a href="#">Categories</a></li>
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
										<h2>Blog: categories</h2>
										<div class="buttonHolderRight">
											<a href="/private/en/blog/add_category?token=true" class="button icon iconAdd"><span>Add category</span></a>
										</div>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div class="dataGridHolder">
										<table class="dataGrid" cellspacing="0" cellpadding="0" border="0">
											<thead>
												<tr>
													<th class="title">
														<a href="/private/en/blog/categories?offset=&amp;order=title&amp;sort=desc" title="sorted ascending" class="sortable sorted sortedAsc">
															Title
														</a>
													</th>
													<th class="edit">
														<span>&#160;</span>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr id="row-1" class="isDefault" class="odd">
													<td class="title" data-id="{id:1}">
														Default
													</td>
													<td class="action actionEdit">
														<a href="/private/en/blog/edit_category?token=true&amp;id=1" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div> <!-- End of browse dataGrid -->

									<div class="pageTitle">
										<h2>Blog: add category</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/blog/add_category?token=true" method="post" id="addCategory" class="forkForms submitWithLink">
										<input type="hidden" value="addCategory" id="formAddCategory" name="form" />
										<input type="hidden" name="form_token" id="formTokenAddCategory" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<div class="tabs">
											<ul>
												<li><a href="#tabContent">Content</a></li>
												<li><a href="#tabSEO">SEO</a></li>
											</ul>

											<div id="tabContent">
												<table border="0" cellspacing="0" cellpadding="0" width="100%">
													<tr>
														<td id="leftColumn">
															<p>
																<label for="title">Title<abbr title="required field">*</abbr></label>
																<input value="" id="title" name="title" maxlength="255" type="text" class="inputText" />
																</p>
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
																<input value="" id="url" name="url" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="Add category" />
											</div>
										</div>
									</form> <!-- End of add form -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<div class="pageTitle">
										<h2>Blog: edit category "Default"</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/blog/edit_category?token=true&amp;id=1" method="post" id="editCategory" class="forkForms submitWithLink">
										<input type="hidden" value="editCategory" id="formEditCategory" name="form" />
										<input type="hidden" name="form_token" id="formTokenEditCategory" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<div class="tabs">
											<ul>
												<li><a href="#tabContent">Content</a></li>
												<li><a href="#tabSEO">SEO</a></li>
											</ul>

											<div id="tabContent">
												<table border="0" cellspacing="0" cellpadding="0" width="100%">
													<tr>
														<td id="leftColumn">
															<p>
																<label for="title">Title<abbr title="required field">*</abbr></label>
																<input value="Default" id="title" name="title" maxlength="255" type="text" class="inputText" />
															</p>

															<ul class="inputList">
																<li>
																	<label for="isDefault">Make default category (current default category is: Default).</label>
																	<input type="checkbox" value="Y" id="isDefault" name="is_default" class="inputCheckbox" disabled="disabled" checked="checked" />
																</li>
															</ul>
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
																<input value="Default" id="pageTitle" name="page_title" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<p>
															<label for="metaDescriptionOverwrite">Description</label>
															<span class="helpTxt">Briefly summarize the content. This summary is shown in the results of search engines.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="metaDescriptionOverwrite" name="meta_description_overwrite" class="inputCheckbox" />
																<input value="Default" id="metaDescription" name="meta_description" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
														<p>
															<label for="metaKeywordsOverwrite">Keywords</label>
															<span class="helpTxt">Choose a number of wellthought terms that describe the content.</span>
														</p>
														<ul class="inputList checkboxTextFieldCombo">
															<li>
																<input type="checkbox" value="Y" id="metaKeywordsOverwrite" name="meta_keywords_overwrite" class="inputCheckbox" />
																<input value="Default" id="metaKeywords" name="meta_keywords" maxlength="255" type="text" class="inputText" />
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
																<input value="default" id="url" name="url" maxlength="255" type="text" class="inputText" />
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="Save" />
											</div>
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