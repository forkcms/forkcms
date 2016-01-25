{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:txtTitleError} has-error{/option:txtTitleError}">
        <label class="control-label" for="title">{$lblTitle|ucfirst}</label>
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
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabContent" aria-controls="content" role="tab" data-toggle="tab">{$lblContent|ucfirst}</a>
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
              <div class="col-md-8">
                <div class="form-group{option:txtTextError} has-error{/option:txtTextError}">
                  <label for="text" class="control-label">
                    {$lblMainContent|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtText} {$txtTextError}
                </div>
                {option:imageIsAllowed}
                <div class="form-group{option:fileImageError} has-error{/option:fileImageError}">
                  <label for="image" class="control-label">{$lblImage|ucfirst}</label>
                  {$fileImage} {$fileImageError}
                </div>
                {/option:imageIsAllowed}
                <div class="form-group{option:txtIntroductionError} has-error{/option:txtIntroductionError}">
                  <label for="introduction" class="control-label">
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
                    <div class="form-group{option:txtPublishOnDateError} has-error{/option:txtPublishOnDateError}">
                      <label for="publishOnDate" class="control-label">{$lblPublishOn|ucfirst}</label>
                      {$txtPublishOnDate} {$txtPublishOnDateError}
                    </div>
                    <div class="form-group{option:txtPublishOnTimeError} has-error{/option:txtPublishOnTimeError}">
                      <label for="publishOnTime" class="control-label">{$lblAt}</label>
                      {$txtPublishOnTime} {$txtPublishOnTimeError}
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">{$lblMetaData|ucfirst}</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group{option:ddmCategoryIdError} has-error{/option:ddmCategoryIdError}">
                      <label for="categoryId" class="control-label">{$lblCategory|ucfirst}</label>
                      {$ddmCategoryId} {$ddmCategoryIdError}
                    </div>
                    <div class="form-group{option:ddmUserIdError} has-error{/option:ddmUserIdError}">
                      <label for="userId" class="control-label">{$lblAuthor|ucfirst}</label>
                      {$ddmUserId} {$ddmUserIdError}
                    </div>
                    {option:showTagsIndex}
                    <div class="form-group{option:txtTagsError} has-error{/option:txtTagsError}">
                      <label for="tags" class="control-label">{$lblTags|ucfirst}</label>
                      {$txtTags} {$txtTagsError}
                    </div>
                    {/option:showTagsIndex}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabComments">
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
        <div class="btn-group pull-right" role="group">
          <a href="#" id="saveAsDraft" class="btn btn-default">
            <span class="fa fa-file-o"></span>&nbsp;
            {$lblSaveDraft|ucfirst}
          </a>
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="addCategoryDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblAddCategory|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title h4">{$lblAddCategory|ucfirst}</span>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="categoryTitle" class="control-label">
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
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
