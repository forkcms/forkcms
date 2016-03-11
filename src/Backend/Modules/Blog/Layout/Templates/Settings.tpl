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
              <div class="form-group{option:ddmOverviewNumberOfItemsError} has-error{/option:ddmOverviewNumberOfItemsError}">
                <label for="overviewNumberOfItems" class="control-label">{$lblItemsPerPage|ucfirst}</label>
                <br>
                <br>
                {$ddmOverviewNumberOfItems} {$ddmOverviewNumberOfItemsError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group{option:ddmRecentArticlesFullNumberOfItemsError} has-error{/option:ddmRecentArticlesFullNumberOfItemsError}">
                <label for="recentArticlesFullNumberOfItems" class="control-label">{$msgNumItemsInRecentArticlesFull|ucfirst}</label>
                {$ddmRecentArticlesFullNumberOfItems} {$ddmRecentArticlesFullNumberOfItemsError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group{option:ddmRecentArticlesListNumberOfItemsError} has-error{/option:ddmRecentArticlesListNumberOfItemsError}">
                <label for="recentArticlesListNumberOfItems" class="control-label">{$msgNumItemsInRecentArticlesList|ucfirst}</label>
                {$ddmRecentArticlesListNumberOfItems} {$ddmRecentArticlesListNumberOfItemsError}
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
            {$lblComments|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="allowComments">
                  {$chkAllowComments} {$lblAllowComments|ucfirst}
                </label>
              </li>
              <li class="checkbox">
                <label for="moderation">
                  {$chkModeration} {$lblEnableModeration|ucfirst}
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
            <p class="help-block">{$msgFollowAllCommentsInRSS|sprintf:{$commentsRSSURL}}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  {option:isGod}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblImage|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="showImageForm">
                  {$chkShowImageForm} {$msgShowImageForm}
                </label>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  {/option:isGod}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblNotifications|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="notifyByEmailOnNewCommentToModerate">
                  {$chkNotifyByEmailOnNewCommentToModerate} {$msgNotifyByEmailOnNewCommentToModerate|ucfirst}
                </label>
              </li>
              <li class="checkbox">
                <label for="notifyByEmailOnNewComment">
                  {$chkNotifyByEmailOnNewComment} {$msgNotifyByEmailOnNewComment|ucfirst}
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
            {$lblSEO|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <p>{$msgHelpPingServices}:</p>
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="pingServices">
                  {$chkPingServices} {$lblPingBlogServices|ucfirst}
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
            {$lblRSSFeed|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtRssTitleError} has-error{/option:txtRssTitleError}">
            <label for="rssTitle" class="control-label">
              {$lblTitle|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            <p class="help-block">{$msgHelpRSSTitle}</p>
            {$txtRssTitle} {$txtRssTitleError}
          </div>
          <div class="form-group{option:txtRssDescriptionError} has-error{/option:txtRssDescriptionError}">
            <label for="rssDescription" class="control-label">{$lblDescription|ucfirst}</label>
            <p class="help-block">{$msgHelpRSSDescription}</p>
            {$txtRssDescription} {$txtRssDescriptionError}
          </div>
          <div class="form-group">
            <p>{$msgHelpMeta}:</p>
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="rssMeta">{$chkRssMeta} {$lblMetaInformation|ucfirst}</label>
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
          <button id="save" type="submit" name="save" class="btn btn-success"><span class="fa fa-floppy-o"></span> {$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
