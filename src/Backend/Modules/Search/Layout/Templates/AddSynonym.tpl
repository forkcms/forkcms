{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblAddSynonym|ucfirst}</h2>
  </div>
  <div class="col-md-6">

  </div>
</div>
{form:addItem}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:txtTermError} has-error{/option:txtTermError}">
        <label for="term" class="control-label">
          {$lblTerm|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtTerm} {$txtTermError}
      </div>
      <div class="form-group{option:txtSynonymError} has-error{/option:txtSynonymError}">
        <div class="fakeP">
          <label for="addValue-synonym" class="control-label">
            {$lblSynonyms|ucfirst}
            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
          </label>
          <div class="itemAdder">
            {$txtSynonym} {$txtSynonymError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-success">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAddSynonym|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:addItem}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
