<div class="row">
  <div class="col-md-12">
    <h3>{$lblSEO|ucfirst}</h3>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <h4>{$lblTitles|ucfirst}</h4>
    <div class="form-group">
      <ul class="list-unstyled checkboxTextFieldCombo">
        <li class="checkbox">
          <label for="pageTitleOverwrite" class="visuallyHidden">{$chkPageTitleOverwrite} <b>{$lblPageTitle|ucfirst}</b></label>
          <p class="help-block">{$msgHelpPageTitle}</p>
          {$txtPageTitle} {$txtPageTitleError}
        </li>
      </ul>
    </div>
    {option:chkNavigationTitleOverwrite}
    <div class="form-group">
      <ul class="list-unstyled checkboxTextFieldCombo">
        <li class="checkbox">
          <label for="navigationTitleOverwrite" class="visuallyHidden">{$chkNavigationTitleOverwrite} <b>{$lblNavigationTitle|ucfirst}</b></label>
          <p class="help-block">{$msgHelpNavigationTitle}</p>
          {$txtNavigationTitle} {$txtNavigationTitleError}
        </li>
      </ul>
    </div>
    {/option:chkNavigationTitleOverwrite}
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <h4>{$lblMetaInformation|ucfirst}</h4>
    <div class="form-group">
      <ul class="list-unstyled checkboxTextFieldCombo">
        <li class="checkbox">
          <label for="metaDescriptionOverwrite" class="visuallyHidden">{$chkMetaDescriptionOverwrite} <b>{$lblDescription|ucfirst}</b></label>
          <p class="help-block">{$msgHelpMetaDescription}</p>
          {$txtMetaDescription} {$txtMetaDescriptionError}
        </li>
      </ul>
    </div>
    <div class="form-group">
      <ul class="list-unstyled checkboxTextFieldCombo">
        <li class="checkbox">
          <label for="metaDescriptionOverwrite" class="visuallyHidden">{$chkMetaKeywordsOverwrite} <b>{$lblKeywords|ucfirst}</b></label>
          <p class="help-block">{$msgHelpMetaKeywords}</p>
          {$txtMetaKeywords} {$txtMetaKeywordsError}
        </li>
      </ul>
    </div>
    <div class="form-group">
      <label for="metaDescriptionOverwrite" class="visuallyHidden">{$lblExtraMetaTags|ucfirst}</label>
      <p class="help-block">{$msgHelpMetaCustom}</p>
      {$txtMetaCustom} {$txtMetaCustomError}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <h4>{$lblURL|ucfirst}</h4>
    <div class="form-group">
      <ul class="list-unstyled checkboxTextFieldCombo">
        <li class="checkbox">
          <label for="urlOverwrite" class="visuallyHidden">{$chkUrlOverwrite} <b>{$lblCustomURL|ucfirst}</b></label>
          <p class="help-block">{$msgHelpMetaURL}</p>
          <div class="form-inline">
            <span id="urlFirstPart">
              {option:detailUrl}{$detailUrl}{/option:detailUrl}
              {option:!detailUrl}{$SITE_URL}{option:prefixURL}{$prefixURL}{/option:prefixURL}/{/option:!detailUrl}
            </span>
            {$txtUrl} {$txtUrlError}
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <h4>{$lblSEO|ucfirst}</h4>
    <div class="form-inline">
      <div class="form-group">
        <p><b>{$lblIndex}</b></p>
        {option:rbtSeoIndexError}
        <div class="alert alert-danger">{$rbtSeoIndexError}</div>
        {/option:rbtSeoIndexError}
        <ul class="list-unstyled inputListHorizontal">
          {iteration:seo_index}
          <li class="radio">
            <label for="{$seo_index.id}">{$seo_index.rbtSeoIndex} {$seo_index.label}</label>
          </li>
          {/iteration:seo_index}
        </ul>
      </div>
    </div>
    <div class="form-inline">
      <div class="form-group">
        <p><b>{$lblFollow}</b></p>
        {option:rbtSeoFollowError}
        <div class="alert alert-danger">{$rbtSeoFollowError}</div>
        {/option:rbtSeoFollowError}
        <ul class="list-unstyled inputListHorizontal">
          {iteration:seo_follow}
          <li class="radio">
            <label for="{$seo_follow.id}">{$seo_follow.rbtSeoFollow} {$seo_follow.label}</label>
          </li>
          {/iteration:seo_follow}
        </ul>
      </div>
    </div>
  </div>
</div>
{* Hidden settings, used for the Ajax-call to verify the url *}
{$hidMetaId}
{$hidBaseFieldName}
{$hidCustom}
{$hidClassName}
{$hidMethodName}
{$hidParameters}
