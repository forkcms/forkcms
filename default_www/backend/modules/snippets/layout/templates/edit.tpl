{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
			<td id="contentHolder">
				<div id="statusBar">
					<p class="breadcrumb">Snippets &gt; {$msgHeaderEdit|sprintf:{$title}}</p>
				</div>

				<div class="inner">
					{option:usingRevision}<p class="warning">{$msgUsingARevision}</p>{/option:usingRevision}

					{form:edit}
					<label for="title">{$lblTitle|ucfirst}</label>
					<p>{$txtTitle} {$txtTitleError}</p>


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
							<h3>{$lblRevisions|ucfirst}</h3>
							<p>{$msgRevisionsExplanation}</p>
							{option:revisions}{$revisions}{/option:revisions}
							{option:!revisions}{$msgNoRevisions}{/option:!revisions}
						</div>
					</div>

					<div class="fullwidthOptions">
						
						<a href="{$var|geturl:'delete'}&id={$recordid}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
							<span><span><span>{$lblDelete|ucfirst}</span></span></span>
						</a>

						<div class="buttonHolderRight">
							{$btnSave}
						</div>
					</div>

					<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
						<p>
							<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
							{$msgConfirmDelete|sprintf:{$recordtitle}}
						</p>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}