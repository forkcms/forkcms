{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAddTemplate|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-inline form-group">
        <p class="text-info">{$msgHelpTemplateLocation}</p>
        <label for="file">{$msgPathToTemplate|ucfirst}</label>
        <label for="theme" class="hide">{$lblTheme|ucfirst}</label>
        {$ddmTheme}<small><code>/Core/Layout/Templates/</code></small>{$txtFile} {$ddmThemeError} {$txtFileError}
      </div>
      <div class="form-group">
        <label for="label">{$lblLabel|ucfirst}</label>
        {$txtLabel} {$txtLabelError}
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblPositions|ucfirst}</h3>
        </div>
        <div id="positions" class="panel-body">
          {iteration:positions}
          <div class="form-group jsPosition"{option:!positions.i} style="display: none"{/option:!positions.i}>
            <div class="row">
              {* Title & button to delete this position *}
              <div class="col-md-2">
                <div class="form-group">
                  <label for="position{$positions.i}">
                    <span>{$lblPosition|ucfirst}</span>
                    <a href="#" class="btn text-danger jsDeletePosition" title="{$lblDeletePosition|ucfirst}">
                      <span class="glyphicon glyphicon-trash"></span>&nbsp;
                    </a>
                  </label>
                </div>
              </div>
              {* Position name *}
              <div class="col-md-10">
                <div class="form-group">
                  {$positions.txtPosition}
                  {$positions.txtPositionError}
                </div>
              </div>
            </div>
            <div class="row jsBlocks">
              {* Default blocks for this position *}
              {option:positions.blocks}
              {iteration:positions.blocks}
              <div class="col-md-10 col-md-offset-2 jsBlock">
                <div class="form-inline form-group">
                  <div class="form-group">
                    {$positions.blocks.ddmType}
                    {$positions.blocks.ddmTypeError}
                  </div>
                  {* Button to remove block from this position *}
                  <a href="#" class="btn text-danger jsDeleteBlock" title="{$lblDeleteBlock|ucfirst}">
                    <span class="glyphicon glyphicon-trash"></span>&nbsp;
                  </a>
                </div>
              </div>
              {/iteration:positions.blocks}
              {/option:positions.blocks}
              <div class="col-md-10 col-md-offset-2">
                <div class="btn-toolbar">
                  <div class="btn-group" role="group">
                    {* Button to add new default block to this position *}
                    <a href="#" class="btn btn-default jsAddBlock">
                      <span class="glyphicon glyphicon-plus"></span>&nbsp;
                      <span>{$lblAddBlock|ucfirst}</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/iteration:positions}
          {* Button to add new position *}
          <div class="btn-toolbar">
            <div class="btn-group" role="group">
              <a href="#" class="btn btn-primary jsAddPosition">
                <span class="glyphicon glyphicon-plus"></span>&nbsp;
                <span>{$lblAddPosition|ucfirst}</span>
              </a>
            </div>
          </div>
          {option:formErrors}
          <p class="text-danger">{$formErrors}</p>
          {/option:formErrors}
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <label for="format">{$lblLayout|ucfirst}</label>
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <p class="text-info">{$msgHelpTemplateFormat}</p>
            {$txtFormat} {$txtFormatError}
          </div>
          <div>
            {$msgHelpPositionsLayout}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblStatus|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="active">{$chkActive} {$lblActive|ucfirst}</label> {$chkActiveError}
              </li>
              <li class="checkbox">
                <label for="default">{$chkDefault} {$lblDefault|ucfirst}</label> {$chkDefaultError}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" name="add" class="btn btn-primary" type="submit">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;
            {$lblSave|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
