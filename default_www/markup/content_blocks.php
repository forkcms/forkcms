<?php require_once 'head.php'; ?>

<body id="contentBlocks" class="contentBlocksIndex">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">Content blocks markup</a></h1>
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
												<li class="selected"><a href="#">Content blocks</a></li>
												<li><a href="tags.php">Tags</a></li>
												<li><a href="blog_articles.php">Blog</a></li>
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
										<h2>Content blocks</h2>
										<div class="buttonHolderRight">
											<a href="#" class="button icon iconAdd" title="Add content block">
												<span>Add content block</span>
											</a>
										</div>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div class="dataGridHolder">
										<table class="dataGrid" cellspacing="0" cellpadding="0" border="0">
											<thead>
												<tr>
													<th class="title">
														<a href="/private/en/content_blocks/index?offset=0&amp;order=title&amp;sort=desc" title="sorted ascending" class="sortable sorted sortedAsc">
															Title
														</a>
													</th>
													<th class="edit">
														<span>&#160;</span>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr id="row-2" class="odd">
													<td class="title">
														<a href="/private/en/content_blocks/edit?token=true&amp;id=2" title="">Another content block</a>
													</td>
													<td class="action actionEdit">
														<a href="/private/en/content_blocks/edit?token=true&amp;id=2" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
												<tr id="row-1" class="even">
													<td class="title">
														<a href="/private/en/content_blocks/edit?token=true&amp;id=1" title="">This is a content block</a>
													</td>
													<td class="action actionEdit">
														<a href="/private/en/content_blocks/edit?token=true&amp;id=1" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div> <!-- End of browse dataGrid -->

									<div class="pageTitle">
										<h2>Content blocks: add content block</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/content_blocks/add?token=true" method="post" id="add" class="forkForms submitWithLink">
										<input type="hidden" value="add" id="formAdd" name="form" />
										<input type="hidden" name="form_token" id="formTokenAdd" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<p>
											<input value="" id="title" name="title" maxlength="255" type="text" class="inputText" />
										</p>

										<div class="box">
											<div class="heading">
												<h3>Content</h3>
											</div>
											<div class="content">
												<fieldset>
													<p style="position: relative;">
														<textarea id="text" name="text" cols="62" rows="5" class="inputEditor "></textarea>
													</p>
													<p>
														<label for="hidden">
															<input type="checkbox" value="Y" id="hidden" name="hidden" class="inputCheckbox" checked="checked" />
															visible on site
														</label>
													</p>
												</fieldset>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="Add content block" />
											</div>
										</div>
									</form> <!-- End of add form -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<div class="pageTitle">
										<h2>Content blocks: edit content block "Another content block"</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/content_blocks/edit?token=true&amp;id=2" method="post" id="edit" class="forkForms submitWithLink">
										<input type="hidden" value="edit" id="formEdit" name="form" />
										<input type="hidden" name="form_token" id="formTokenEdit" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<div class="tabs">
											<ul>
												<li><a href="#tabContent">Content</a></li>
												<li class="notImportant"><a href="#tabRevisions">Previous versions</a></li>
											</ul>

											<div id="tabContent">
												<fieldset>
													<p>
														<label for="title">Title<abbr title="required field">*</abbr></label>
														<input value="Another content block" id="title" name="title" maxlength="255" type="text" class="inputText" />
													</p>
													<p style="position: relative;">
														<label for="text">Content</label>
														<textarea id="text" name="text" cols="62" rows="5" class="inputEditor ">Content</textarea>
													</p>
													<p>
														<label for="hidden">
															<input type="checkbox" value="Y" id="hidden" name="hidden" class="inputCheckbox" checked="checked" />
															visible on site
														</label>
													</p>
												</fieldset>
											</div>

											<div id="tabRevisions">
												<div class="dataGridHolder">
													<div class="tableHeading">
														<div class="oneLiner">
															<h3 class="floater">Previous versions</h3>
															<abbr class="help">(?)</abbr>
															<div class="balloon balloonAlt" style="display: none;">
																<p>The last saved versions are kept here. The current version will only be overwritten when you save your changes.</p>
															</div>
														</div>
													</div>

													<table border="0" cellspacing="0" cellpadding="0" class="dataGrid">
														<tr>
															<td>
																<p>There are no previous versions yet.</p>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>

										<div class="fullwidthOptions">
											<a href="/private/en/content_blocks/delete?token=true&amp;id=2" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
												<span>Delete</span>
											</a>
											<div class="buttonHolderRight">
												<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="Save" />
											</div>
										</div>

										<div id="confirmDelete" title="Delete?" style="display: none;">
											<p>
												Are your sure you want to delete the content block "Another content block"?
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