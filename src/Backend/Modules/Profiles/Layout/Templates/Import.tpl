{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblImport|ucfirst}</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showProfilesIndex}
        <a href="{$var|geturl:'index'}" class="btn btn-default" title="{$lblCancel|ucfirst}">
          <span class="glyphicon glyphicon-remove"></span>&nbsp;
          {$lblCancel|ucfirst}
        </a>
        {/option:showProfilesIndex}
        <a href="{$var|geturl:'export_template'}" class="btn btn-default" title="{$lblExportTemplate|ucfirst}">
          <span class="glyphicon glyphicon-export"></span>&nbsp;
          {$lblExportTemplate|ucfirst}
        </a>
      </div>
    </div>
  </div>
</div>
{form:import}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="group">{$lblGroup|ucfirst}</label>
        {$ddmGroup} {$ddmGroupError}
      </div>
      <div class="form-group">
        <label for="file">
          {$lblFile|ucfirst}&nbsp;
          <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
        </label>
        {$fileFile} {$fileFileError}
      </div>
      <div class="form-group">
        <ul class="list-unstyled">
          <li class="checkbox">
            <label>{$chkOverwriteExisting} {$msgOverwriteExisting}</label>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="importButton" type="submit" name="add" class="btn btn-primary">{$lblImport|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:import}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
