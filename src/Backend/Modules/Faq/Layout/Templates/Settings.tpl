{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblSettings|ucfirst}</h2>
  </div>
</div>
{form:settings}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblPagination|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group{option:ddmOverviewNumberOfItemsPerCategoryError} has-error{/option:ddmOverviewNumberOfItemsPerCategoryError}">
                <label for="overviewNumberOfItemsPerCategory" class="control-label">{$lblItemsPerCategory|ucfirst}</label>
                {$ddmOverviewNumberOfItemsPerCategory} {$ddmOverviewNumberOfItemsPerCategoryError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group{option:ddmMostReadNumberOfItemsError} has-error{/option:ddmMostReadNumberOfItemsError}">
                <label for="mostReadNumberOfItems" class="control-label">{$msgNumMostReadItems}</label>
                {$ddmMostReadNumberOfItems} {$ddmMostReadNumberOfItemsError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group{option:ddmRelatedNumberOfItemsError} has-error{/option:ddmRelatedNumberOfItemsError}">
                <label for="relatedNumberOfItems" class="control-label">{$msgNumRelatedItems}</label>
                {$ddmRelatedNumberOfItems} {$ddmRelatedNumberOfItemsError}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblCategories|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="allowMultipleCategories">
                  {$chkAllowMultipleCategories} {$lblAllowMultipleCategories|ucfirst}
                </label>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblFeedback|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="allowFeedback">
                  {$chkAllowFeedback} {$lblAllowFeedback|ucfirst}
                </label>
              </li>
              <li class="checkbox">
                <label for="allowOwnQuestion">
                  {$chkAllowOwnQuestion} {$lblAllowOwnQuestion|ucfirst}
                </label>
              </li>
              <li class="checkbox">
                <label for="sendEmailOnNewFeedback">
                  {$chkSendEmailOnNewFeedback} {$lblSendEmailOnNewFeedback|ucfirst}
                </label>
              </li>
              <li class="checkbox">
                <label for="spamfilter">
                  {$chkSpamfilter} {$lblFilterCommentsForSpam|ucfirst}
                </label>
                <p class="help-block">
                  {$msgHelpSpamFilter}
                  {option:noAkismetKey}
                  <br />
                  <span class="text-warning">
                    <span class="fa fa-warning"></span> {$msgNoAkismetKey|sprintf:{$var|geturl:'index':'settings'}}
                  </span>
                  {/option:noAkismetKey}
                </p>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
