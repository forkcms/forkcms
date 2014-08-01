{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblBlog|ucfirst}: {$msgEditCommentOn|sprintf:{$itemTitle}}</h2>
	<div class="buttonHolderRight">
		<a href="{$SITE_URL}{$itemURL}" class="button icon iconZoom previewButton targetBlank">
			<span>{$lblView|ucfirst}</span>
		</a>
	</div>
</div>

{form:editComment}
	<div class="box">
		<div class="heading">
			<h3>{$lblComment|ucfirst}</h3>
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
