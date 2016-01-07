{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-actions">
  <div class="col-md-12">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="name">
          {$lblReferenceCode|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        <p class="text-info">{$msgHelpAddName}</p>
        {$txtName} {$txtNameError}
      </div>
      <div class="form-group">
        <label for="value">
          {$lblTranslation|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        <p class="text-info">{$msgHelpAddValue}</p>
        {$txtValue} {$txtValueError}
      </div>
      <div class="form-group">
        <label for="language">{$lblLanguage|ucfirst}</label>
        {$ddmLanguage} {$ddmLanguageError}
      </div>
      <div class="form-group">
        <label for="application">{$lblApplication|ucfirst}</label>
        {$ddmApplication} {$ddmApplicationError}
      </div>
      <div class="form-group">
        <label for="module">{$lblModule|ucfirst}</label>
        {$ddmModule} {$ddmModuleError}
      </div>
      <div class="form-group">
        <label for="type">{$lblType|ucfirst}</label>
        {$ddmType} {$ddmTypeError}
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
