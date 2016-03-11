{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblQuestions|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showFaqAdd}
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAdd|ucfirst}
        </a>
        {/option:showFaqAdd}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrids}
    {iteration:dataGrids}
    <div id="dataGrid-{$dataGrids.id}" class="panel panel-default jsDataGridQuestionsHolder">
      <div class="panel-heading">
        <h3 class="panel-title">{$dataGrids.title}</h3>
      </div>
      {option:dataGrids.content}
      {$dataGrids.content}
      {/option:dataGrids.content}
      {option:!dataGrids.content}
      <div class="panel-body">
        {$emptyDatagrid}
      </div>
      {/option:!dataGrids.content}
    </div>
    {/iteration:dataGrids}
    {/option:dataGrids}
    {option:!dataGrids}
    <div class="panel-body">
      <p>{$msgNoItems}</p>
    </div>
    {/option:!dataGrids}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
