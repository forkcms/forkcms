{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" width="264">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td id="treeHolder">
						<div id="treeOptions">
							<div class="buttonHolder">
								<a href="{$var|geturl:'index'}" class="button icon iconBack iconOnly"><span><span><span>{$lblBack|ucfirst}</span></span></span></a>
								<a href="{$var|geturl:'add'}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
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
			{option:!formError}
			<div id="report" class="hidden">
				<div class="singleMessage">
					<p>&nbsp;</p>
				</div>
			</div>
			{/option:!formError}

			<div class="inner">

				{form:add}
					{$txtTitle} {$txtTitleError}
					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$SITE_URL}">{$SITE_URL}</a></span>
							</p>
						</div>
					</div>
					<div id="tabs" class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
							<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
							<li><a href="#tabTemplate">{$lblTemplate|ucfirst}</a></li>
							<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td>
											<div id="editContent">
												{iteration:blocks}
												<div id="block-{$blocks.index}" class="contentBlock">
													<div class="contentTitle selected hover">
														<table border="0" cellpadding="0" cellspacing="0">
															<tbody><tr>
																<td class="numbering">{$blocks.index}</td>
																<td>
																	<div class="oneLiner">
																		<p><span class="blockName">{$blocks.name}</span></p>
																		<p>{$blocks.ddmExtraId}</p>
																	</div>
																</td>
															</tr>
														</tbody></table>
													</div>
													<div class="editContent">
														<fieldset id="blockContentHTML-{$blocks.index}">
															{$blocks.txtHTML}
														</fieldset>
														<fieldset id="blockContentExtra-{$blocks.index}">
															<p>&nbsp;</p>
														</fieldset>
													</div>
												</div>
												{/iteration:blocks}
											</div>
										</td>
										<td id="pagesSide">
											<div id="publishOptions" class="box">
												<div class="heading">
													<h3>{$lblPublish|ucfirst}</h3>
												</div>
												<!-- @later
												<div class="options">
													<div class="buttonHolder">
														<a href="#" class="button icon iconZoom previewButton" target="_blank">
															<span><span><span>{$lblPreview|ucfirst}</span></span></span>
														</a>
													</div>
												</div>
												 -->
												<div class="options">
													<ul class="inputList">
														{iteration:hidden}
														<li>
															{$hidden.rbtHidden}
															<label for="{$hidden.id}">{$hidden.label}</label>
														</li>
														{/iteration:hidden}
													</ul>
												</div>
												<div class="footer">
													<table border="0" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td><p>&nbsp;</p></td>
																<td>
																	<div class="buttonHolderRight">
																		<input id="save" class="inputButton button" type="submit" name="save" value="{$lblSave|ucfirst}" />
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>

											<div class="box" id="template">
												<div class="heading">
													<h4>{$lblTemplate|ucfirst}: {$templatelabel}</h4>
													<div class="buttonHolderRight">
														<a href="#tabTemplate" class="tabSelect button icon iconEdit iconOnly">
															<span><span><span>{$lblEdit|ucfirst}</span></span></span>
														</a>
													</div>
												</div>
												<div class="options">
													<!-- [A,B],[C,D,0],[E,E,0] -->
													<div id="templateVisual" class="templateVisual current">
														{$templatehtml}
													</div>

													<table id="templateDetails" class="infoGrid" border="0" cellpadding="0" cellspacing="0">
														<tbody>
														{iteration:blocks}
															<tr>
																<th class="numbering">{$blocks.index}</th>
																<td class="blockName">{$blocks.name}</td>
															</tr>
														{/iteration:blocks}
													</tbody>
												</table>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="tabSEO">
							<div class="box boxLevel2">
								<div class="heading">
									<h3>{$lblTitles|ucfirst}</h3>
								</div>
								<div class="options">
									<p>
										<label for="meta_pagetitle_overwrite">{$lblPageTitle|ucfirst}</label>
										<span class="helpTxt">{$msgHelpPageTitle}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkPageTitleOverwrite}
											{$txtPageTitle} {$txtPageTitleError}
										</li>
									</ul>
									<p>
										<label for="navigation_title_overwrite">{$lblNavigationTitle|ucfirst}</label>
										<span class="helpTxt">{$msgHelpNavigationTitle}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkNavigationTitleOverwrite}
											{$txtNavigationTitle} {$txtNavigationTitleError}
										</li>
									</ul>
								</div>
							</div>

							<div id="seoNofollow" class="box boxLevel2">
								<div class="heading">
									<h3>Nofollow</h3>
								</div>
								<div class="options">
									<fieldset>
										<p class="helpTxt">{$msgHelpNoFollow}</p>
										<ul class="inputList">
											<li>
												{$chkNoFollow}
												<label for="noFollow">{$msgActivateNoFollow|ucfirst}</label>
											</li>
										</ul>
									</fieldset>
								</div>
							</div>

							<div id="seoMeta" class="box boxLevel2">
								<div class="heading">
									<h3>{$lblMetaInformation|ucfirst}</h3>
								</div>
								<div class="options">
									<p>
										<label for="meta_description_overwrite">{$lblMetaDescription|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaDescription}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkMetaDescriptionOverwrite}
											{$txtMetaDescription} {$txtMetaDescriptionError}
										</li>
									</ul>

									<p>
										<label for="meta_keywords_overwrite">{$lblMetaKeywords|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaKeywords}</span>
									</p>

									<ul class="inputList">
										<li>
											{$chkMetaKeywordsOverwrite}
											{$txtMetaKeywords} {$txtMetaKeywordsError}
										</li>
									</ul>

									<p>
										<label for="meta_custom">{$lblMetaCustom|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaCustom}</span>
										{$txtMetaCustom} {$txtMetaCustomError}
									</p>
								</div>
							</div>

							<div class="box boxLevel2">
								<div class="heading">
									<h3>{$lblURL}</h3>
								</div>
								<div class="options">

									<label for="url_overwrite">{$lblCustomURL|ucfirst}</label>
									<span class="helpTxt">{$msgHelpMetaURL}</span>

									<ul class="inputList">
										<li>
											{$chkUrlOverwrite}
											<span id="urlFirstPart">{$SITE_URL}</span>{$txtUrl} {$txtUrlError}
										</li>
									</ul>

								</div>
							</div>
						</div>
						<div id="tabTemplate">
							<ul class="inputList" id="templateList">
							{iteration:templates}
								<li>
									<input type="radio" id="template{$templates.id}" value="{$templates.id}" name="template_id" class="inputRadio"{option:templates.checked} checked="checked"{/option:templates.checked} />
									<label for="template{$templates.id}">{$templates.label}</label>
									<div class="templateVisual current">
										{$templates.html}
									</div>
								</li>
							{/iteration:templates}
							</ul>
						</div>
						<div id="tabTags">
							<div class="box boxLevel2">
								<div class="heading">
									<h3>Tags</h3>
								</div>
								<div class="options">
									{$txtTags} {$txtTagsError}
								</div>
							</div>
						</div>
					</div>
					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="add" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var templates = {};
	{iteration:templates}templates[{$templates.id}] = {$templates.json};{/iteration:templates}

	var extraData = {};
	{iteration:extras}extraData[{$extras.id}] = {$extras.json}; {/iteration:extras}
</script>


{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}