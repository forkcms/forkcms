{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_MODULES_PATH}/Dashboard/Layout/Templates/StructureStart.tpl}
<div id="dashboardWidgets" class="row fork-dashboard">
  <div class="col-md-12">
    <div id="editDashboardMessage" class="alert alert-info" role="alert" style="display:none;">
      {$msgHelpEditDashboard}
      <a href="#" id="doneEditingDashboard">{$lblDone|ucfirst}</a>
    </div>
  </div>
  <div class="col-md-4 fork-dashboard-column column">
    {iteration:leftColumn}
    <div class="fork-widget sortableWidget{option:leftColumn.hidden} isRemoved{/option:leftColumn.hidden}" data-module="{$leftColumn.module}" data-widget="{$leftColumn.widget}" data-title="{$leftColumn.title}"{option:leftColumn.hidden} style="display: none;"{/option:leftColumn.hidden}>
      <a href="#" class="editDashboardClose close ui-dialog-titlebar-close ui-corner-all" style="display: none;">&times;</a>
      {option:leftColumn.hidden}
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">{$leftColumn.title}</h2>
        </div>
        <div class="panel-body" style="display: none;">
          {$msgWillBeEnabledOnSave}
        </div>
      </div>
      {/option:leftColumn.hidden}
      {option:!leftColumn.hidden}
      {include:{$leftColumn.template}}
      {/option:!leftColumn.hidden}
    </div>
    {/iteration:leftColumn}
    &#160;
  </div>
  <div class="col-md-4 fork-dashboard-column column">
    {iteration:middleColumn}
    <div class="fork-widget sortableWidget{option:middleColumn.hidden} isRemoved{/option:middleColumn.hidden}" data-module="{$middleColumn.module}" data-widget="{$middleColumn.widget}" data-title="{$middleColumn.title}"{option:middleColumn.hidden} style="display: none;"{/option:middleColumn.hidden}>
      <a href="#" class="editDashboardClose close ui-dialog-titlebar-close ui-corner-all" style="display: none;">&times;</a>
      {option:middleColumn.hidden}
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">{$middleColumn.title}</h2>
        </div>
        <div class="panel-body" style="display: none;">
          {$msgWillBeEnabledOnSave}
        </div>
      </div>
      {/option:middleColumn.hidden}
      {option:!middleColumn.hidden}
      {include:{$middleColumn.template}}
      {/option:!middleColumn.hidden}
    </div>
    {/iteration:middleColumn}
    &#160;
  </div>
  <div class="col-md-4 fork-dashboard-column column">
    {iteration:rightColumn}
    <div class="fork-widget sortableWidget{option:rightColumn.hidden} isRemoved{/option:rightColumn.hidden}" data-module="{$rightColumn.module}" data-widget="{$rightColumn.widget}" data-title="{$rightColumn.title}"{option:rightColumn.hidden} style="display: none;"{/option:rightColumn.hidden}>
      <a href="#" class="editDashboardClose close ui-dialog-titlebar-close ui-corner-all" style="display: none;">&times;</a>
      {option:rightColumn.hidden}
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">{$rightColumn.title}</h2>
        </div>
        <div class="panel-body" style="display: none;">
          {$msgWillBeEnabledOnSave}
        </div>
      </div>
      {/option:rightColumn.hidden}
      {option:!rightColumn.hidden}
      {include:{$rightColumn.template}}
      {/option:!rightColumn.hidden}
    </div>
    {/iteration:rightColumn}
    &#160;
  </div>
</div>
<div class="row fork-dashboard-actions">
  <div class="col-md-4 col-md-offset-4 text-center">
    <a href="#" id="editDashboard">
      {$msgEditYourDashboard}
    </a>
  </div>
</div>
{include:{$BACKEND_MODULES_PATH}/Dashboard/Layout/Templates/StructureEnd.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
