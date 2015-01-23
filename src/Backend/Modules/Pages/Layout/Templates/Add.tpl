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
                        <div class="subtleBox">
                            <div class="heading">
                                <h3>Settings</h3>
                            </div>
                            <div class="options">
                                <ul class="inputList">
                                    {iteration:hidden}
                                        <li>{$hidden.rbtHidden} <label
                                                    for="{$hidden.id}">{$hidden.label|ucfirst}</label></li>
                                    {/iteration:hidden}
                                </ul>
                                <p>
                                    <label for="isAction">{$chkIsAction} {$msgIsAction}</label>
                                </p>
                                {option:isGod}
                                    <ul class="inputList">
                                        {iteration:allow}
                                            <li>{$allow.chkAllow} <label for="{$allow.id}">{$allow.label}</label></li>
                                        {/iteration:allow}
                                    </ul>
                                {/option:isGod}
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabRedirect">
                        <div class="subtleBox">
                            <div class="heading">
                                <h3>Redirect</h3>
                            </div>
                            <div class="options">
                                {$rbtRedirectError}
                                <ul class="inputList radiobuttonFieldCombo">
                                    {iteration:redirect}
                                        <li>
                                            <label for="{$redirect.id}">{$redirect.rbtRedirect} {$redirect.label}</label>
                                            {option:redirect.isInternal}
                                                <label for="internalRedirect"
                                                       class="visuallyHidden">{$redirect.label}</label>
                                            {$ddmInternalRedirect} {$ddmInternalRedirectError}
                                                <span class="helpTxt">{$msgHelpInternalRedirect}</span>
                                            {/option:redirect.isInternal}

                                            {option:redirect.isExternal}
                                                <label for="externalRedirect"
                                                       class="visuallyHidden">{$redirect.label}</label>
                                            {$txtExternalRedirect} {$txtExternalRedirectError}
                                                <span class="helpTxt">{$msgHelpExternalRedirect}</span>
                                            {/option:redirect.isExternal}
                                        </li>
                                    {/iteration:redirect}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabTags">
                        <div class="subtleBox">
                            <div class="heading">
                                <h3>
                                    <label for="addValue-tags">{$lblTags|ucfirst}</label>
                                </h3>
                            </div>
                            <div class="options">
                                {$txtTags} {$txtTagsError}
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabSEO">
                        <div class="subtleBox">
                            <div class="heading">
                                <h3>{$lblTitles|ucfirst}</h3>
                            </div>
                            <div class="options">
                                <p>
                                    <label for="pageTitleOverwrite">{$lblPageTitle|ucfirst}</label>
                                    <span class="helpTxt">{$msgHelpPageTitle}</span>
                                </p>
                                <ul class="inputList checkboxTextFieldCombo">
                                    <li>
                                        {$chkPageTitleOverwrite}
                                        <label for="pageTitle" class="visuallyHidden">{$lblPageTitle|ucfirst}</label>
                                        {$txtPageTitle} {$txtPageTitleError}
                                    </li>
                                </ul>
                                <p>
                                    <label for="navigationTitleOverwrite">{$lblNavigationTitle|ucfirst}</label>
                                    <span class="helpTxt">{$msgHelpNavigationTitle}</span>
                                </p>
                                <ul class="inputList checkboxTextFieldCombo">
                                    <li>
                                        {$chkNavigationTitleOverwrite}
                                        <label for="navigationTitle"
                                               class="visuallyHidden">{$lblNavigationTitle|ucfirst}</label>
                                        {$txtNavigationTitle} {$txtNavigationTitleError}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div id="seoMeta" class="subtleBox">
                            <div class="heading">
                                <h3>{$lblMetaInformation|ucfirst}</h3>
                            </div>
                            <div class="options">
                                <p>
                                    <label for="metaDescriptionOverwrite">{$lblDescription|ucfirst}</label>
                                    <span class="helpTxt">{$msgHelpMetaDescription}</span>
                                </p>
                                <ul class="inputList checkboxTextFieldCombo">
                                    <li>
                                        {$chkMetaDescriptionOverwrite}
                                        <label for="metaDescription"
                                               class="visuallyHidden">{$lblDescription|ucfirst}</label>
                                        {$txtMetaDescription} {$txtMetaDescriptionError}
                                    </li>
                                </ul>
                                <p>
                                    <label for="metaKeywordsOverwrite">{$lblKeywords|ucfirst}</label>
                                    <span class="helpTxt">{$msgHelpMetaKeywords}</span>
                                </p>
                                <ul class="inputList checkboxTextFieldCombo">
                                    <li>
                                        {$chkMetaKeywordsOverwrite}
                                        <label for="metaKeywords" class="visuallyHidden">{$lblKeywords|ucfirst}</label>
                                        {$txtMetaKeywords} {$txtMetaKeywordsError}
                                    </li>
                                </ul>
                                <div class="textareaHolder">
                                    <p>
                                        <label for="metaCustom">{$lblExtraMetaTags|ucfirst}</label>
                                        <span class="helpTxt">{$msgHelpMetaCustom}</span>
                                    </p>
                                    {$txtMetaCustom} {$txtMetaCustomError}
                                </div>
                            </div>
                        </div>

                        <div class="subtleBox">
                            <div class="heading">
                                <h3>{$lblURL}</h3>
                            </div>
                            <div class="options">
                                <p>
                                    <label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
                                    <span class="helpTxt">{$msgHelpMetaURL}</span>
                                </p>
                                <ul class="inputList checkboxTextFieldCombo">
                                    <li>
                                        {$chkUrlOverwrite}
                                        <label for="url" class="visuallyHidden">{$lblCustomURL|ucfirst}</label>
                                        <span id="urlFirstPart">{$SITE_URL}{$prefixURL}</span>{$txtUrl} {$txtUrlError}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="subtleBox">
                            <div class="heading">
                                <h3>{$lblSEO|uppercase}</h3>
                            </div>
                            <div class="options">
                                <p class="label">{$lblIndex}</p>
                                {$rbtSeoIndexError}
                                <ul class="inputList inputListHorizontal">
                                    {iteration:seo_index}
                                        <li>
                                            <label for="{$seo_index.id}">{$seo_index.rbtSeoIndex} {$seo_index.label}</label>
                                        </li>
                                    {/iteration:seo_index}
                                </ul>
                                <p class="label">{$lblFollow}</p>
                                {$rbtSeoFollowError}
                                <ul class="inputList inputListHorizontal">
                                    {iteration:seo_follow}
                                        <li>
                                            <label for="{$seo_follow.id}">{$seo_follow.rbtSeoFollow} {$seo_follow.label}</label>
                                        </li>
                                    {/iteration:seo_follow}
                                </ul>
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
