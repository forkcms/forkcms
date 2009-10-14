{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	<h2>{$msgHeaderAdd}</h2>

	<div id="pages">
		<div id="tree">
			TREE
		</div>
		<div id="form">
		{form:add}
			{$txtTemplateId}
			<label for="title">{$lblTitle|ucfirst}</label>
			<p>{$txtTitle} {$txtTitleError}</p>
			
			<label for="template">{$lblTemplate|ucfirst}</label>
			<p>{$ddmTemplateId} {$ddmTemplateIdError}</p>
			
			<div id="tabs" class="tabs">
				<ul>
					{iteration:blocks}<li><a href="#contentBlock-{$blocks.index}" id="tabsBlock-{$blocks.index}">&nbsp;</a></li>{/iteration:blocks}
				</ul>
				
				{iteration:blocks}
				<div id="contentBlock-{$blocks.index}">
					<fieldset>
						<label for="extra_id">{$lblExtra}</label>
						<p>{$blocks.ddmExtraId} {$blocks.ddmExtraIdError}</p>
						<label for="content">{$lblContent|ucfirst}</label>
						<p>{$blocks.txtHTML} {$blocks.txtHTMLError}</p>
					</fieldset>
				</div>
				{/iteration:blocks}
			</div>

			<p>{$btnSubmit}</p>

			<div>
				<fieldset>
					<legend>{$lblStatus}</legend>
					<ul>
						{iteration:hidden}<li><label for="{$hidden.id}"> {$hidden.rbtHidden} {$hidden.label}</label></li>{/iteration:hidden}
					</ul>
					{$rbtHiddenError}
				</fieldset>
				
				<fieldset>
					<legend>{$lblSEO}</legend>

					<label for="pageTitleOverwrite">{$lblPageTitle|ucfirst}</label>
					<p>{$chkPageTitleOverwrite} {$txtPageTitle} {$txtPageTitleError} {$chkPageTitleOverwriteError}</p>

					<label for="navigationTitleOverwrite">{$lblNavigationTitle|ucfirst}</label>
					<p>{$chkNavigationTitleOverwrite} {$txtNavigationTitle} {$txtNavigationTitleError} {$chkNavigationTitleOverwriteError}</p>
				</fieldset>

				<fieldset>
					<legend>{$lblMetaInformation}</legend>

					<label for="metaDescriptionOverwrite">{$lblMetaDescription|ucfirst}</label>
					<p>{$chkMetaDescriptionOverwrite} {$txtMetaDescription} {$txtMetaDescriptionError} {$chkMetaDescriptionOverwriteError}</p>

					<label for="metaKeywordsOverwrite">{$lblMetaKeywords|ucfirst}</label>
					<p>{$chkMetaKeywordsOverwrite} {$txtMetaKeywords} {$txtMetaKeywordsError} {$chkMetaKeywordsOverwriteError}</p>

					<label for="metaCustom">{$lblMetaCustom|ucfirst}</label>
					<p>{$txtMetaCustom} {$txtMetaCustomError}</p>
				</fieldset>
				
				<fieldset>
					<legend>{$lblURL}</legend>

					<label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
					<p>{$chkUrlOverwrite} {$txtUrl} {$txtUrlError} {$chkUrlOverwriteError}</p>
				</fieldset>
				
			</div>
		{/form:add}
		</div>
	</div>
	
	<script type="text/javascript">
		var defaultTemplateId = {$defaultTemplateId};
		var templates = { {iteration:templates}'{$templates.id}' : {id: {$templates.id}, label: '{$templates.label}', path: '{$templates.path}', numberOfBlocks: {$templates.number_of_blocks}, names: new Array({$templates.namesString}) },{/iteration:templates} };
	</script>
	
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}