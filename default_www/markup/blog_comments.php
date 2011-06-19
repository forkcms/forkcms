<?php require_once 'head.php'; ?>

<body id="blog" class="blogComments blogEditComment">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">Blog comments markup</a></h1>
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
														<li class="selected"><a href="#">Comments</a></li>
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
										<h2>Blog: comments</h2>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div id="tabs" class="tabs">
										<ul>
											<li><a href="#tabPublished">Published (3)</a></li>
											<li><a href="#tabModeration">Waiting for moderation (0)</a></li>
											<li><a href="#tabSpam">Spam (0)</a></li>
										</ul>

										<div id="tabPublished">
											<form action="/private/en/blog/mass_comment_action?token=true" method="get" class="forkForms" id="commentsPublished">
												<div class="dataGridHolder">
													<input type="hidden" name="from" value="published" />
													<table class="dataGrid" cellspacing="0" cellpadding="0" border="0">
														<thead>
															<tr>
																<th class="checkbox">
																	<span>
																		<span class="checkboxHolder">
																			<input type="checkbox" name="toggleChecks" value="toggleChecks" />
																		</span>
																	</span>
																</th>
																<th class="date">
																	<a href="/private/en/blog/comments?offset=0&amp;order=created_on&amp;sort=asc#tabPublished" title="sorted descending" class="sortable sorted sortedDesc">Date</a>
																</th>
																<th class="author">
																	<a href="/private/en/blog/comments?offset=0&amp;order=author&amp;sort=asc#tabPublished" title="sort ascending" class="sortable">Author</a>
																</th>
																<th class="text">
																	<a href="/private/en/blog/comments?offset=0&amp;order=text&amp;sort=asc#tabPublished" title="sort ascending" class="sortable">Comment</a>
																</th>
																<th class="edit">
																	<span>&#160;</span>
																</th>
																<th class="mark_as_spam">
																	<span>&#160;</span>
																</th>
															</tr>
														</thead>
														<tbody>
															<tr id="row-1" class="odd">
																<td class="checkbox">
																	<span>
																		<input type="checkbox" name="id[]" value="1" class="inputCheckbox" />
																	</span>
																</td>
																<td class="date">
																	<abbr title="16 March 2011 14:11">20 hours ago</abbr>
																</td>
																<td class="author">
																	Matthias Mullie
																</td>
																<td class="text">
																	<p>
																		<em>Comment on: <a href="/en/blog/detail/nunc-sediam-est#comment-1">Nunc sediam est</a></em>
																	</p>
																	<p>cool!</p>
																</td>
																<td class="action actionEdit">
																	<a href="/private/en/blog/edit_comment?token=true&amp;id=1" class="button icon iconEdit linkButton">
																		<span>edit</span>
																	</a>
																</td>
																<td class="action actionMarkAsSpam">
																	<a href="/private/en/blog/mass_comment_action?token=true&amp;id=1&amp;from=published&amp;action=spam" class="button icon iconMarkAsSpam linkButton">
																		<span>mark as spam</span>
																	</a>
																</td>
															</tr>
															<tr id="row-2" class="even">
																<td class="checkbox">
																	<span>
																		<input type="checkbox" name="id[]" value="2" class="inputCheckbox" />
																	</span>
																</td>
																<td class="date">
																	<abbr title="16 March 2011 14:11">20 hours ago</abbr>
																</td>
																<td class="author">
																	Davy Hellemans
																</td>
																<td class="text">
																	<p>
																		<em>Comment on: <a href="/en/blog/detail/nunc-sediam-est#comment-2">Nunc sediam est</a></em>
																	</p>
																	<p>awesome!</p>
																</td>
																<td class="action actionEdit">
																	<a href="/private/en/blog/edit_comment?token=true&amp;id=2" class="button icon iconEdit linkButton">
																		<span>edit</span>
																	</a>
																</td>
																<td class="action actionMarkAsSpam">
																	<a href="/private/en/blog/mass_comment_action?token=true&amp;id=2&amp;from=published&amp;action=spam" class="button icon iconMarkAsSpam linkButton">
																		<span>mark as spam</span>
																	</a>
																</td>
															</tr>
															<tr id="row-3" class="odd">
																<td class="checkbox">
																	<span>
																		<input type="checkbox" name="id[]" value="3" class="inputCheckbox" />
																	</span>
																</td>
																<td class="date">
																	<abbr title="16 March 2011 14:11">20 hours ago</abbr>
																</td>
																<td class="author">
																	Tijs Verkoyen
																</td>
																<td class="text">
																	<p><em>Comment on: <a href="/en/blog/detail/nunc-sediam-est#comment-3">Nunc sediam est</a></em></p>
																	<p>wicked!</p>
																</td>
																<td class="action actionEdit">
																	<a href="/private/en/blog/edit_comment?token=true&amp;id=3" class="button icon iconEdit linkButton">
																		<span>edit</span>
																	</a>
																</td>
																<td class="action actionMarkAsSpam">
																	<a href="/private/en/blog/mass_comment_action?token=true&amp;id=3&amp;from=published&amp;action=spam" class="button icon iconMarkAsSpam linkButton">
																		<span>mark as spam</span>
																	</a>
																</td>
															</tr>
														</tbody>
														<tfoot>
															<tr>
																<td colspan="6">
																	<div class="tableOptionsHolder">
																		<div class="tableOptions">
																			<div class="oneLiner massAction">
																				<p>
																					<label for="actionPublished">With selected</label>
																				</p>
																				<p>
																					<select id="actionPublished" name="action" class="inputDropdown" size="1">
																						<option value="moderation">move to moderation</option>
																						<option value="spam" selected="selected" data-message-id="confirmSpamPublished">move to spam</option>
																						<option value="delete" data-message-id="confirmDeletePublished">delete</option>
																					</select>
																				</p>
																				<div class="buttonHolder">
																					<a href="#" class="submitButton button">
																						<span>Execute</span>
																					</a>
																				</div>
																			</div>
																		</div>
																	</div>
																</td>
															</tr>
														</tfoot>
													</table>
												</div>
											</form>
										</div>

										<div id="tabModeration">
											There are no comments in this category yet.
										</div>

										<div id="tabSpam">
											There are no comments in this category yet.
										</div>
									</div>

									<div id="confirmDeletePublished" title="Delete?" style="display: none;">
										<p>Are your sure you want to delete this/these item(s)?</p>
									</div>

									<div id="confirmSpamPublished" title="Spam?" style="display: none;">
										<p>Are your sure you want to mark this/these item(s) as spam?</p>
									</div>

									<div id="confirmDeleteModeration" title="Delete?" style="display: none;">
										<p>Are your sure you want to delete this/these item(s)?</p>
									</div>

									<div id="confirmSpamModeration" title="Spam?" style="display: none;">
										<p>Are your sure you want to mark this/these item(s) as spam?</p>
									</div>

									<div id="confirmDeleteSpam" title="Delete?" style="display: none;">
										<p>Are your sure you want to delete this/these item(s)?</p>
									</div> <!-- End of browse dataGrid -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<div class="pageTitle">
										<h2>Blog: edit comment on "Nunc sediam est"</h2>
										<div class="buttonHolderRight">
											<a href="http://forkcms.local/en/blog/detail/nunc-sediam-est#comment-1" class="button icon iconZoom previewButton targetBlank">
												<span>View</span>
											</a>
										</div>
									</div>

									<form accept-charset="UTF-8" action="/private/en/blog/edit_comment?token=true&amp;id=1" method="post" id="editComment" class="forkForms submitWithLink">
										<input type="hidden" value="editComment" id="formEditComment" name="form" />
										<input type="hidden" name="form_token" id="formTokenEditComment" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<div class="box">
											<div class="heading">
												<h3>Comment</h3>
											</div>

											<div class="options">
												<p>
													<label for="author">Author<abbr title="required field">*</abbr></label>
													<input value="Matthias Mullie" id="author" name="author" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="email">E-mail<abbr title="required field">*</abbr></label>
													<input value="matthias@spoon-library.com" id="email" name="email" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="website">Website</label>
													<input value="http://www.anantasoft.com" id="website" name="website" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="text">Text<abbr title="required field">*</abbr></label>
													<textarea id="text" name="text" cols="62" rows="5" class="textarea">cool!</textarea>
												</p>
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