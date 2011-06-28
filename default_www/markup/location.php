<?php require_once 'head.php'; ?>

<body id="location" class="locationIndex locationAddEdit">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/en" title="Visit website">Location markup</a></h1>
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
												<li class="selected"><a href="#">Location</a></li>
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
										<h2>Location</h2>
										<div class="buttonHolderRight">
											<a href="/private/en/location/add?token=true" class="button icon iconAdd" title="Add">
												<span>Add</span>
											</a>
										</div>
									</div> <!-- End of pagetitle -->

									<p>There are no items yet.</p> <!-- End of no items -->

									<div class="box">
										<div class="heading">
											<h3>Map</h3>
										</div>

										<div class="options">
											<div id="map" style="height: 300px; width: 100%;"></div>
											<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
											<script type="text/javascript">
												// create boundaries
												var latlngBounds = new google.maps.LatLngBounds();

												// set options
												var options =
												{
													// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
													zoom: 'auto' == 'auto' ? 0 : auto,
													// set default center as first item's location
													center: new google.maps.LatLng(51.0798, 3.72814),
													// no interface, just the map
													disableDefaultUI: true,
													// no dragging the map around
													draggable: false,
													// no zooming in/out using scrollwheel
													scrollwheel: false,
													// no double click zoom
													disableDoubleClickZoom: true,
													// set map type
													mapTypeId: google.maps.MapTypeId.ROADMAP					};

												// create map
												var map = new google.maps.Map(document.getElementById('map'), options);

												// function to add markers to the map
												function addMarker(lat, lng, title, text)
												{
													// create position
													position = new google.maps.LatLng(lat, lng);

													// add to boundaries
													latlngBounds.extend(position);

													// add marker
													var marker = new google.maps.Marker(
													{
														// set position
														position: position,
														// add to map
														map: map,
														// set title
														title: title
													});

													// add click event on marker
													google.maps.event.addListener(marker, 'click', function()
													{
														// create infowindow
														new google.maps.InfoWindow({ content: '<h1>'+ title +'</h1>' + text }).open(map, marker);
													});
												}

												// loop items and add to map
																		addMarker(51.0798, 3.72814, 'Testlocatie', '<p>Dit is mijn locatie</p>');
												// set center to the middle of our boundaries
												map.setCenter(latlngBounds.getCenter());

												// set zoom automatically, defined by points (if allowed)
												if('auto' == 'auto') map.fitBounds(latlngBounds);
											</script>
										</div>
									</div>

									<div class="dataGridHolder">
										<table class="dataGrid" cellspacing="0" cellpadding="0" border="0">
											<thead>
												<tr>
													<th class="title">
														<a href="/private/en/location/index?offset=0&amp;order=title&amp;sort=asc" title="sort ascending" class="sortable">Title</a>
													</th>
													<th class="address">
														<a href="/private/en/location/index?offset=0&amp;order=address&amp;sort=desc" title="sorted ascending" class="sortable sorted sortedAsc">Address</a>
													</th>
													<th class="edit">
														<span>&#160;</span>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr id="row-1" class="odd">
													<td class="title">
														<a href="/private/en/location/edit?token=true&amp;id=1" title="">Testlocatie</a>
													</td>
													<td class="address">
														Voorhavenlaan 31, 9000 Gent, BE
													</td>
													<td class="action actionEdit">
														<a href="/private/en/location/edit?token=true&amp;id=1" class="button icon iconEdit linkButton">
															<span>edit</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div> <!-- End of browse dataGrid -->

									<div class="pageTitle">
										<h2>Location: add</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/location/add?token=true" method="post" id="add" class="forkForms submitWithLink">
										<input type="hidden" value="add" id="formAdd" name="form" />
										<input type="hidden" name="form_token" id="formTokenAdd" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<p>
											<input value="" id="title" name="title" maxlength="255" type="text" class="inputText" />
										</p>

										<div class="box">
											<div class="heading">
												<h3>Content</h3>
											</div>
											<div class="options">
												<p>
													<textarea id="text" name="text" cols="62" rows="5" class="inputEditor "></textarea>
												</p>
											</div>
										</div>

										<div class="box horizontal">
											<div class="heading">
												<h3>Address</h3>
											</div>
											<div class="options">
												<p>
													<label for="street">Street<abbr title="required field">*</abbr></label>
													<input value="" id="street" name="street" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="number">Number<abbr title="required field">*</abbr></label>
													<input value="" id="number" name="number" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="zip">Zip code<abbr title="required field">*</abbr></label>
													<input value="" id="zip" name="zip" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="city">City<abbr title="required field">*</abbr></label>
													<input value="" id="city" name="city" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="country">Country<abbr title="required field">*</abbr></label>
													<select id="country" name="country" class="select" size="1">
														<option value="">All counrties</option>
													</select>
												</p>
											</div>
										</div>

										<div class="fullwidthOptions">
											<div class="buttonHolderRight">
												<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="Publish" />
											</div>
										</div>
									</form> <!-- End of add form -->

									<div class="hr" style="margin-top: 24px"><hr /></div>

									<div class="pageTitle">
										<h2>Location: edit</h2>
									</div>

									<form accept-charset="UTF-8" action="/private/en/location/edit?token=true&amp;id=1" method="post" id="edit" class="forkForms submitWithLink">
										<input type="hidden" value="edit" id="formEdit" name="form" />
										<input type="hidden" name="form_token" id="formTokenEdit" value="e3b4841cc3d6a281072d1ea3cfad6bd8" />

										<p>
											<input value="Testlocatie" id="title" name="title" maxlength="255" type="text" class="inputText" />
										</p>

										<div class="box">
											<div class="heading">
												<h3>Map</h3>
											</div>

											<div class="options">
												<div id="map" style="height: 300px; width: 100%;"></div>
											</div>
										</div>

										<div class="box">
											<div class="heading">
												<h3>Content</h3>
											</div>

											<div class="options">
												<p>
													<textarea id="text" name="text" cols="62" rows="5" class="inputEditor ">
														&lt;p&gt;Dit is mijn locatie&lt;/p&gt;
													</textarea>
												</p>
											</div>
										</div>

										<div class="box horizontal">
											<div class="heading">
												<h3>Address</h3>
											</div>
											<div class="options">
												<p>
													<label for="street">Street<abbr title="required field">*</abbr></label>
													<input value="Voorhavenlaan" id="street" name="street" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="number">Number<abbr title="required field">*</abbr></label>
													<input value="31" id="number" name="number" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="zip">Zip code<abbr title="required field">*</abbr></label>
													<input value="9000" id="zip" name="zip" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="city">City<abbr title="required field">*</abbr></label>
													<input value="Gent" id="city" name="city" maxlength="255" type="text" class="inputText" />
												</p>
												<p>
													<label for="country">Country<abbr title="required field">*</abbr></label>
													<select id="country" name="country" class="select" size="1">
														<option value="">All countries</option>
													</select>
									 			</p>
											</div>
										</div>

										<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
										<script type="text/javascript">
											// create position
											var position = new google.maps.LatLng(51.0798, 3.72814);

											// create boundaries and add position
											var latlngBounds = new google.maps.LatLngBounds(position);

											// set options
											var options =
											{
												// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
												zoom: 15,
												// set default center as first item's location
												center: new google.maps.LatLng(51.0798, 3.72814),
												// no interface, just the map
												disableDefaultUI: true,
												// no dragging the map around
												draggable: false,
												// no zooming in/out using scrollwheel
												scrollwheel: false,
												// no double click zoom
												disableDoubleClickZoom: true,
												// set map type
												mapTypeId: google.maps.MapTypeId.ROADMAP			};

											// create map
											var map = new google.maps.Map(document.getElementById('map'), options);

											// add marker
											var marker = new google.maps.Marker(
											{
												position: position,
												map: map,
												title: 'Testlocatie'
											});

											// add click event on marker
											google.maps.event.addListener(marker, 'click', function()
											{
												// create infowindow
												new google.maps.InfoWindow({ content: '<h1>Testlocatie</h1><p>Dit is mijn locatie</p>' }).open(map, marker);
											});
										</script>

										<div class="fullwidthOptions">
											<a href="/private/en/location/delete?token=true&amp;id=1" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
												<span>Delete</span>
											</a>
											<div class="buttonHolderRight">
												<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="Save" />
											</div>
										</div>

										<div id="confirmDelete" title="Delete?" style="display: none;">
											<p>
												Are you sure you want to delete the item "Testlocatie"?
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