{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" width="264">
			<table border="0" cellspacing="0" cellpadding="0">

				<tr>
					<td id="treeHolder">
						<div id="treeOptions">
							<div class="buttonHolder">
								<a class="button icon iconBack iconOnly" href="{$var|geturl:"index"}">
									<span><span><span>{$lblBack|ucfirst}</span></span></span>
								</a>
								<a href="{$var|geturl:"add"}" class="button icon iconAdd">
									<span><span><span>{$lblAdd|ucfirst}</span></span></span>
								</a>
							</div>
						</div>
						<div id="tree">
							TREE
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td id="fullwidthSwitch">
			<a href="#close">&nbsp;</a>
		</td>
		<td id="content">
			<div class="inner">
				{form:add}
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td>
								{$txtTitle} {$txtTitleError}
								<div id="pageUrl">
									<div class="oneLiner">
										<p>
											<span><a href="#">http://www.mysite.be/nl/</a></span>
										</p>
									</div>
								</div>
								<br />
								<div id="editContent">
									<!-- Content A -->
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
											<fieldset>
												

											</fieldset>
										</div>
									</div>
									
									<!-- Content C -->
									<div class="contentC contentBlock" rel="contentC">
										<div class="contentTitle selected">
											<table border="0" cellspacing="0" cellpadding="0">

												<tr>
													<td class="numbering">
														C
													</td>
													<td>
														<div class="oneLiner">
															<p>
																<a href="#tabsContent">
																	<span>Side image</span>

																</a>
															</p>
															<p>
																<select id="contentType">
																	<optgroup label="Inhoudsvelden">
																		<option>Editorveld (Rich text)</option>
																		<option selected="selected">Image</option>
																		<option>Flash-content</option>

																	</optgroup>
																	<optgroup label="Modules">
																		<option>Blog</option>
																		<option>Gallery</option>
																		<option>Nieuws</option>
																		<option>FAQ</option>
																		<option>Contest: win concert tickets</option>

																	</optgroup>
																</select>
															</p>
														</div>
													</td>
												</tr>
											</table>
										</div>
										<div class="editContent">

											<fieldset>
												
												<div class="contentTypeImageEdit">
													
													<table border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td>
																<img src="images/sample_content/fields-1.jpg" style="width: 100px;"/>
															</td>
															<td>
																<label for="editCurrentFile">Upload a new file:</label>

																<input type="file" class="inputFile" id="editCurrentFile" />
															</td>
														</tr>
													</table>
													
												</div>

											</fieldset>
										</div>
									</div>

									
									<!-- Content D -->
									<div class="contentD contentBlock" rel="contentD">
										<div class="contentTitle selected">
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td class="numbering">
														D
													</td>
													<td>

														<div class="oneLiner">
															<p>
																<a href="#tabsContent">
																	<span>Win win win</span>
																</a>
															</p>
															<p>
																<select id="contentType">

																	<optgroup label="Inhoudsvelden">
																		<option>Editorveld (Rich text)</option>
																		<option>Image</option>
																		<option>Flash-content</option>
																	</optgroup>
																	<optgroup label="Modules">
																		<option>Blog</option>

																		<option>Gallery</option>
																		<option>Nieuws</option>
																		<option>FAQ</option>
																		<option selected="selected">Contest: win concert tickets</option>
																	</optgroup>
																</select>
															</p>

														</div>
													</td>
												</tr>
											</table>
										</div>
										<div class="editContent">
											<fieldset>
												
												<div class="contentTypeModule">
													
													<p>A module is attached. Go to <a href="#">contest > win concert tickets</a> to edit this content.</p>

													
												</div>

											</fieldset>
										</div>
									</div>
									
									<!-- Content E -->
									<div class="contentE contentBlock" rel="contentE">
										<div class="contentTitle selected">
											<table border="0" cellspacing="0" cellpadding="0">

												<tr>
													<td class="numbering">
														E
													</td>
													<td>
														<div class="oneLiner">
															<p>
																<a href="#tabsContent">
																	<span>Video</span>

																</a>
															</p>
															<p>
																<select id="contentType">
																	<optgroup label="Inhoudsvelden">
																		<option>Editorveld (Rich text)</option>
																		<option>Image</option>
																		<option selected="selected">Flash-content</option>

																	</optgroup>
																	<optgroup label="Modules">
																		<option>Blog</option>
																		<option>Gallery</option>
																		<option>Nieuws</option>
																		<option>FAQ</option>
																		<option>Contest: win concert tickets</option>

																	</optgroup>
																</select>
															</p>
														</div>
													</td>
												</tr>
											</table>
										</div>
										<div class="editContent">

											<fieldset>
												
												<div class="contentTypeFlash">
													<textarea name="flash" rows="8" cols="50">&lt;object width=&quot;320&quot; height=&quot;265&quot;&gt;&lt;param name=&quot;movie&quot; value=&quot;http://www.youtube.com/v/6ULWz53hqA8&amp;hl=en&amp;fs=1&amp;&quot;&gt;&lt;/param&gt;&lt;param name=&quot;allowFullScreen&quot; value=&quot;true&quot;&gt;&lt;/param&gt;&lt;param name=&quot;allowscriptaccess&quot; value=&quot;always&quot;&gt;&lt;/param&gt;&lt;embed src=&quot;http://www.youtube.com/v/6ULWz53hqA8&amp;hl=en&amp;fs=1&amp;&quot; type=&quot;application/x-shockwave-flash&quot; allowscriptaccess=&quot;always&quot; allowfullscreen=&quot;true&quot; width=&quot;320&quot; height=&quot;265&quot;&gt;&lt;/embed&gt;&lt;/object&gt;</textarea>
												</div>

											</fieldset>
										</div>
									</div>
									

								</div>

								

								<table border="0" cellspacing="0" cellpadding="0" id="advancedOptions">
									<tr>
										<td>
											
											<div class="collapseBox" id="seo">
												<div class="collapseBoxHeading">
													<div class="buttonHolderSingle">
														<a href="#" class="button icon iconCollapsed iconOnly"><span><span><span>+</span></spa></span></a>
													</div>

													<h4><a href="#">SEO</a></h4>
												</div>
												<div class="options" style="display: none;">
												
													<table border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td>
																
																<div id="titles" class="box boxLevel2">
																	<div class="heading">

																		<h3>Titels</h3>
																	</div>
																	<div class="options last">
																		
																		<p>
																			<label for="meta_pagetitle_overwrite">Paginatitel</label>
																			<span class="helpTxt">De titel die in het venster van de browser staat (<code>&lt;title&gt;</code>).</span>

																			
																			<ul class="inputList">
																				<input type="checkbox" class="inputCheckbox" value="Y" name="seoPagetitleOverwrite" id="seoPagetitleOverwrite"/>
																				<input type="text" class="inputText inputDisabled" maxlength="255" value="" name="seoPagetitle" id="seoPagetitle"/>
																			</ul>
																		</p>

																		<p>
																			<label for="navigation_title_overwrite">Titel in het navigatiemenu</label>
																			<span class="helpTxt">Als de paginatitel te lang is om in het menu te passen, geef dan een verkorte titel in.</span>

																			<input type="checkbox" class="inputCheckbox" value="Y" name="navigation_title_overwrite" id="navigation_title_overwrite"/>
																			<input type="text" class="inputText inputDisabled" maxlength="255" value="" name="navigation_title" id="navigation_title"/> </dd>
																		</p>
																		
																		
																	</div>
																</div>

																<div id="seoNofollow" class="box boxLevel2">
																	<div class="heading">
																		<h3>Nofollow</h3>

																	</div>
																	<div class="options last">
																		<fieldset>
																			<p class="helpTxt">Zorgt ervoor dat deze pagina de interne PageRank niet be&iuml;nvloedt.</p>
																			<ul class="inputList">
																				<li>
																					<input type="checkbox" class="inputCheckbox" value="Y" name="nofollow" id="nofollow" />
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
																	<div class="options last">
																		<fieldset>
																			<p>
																				<label for="meta_description_overwrite">Omschrijving pagina</label>
																				<span class="helpTxt">De pagina-omschrijving die wordt getoond in de resultaten van zoekmachines. Hou het kort en krachtig.</span>
																			</p>
																			<ul class="inputList">

																				<li>
																					<input type="checkbox" class="inputCheckbox" value="Y" name="meta_description_overwrite" id="meta_description_overwrite"/>
																					<input type="text" class="inputText inputDisabled" maxlength="255" value="" name="meta_description" id="meta_description"/>
																				</li>
																			</ul>
																		
																			<p>
																				<label for="meta_keywords_overwrite">Sleutelwoorden pagina</label>
																				<span class="helpTxt">De sleutelwoorden (<em>keywords</em>) die deze pagina omschrijven.</span>

																			</p>
																		
																			<ul class="inputList">
																				<li>
																					<input type="checkbox" class="inputCheckbox" value="Y" name="meta_keywords_overwrite" id="meta_keywords_overwrite"/>
																					<input type="text" class="inputText inputDisabled" maxlength="255" value="" name="meta_keywords" id="meta_keywords"/>
																				</li>
																			</ul>
																		
																			<p>
																				<label for="meta_custom">Extra metatags</label>

																				<span class="helpTxt">Laat toe om extra, op maat gemaakte metatags toe te voegen.</span>
																				<textarea rows="8" cols="40"></textarea>
																			</p>
																		</fieldset>
																	</div>
																</div>
																
																<div id="seoUrl" class="box boxLevel2">
																	<div class="heading">

																		<h3>URL</h3>
																	</div>
																	<div class="options last">
																		
																		<label for="url_overwrite">Aangepaste URL</label>
																		<span class="helpTxt">Vervang de automatisch gegenereerde URL door een zelfgekozen URL.</span>

																		<ul class="inputList">
																			<li>

																				<input type="checkbox" class="inputCheckbox" value="Y" name="url_overwrite" id="url_overwrite"/>
																				<span id="urlFirstPart">http://www.abconcerts.be/nl/</span>
																				<input type="text" class="inputText inputDisabled" maxlength="255" value="" name="url" id="url"/>
																			</li>
																		</ul>

																	</div>
																</div>
																
															</td>

														</tr>

													</table>

												</div>
											</div>

											<div class="collapseBox" id="versions">
												<div class="collapseBoxHeading">
													<div class="buttonHolderSingle">

														<a href="#" class="button icon iconCollapsed iconOnly"><span><span><span>+</span></spa></span></a>
													</div>
													<h4><a href="#">Version History</a></h4>
												</div>
												
												<div class="options" style="display: none;">

													<div class="oneLiner">
														<h3 class="floater">Page versions</h3>

														<abbr title="De 20 laatst opgeslagen paginaversies worden hier bijgehouden. 'Gebruik deze versie' opent een vroegere versie. De huidige versie wordt pas overschreven als je de pagina opslaat." class="help floater">(?)</abbr>
													</div>
													
													<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
														<thead>
															<tr>
																<th width="20%" class="sortable sorted">
																	<a href="#">By</a>
																</th>

																<th width="40%" class="sortable">
																	<a href="#">Date</a>
																</th>
																<th width="40%" colspan="2">
																	&nbsp;<!-- action -->
																</th>
															</tr>
														</thead>

														<tbody>
															<tr>
																<td>
																	<div class="user">
																		<a href="#">
																		<img src="images/avatars/fun/mushimushi.png" />
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
																		<img src="images/avatars/fun/mushimush2.png" />
																		&#x3069;&#x30FC;&#x3082;&#x304F;&#x3093;

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
																		<img src="images/avatars/fun/mushimush2.png" />
																		&#x3069;&#x30FC;&#x3082;&#x304F;&#x3093;
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
																		<img src="images/avatars/fun/mushimush2.png" />
																		&#x3069;&#x30FC;&#x3082;&#x304F;&#x3093;
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

											<div class="collapseBox" id="security">

												<div class="collapseBoxHeading">
													<div class="buttonHolderSingle">
														<a href="#" class="button icon iconCollapsed iconOnly"><span><span><span>+</span></spa></span></a>
													</div>
													<h4><a href="#">Permissions</a></h4>
												</div>
												
												<div class="options" style="display: none;">
													<!-- @todo --> ???
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
									<div class="options last">
										<div class="buttonHolder">
											<a href="../default_www/index.html" class="button icon iconZoom" target="_blank"><span><span><span>Preview</span></span></span></a>
										</div>
									</div>
									<div class="options">

										<ul class="inputList">
											<li>
												<input type="radio" id="visibilityPublic" class="inputRadio"  name="visibilityOptions" checked="checked" />
												<label for="visibilityPublic">Visible</label>
											</li>
											<li>
												<input type="radio" id="visibilityPrivate" class="inputRadio" name="visibilityOptions" />
												<label for="visibilityPrivate">Not visible</label>

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
														<a href="#" class="button mainButton"><span><span><span>Save</span></span></span></a>
													</div>
												</td>
											</tr>
										</table>

									</div>
								</div>
								
								<div class="box" id="template">
									<div class="heading">
										<h4>Template: About</h4>
										<div class="buttonHolderRight">
											<a href="#" class="button icon iconEdit iconOnly" id="editTemplate"><span><span><span>Edit</span></span></span></a>
										</div>

									</div>
									<div class="options last">
										
										<!-- [A,B],[C,D,0],[E,E,0] -->
										
										<div class="templateVisual current">
											<table border="0" cellspacing="2" cellpadding="2">
												<tr>
													<td class="selected"><a href="#" title="Main content" rel="contentA">A</a></td>
													<td><a href="#" title="Blog" rel="contentB">B</a></td>

												</tr>
											</table>
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td><a href="#" title="ZijImage" rel="contentC">C</a></td>
													<td><a href="#" title="Win win win!" rel="contentD">D</a></td>
													<td></td>
												</tr>

											</table>
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td colspan="2"><a href="#" title="Video" rel="contentE">E</a></td>
													<td></td>
												</tr>
											</table>
										</div>

										
										<table id="templateDetails" class="infoGrid" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<th>A</th>
												<td>Main content</td>
											</tr>
											<tr>
												<th>B</th>

												<td><a href="#">Blog</a></td>
											</tr>
											<tr>
												<th>C</th>
												<td><a href="#">Side image</a></td>
											</tr>
											<tr>

												<th>D</th>
												<td><a href="#">Win win win!</a></td>
											</tr>
											<tr>
												<th>E</th>
												<td><a href="#">Video</a></td>
											</tr>

										</table>
										
									</div>
								</div>
								
								
								<!-- <div id="authors" class="box">
									<div class="heading">
										<h4>Author(s)</h4>
									</div>
									<div class="options">
										<ul>
											<li>
												<div class="avatarAndNickName">
													<a href="#">
														<img src="images/avatars/fun/mushimushi.png" width="24" height="24" alt="Mushimush2">
														<span>You</span>
													</a>
												</div>
											</li>
											<li>
												<div class="avatarAndNickName">
													<a href="#">
														<img src="images/avatars/fun/mushimush2.png" width="24" height="24" alt="Mushimush2">
														<span>Domo Kun</span>
													</a>
												</div>
											</li>
										</ul>
										<div class="buttonHolderRight">
											<a href="#" class="button">
												<b>&nbsp;</b>
												<span>Edit</span>
												<i>&nbsp;</i>
											</a>
										</div>
									</div>
								</div> -->

								<div id="tags" class="box">
									<div class="heading">
										<h4>Tags</h4>
									</div>

									<div class="options last">
										<!-- <label for="addTag">Add tags:</label> -->
										<div class="oneLiner">
											<p><input type="text" class="inputText" id="addTag" /></p>
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

								
							</td>
						
						
						</tr>
					</table>
					
					<div id="fullwidthOptions">
						<a href="#" class="button linkButton icon iconDelete"><span><span><span>Delete page</span></span></span></a>
						<div class="buttonHolderRight">

							<a href="#" class="button mainButton"><span><span><span>Save</span></span></span></a>
						</div>
					</div>

			</form>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}