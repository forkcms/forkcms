{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$msgEditArticle|sprintf:{$item.title}|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$detailURL}/{$item.url}{option:item.revision_id}?revision={$item.revision_id}{/option:item.revision_id}" class="btn btn-default" target="_blank">
          <span class="fa fa-eye"></span>
          <span>{$lblView|ucfirst}</span>
        </a>
      </div>
    </div>
  </div>
</div>
{form:edit}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="title">{$lblTitle|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
      </div>
      {option:detailURL}
        <a href="{$detailURL}/{$item.url}">
          <small>{$detailURL}/<span id="generatedUrl">{$item.url}</span></small>
        </a>
      {/option:detailURL}
      {option:!detailURL}
      <p class="text-warning"><span class="fa fa-warning"></span> {$errNoModuleLinked}</p>
      {/option:!detailURL}
    </div>
  </div>
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
          <li role="presentation">
            <a href="#tabComments" aria-controls="comments" role="tab" data-toggle="tab">{$lblComments|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabSEO" aria-controls="seo" role="tab" data-toggle="tab">{$lblSEO|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabContent">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblContent|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <div class="form-group">
                  <label for="text">
                    {$lblMainContent|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtText} {$txtTextError}
                </div>
                {option:imageIsAllowed}
                <div class="form-group">
                  <label for="image">{$lblImage|ucfirst}</label>
                  {option:item.image}
                  <div>
                    <img src="{$FRONTEND_FILES_URL}/blog/images/128x128/{$item.image}" class="img-thumbnail" width="128" height="128" alt="{$lblImage|ucfirst}" />
                  </div>
                  <ul class="list-unstyled">
                    <li class="checkbox">
                      <label for="deleteImage">{$chkDeleteImage} {$lblDelete|ucfirst}</label>
                      {$chkDeleteImageError}
                    </li>
                  </ul>
                  {/option:item.image}
                  {$fileImage} {$fileImageError}
                </div>
                {/option:imageIsAllowed}
                <div class="form-group">
                  <label for="introduction">
                    {$lblSummary|ucfirst}
                    <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpSummary}"></abbr>
                  </label>
                  {$txtIntroduction} {$txtIntroductionError}
                </div>
              </div>
              <div class="col-md-4">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">{$lblStatus|ucfirst}</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <ul class="list-unstyled">
                        {iteration:hidden}
                        <li class="radio">
                          <label for="{$hidden.id}">{$hidden.rbtHidden} {$hidden.label}</label>
                        </li>
                        {/iteration:hidden}
                      </ul>
                    </div>
                    <div class="form-group">
                      <label for="publishOnDate">{$lblPublishOn|ucfirst}</label>
                      {$txtPublishOnDate} {$txtPublishOnDateError}
                      <label for="publishOnTime">{$lblAt}</label>
                      {$txtPublishOnTime} {$txtPublishOnTimeError}
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">{$lblMetaData|ucfirst}</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label for="categoryId">{$lblCategory|ucfirst}</label>
                      {$ddmCategoryId} {$ddmCategoryIdError}
                    </div>
                    <div class="form-group">
                      <label for="userId">{$lblAuthor|ucfirst}</label>
                      {$ddmUserId} {$ddmUserIdError}
                    </div>
                    {option:showTagsIndex}
                    <div class="form-group">
                      <label for="tags">{$lblTags|ucfirst}</label>
                      {$txtTags} {$txtTagsError}
                    </div>
                    {/option:showTagsIndex}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabVersions">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblVersions|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                {option:drafts}
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">
                      {$lblDrafts|ucfirst}
                      <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpDrafts}"></abbr>
                    </h3>
                  </div>
                  {$drafts}
                </div>
                {/option:drafts}
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">
                      {$lblPreviousVersions|ucfirst}
                      <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpRevisions}"></abbr>
                    </h3>
                  </div>
                  {option:revisions}
                  {$revisions}
                  {/option:revisions}
                  {option:!revisions}
                  <div class="panel-body">
                    <p>{$msgNoRevisions}</p>
                  </div>
                  {/option:!revisions}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabComments">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblComments|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <ul class="list-unstyled">
                    <li class="checkbox">
                      <label for="allowComments">{$chkAllowComments} {$lblAllowComments|ucfirst}</label>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/Layout/Templates/Seo.tpl}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
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
          <a href="#" id="saveAsDraft" class="btn btn-primary">
            <span class="fa fa-file-o"></span>&nbsp;
            {$lblSaveDraft|ucfirst}
          </a>
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="fa fa-check"></span>
            {$lblPublish|ucfirst}
          </button>
        </div>
      </div>
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
          <p>{$msgConfirmDelete|sprintf:{$item.title}}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
          <a href="{$var|geturl:'delete'}&amp;id={$item.id}{option:categoryId}&amp;category={$categoryId}{/option:categoryId}" class="btn btn-primary">
            {$lblOK|ucfirst}
          </a>
        </div>
      </div>
    </div>
  </div>
  {/option:showContentBlocksDelete}
  <div class="modal fade" id="addCategoryDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblAddCategory|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title h4">{$lblAddCategory|ucfirst}</span>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="categoryTitle">
              {$lblTitle|ucfirst}
              <abbr title="{$lblRequiredField|ucfirst}"></abbr>
            </label>
            <input type="text" name="categoryTitle" id="categoryTitle" class="form-control" maxlength="255" />
            <p class="text-danger" id="categoryTitleError" style="display: none;">{$errFieldIsRequired|ucfirst}</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
          <button id="addCategorySubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
