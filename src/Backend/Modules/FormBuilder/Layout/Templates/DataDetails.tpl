{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblFormData|sprintf:{$name}|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showFormBuilderData}
        <a href="{$var|geturl:'data'}&amp;id={$formId}&amp;start_date={$filter.start_date}&amp;end_date={$filter.end_date}" class="btn btn-default">
          <span class="fa fa-chevron-left"></span>&nbsp;
          {$lblBackToData|ucfirst}
        </a>
        {/option:showFormBuilderData}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblContent|ucfirst}</h3>
      </div>
      <div class="panel-body">
        {option:data}
        <table class="table">
          {iteration:data}
          <tr>
            <th>{$data.label}:</th>
            <td>{$data.value}</td>
          </tr>
          {/iteration:data}
        </table>
        {/option:data}
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblSenderInformation|ucfirst}</h3>
      </div>
      <div class="panel-body">
        <table class="table">
          <tr>
            <th>{$lblSentOn|ucfirst}:</th>
            <td>{$sentOn|formatdatetime}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="row fork-page-actions">
  <div class="col-md-12">
    <div class="btn-toolbar">
      <div class="btn-group pull-left" role="group">
        {option:showFormBuilderMassDataAction}
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
          <span class="fa fa-trash-o"></span>
          {$lblDelete|ucfirst}
        </button>
        {/option:showFormBuilderMassDataAction}
      </div>
    </div>
    {option:showFormBuilderMassDataAction}
    <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <span class="modal-title h4">{$lblDelete|ucfirst}</span>
          </div>
          <div class="modal-body">
            <p>{$msgConfirmDeleteData}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
            <a href="{$var|geturl:'mass_data_action'}&amp;action=delete&amp;form_id={$formId}&amp;id={$id}" class="btn btn-primary">
              {$lblOK|ucfirst}
            </a>
          </div>
        </div>
      </div>
    </div>
    {/option:showFormBuilderMassDataAction}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
