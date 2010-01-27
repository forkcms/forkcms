{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

<h2>{$msgHeaderAdd}</h2>
{form:add}
	<fieldset>
		<label for="title">{$lblTitle|ucfirst}</label>
		<p>{$txtTitle} {$txtTitleError}</p>

		<label for="content">{$lblContent|ucfirst}</label>
		<p>{$txtContent} {$txtContentError}</p>

		<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$msgVisibleOnSite}</label></p>

		<p>{$btnSave}</p>
	</fieldset>
{/form:add}

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}