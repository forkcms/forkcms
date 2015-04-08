{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditCommentOn|sprintf:{$itemTitle}|ucfirst}</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$SITE_URL}{$itemURL}" class="btn btn-default" target="_blank">
          <span class="glyphicon glyphicon-search"></span>
          <span>{$lblView|ucfirst}</span>
        </a>
      </div>
    </div>
  </div>
</div>
{form:editComment}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="author">
          {$lblAuthor|ucfirst}
          <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
        </label>
        {$txtAuthor} {$txtAuthorError}
      </div>
      <div class="form-group">
        <label for="email">
          {$lblEmail|ucfirst}
          <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
        </label>
        {$txtEmail} {$txtEmailError}
      </div>
      <div class="form-group">
        <label for="website">{$lblWebsite|ucfirst}</label>
        {$txtWebsite} {$txtWebsiteError}
      </div>
      <div class="form-group">
        <label for="text">
          {$lblText|ucfirst}
          <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
        </label>
        {$txtText} {$txtTextError}
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:editComment}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
