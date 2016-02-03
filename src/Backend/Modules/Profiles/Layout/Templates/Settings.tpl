{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblModuleSettings|ucfirst}: {$lblProfiles}</h2>
  </div>
</div>

{form:settings}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblNotifications|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtProfileNotificationEmailError} has-error{/option:txtProfileNotificationEmailError}">
            <ul class="list-unstyled">
              <li class="checkbox"><label for="sendNewProfileAdminMail">{$chkSendNewProfileAdminMail} {$lblSendNewProfileAdminMail|ucfirst}</label></li>
              <li class="checkbox" id="overwriteProfileNotificationEmailBox"><label for="overwriteProfileNotificationEmail">{$chkOverwriteProfileNotificationEmail} {$lblOverwriteProfileNotificationEmail|ucfirst}</label><br/>
                  <span id="profileNotificationEmailBox">
                      {$txtProfileNotificationEmail}<abbr title="{$lblRequiredField|ucfirst}">*</abbr> {$txtProfileNotificationEmailError}
                  </span>
              </li>
            </ul>
          </div>
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox"><label for="sendNewProfileMail">{$chkSendNewProfileMail} {$lblSendNewProfileMail|ucfirst}</label></li>
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
