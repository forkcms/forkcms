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
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabContent" aria-controls="content" role="tab" data-toggle="tab">{$lblContent|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabSEO" aria-controls="seo" role="tab" data-toggle="tab">{$lblSEO|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabContent">
            <div class="form-group{option:txtTitleError} has-error{/option:txtTitleError}">
              <label for="title" class="control-label">
                {$lblTitle|ucfirst}
                <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
              </label>
              {$txtTitle} {$txtTitleError}
            </div>
            {option:detailURL}
            <a href="{$detailURL}">
              <small>{$detailURL}/<span id="generatedUrl"></span></small>
            </a>
            {/option:detailURL}
            {option:!detailURL}
            <p class="text-warning"><span class="fa fa-warning"></span> {$errNoModuleLinked}</p>
            {/option:!detailURL}
          </div>
          <div role="tabpanel" class="tab-pane" id="tabSEO">
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
          {option:showFaqDeleteCategory}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showFaqDeleteCategory}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="fa fa-check"></span>&nbsp;{$lblPublish|ucfirst}
          </button>
        </div>
      </div>
      {option:showFaqDeleteCategory}
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
      {/option:showFaqDeleteCategory}
    </div>
  </div>
{/form:editCategory}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
