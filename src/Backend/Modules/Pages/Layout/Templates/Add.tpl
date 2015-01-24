{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureStart.tpl}
<div class="row fork-module-header">
    <div class="col-md-12">
        <h2>{$lblAdd|ucfirst}</h2>
        {option:showPagesIndex}
            <div class="btn-toolbar pull-right">
                <div class="btn-group" role="group">
                    <a href="{$var|geturl:'index'}" class="btn btn-primary" title="{$lblOverview|ucfirst}">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                        {$lblOverview|ucfirst}
                    </a>
                </div>
            </div>
        {/option:showPagesIndex}
    </div>
</div>
{form:add}
    {$hidTemplateId}
    <div class="row fork-module-content">
        <div class="col-md-12">
            <div class="form-group">
                <label for="title">{$lblTitle|ucfirst}</label>
                {$txtTitle} {$txtTitleError}
            </div>
        </div>
        <div class="col-md-12">
            <a id="generatedUrl" data-url="{$SITE_URL}{$prefixURL}/"
               href="{$SITE_URL}{$prefixURL}/">{$SITE_URL}{$prefixURL}/</a>
        </div>
    </div>
    <div class="row fork-module-content">
        <div class="col-md-12">
            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabContent" aria-controls="home" role="tab"
                           data-toggle="tab">{$lblContent|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabSettings" aria-controls="profile" role="tab"
                           data-toggle="tab">{$lblSettings|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabRedirect" aria-controls="messages" role="tab"
                           data-toggle="tab">{$lblRedirect|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabTags" aria-controls="settings" role="tab" data-toggle="tab">{$lblTags|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabSEO" aria-controls="settings" role="tab" data-toggle="tab">{$lblSEO|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabContent">
                        <div id="editTemplate" class="row">
                            <div class="col-md-12">
                                {* Do not change the ID! *}
                                <h3>{$lblTemplate|ucfirst}: <span id="tabTemplateLabel">&nbsp;</span></h3>

                                <div class="btn-toolbar pull-right">
                                    <div class="btn-group" role="group">
                                        <button class="btn" data-toggle="modal" data-target="#changeTemplate">
                                            <span class="glyphicon glyphicon-th"></span>
                                            {$lblChangeTemplate|ucfirst}
                                        </button>
                                    </div>
                                </div>
                                {option:formErrors}<span class="formError">{$formErrors}</span>{/option:formErrors}
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
                                                            colspan="1" class="box">
                                                            <div class="heading linkedBlocksTitle">
                                                                <h4>{$lblFallback|ucfirst}</h4></div>
                                                            <div class="linkedBlocks">
                                                                <!-- linked blocks will be added here --></div>
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
                    <div role="tabpanel" class="tab-pane" id="tabSettings">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblSettings|ucfirst}</h3>
                            </div>
                        </div>
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
                                <h3>{$lblRedirect|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {option:rbtRedirectError}
                                <div class="alert alert-danger">{$rbtRedirectError}</div>
                                {/option:rbtRedirectError}
                                <div class="form-group">
                                    <ul class="list-unstyled radiobuttonFieldCombo">
                                        {iteration:redirect}
                                        <li class="radio">
                                            <label for="{$redirect.id}">{$redirect.rbtRedirect} {$redirect.label}</label>
                                            {option:redirect.isInternal}
                                            <label for="internalRedirect" class="hidden">{$redirect.label}</label>
                                            <p class="text-info">{$msgHelpInternalRedirect}</p>
                                            {$ddmInternalRedirect} {$ddmInternalRedirectError}
                                            {/option:redirect.isInternal}
                                            {option:redirect.isExternal}
                                            <label for="externalRedirect" class="hidden">{$redirect.label}</label>
                                            <p class="text-info">{$msgHelpExternalRedirect}</p>
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
                                <h3>{$lblTags|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {$txtTags} {$txtTagsError}
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabSEO">
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
                                            <p class="text-info">{$msgHelpPageTitle}</p>
                                            {$txtPageTitle} {$txtPageTitleError}
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <ul class="list-unstyled checkboxTextFieldCombo">
                                        <li class="checkbox">
                                            <label for="navigationTitleOverwrite" class="visuallyHidden">{$chkNavigationTitleOverwrite} <b>{$lblNavigationTitle|ucfirst}</b></label>
                                            <p class="text-info">{$msgHelpNavigationTitle}</p>
                                            {$txtNavigationTitle} {$txtNavigationTitleError}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>{$lblMetaInformation|ucfirst}</h4>
                                <div class="form-group">
                                    <ul class="list-unstyled checkboxTextFieldCombo">
                                        <li class="checkbox">
                                            <label for="metaDescriptionOverwrite" class="visuallyHidden">{$chkMetaDescriptionOverwrite} <b>{$lblDescription|ucfirst}</b></label>
                                            <p class="text-info">{$msgHelpMetaDescription}</p>
                                            {$txtMetaDescription} {$txtMetaDescriptionError}
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <ul class="list-unstyled checkboxTextFieldCombo">
                                        <li class="checkbox">
                                            <label for="metaDescriptionOverwrite" class="visuallyHidden">{$chkMetaKeywordsOverwrite} <b>{$lblKeywords|ucfirst}</b></label>
                                            <p class="text-info">{$msgHelpMetaKeywords}</p>
                                            {$txtMetaKeywords} {$txtMetaKeywordsError}
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label for="metaDescriptionOverwrite" class="visuallyHidden">{$lblExtraMetaTags|ucfirst}</label>
                                    <p class="text-info">{$msgHelpMetaCustom}</p>
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
                                            <p class="text-info">{$msgHelpMetaURL}</p>
                                            <span id="urlFirstPart">{$SITE_URL}{$prefixURL}/</span>{$txtUrl} {$txtUrlError}
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="pageButtons" class="row fork-page-actions">
        <div class="col-md-12">
            <div class="btn-toolbar">
                <div class="btn-group pull-right" role="group">
                    <a href="#" id="saveAsDraft" class="btn btn-primary"><span class="glyphicon glyphicon-save"></span>&nbsp;{$lblSaveDraft|ucfirst}
                    </a>
                    <button id="addButton" type="submit" name="add" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>&nbsp;{$lblAdd|ucfirst}</button>
                </div>
            </div>
        </div>
    </div>
{/form:add}
<div>
    {*
    This is the HTML content, hidden
    *}
    <div class="modal fade" id="editContent" tabindex="-1" role="dialog" aria-labelledby="{$lblEditor|ucfirst}"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="modal-title h4">{$lblEditor|ucfirst}</span>
                </div>
                <div class="modal-body">
                    {iteration:positions}
                    {iteration:positions.blocks}
                        <div class="alert alert-warning">
                            {$msgContentSaveWarning}
                        </div>
                        <div class="box contentBlock" style="margin: 0;">
                            <div class="blockContentHTML optionsRTE">
                                <fieldset>
                                    {$positions.blocks.txtHTML}
                                    {$positions.blocks.txtHTMLError}
                                </fieldset>
                            </div>

                            {* this will store the selected extra *}
                            {$positions.blocks.hidExtraId}

                            {* this will store the selected position *}
                            {$positions.blocks.hidPosition}

                            {* this will store the visible/hidden state *}
                            <div style="display: none">{$positions.blocks.chkVisible}</div>
                        </div>
                    {/iteration:positions.blocks}
                    {/iteration:positions}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button id="editContentSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
                </div>
            </div>
        </div>
    </div>

    {*
    Dialog to select the content (editor, module or widget).
    Do not change the ID!
    *}
    <div class="modal fade" id="addBlock" tabindex="-1" role="dialog" aria-labelledby="{$lblChooseContent|ucfirst}"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="modal-title h4">{$lblChooseContent|ucfirst}</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="extraForBlock" name="extraForBlock" value=""/>

                    <p>{$msgHelpBlockContent}</p>

                    <div id="extraWarningAlreadyBlock" class="alert alert-warning">{$msgModuleBlockAlreadyLinked}</div>
                    <div id="extraWarningHomeNoBlock" class="alert alert-warning">{$msgHomeNoBlock}</div>
                    <div class="form-group">
                        <label for="extraType">{$lblType|ucfirst}</label>
                        {$ddmExtraType}
                    </div>
                    <div id="extraModuleHolder" class="form-group" style="display: none;">
                        <label for="extraModule">{$lblWhichModule|ucfirst}</label>
                        <select id="extraModule" class="form-control">
                            <option value="-1">-</option>
                        </select>
                    </div>
                    <div id="extraExtraIdHolder" class="form-group" style="display: none;">
                        <label for="extraExtraId">{$lblWhichWidget|ucfirst}</label>
                        <select id="extraExtraId" class="form-control">
                            <option value="-1">-</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button id="addBlockSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
                </div>
            </div>
        </div>
    </div>

    {*
    Dialog to select another template.
    Do not change the ID!
    *}
    <div class="modal fade" id="changeTemplate" tabindex="-1" role="dialog"
         aria-labelledby="{$lblChooseATemplate|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="modal-title h4">{$lblChooseATemplate|ucfirst}</span>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">{$msgTemplateChangeWarning}</div>
                    <div id="templateList">
                        <ul class="list-unstyled">
                            {iteration:templates}
                            {option:templates.break}
                        </ul>
                        <ul class="list-unstyled lastChild">
                            {/option:templates.break}
                            <li{option:templates.disabled} class="disabled"{/option:templates.disabled}>
                                <label for="template{$templates.id}" class="radio">
                                    <input type="radio" id="template{$templates.id}" value="{$templates.id}"
                                           name="template_id_chooser"
                                           class="inputRadio"{option:templates.checked} checked="checked"{/option:templates.checked}{option:templates.disabled} disabled="disabled"{/option:templates.disabled} />{$templates.label}
                                </label>

                                <div class="templateVisual current">
                                    {$templates.html}
                                </div>
                            </li>
                            {/iteration:templates}
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button id="changeTemplateSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
                </div>
            </div>
        </div>
    </div>

    {*
    Dialog to confirm block removal.
    Do not change the ID!
    *}
    <div class="modal fade" id="confirmDeleteBlock" tabindex="-1" role="dialog" aria-labelledby="{$lblDeleteBlock|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h4">{$lblDeleteBlock|ucfirst}</span>
                </div>
                <div class="modal-body">
                    <p>{$msgConfirmDeleteBlock}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button id="confirmDeleteBlockSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
                </div>
            </div>
        </div>
    </div>

    {*
    Text editor block
    *}
    <div class="modal fork-modal-ckeditor fade" id="blockHtml" tabindex="-1" role="dialog" aria-labelledby="{$lblEditor|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <span class="modal-title h4">{$lblEditor|ucfirst}</span>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        {$msgContentSaveWarning}
                    </div>
                    <div class="form-group">
                        {$txtHtml}
                        {$txtHtmlError}
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="blockHtmlCancel" type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                    <button id="blockHtmlSubmit" type="button" class="btn btn-primary">{$lblOK|ucfirst}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden">
    <script type="text/javascript">
        //<![CDATA[
        // all the possible templates
        var templates = {};
        {iteration:templates}templates[{$templates.id}] = {$templates.json};
        {/iteration:templates}

        // the data for the extra's
        var extrasData = {};
        {option:extrasData}extrasData = {$extrasData};
        {/option:extrasData}

        // the extra's, but in a way we can access them based on their ID
        var extrasById = {};
        {option:extrasById}extrasById = {$extrasById};
        {/option:extrasById}

        // indicator that the default blocks may be set on pageload
        var initDefaults = true;
        //]]>
    </script>
</div>
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureEnd.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
