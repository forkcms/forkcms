{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:editComment}
	<div class="box">
		<div class="heading">
			<h3>{$lblBlog|ucfirst}: {$msgEditComment}</h3>
		</div>
		<div class="options">
			<p>
				<label for="author">{$lblAuthor|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtAuthor} {$txtAuthorError}
			</p>
			<p>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail} {$txtEmailError}
			</p>
			<p>
				<label for="website">{$lblWebsite|ucfirst}</label>
				{$txtWebsite} {$txtWebsiteError}
			</p>
			<p>
				<label for="text">{$lblText|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtText} {$txtTextError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:editComment}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}