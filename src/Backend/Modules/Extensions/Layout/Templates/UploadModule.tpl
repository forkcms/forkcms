{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblUploadModule|ucfirst}</h2>
  </div>
</div>
{option:zlibIsMissing}
<div class="row fork-module-messages">
  <div class="col-md-12">
    <div class="alert alert-danger" role="alert">
      {$msgZlibIsMissing}
    </div>
  </div>
</div>
{/option:zlibIsMissing}
{option:notWritable}
<div class="row fork-module-messages">
  <div class="col-md-12">
    <div class="alert alert-danger" role="alert">
      {$msgThemesNotWritable}
    </div>
  </div>
</div>
{/option:notWritable}
{option:!zlibIsMissing}
{form:upload}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:fileFileError} has-error{/option:fileFileError}">
        <label for="file" class="control-label">
          {$lblFile|ucfirst}&nbsp;
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$fileFile} {$fileFileError}
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="importButton" type="submit" name="add" class="btn btn-primary">{$lblInstall|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:upload}
{/option:!zlibIsMissing}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
