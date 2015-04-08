{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditQuestion|sprintf:{$item.question}|ucfirst}</h2>
  </div>
</div>
{form:edit}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="title">{$lblQuestion|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
      </div>
      {option:detailURL}
      <p><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></p>
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
          <li role="presentation">
            <a href="#tabFeedback" aria-controls="feedback" role="tab" data-toggle="tab">
              {$lblFeedback|ucfirst}
              <span class="badge positiveFeedback">{$item.num_usefull_yes}</span>
              <span class="badge negativeFeedback">{$item.num_usefull_no}</span>
            </a>
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
          <div role="tabpanel" class="tab-pane" id="tabFeedback">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblFeedback|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                {option:feedback}
                <p class="text-info">{$msgFeedbackInfo}</p>
                <div class="panel-group" id="feedback" role="tablist" aria-multiselectable="false">
                  {iteration:feedback}
                  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="feedback-heading-{$feedback.id}">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#feedback" href="#feedback-collapse-{$feedback.id}" aria-expanded="true" aria-controls="feedback-collapse-{$feedback.id}">
                          {$feedback.text|truncate:150}
                        </a>
                      </h4>
                    </div>
                    <div id="feedback-collapse-{$feedback.id}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="feedback-heading-{$feedback.id}">
                      <div class="panel-body">
                        {$feedback.text}
                      </div>
                      {option:showFaqDeleteFeedback}
                      <div class="panel-footer">
                        <div class="btn-toolbar">
                          <div class="btn-group pull-left" role="group">
                            <a href="{$var|geturl:'delete_feedback'}&amp;id={$feedback.id}" class="btn btn-danger">
                              <span class="glyphicon glyphicon-trash"></span>
                              {$lblDelete|ucfirst}
                            </a>
                          </div>
                        </div>
                      </div>
                      {/option:showFaqDeleteFeedback}
                    </div>
                  </div>
                  {/iteration:feedback}
                </div>
              </div>
            </div>
            {/option:feedback}
            {option:!feedback}
            <p>{$msgNoFeedbackItems}</p>
            {/option:!feedback}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showFaqDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="glyphicon glyphicon-trash"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showFaqDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;{$lblPublish|ucfirst}
          </button>
        </div>
      </div>
      {option:showFaqDelete}
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
              <a href="{$var|geturl:'delete'}&amp;id={$item.id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showFaqDelete}
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
