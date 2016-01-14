{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblTranslations|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showLocaleAdd}
        <a href="{$var|geturl:'add'}{$filter}" class="btn btn-default jsButtonAdd">
          <span class="fa fa-plus"></span>&nbsp;
          <span>{$lblAdd|ucfirst}</span>
        </a>
        {/option:showLocaleAdd}
        {option:showLocaleExport}
        <a href="{$var|geturl:'export'}{$filter}" class="btn btn-default jsButtonExport">
          <span class="fa fa-upload"></span>&nbsp;
          <span>{$lblExport|ucfirst}</span>
        </a>
        {/option:showLocaleExport}
        {option:showLocaleImport}
        <a href="{$var|geturl:'import'}{$filter}" class="btn btn-default jsButtonImport">
          <span class="fa fa-download"></span>&nbsp;
          <span>{$lblImport|ucfirst}</span>
        </a>
        {/option:showLocaleImport}
      </div>
    </div>
  </div>
</div>

<div class="row fork-module-content">
  <div class="col-md-12">
    {form:filter}
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group{option:ddmApplicationError} has-error{/option:ddmApplicationError}">
                <label for="application">{$lblApplication|ucfirst}</label>
                {$ddmApplication} {$ddmApplicationError}
              </div>
              <div class="form-group{option:ddmModuleError} has-error{/option:ddmModuleError}">
                <label for="module">{$lblModule|ucfirst}</label>
                {$ddmModule} {$ddmModuleError}
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>{$lblTypes|ucfirst}</label>
                {option:type}
                  <ul class="list-unstyled">
                    {iteration:type}
                      <li class="checkbox">
                        <label for="{$type.id}">{$type.chkType} {$type.label|ucfirst}</label>
                      </li>
                    {/iteration:type}
                  </ul>
                {/option:type}
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>{$lblLanguages|ucfirst}</label>
                {option:language}
                  <ul class="list-unstyled">
                    {iteration:language}
                      <li class="checkbox">
                        <label for="{$language.id}">{$language.chkLanguage} {$language.label|ucfirst}</label>
                      </li>
                    {/iteration:language}
                  </ul>
                {/option:language}
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
                <label for="name">
                  {$lblReferenceCode|ucfirst}&nbsp;
                  <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpName}"></abbr>
                </label>
                {$txtName} {$txtNameError}
              </div>
              <div class="form-group{option:txtValueError} has-error{/option:txtValueError}">
                <label for="value">
                  {$lblValue|ucfirst}&nbsp;
                  <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpValue}"></abbr>
                </label>
                {$txtValue} {$txtValueError}
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <div class="btn-toolbar">
            <div class="btn-group pull-right">
              <button id="search" class="btn btn-primary" type="submit" name="search">
                <span class="fa fa-refresh"></span>&nbsp;
                {$lblUpdateFilter|ucfirst}
              </button>
            </div>
          </div>
        </div>
      </div>
    {/form:filter}
  </div>
</div>
{option:dgLabels}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblLabels|ucfirst}
        </h3>
      </div>
      {$dgLabels}
    </div>
  </div>
</div>
{/option:dgLabels}
{option:dgMessages}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblMessages|ucfirst}
        </h3>
      </div>
      {$dgMessages}
    </div>
  </div>
</div>
{/option:dgMessages}
{option:dgErrors}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblErrors|ucfirst}
        </h3>
      </div>
      {$dgErrors}
    </div>
  </div>
</div>
{/option:dgErrors}
{option:dgActions}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblActions|ucfirst}&nbsp;
          <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpActionValue}"></abbr>
        </h3>
      </div>
      {$dgActions}
    </div>
  </div>
</div>
{/option:dgActions}
{option:hasSubmissions}
  {option:noItems}
    <div class="row fork-module-content">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <p>{$msgNoItemsFilter|sprintf:{$addURL}}</p>
          </div>
        </div>
      </div>
    </div>
  {/option:noItems}
{/option:hasSubmissions}
{option:!hasSubmissions}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <p>{$msgStartSearch}</p>
        </div>
      </div>
    </div>
  </div>
{/option:!hasSubmissions}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
