{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" width="264">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td id="treeHolder">
						<div id="treeOptions">
							<div class="buttonHolder">
								<a href="{$var|geturl:"index"}" class="button icon iconBack iconOnly"><span><span><span>{$lblBack|ucfirst}</span></span></span></a>
								<a href="{$var|geturl:"add"}" class="button icon iconAdd"><span><span><span>{$lblAdd}</span></span></span></a>
							</div>
						</div>
						<div id="tree">
							{$tree}
						</div>
					</td>
				</tr>
			</table>

		</td>
		<td id="fullwidthSwitch"><a href="#close">&nbsp;</a></td>
		<td id="contentHolder">
			<div class="inner">
				{form:add}
					{$txtTitle} {$txtTitleError}
					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="#">http://www.mysite.be/nl/</a></span>
							</p>
						</div>
					</div>
					<div id="tabs" class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
							<li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
							<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
							<li><a href="#tabSecurity">{$lblSecurity|ucfirst}</a></li>
							<li><a href="#tabTemplate">{$lblTemplate|ucfirst}</a></li>
							<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							{iteration:blocks}
									<div class="content{$blocks.index} contentBlock" rel="contentA">
										<div class="contentTitle selected">
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td class="numbering">
														{$blocks.index}
													</td>
													<td>
														<div class="oneLiner">
															<p>
																<a href="#tabsContent">
																	<span>{$blocks.name}</span>
																</a>
															</p>
															<p>
																{$blocks.ddmExtraId} {$blocks.ddmExtraIdError}
															</p>
														</div>
													</td>
												</tr>
											</table>
										</div>
										<div class="editContent">
											<fieldset>
												{$blocks.txtHTML} {$blocks.txtHTMLError}

												<div class="contentTypeModule">
													<p>A module is attached. Go to <a href="#">blog</a> to edit this content.</p>

												</div>
											</fieldset>
										</div>
									</div>
									{/iteration:blocks}
						</div>
						<div id="tabVersions">
							<div class="datagridHolder">
								<div class="tableHeading">
									<div class="oneLiner">
										<h3 class="floater">{$lblVersions}</h3>
										<abbr title="De 20 laatst opgeslagen paginaversies worden hier bijgehouden. 'Gebruik deze versie' opent een vroegere versie. De huidige versie wordt pas overschreven als je de pagina opslaat." class="help floater">(?)</abbr>
									</div>
								</div>
								<table class="datagrid" border="0" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<th class="sortable sorted" width="20%">
												<a href="#">By</a>
											</th>
											<th class="sortable" width="40%">
												<a href="#">Date</a>
											</th>
											<th colspan="2" width="40%">
												&nbsp;<!-- action -->
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<div class="user">
													<a href="#">
													<img src="images/avatars/fun/mushimushi.png">
													Johan Wolfe
													</a>
												</div>
											</td>
											<td class="date">
												<abbr title="22-11-08 15:27">1 minute ago</abbr>
											</td>
											<td class="action">
												<div class="buttonHolder">
													<a href="#" class="button"><span><span><span>Preview</span></span></span></a>
													<a href="#" class="button"><span><span><span>Revert to this version</span></span></span></a>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="user">
													<a href="#">
													<img src="images/avatars/fun/mushimush2.png">
													どーもくん
													</a>
												</div>
											</td>
											<td class="date">
												<abbr title="22-11-08 15:27">3 hours ago</abbr>
											</td>
											<td class="action">
												<div class="buttonHolder">
													<a href="#" class="button"><span><span><span>Preview</span></span></span></a>
													<a href="#" class="button"><span><span><span>Revert to this version</span></span></span></a>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="user">
													<a href="#">
													<img src="images/avatars/fun/mushimush2.png">
													どーもくん
													</a>
												</div>
											</td>
											<td class="date">
												<abbr title="22-11-08 15:27">3 hours ago</abbr>
											</td>
											<td class="action">
												<div class="buttonHolder">
													<a href="#" class="button"><span><span><span>Preview</span></span></span></a>
													<a href="#" class="button"><span><span><span>Revert to this version</span></span></span></a>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="user">
													<a href="#">
													<img src="images/avatars/fun/mushimush2.png">
													どーもくん
													</a>
												</div>
											</td>
											<td class="date">
												<abbr title="22-11-08 15:27">1 day ago</abbr>
											</td>
											<td class="action">
												<div class="buttonHolder">
													<a href="#" class="button"><span><span><span>Preview</span></span></span></a>
													<a href="#" class="button"><span><span><span>Revert to this version</span></span></span></a>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div id="tabSEO">
							<div id="titles" class="box boxLevel2">
								<div class="heading">
									<h3>Titels</h3>
								</div>
								<div class="options">

									<p>
										<label for="meta_pagetitle_overwrite">Paginatitel</label>
										<span class="helpTxt">De titel die in het venster van de browser staat (<code>&lt;title&gt;</code>).</span>

										</p><ul class="inputList">
											<input class="inputCheckbox" value="Y" name="seoPagetitleOverwrite" id="seoPagetitleOverwrite" type="checkbox">
											<input class="inputText inputDisabled" maxlength="255" value="" name="seoPagetitle" id="seoPagetitle" type="text">
										</ul>


									<p>
										<label for="navigation_title_overwrite">Titel in het navigatiemenu</label>
										<span class="helpTxt">Als de paginatitel te lang is om in het menu te passen, geef dan een verkorte titel in.</span>
										<input class="inputCheckbox" value="Y" name="navigation_title_overwrite" id="navigation_title_overwrite" type="checkbox">
										<input class="inputText inputDisabled" maxlength="255" value="" name="navigation_title" id="navigation_title" type="text">
									</p>


								</div>
							</div>

							<div id="seoNofollow" class="box boxLevel2">
								<div class="heading">
									<h3>Nofollow</h3>
								</div>
								<div class="options">
									<fieldset>
										<p class="helpTxt">Zorgt ervoor dat deze pagina de interne PageRank niet beïnvloedt.</p>
										<ul class="inputList">
											<li>
												<input class="inputCheckbox" value="Y" name="nofollow" id="nofollow" type="checkbox">
												<label for="no_follow">Activeer <code>rel="nofollow"</code></label>
											</li>
										</ul>
									</fieldset>
								</div>
							</div>

							<div id="seoMeta" class="box boxLevel2">
								<div class="heading">
									<h3>Meta informatie</h3>
								</div>
								<div class="options">

									<p>
										<label for="meta_description_overwrite">Omschrijving pagina</label>
										<span class="helpTxt">De pagina-omschrijving die wordt getoond in de resultaten van zoekmachines. Hou het kort en krachtig.</span>
									</p>
									<ul class="inputList">
										<li>
											<input class="inputCheckbox" value="Y" name="meta_description_overwrite" id="meta_description_overwrite" type="checkbox">
											<input class="inputText inputDisabled" maxlength="255" value="" name="meta_description" id="meta_description" type="text">
										</li>
									</ul>

									<p>
										<label for="meta_keywords_overwrite">Sleutelwoorden pagina</label>
										<span class="helpTxt">De sleutelwoorden (<em>keywords</em>) die deze pagina omschrijven.</span>
									</p>

									<ul class="inputList">
										<li>
											<input class="inputCheckbox" value="Y" name="meta_keywords_overwrite" id="meta_keywords_overwrite" type="checkbox">
											<input class="inputText inputDisabled" maxlength="255" value="" name="meta_keywords" id="meta_keywords" type="text">
										</li>
									</ul>

									<p>
										<label for="meta_custom">Extra metatags</label>
										<span class="helpTxt">Laat toe om extra, op maat gemaakte metatags toe te voegen.</span>
										<textarea rows="8" cols="40"></textarea>
									</p>
								</div>
							</div>

							<div id="seoUrl" class="box boxLevel2">
								<div class="heading">
									<h3>URL</h3>
								</div>
								<div class="options">

									<label for="url_overwrite">Aangepaste URL</label>
									<span class="helpTxt">Vervang de automatisch gegenereerde URL door een zelfgekozen URL.</span>

									<ul class="inputList">
										<li>
											<input class="inputCheckbox" value="Y" name="url_overwrite" id="url_overwrite" type="checkbox">
											<span id="urlFirstPart">http://www.abconcerts.be/nl/</span>
											<input class="inputText inputDisabled" maxlength="255" value="" name="url" id="url" type="text">
										</li>
									</ul>

								</div>
							</div>
						</div>
						<div id="tabSecurity">
						</div>
						<div id="tabTemplate">
						</div>
						<div id="tabTags">
							<div id="tags" class="box boxLevel2">
								<div class="heading">
									<h3>Tags</h3>
								</div>
								<div class="options last">
									<!-- <label for="addTag">Add tags:</label> -->
									<div class="oneLiner">
										<p><input class="inputText" id="addTag" type="text"></p>
										<div class="buttonHolder">
											<a href="#" class="button icon iconAdd"><span><span><span>Add</span></span></span></a>
										</div>
									</div>
									<!-- <label>Current tags:</label> -->
									<ul id="tagsList">
										<li><span><strong>Music</strong> <a href="#" title="Delete tag">X</a></span></li>
										<li><span><strong>Concerts</strong> <a href="#" title="Delete tag">X</a></span></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="fullwidthOptions">
						<a href="#" class="button linkButton icon iconDelete"><span><span><span>Delete page</span></span></span></a>
						<div class="buttonHolderRight">
							{$btnAdd}
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}