{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
    <div class="col-md-12">
        <h2>{$lblAddNewMailing|ucfirst}</h2>
    </div>
</div>
<div class="row fork-module-heading">
    <div class="col-md-12">
        <nav class="navbar navbar-default" role="navigation">
            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="{$var|geturl:'add'}">1. {$lblWizardInformation|ucfirst}</a>
                </li>
                <li class="navbar-text">
                    <span>2. {$lblWizardTemplate|ucfirst}</span>
                </li>
                <li class="navbar-text">
                    <span>3. {$lblWizardContent|ucfirst}</span>
                </li>
                <li class="navbar-text">
                    <span>4. {$lblWizardSend|ucfirst}</span>
                </li>
            </ul>
        </nav>
    </div>
</div>
<div class="row fork-module-content">
    <div class="col-md-12">
        {form:add}
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                {$lblSettings|ucfirst}
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
                                <label for="name">
                                    {$lblName|ucfirst}
                                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                </label>
                                <p class="help-block">{$msgNameInternalUseOnly}</p>
                                {$txtName} {$txtNameError}
                            </div>
                            {option:ddmCampaign}
                            <div class="form-group{option:ddmCampaignError} has-error{/option:ddmCampaignError}">
                                <label for="campaign">
                                    {$lblCampaign|ucfirst}
                                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                </label>
                                {$ddmCampaign} {$ddmCampaignError}
                            </div>
                            {/option:ddmCampaign}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                {$lblSender|ucfirst}
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-group{option:txtFromNameError} has-error{/option:txtFromNameError}">
                                <label for="fromName">
                                    {$lblName|ucfirst}
                                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                </label>
                                {$txtFromName} {$txtFromNameError}
                            </div>
                            <div class="form-group{option:txtFromEmailError} has-error{/option:txtFromEmailError}">
                                <label for="fromEmail">
                                    {$lblEmailAddress|ucfirst}
                                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                </label>
                                {$txtFromEmail} {$txtFromEmailError}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                {$lblReplyTo|ucfirst}
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-group{option:txtReplyToEmailError} has-error{/option:txtReplyToEmailError}">
                                <label for="replyToEmail">
                                    {$lblEmailAddress|ucfirst}
                                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                </label>
                                {$txtReplyToEmail} {$txtReplyToEmailError}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                {$lblRecipients|ucfirst}
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                {option:chkGroupsError}
                                    <p class="text-danger">{$chkGroupsError}</p>
                                {/option:chkGroupsError}
                                <ul class="list-unstyled">
                                    {iteration:groups}
                                    <li class="checkbox">
                                        <label for="{$groups.id}">
                                            {$groups.chkGroups}
                                            <attr title="{$msgGroupsNumberOfRecipients|sprintf:{$groups.recipients}}">
                                                {$groups.label|ucfirst}
                                                ({option:groups.recipients}{$groups.recipients}{/option:groups.recipients}{option:!groups.recipients}{$lblQuantityNo}{/option:!groups.recipients} {option:groups.single}{$lblEmailAddress}{/option:groups.single}{option:!groups.single}{$lblEmailAddresses}{/option:!groups.single})
                                            </attr>
                                        </label>
                                    </li>
                                    {/iteration:groups}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                {$msgTemplateLanguage|ucfirst}
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                {option:rbtLanguagesError}
                                    <p class="text-danger">{$rbtLanguagesError}</p>
                                {/option:rbtLanguagesError}
                                <ul class="list-unstyled">
                                    {iteration:languages}
                                    <li class="checkbox">
                                        <label for="{$languages.id}">
                                            {$languages.rbtLanguages} {$languages.label|ucfirst}
                                        </label>
                                    </li>
                                    {/iteration:languages}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-toolbar">
                        <div class="btn-group pull-right" role="group">
                            <button id="toStep2" type="submit" name="to_step_2" class="btn btn-primary">
                                {$lblToStep|ucfirst} 2
                                &nbsp;<span class="fa fa-chevron-right"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        {/form:add}
    </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
