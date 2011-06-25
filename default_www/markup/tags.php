<?php require_once 'head.php'; ?>

<body id="tags" class="tagsIndex tagsAddEdit">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">Tags markup</a></h1>
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
												<li class="selected"><a href="#">Tags</a></li>
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
										<h2>Tags</h2>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<form action="/private/en/tags/mass_action?token=true" method="get" class="forkForms submitWithLink" id="tagsForm">
										<div class="dataGridHolder">
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
														<th class="tag">
															<a href="/private/en/tags/index?offset=0&amp;order=tag&amp;sort=asc" title="sort ascending" class="sortable">Name</a>
														</th>
														<th class="num_tags">
															<a href="/private/en/tags/index?offset=0&amp;order=num_tags&amp;sort=asc" title="sorted descending" class="sortable sorted sortedDesc">
																Amount
															</a>
														</th>
														<th class="edit">
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
														<td class="tag" data-id="{id:1}">
															test
														</td>
														<td class="num_tags">
															1
														</td>
														<td class="action actionEdit">
															<a href="/private/en/tags/edit?token=true&amp;id=1" class="button icon iconEdit linkButton">
															<span>edit</span>
															</a>
														</td>
													</tr>
													<tr id="row-2" class="even">
														<td class="checkbox">
															<span>
																<input type="checkbox" name="id[]" value="2" class="inputCheckbox" />
															</span>
														</td>
														<td class="tag" data-id="{id:2}">
															othertest
														</td>
														<td class="num_tags">
															1
														</td>
														<td class="action actionEdit">
															<a href="/private/en/tags/edit?token=true&amp;id=2" class="button icon iconEdit linkButton">
																<span>edit</span>
															</a>
														</td>
													</tr>
												</tbody>
												<tfoot>
													<tr>
														<td colspan="4">
															<div class="tableOptionsHolder">
																<div class="tableOptions">
																	<div class="oneLiner massAction">
																		<p>
																			<label for="action">With selected</label>
																		</p>
																		<p>
																			<select id="action" name="action" class="inputDropdown" size="1">
																				<option value="delete" selected="selected" message-id="confirmDelete">delete</option>
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
									</form> <!-- End of tags browse form -->

									<div id="confirmDelete" title="Delete?" style="display: none;">
										<p>Are your sure you want to delete this/these item(s)?</p>
									</div> <!-- End of confirm delete -->

									<div class="pageTitle">
										<h2>Tags: edit tag "test"</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/tags/edit?token=true&amp;id=1" method="post" id="edit" class="forkForms submitWithLink">
										<input type="hidden" value="edit" id="formEdit" name="form" />
										<input type="hidden" name="form_token" id="formTokenEdit" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<div class="box">
											<div class="heading">
												<h3>Tags: edit tag "test"</h3>
											</div>
											<div class="options horizontal">
												<p>
													<label for="name">Name<abbr title="required field">*</abbr></label>
													<input value="test" id="name" name="name" maxlength="255" type="text" class="inputText" />
												</p>
												<div class="fakeP">
													<label>Used in</label>
													<p>Not yet used.</p>
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