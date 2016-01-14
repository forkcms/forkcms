{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:txtTitleError} has-error{/option:txtTitleError}">
        <label for="title">{$lblTitle|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
      </div>
      <div class="form-group{option:txtTextError} has-error{/option:txtTextError}">
        <label for="text">
          {$lblContent|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtText} {$txtTextError}
      </div>
      {option:ddmTemplate}
        <div class="form-group{option:ddmTemplateError} has-error{/option:ddmTemplateError}">
          <label for="template">{$lblTemplate|ucfirst}</label>
          {$ddmTemplate} {$ddmTemplateError}
        </div>
      {/option:ddmTemplate}
      <div class="form-group">
        <label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite|ucfirst}</label>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
