{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
			<td id="contentHolder">
				<div id="statusBar">
					<p class="breadcrumb">Snippets > {$msgHeaderEdit|sprintf:{$title}}</p>
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
						
					<p>
						{$btnSave}
						<a href="{$var|geturl:'delete'}&amp;id={$id}" class="askConfirmation" title="{$lblDelete|ucfirst}">
							<span style="display: none" class="message" title="{$lblDelete|ucfirst}">{$msgConfirmDelete|sprintf:{$title}}</span>
							{$lblDelete|ucfirst}
						</a>
					</p>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}