{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditUser|sprintf:{$record.settings.nickname}|ucfirst}</h2>
  </div>
</div>
{form:edit}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <table class="table table-striped">
        <tr>
          <td rowspan="6" class="text-center align-middle">
            {option:record.settings.avatar}
            <img class="img-circle" src="{$FRONTEND_FILES_URL}/backend_users/avatars/source/{$record.settings.avatar}" alt="" />
            {/option:record.settings.avatar}
          </td>
          <th>{$lblName|ucfirst}:</th>
          <td><strong>{$record.settings.name} {$record.settings.surname}</strong></td>
        </tr>
        <tr>
          <th>{$lblNickname|ucfirst}:</th>
          <td>{$record.settings.nickname}</td>
        </tr>
        <tr>
          <th>{$lblEmail|ucfirst}:</th>
          <td>{$record.email}</td>
        </tr>
        <tr>
          <th>{$lblLastLogin|ucfirst}:</th>
          <td>
            {option:record.settings.last_login}{$record.settings.last_login|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}{/option:record.settings.last_login}
            {option:!record.settings.last_login}{$lblNoPreviousLogin}{/option:!record.settings.last_login}
          </td>
        </tr>
        {option:record.settings.last_failed_login_attempt}
        <tr>
          <th>{$lblLastFailedLoginAttempt|ucfirst}:</th>
          <td>{$record.settings.last_failed_login_attempt|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}</td>
        </tr>
        {/option:record.settings.last_failed_login_attempt}
        <tr>
          <th>{$lblLastPasswordChange|ucfirst}:</th>
          <td>
            {option:record.settings.last_password_change}{$record.settings.last_password_change|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}{/option:record.settings.last_password_change}
            {option:!record.settings.last_password_change}{$lblNever}{/option:!record.settings.last_password_change}
          </td>
        </tr>
      </table>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabProfile" aria-controls="profile" role="tab" data-toggle="tab">{$lblProfile|ucfirst}</a>
          </li>
          {option:allowPasswordEdit}
          <li role="presentation">
            <a href="#tabPassword" aria-controls="password" role="tab" data-toggle="tab">{$lblPassword|ucfirst}</a>
          </li>
          {/option:allowPasswordEdit}
          <li role="presentation">
            <a href="#tabSettings" aria-controls="settings" role="tab" data-toggle="tab">{$lblSettings|ucfirst}</a>
          </li>
          {option:allowUserRights}
          <li role="presentation">
            <a href="#tabPermissions" aria-controls="permissions" role="tab" data-toggle="tab">{$lblPermissions|ucfirst}</a>
          </li>
          {/option:allowUserRights}
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabProfile">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="email">
                    {$lblEmail|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtEmail} {$txtEmailError}
                </div>
                <div class="form-group">
                  <label for="name">
                    {$lblName|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtName} {$txtNameError}
                </div>
                <div class="form-group">
                  <label for="surname">
                    {$lblSurname|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtSurname} {$txtSurnameError}
                </div>
                <div class="form-group">
                  <label for="nickname">
                    {$lblNickname|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  <p class="help-block">{$msgHelpNickname}</p>
                  {$txtNickname} {$txtNicknameError}
                </div>
                <div class="form-group">
                  <label for="avatar">{$lblAvatar|ucfirst}</label>
                  <p class="help-block">{$msgHelpAvatar}</p>
                  {$fileAvatar} {$fileAvatarError}
                </div>
              </div>
            </div>
          </div>
          {option:allowPasswordEdit}
          <div role="tabpanel" class="tab-pane" id="tabPassword">
            {option:showPasswordStrength}
            <div class="row">
              <div class="col-md-12">
                <h4>{$lblCurrentPassword|ucfirst}</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>{$lblPasswordStrength|ucfirst}</label>
                  <span class="strength {$record.settings.password_strength}">{$passwordStrengthLabel|ucfirst}</span>
                </div>
              </div>
            </div>
            {/option:showPasswordStrength}
            <div class="row">
              <div class="col-md-12">
                <h4>{$lblChangePassword|ucfirst}</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="newPassword">
                    {$lblPassword|ucfirst}&nbsp;
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  <table id="passwordStrengthMeter" class="passwordStrength" data-id="newPassword">
                    <tr>
                      <td class="strength" id="passwordStrength">
                        <p class="strength none">
                          <span class="label label-default">None</span>
                        </p>
                        <p class="strength weak">
                          <span class="label label-danger">Weak</span>
                        </p>
                        <p class="strength average">
                          <span class="label label-warning">Average</span>
                        </p>
                        <p class="strength strong">
                          <span class="label label-success">Strong</span>
                        </p>
                      </td>
                      <td>
                        <p class="help-block">&nbsp;{$msgHelpStrongPassword}</p>
                      </td>
                    </tr>
                  </table>
                  <div class="form-group form-inline">
                    <div class="form-group">
                      {$txtNewPassword} {$txtNewPasswordError}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="confirmPassword">
                    {$lblConfirmPassword|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  <div class="form-group form-inline">
                    <div class="form-group">
                      {$txtConfirmPassword} {$txtConfirmPasswordError}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/option:allowPasswordEdit}
          <div role="tabpanel" class="tab-pane" id="tabSettings">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="interfaceLanguage">{$lblLanguage|ucfirst}</label>
                  {$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}
                </div>
                <div class="form-group">
                  <label for="dateFormat">{$lblDateFormat|ucfirst}</label>
                  {$ddmDateFormat} {$ddmDateFormatError}
                </div>
                <div class="form-group">
                  <label for="timeFormat">{$lblTimeFormat|ucfirst}</label>
                  {$ddmTimeFormat} {$ddmTimeFormatError}
                </div>
                <div class="form-group">
                  <label for="numberFormat">{$lblNumberFormat|ucfirst}</label>
                  {$ddmNumberFormat} {$ddmNumberFormatError}
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblCSV|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="csvSplitCharacter">{$lblSplitCharacter|ucfirst}</label>
                  {$ddmCsvSplitCharacter} {$ddmCsvSplitCharacterError}
                </div>
                <div class="form-group">
                  <label for="csvLineEnding">{$lblLineEnding|ucfirst}</label>
                  {$ddmCsvLineEnding} {$ddmCsvLineEndingError}
                </div>
              </div>
            </div>
          </div>
          {option:allowUserRights}
          <div role="tabpanel" class="tab-pane" id="tabPermissions">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <ul class="list-unstyled">
                    <li class="checkbox">
                      <label for="active">{$chkActive} {$msgHelpActive}</label> {$chkActiveError}
                    </li>
                    <li class="checkbox">
                      <label for="apiAccess">{$chkApiAccess} {$msgHelpAPIAccess}</label> {$chkApiAccessError}
                    </li>
                  </ul>
                </div>
                <div class="form-group">
                  <p>{$lblGroups|ucfirst}</p>
                  <ul id="groupList" class="list-unstyled">
                    {iteration:groups}
                    <li class="checkbox">
                      <label for="{$groups.id}">{$groups.chkGroups} {$groups.label}</label>
                    </li>
                    {/iteration:groups}
                  </ul>
                  {$chkGroupsError}
                </div>
              </div>
            </div>
          </div>
          {/option:allowUserRights}
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showUsersDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showUsersDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
          </button>
        </div>
      </div>
      {option:showUsersDelete}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmDelete|sprintf:{$record.settings.nickname}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete'}&amp;id={$record.id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showUsersDelete}
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
