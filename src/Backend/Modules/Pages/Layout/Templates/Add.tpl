{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureStart.tpl}
<div class="row fork-module-heading">
    <div class="col-md-6">
        <h2>{$lblAdd|ucfirst}</h2>
    </div>
    <div class="col-md-6">
        {option:showPagesIndex}
        <div class="btn-toolbar pull-right">
            <div class="btn-group" role="group">
                <a href="{$var|geturl:'index'}" class="btn btn-default" title="{$lblOverview|ucfirst}">
                    <span class="fa fa-list"></span>
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
            <a href="{$detailURL}">
                <small>{$SITE_URL}{$prefixURL}/<span id="generatedUrl"></span></small>
            </a>
        </div>
    </div>
    <div class="row fork-module-content">
        <div class="col-md-12">
            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabContent" aria-controls="home" role="tab" data-toggle="tab">{$lblContent|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabSettings" aria-controls="profile" role="tab" data-toggle="tab">{$lblSettings|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabRedirect" aria-controls="messages" role="tab" data-toggle="tab">{$lblRedirect|ucfirst}</a>
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
                                        <button type="button" class="btn" data-toggle="modal" data-target="#changeTemplate">
                                            <span class="fa fa-th"></span>
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
                        {include:{$BACKEND_CORE_PATH}/Layout/Templates/Seo.tpl}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="pageButtons" class="row fork-page-actions">
        <div class="col-md-12">
            <div class="btn-toolbar">
                <div class="btn-group pull-right" role="group">
                    <a href="#" id="saveAsDraft" class="btn btn-primary">
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
    {include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/Dialogs.tpl}
{/form:add}
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
