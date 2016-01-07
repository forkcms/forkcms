{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAddEmail|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="email">{$lblEmailAddress|ucfirst}</label>
        {$txtEmail} {$txtEmailError}
      </div>
      <div class="form-group">
        {option:groups}
        {option:chkGroupsError}
        <p class="text-danger">{$chkGroupsError}</p>
        {/option:chkGroupsError}
        <ul class="list-unstyled">
          {iteration:groups}
          <li class="checkbox">
            <label for="{$groups.id}">{$groups.chkGroups} {$groups.label|ucfirst}</label>
          </li>
          {/iteration:groups}
        </ul>
        {/option:groups}
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
