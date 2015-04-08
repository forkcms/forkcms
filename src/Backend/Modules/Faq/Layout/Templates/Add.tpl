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
      <div class="form-group">
        <label for="title">{$lblQuestion|ucfirst}</label>
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
            <a href="#tabContent" aria-controls="content" role="tab" data-toggle="tab">{$lblContent|ucfirst}</a>
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
                <div class="form-group optionsRTE">
                  <label for="answer">{$lblAnswer|ucfirst}
                    <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                  </label>
                  {$txtAnswer} {$txtAnswerError}
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
                      <label for="tags">{$lblTags|ucfirst}</label>
                      {$txtTags} {$txtTagsError}
                    </div>
                  </div>
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
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="glyphicon glyphicon-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
