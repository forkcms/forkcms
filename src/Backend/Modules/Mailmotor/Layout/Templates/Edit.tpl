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
        {iteration:wizard}
        <li class="{option:wizard.selected}active{/option:wizard.selected}{option:!wizard.stepLink}navbar-text{/option:!wizard.stepLink}">
          {option:wizard.stepLink}
          <a href="{$var|geturl:'edit'}&amp;id={$mailing.id}&amp;step={$wizard.id}">
          {/option:wizard.stepLink}
            <span>{$wizard.id}. {$wizard.label|ucfirst}</span>
          {option:wizard.stepLink}
          </a>
          {/option:wizard.stepLink}
        </li>
        {/iteration:wizard}
      </ul>
    </nav>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:step1}
    {form:step1}
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
                {$lblToStep|ucfirst}
                &nbsp;<span class="fa fa-chevron-right"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    {/form:step1}
    {/option:step1}
    {option:step2}
    {form:step2}
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                {$lblChooseTemplate|ucfirst}
              </h4>
            </div>
            <div class="panel-body">
              <div class="form-group">
                <p class="help-block">{$msgHelpTemplates}</p>
                {option:rbtTemplatesError}
                  <p class="text-danger">{$rbtTemplatesError}</p>
                {/option:rbtTemplatesError}
                <ul id="templateSelection" class="selectThumbList list-unstyled list-inline">
                  {iteration:templates}
                  <li{option:templates.selected} class="active"{/option:templates.selected}>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <label for="{$templates.id}" class="panel-title">
                          {$templates.rbtTemplates}
                          <span>{$templates.label|ucfirst}</span>
                        </label>
                      </div>
                      <div class="panel-body">
                        <img src="/src/Backend/Modules/Mailmotor/Templates/{$templates.language}/{$templates.value}/images/thumb.jpg" width="172" height="129" class="img-thumbnail" alt="{$templates.label|ucfirst}" />
                      </div>
                    </div>
                  </li>
                  {/iteration:templates}
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
              <button id="toStep3" type="submit" name="to_step_3" class="btn btn-primary">
                {$lblToStep|ucfirst} 3
                &nbsp;<span class="fa fa-chevron-right"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    {/form:step2}
    {/option:step2}
    {option:step3}
    {form:step3}
      <div class="row">
        <div class="col-md-12">
          <div class="form-group{option:txtSubjectError} has-error{/option:txtSubjectError}">
            <label for="subject">
              {$lblSubject|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtSubject} {$txtSubjectError}
          </div>
          <div class="form-group">
            <label for="content">
              {$lblContent|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            <div id="iframeBox">
              <iframe id="contentBox" src="{$var|geturl:'edit_mailing_iframe'}&amp;id={$mailing.id}" height="100%" width="100%"></iframe>
            </div>
          </div>
          {option:txtContentPlain}
          <div class="form-group{option:txtContentPlainError} has-error{/option:txtContentPlainError}">
            <label for="subject">
              {$lblPlainTextVersion|ucfirst}
            </label>
            {$txtContentPlain} {$txtContentPlainError}
          </div>
          {/option:txtContentPlain}
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="btn-toolbar">
            <div class="btn-group pull-right" role="group">
              <button id="sendContent" type="submit" name="to_step_4" class="btn btn-primary">
                {$lblToStep|ucfirst} 4
                &nbsp;<span class="fa fa-chevron-right"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    {/form:step3}
    {/option:step3}
    {option:step4}
    {form:step4}
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                {$lblPreview|ucfirst}
              </h4>
            </div>
            <div class="panel-body">
              {option:previewURL}
              <div id="iframeBox">
                <iframe id="contentBox" src="{$previewURL}" height="100%" width="100%" style="border-right: 1px solid rgb(221, 221, 221); border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color rgb(221, 221, 221) rgb(221, 221, 221); -moz-box-sizing: border-box;"></iframe>
              </div>
              {/option:previewURL}
              {option:!previewURL}
              <div class="options">
                <p><span class="infoMessage">{$errNoModuleLinked}</span></p>
              </div>
              {/option:!previewURL}
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                {$lblSendPreview|ucfirst}
              </h4>
            </div>
            <div class="panel-body">
              <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
                <label for="email">{$lblEmailAddress|ucfirst}</label>
                {$txtEmail} {$txtEmailError}
              </div>
            </div>
            <div class="panel-footer">
              <div class="btn-toolbar">
                <div class="btn-group pull-right">
                  <button id="sendPreview" type="submit" class="btn btn-default" name="send_preview">
                    <span class="fa fa-chevron-right"></span>&nbsp;
                    {$lblSendPreview|ucfirst}
                  </button>
                </div>
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
                {$lblSendOn|ucfirst}
              </h4>
            </div>
            <div class="panel-body">
              <div class="form-group">
                <label for="sendOnDate">{$lblSendDate|ucfirst}</label>
                {$txtSendOnDate}
              </div>
              <div class="form-group">
                <label for="sendOnTime">{$lblAt}</label>
                {$txtSendOnTime}
              </div>
            </div>
            <div class="panel-footer">
              <div class="btn-toolbar">
                <div class="btn-group pull-right">
                  <a id="sendMailing" href="#" class="btn btn-primary" title="{$lblSendMailing|ucfirst}">
                    <span class="fa fa-send-o"></span>&nbsp;
                    {$lblSendMailing|ucfirst}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="sendMailingConfirmationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="{$msgMailingConfirmTitle|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$msgMailingConfirmSend|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$recipientStatistics} <span id="sendOn" style="display: none;">{$msgSendOn}</span></p>
              <p>{$msgPeopleGroups}</p>
              <p>
                {iteration:groups}{$groups.name} {option:groups.comma}, {/option:groups.comma}{/iteration:groups}
              </p>
              <table class="table">
                <tr>
                  <th>{$lblTemplateLanguage|ucfirst}</th>
                  <td>{$templateLanguage}</td>
                </tr>
                <tr>
                  <th>{$lblPrice|ucfirst}</th>
                  <td>&euro; {$price}</td>
                </tr>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a id="sendMailingConfirmationSubmit" href="#" class="btn btn-primary">
                {$lblSendMailing|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
    {/form:step4}
    {/option:step4}
  </div>
</div>
<script type="text/javascript">
  //<![CDATA[
    var variables = [];
    variables = { mailingId: '{$mailing.id}' };
  //]]>
</script>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
