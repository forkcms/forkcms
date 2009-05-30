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
		
		<div class="tabsContent">
			<div id="first" class="tabTab">
				{form:edit}
					<fieldset>
						<label for="title">{$lblTitle|ucfirst}</label>
						<p>{$txtTitle} {$txtTitleError}</p>
			
						<label for="content">{$lblContent|ucfirst}</label>
						<p>{$txtContent} {$txtContentError}</p>
						
						<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label></p>
						
						<p>{$btnSubmit}</p>
					</fieldset>
				{/form:edit}
				<p>
					<a href="{$var|geturl:delete}?id={$id}" class="askConfirmation" rel="{$msgConfirmDelete|sprintf:{$title}}" title="{$lblDelete}">{$lblDelete}</a>
				</p>
			</div>
			
			<div id="revisions" class="tabTab">
				<h3>{$lblRevisions|ucfirst}</h3>
				<p>{$msgRevisionsExplanation}</p>
				{option:revisions}{$revisions}{/option:revisions}
				{option:!revisions}{$msgNoRevisions}{/option:!revisions}
			</div>
		</div>
	</div>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}