{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblEditGroup|ucfirst}</h2>
  </div>
</div>
{form:edit}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="name">
          {$lblName|ucfirst}
          <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
        </label>
        {$txtName} {$txtNameError}
      </div>
      <div class="form-group">
        <p class="text-info">{$msgDefaultGroup|ucfirst}</p>
        <ul class="list-unstyled">
          {iteration:default}
          <li class="radio">
            <label for="{$default.id}">{$default.rbtDefault} {$default.label}</label>
          </li>
          {/iteration:default}
        </ul>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="saveButton" type="submit" name="save" class="btn btn-primary">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;
            {$lblSave|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
