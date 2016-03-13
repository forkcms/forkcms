{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureStart.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblEdit|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showPagesAdd}
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          <span>{$lblAdd|ucfirst}</span>
        </a>
        {/option:showPagesAdd}
        {option:!item.is_hidden}
        <a href="{$SITE_URL}{$item.full_url}{option:appendRevision}?page_revision={$item.revision_id}{/option:appendRevision}" class="btn btn-default" target="_blank">
          <span class="fa fa-eye"></span>&nbsp;
          <span>{$lblView|ucfirst}</span>
        </a>
        {/option:!item.is_hidden}
        {option:showPagesIndex}
        <a href="{$var|geturl:'index'}" class="btn btn-default" title="{$lblOverview|ucfirst}">
          <span class="fa fa-list"></span>&nbsp;
          {$lblOverview|ucfirst}
        </a>
        {/option:showPagesIndex}
      </div>
    </div>
  </div>
</div>
{form:edit}
  {$hidTemplateId}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:txtTitleError} has-error{/option:txtTitleError}">
        <label for="title" class="control-label">{$lblTitle|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
      </div>
      <a data-url="{$SITE_URL}{$prefixURL}/{$item.url}{option:appendRevision}?page_revision={$item.revision_id}{/option:appendRevision}" href="{$SITE_URL}{$prefixURL}/{$item.url}{option:appendRevision}?page_revision={$item.revision_id}{/option:appendRevision}">
        <small>{$SITE_URL}{$prefixURL}/<span id="generatedUrl">{$item.url}</span></small>
      </a>
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
            <a href="#tabSettings" aria-controls="settings" role="tab" data-toggle="tab">{$lblSettings|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabRedirect" aria-controls="redirect" role="tab" data-toggle="tab">{$lblRedirect|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabTags" aria-controls="tags" role="tab" data-toggle="tab">{$lblTags|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabSEO" aria-controls="seo" role="tab" data-toggle="tab">{$lblSEO|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabContent">
            <div id="editTemplate" class="row">
              <div class="col-md-6">
                {* Do not change the ID! *}
                <h3>{$lblTemplate|ucfirst}: <span id="tabTemplateLabel">&nbsp;</span></h3>
              </div>
              <div class="col-md-6">
                <div class="btn-toolbar pull-right">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn" data-toggle="modal" data-target="#changeTemplate">
                      <span class="fa fa-th"></span>
                      {$lblChangeTemplate|ucfirst}
                    </button>
                  </div>
                </div>
                {option:formErrors}
                <span class="formError text-danger">{$formErrors}</span>
                {/option:formErrors}
              </div>
              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-body">
                    <div id="templateVisualFallback" style="display: none">
                      <div id="fallback" class="generalMessage singleMessage infoMessage">
                        <div id="fallbackInfo">
                          {$msgFallbackInfo}
                        </div>
                        <table cellspacing="2">
                          <tbody>
                            <tr>
                              <td data-position="fallback" id="templatePosition-fallback"
                                colspan="1">
                                <div class="heading linkedBlocksTitle">
                                  <h4>{$lblFallback|ucfirst}</h4></div>
                                <div class="linkedBlocks"><!-- linked blocks will be added here --></div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div id="templateVisualLarge">
                      &nbsp;
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabVersions">
            {option:drafts}
            <div class="row">
              <div class="col-md-12">
                <h4>{$lblDrafts|ucfirst} <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpDrafts}"></abbr></h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                {$drafts}
              </div>
            </div>
            {/option:drafts}
            <div class="row">
              <div class="col-md-12">
                <h4>{$lblPreviousVersions|ucfirst} <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpRevisions}"></abbr></h4>
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
          <div role="tabpanel" class="tab-pane" id="tabSettings">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <ul class="list-unstyled">
                    {iteration:hidden}
                    <li class="radio">
                      <label for="{$hidden.id}">{$hidden.rbtHidden} {$hidden.label|ucfirst}</label>
                    </li>
                    {/iteration:hidden}
                  </ul>
                </div>
                <div class="form-group">
                  <ul class="list-unstyled">
                    <li class="checkbox">
                      <label for="isAction">{$chkIsAction} {$msgIsAction}</label>
                    </li>
                  </ul>
                </div>
                {option:isGod}
                <div class="form-group">
                  <ul class="list-unstyled">
                    {iteration:allow}
                    <li class="checkbox">
                      <label for="{$allow.id}">{$allow.chkAllow} {$allow.label}</label>
                    </li>
                    {/iteration:allow}
                  </ul>
                </div>
                {/option:isGod}
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabRedirect">
            <div class="row">
              <div class="col-md-12">
                {option:rbtRedirectError}
                <div class="alert alert-danger">{$rbtRedirectError}</div>
                {/option:rbtRedirectError}
                <div class="form-group{option:ddmInternalRedirectError} has-error{/option:ddmInternalRedirectError}{option:txtExternalRedirectError} has-error{/option:txtExternalRedirectError}">
                  <ul class="list-unstyled radiobuttonFieldCombo">
                    {iteration:redirect}
                    <li class="radio">
                      <label for="{$redirect.id}">{$redirect.rbtRedirect} {$redirect.label}</label>
                      {option:redirect.isInternal}
                      <label for="internalRedirect" class="hidden">{$redirect.label}</label>
                      <p class="help-block">{$msgHelpInternalRedirect}</p>
                      {$ddmInternalRedirect} {$ddmInternalRedirectError}
                      {/option:redirect.isInternal}
                      {option:redirect.isExternal}
                      <label for="externalRedirect" class="hidden">{$redirect.label}</label>
                      <p class="help-block">{$msgHelpExternalRedirect}</p>
                      {$txtExternalRedirect} {$txtExternalRedirectError}
                      {/option:redirect.isExternal}
                    </li>
                    {/iteration:redirect}
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabTags">
            <div class="row">
              <div class="col-md-12">
                {$txtTags} {$txtTagsError}
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
  <div id="pageButtons" class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showPagesDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showPagesDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <a href="#" id="saveAsDraft" class="btn btn-default">
            <span class="fa fa-file-o"></span>&nbsp;
            {$lblSaveDraft|ucfirst}
          </a>
          <button id="editButton" type="submit" name="edit" class="btn btn-success">
            <span class="fa fa-floppy-o"></span>&nbsp;
            {$lblSave|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
  {include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/Dialogs.tpl}
{/form:edit}
<div class="hidden">
  <script type="text/javascript">
    //<![CDATA[
    //the ID of the page
    var pageID = {$item.id};

    // all the possible templates
    var templates = {};
    {iteration:templates}templates[{$templates.id}] = {$templates.json};{/iteration:templates}

    // the data for the extra's
    var extrasData = {};
    {option:extrasData}extrasData = {$extrasData};{/option:extrasData}

    // the extra's, but in a way we can access them based on their ID
    var extrasById = {};
    {option:extrasById}extrasById = {$extrasById};{/option:extrasById}

    // indicator that the default blocks may not be set on pageload
    var initDefaults = false;

    // fix selected state in the tree
    var selectedId = 'page-'+ pageID;
    //]]>
  </script>
</div>
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureEnd.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
