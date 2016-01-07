{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblCustomFields|ucfirst} {$lblFor} {$lblGroup} &ldquo;{$group.name}&rdquo; <abbr class="fa fa-question-circle" data-toggle="tooltip" title="{$msgHelpCustomFields}"></abbr></h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showMailmotorAddCustomField}
        <a href="{$var|geturl:'add_custom_field'}&amp;group_id={$group.id}" class="btn btn-default" title="{$lblAddCustomField|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAddCustomField|ucfirst}
        </a>
        {/option:showMailmotorAddCustomField}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    <form action="{$var|geturl:'mass_custom_field_action'}" method="get" class="forkForms submitWithLink" id="massCustomFieldAction">
      <input type="hidden" name="group_id" value="{$group.id}" />
      {$dataGrid}
    </form>
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoItems}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
