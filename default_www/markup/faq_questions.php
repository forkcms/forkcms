<?php require_once 'head.php'; ?>

<body id="faq" class="faqIndex faqAddEdit">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">FAQ markup</a></h1>
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
												<li><a href="blog_articles.php">Blog</a></li>
												<li><a href="#">Search</a></li>
												<li><a href="location.php">Location</a></li>
												<li class="selected">
													<a href="faq_questions.php">FAQ</a>
													<ul>
														<li class="selected">
															<a href="#">Questions</a>
														</li>
														<li>
															<a href="faq_categories.php">Categories</a>
														</li>
													</ul>
												</li>
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
										<h2>FAQ</h2>
										<div class="buttonHolderRight">
											<a href="/private/en/faq/add?token=true" class="button icon iconAdd" title="Add">
												<span>Add</span>
											</a>
										</div>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div id="dataGridQuestionsHolder">
										<div class="dataGridHolder" id="dataGrid-1">
											<div class="tableHeading">
												<h3>Testcategorie</h3>
											</div>
											<table class="dataGrid sequenceByDragAndDrop" cellspacing="0" cellpadding="0" border="0">
												<thead>
													<tr>
														<th class="dragAndDropHandle">
															<span>&#160;</span>
														</th>
														<th class="question">
															<span>Question</span>
														</th>
														<th class="hidden">
															<span>Hidden</span>
														</th>
														<th class="edit">
															<span>&#160;</span>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr id="1" class="odd">
														<td class="dragAndDropHandle">
															<span>move</span>
														</td>
														<td class="question">
															<a href="/private/en/faq/edit?token=true&amp;id=1" title="">Testquestion</a>
														</td>
														<td class="hidden">
															N
														</td>
														<td class="action actionEdit">
															<a href="/private/en/faq/edit?token=true&amp;id=1" class="button icon iconEdit linkButton">
																<span>edit</span>
															</a>
														</td>
													</tr>
													<tr id="2" class="even">
														<td class="dragAndDropHandle">
															<span>move</span>
														</td>
														<td class="question">
															<a href="/private/en/faq/edit?token=true&amp;id=2" title="">Another testquestion</a>
														</td>
														<td class="hidden">
															N
														</td>
														<td class="action actionEdit">
															<a href="/private/en/faq/edit?token=true&amp;id=2" class="button icon iconEdit linkButton">
																<span>edit</span>
															</a>
														</td>
													</tr>
												</tbody>
											</table>
										</div>

										<div class="dataGridHolder" id="dataGrid-2">
											<div class="tableHeading">
												<h3>Volgende categorie</h3>
											</div>

											<table class="dataGrid sequenceByDragAndDrop" cellspacing="0" cellpadding="0" border="0">
												<thead>
													<tr>
														<th class="dragAndDropHandle">
															<span>&#160;</span>
														</th>
														<th class="question">
															<span>Question</span>
														</th>
														<th class="hidden">
															<span>Hidden</span>
														</th>
														<th class="edit">
															<span>&#160;</span>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr class="noQuestions">
														<td colspan="3">There are no questions in this category.</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div> <!-- End of browse dataGrid -->

									<div class="pageTitle">
										<h2>FAQ: add</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/faq/add?token=true" method="post" id="add" class="forkForms submitWithLink">
										<input type="hidden" value="add" id="formAdd" name="form" />
										<input type="hidden" name="form_token" id="formTokenAdd" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<p>
											<input value="" id="title" name="question" maxlength="255" type="text" class="inputText" />
										</p>

										<div class="ui-tabs">
											<div class="ui-tabs-panel">
												<div class="options">
													<table border="0" cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td id="leftColumn">
																<div class="box">
																	<div class="heading">
																		<h3>Answer<abbr title="required field">*</abbr></h3>
																	</div>
																	<div class="optionsRTE">
																		<textarea id="answer" name="answer" cols="62" rows="5" class="inputEditor "></textarea>
																	</div>
																</div>
															</td>
															<td id="sidebar">
																<div id="questionCategory" class="box">
																	<div class="heading">
																		<h3>Category</h3>
																	</div>

																	<div class="options">
																		<p>
																			<select id="categories" name="categories" class="select" size="1">
																				<option value="1">Testcategorie</option>
																				<option value="2">Volgende categorie</option>
																			</select>
									 									</p>
																	</div>
																</div>

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
																</div>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="Add question" />
											</div>
										</div>
									</form> <!-- End of add form -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<div class="pageTitle">
										<h2>FAQ: Edit question "Testquestion</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/faq/edit?token=true&amp;id=1" method="post" id="edit" class="forkForms submitWithLink">
										<input type="hidden" value="edit" id="formEdit" name="form" />
										<input type="hidden" name="form_token" id="formTokenEdit" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<p>
											<input value="Testquestion" id="title" name="question" maxlength="255" type="text" class="inputText" />
										</p>

										<div class="ui-tabs">
											<div class="ui-tabs-panel">
												<div class="options">
													<table border="0" cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td id="leftColumn">
																<div class="box">
																	<div class="heading">
																		<h3>Answer<abbr title="required field">*</abbr></h3>
																	</div>

																	<div class="optionsRTE">
																		<textarea id="answer" name="answer" cols="62" rows="5" class="inputEditor ">
																			&lt;p&gt;This is the answer for the testquestion&lt;/p&gt;
																		</textarea>
																	</div>
																</div>
															</td>
															<td id="sidebar">
																<div id="questionCategory" class="box">
																	<div class="heading">
																		<h3>Category</h3>
																	</div>

																	<div class="options">
																		<p>
																			<select id="categories" name="categories" class="select" size="1">
																				<option value="1" selected="selected">Testcategorie</option>
																				<option value="2">Volgende categorie</option>
																			</select>
									 									</p>
																	</div>
																</div>

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
																</div>
															</td>
														</tr>
													</table>
												</div>
											</div>

											<div class="fullwidthOptions">
												<a href="/private/en/faq/delete?token=true&amp;id=1" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
													<span>Delete</span>
												</a>
												<div class="buttonHolderRight">
													<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="Publish" />
												</div>
											</div>

											<div id="confirmDelete" title="Delete?" style="display: none;">
												<p>
													Are you sure you want to delete the item "Testquestion"?
												</p>
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