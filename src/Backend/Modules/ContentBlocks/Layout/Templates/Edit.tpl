{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditContentBlock|sprintf:{$title}|ucfirst}</h2>
  </div>
</div>
{form:edit}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabContent" aria-controls="content" role="tab" data-toggle="tab">{$lblContent|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabVersions" aria-controls="versions" role="tab" data-toggle="tab">{$lblVersions|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabContent">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title">{$lblTitle|ucfirst}</label>
                  {$txtTitle} {$txtTitleError}
                </div>
                <div class="form-group">
                  <label for="text">
                    {$lblContent|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtText} {$txtTextError}
                </div>
                {option:ddmTemplate}
                <div class="form-group">
                  <label for="template">{$lblTemplate|ucfirst}</label>
                  {$ddmTemplate} {$ddmTemplateError}
                </div>
                {/option:ddmTemplate}
                <div class="form-group">
                  <label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite|ucfirst}</label>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabVersions">
            <div class="row">
              <div class="col-md-12">
                <h3>
                  {$lblPreviousVersions|ucfirst}
                  <abbr class="fa fa-question-circle" data-toggle="tooltip" title="{$msgHelpRevisions}"></abbr>
                </h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                {option:revisions}
                {$revisions}
                {/option:revisions}
                {option:!revisions}
                <p>{$msgNoRevisions}</p>
                {/option:!revisions}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showContentBlocksDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showContentBlocksDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
          </button>
        </div>
      </div>
      {option:showContentBlocksDelete}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmDelete|sprintf:{$title}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete'}&amp;id={$id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showContentBlocksDelete}
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
