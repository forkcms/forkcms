{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditTranslation|ucfirst|sprintf:{$name}}</h2>
  </div>
</div>
{form:edit}
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
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
