{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$msgEditCommentOn|sprintf:{$itemTitle}|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$SITE_URL}{$itemURL}" class="btn btn-default" target="_blank">
          <span class="fa fa-eye"></span>
          <span>{$lblView|ucfirst}</span>
        </a>
      </div>
    </div>
  </div>
</div>
{form:editComment}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:txtAuthorError} has-error{/option:txtAuthorError}">
        <label for="author" class="control-label">
          {$lblAuthor|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtAuthor} {$txtAuthorError}
      </div>
      <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
        <label for="email" class="control-label">
          {$lblEmail|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtEmail} {$txtEmailError}
      </div>
      <div class="form-group{option:txtWebsiteError} has-error{/option:txtWebsiteError}">
        <label for="website" class="control-label">{$lblWebsite|ucfirst}</label>
        {$txtWebsite} {$txtWebsiteError}
      </div>
      <div class="form-group{option:txtTextError} has-error{/option:txtTextError}">
        <label for="text" class="control-label">
          {$lblText|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtText} {$txtTextError}
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-success"><span class="fa fa-floppy-o"></span> {$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:editComment}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
