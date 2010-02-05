{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
			<td id="contentHolder">
				<div id="statusBar">
					<p class="breadcrumb">{$lblSnippets|ucfirst} &gt; {$msgEditWithItem|sprintf:{$title}}</p>
				</div>
				{option:usingRevision}
				<div class="singleMessage warningMessage">
					<p>{$msgUsingARevision}</p>
				</div>
				{/option:usingRevision}

				{option:formError}
				<div id="report">
					<div class="singleMessage errorMessage">
						<p>{$errFormError}</p>
					</div>
				</div>
				{/option:formError}

				<div class="inner">
					{form:edit}
						<p>
							<label for="title">{$lblTitle|ucfirst}</label>
							{$txtTitle} {$txtTitleError}
						</p>

						<div class="tabs">
							<ul>
								<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
								<li class="notImportant"><a href="#tabRevisions">{$lblRevisions|ucfirst}</a></li>
							</ul>

							<div id="tabContent">
								<fieldset>
									<label for="content">{$lblContent|ucfirst}</label>
									<p>{$txtContent} {$txtContentError}</p>
									<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label></p>
								</fieldset>
							</div>

							<div id="tabRevisions">
								<div class="datagridHolder">
									<div class="tableHeading">
										<div class="oneLiner">
											<h3 class="floater">{$lblRevisions|ucfirst}</h3>
											<abbr class="help floater" title="{$msgHelpRevisions}">(?)</abbr>
										</div>
									</div>

									{option:revisions}{$revisions}{/option:revisions}
									{option:!revisions}{$msgNoRevisions}{/option:!revisions}
								</div>
							</div>
						</div>

						<div class="fullwidthOptions">
							<a href="{$var|geturl:'delete'}&id={$id}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
								<span><span><span>{$lblDelete|ucfirst}</span></span></span>
							</a>

							<div class="buttonHolderRight">
								{$btnSave}
							</div>
						</div>

						<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
							<p>
								<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
								{$msgConfirmDelete|sprintf:{$title}}
							</p>
						</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}