{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblEditEmail|ucfirst}</h2>
  </div>
</div>
{form:edit}
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
  {option:ddmSubscriptions}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblCustomFields|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label for="subscriptions">{$lblGroup|ucfirst}</label>
            {$ddmSubscriptions} {$ddmSubscriptionsError}
          </div>
        </div>
        {option:fields}
        <div class="panel-body">
          {iteration:fields}
          <div class="form-group">
            <label for="{$fields.name}">[{$fields.label}]</label>
            {$fields.txtField} {$fields.txtFieldError}
          </div>
          {/iteration:fields}
        </div>
        {/option:fields}
      </div>
    </div>
  </div>
  {/option:ddmSubscriptions}
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="saveButton" type="submit" name="save" class="btn btn-primary">
            <span class="fa fa-floppy-o"></span>&nbsp;
            {$lblSave|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:edit}
<script type="text/javascript">
  //<![CDATA[
    var variables = [];
    variables['email'] = '{$address.email}';
  //]]>
</script>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
