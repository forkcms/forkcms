{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

<h2>{$msgHeaderEdit|sprintf:{$title}}</h2>
{option:usingRevision}<p class="warning">{$msgUsingARevision}</p>{/option:usingRevision}
<div class="tabs">
	<div class="tabsNavigation">
		<ul>
			<li><a href="{$var|geturl}?id={$id}">{$lblContent|ucfirst}</a></li>
			<li class="notImportant"><a href="{$var|geturl}?id={$id}#revisions">{$lblRevisions|ucfirst}</a></li>
		</ul>
	</div>
	
	{form:edit}
		<div class="tabsContent">
			<div id="first" class="tabTab">
				<fieldset>
					<label for="title">{$lblTitle|ucfirst}</label>
					<p>{$txtTitle} {$txtTitleError}</p>
		
					<label for="content">{$lblContent|ucfirst}</label>
					<p>{$txtContent} {$txtContentError}</p>
					<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label></p>
					
				</fieldset>
			</div>
			
			<div id="revisions" class="tabTab">
				<h3>{$lblRevisions|ucfirst}</h3>
				<p>{$msgRevisionsExplanation}</p>
				{option:revisions}{$revisions}{/option:revisions}
				{option:!revisions}{$msgNoRevisions}{/option:!revisions}
			</div>
		</div>
	
		<p>
			{$btnSubmit}
			<a href="{$var|geturl:delete}?id={$id}" class="askConfirmation" title="{$lblDelete|ucfirst}">
				<span style="display: none" class="message" title="{$lblDelete|ucfirst}">{$msgConfirmDelete|sprintf:{$title}}</span>
				{$lblDelete|ucfirst}
			</a>
		</p>
	{/form:edit}
</div>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}