{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditCategory|sprintf:{$item.title}|ucfirst}</h2>
  </div>
</div>
{form:editCategory}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="title">
          {$lblTitle|ucfirst}
          <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
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
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showBlogDeleteCategory}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="glyphicon glyphicon-trash"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showBlogDeleteCategory}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;{$lblPublish|ucfirst}
          </button>
        </div>
      </div>
      {option:showBlogDeleteCategory}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmDeleteCategory|sprintf:{$item.title}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete_category'}&amp;id={$item.id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showBlogDeleteCategory}
    </div>
  </div>
{/form:editCategory}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
