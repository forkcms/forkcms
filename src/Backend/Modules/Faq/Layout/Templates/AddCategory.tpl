{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAddCategory|ucfirst}</h2>
  </div>
</div>
{form:addCategory}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="title">
          {$lblTitle|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtTitle} {$txtTitleError}
      </div>
      {option:detailURL}
      <p><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl"></span></a></p>
      {/option:detailURL}
      {option:!detailURL}
      <p class="text-warning">{$errNoModuleLinked}</p>
      {/option:!detailURL}
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabSEO" aria-controls="seo" role="tab" data-toggle="tab">{$lblSEO|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/Layout/Templates/Seo.tpl}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAddCategory|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:addCategory}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
