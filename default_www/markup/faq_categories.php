<?php require_once 'head.php'; ?>

<body id="faq" class="faqCategories faqAddCategory faqEditCategory">

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
														<li>
															<a href="faq_questions.php">Questions</a>
														</li>
														<li class="selected">
															<a href="#">Categories</a>
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
										<h2>FAQ: categories</h2>
										<div class="buttonHolderRight">
											<a href="/private/en/faq/add_category?token=true" class="button icon iconAdd"><span>Add category</span></a>
										</div>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div class="dataGridHolder">
										<table class="dataGrid sequenceByDragAndDrop" cellspacing="0" cellpadding="0" border="0">
											<thead>
												<tr>
													<th class="dragAndDropHandle">
														<span>&#160;</span>
													</th>
													<th class="name">
														<span>Name</span>
													</th>
													<th class="edit">
														<span>&#160;</span>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr id="row-1" data-id="1" class="odd">
													<td class="dragAndDropHandle">
														<span>move</span>
													</td>
													<td class="name">
														<a href="/private/en/faq/edit_category?token=true&amp;id=1" title="">Testcategorie</a>
													</td>
													<td class="action actionEdit">
														<a href="/private/en/faq/edit_category?token=true&amp;id=1" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
												<tr id="row-2" data-id="2" class="even">
													<td class="dragAndDropHandle">
														<span>move</span>
													</td>
													<td class="name">
														<a href="/private/en/faq/edit_category?token=true&amp;id=2" title="">Volgende categorie</a>
													</td>
													<td class="action actionEdit">
														<a href="/private/en/faq/edit_category?token=true&amp;id=2" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div> <!-- End of browse dataGrid -->

									<form accept-charset="UTF-8" action="/private/en/faq/add_category?token=true" method="post" id="add_category" class="forkForms submitWithLink">
										<input type="hidden" value="add_category" id="formAddCategory" name="form" />
										<input type="hidden" name="form_token" id="formTokenAddCategory" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<div class="pageTitle">
											<h2>FAQ: add category</h2>
										</div>

										<div class="box horizontal">
											<div class="heading">
												<h3>FAQ: add category</h3>
											</div>

											<div class="options">
												<p>
													<label for="name">Name<abbr title="required field">*</abbr></label>
													<input value="" id="name" name="name" maxlength="255" type="text" class="inputText" />
												</p>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="addButton" class="inputButton button mainButton" type="submit" name="addCategory" value="Add category" />
											</div>
										</div>
									</form> <!-- End of add form -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<form accept-charset="UTF-8" action="/private/en/faq/edit_category?token=true&amp;id=1" method="post" id="edit_category" class="forkForms submitWithLink">
										<input type="hidden" value="edit_category" id="formEditCategory" name="form" />
										<input type="hidden" name="form_token" id="formTokenEditCategory" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />
										<div class="pageTitle">
											<h2>FAQ: edit category "Testcategorie"</h2>
										</div>

										<div class="box horizontal">
											<div class="heading">
												<h3>FAQ: edit category "Testcategorie"</h3>
											</div>
											<div class="options">
												<p>
													<label for="name">Category<abbr title="required field">*</abbr></label>
													<input value="Testcategorie" id="name" name="name" maxlength="255" type="text" class="inputText" />
												</p>
											</div>
										</div>

										<div class="fullwidthOptions">
											<a href="/private/en/faq/delete_category?token=true&amp;id=1" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
												<span>Delete</span>
											</a>
											<div id="confirmDelete" title="Delete?" style="display: none;">
												<p>
													Are you sure you want to delete the category "Testcategorie"?
												</p>
											</div>
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