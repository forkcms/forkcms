{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabProfile" aria-controls="profile" role="tab" data-toggle="tab">{$lblProfile|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabPassword" aria-controls="password" role="tab" data-toggle="tab">{$lblPassword|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabSettings" aria-controls="settings" role="tab" data-toggle="tab">{$lblSettings|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabPermissions" aria-controls="permission" role="tab" data-toggle="tab">{$lblPermissions|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabProfile">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
                      <label for="email" class="control-label">
                        {$lblEmail|ucfirst}&nbsp;
                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                      </label>
                      {$txtEmail} {$txtEmailError}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblPersonalInformation|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
                      <label for="name" class="control-label">
                        {$lblName|ucfirst}
                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                      </label>
                      {$txtName} {$txtNameError}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group{option:txtSurnameError} has-error{/option:txtSurnameError}">
                      <label for="surname" class="control-label">
                        {$lblSurname|ucfirst}
                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                      </label>
                      {$txtSurname} {$txtSurnameError}
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group{option:txtNicknameError} has-error{/option:txtNicknameError}">
                      <label for="nickname" class="control-label">
                        {$lblNickname|ucfirst}
                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                      </label>
                      <p class="help-block">{$msgHelpNickname}</p>
                      {$txtNickname} {$txtNicknameError}
                    </div>
                  </div>
                </div>
                <div class="form-group{option:fileAvatarError} has-error{/option:fileAvatarError}">
                  <label for="avatar" class="control-label">{$lblAvatar|ucfirst}</label>
                  <p class="help-block">{$msgHelpAvatar}</p>
                  {$fileAvatar} {$fileAvatarError}
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="tabPassword" role="tabpanel">
            <div class="form-group">
              <label for="password" class="control-label">
                {$lblPassword|ucfirst}&nbsp;
                <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
              </label>
              <table id="passwordStrengthMeter" class="passwordStrength" data-id="password">
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
              <div class="form-group form-inline{option:txtPasswordError} has-error{/option:txtPasswordError}">
                <div class="form-group">
                  {$txtPassword} {$txtPasswordError}
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="confirmPassword" class="control-label">
                {$lblConfirmPassword|ucfirst}
                <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
              </label>
              <div class="form-group form-inline{option:txtConfirmPasswordError} has-error{/option:txtConfirmPasswordError}">
                <div class="form-group">
                  {$txtConfirmPassword} {$txtConfirmPasswordError}
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="tabSettings" role="tabpanel">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group{option:ddmInterfaceLanguageError} has-error{/option:ddmInterfaceLanguageError}">
                      <label for="interfaceLanguage" class="control-label">{$lblLanguage|ucfirst}</label>
                      {$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group{option:ddmDateFormatError} has-error{/option:ddmDateFormatError}">
                      <label for="dateFormat" class="control-label">{$lblDateFormat|ucfirst}</label>
                      {$ddmDateFormat} {$ddmDateFormatError}
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group{option:ddmTimeFormatError} has-error{/option:ddmTimeFormatError}">
                      <label for="timeFormat" class="control-label">{$lblTimeFormat|ucfirst}</label>
                      {$ddmTimeFormat} {$ddmTimeFormatError}
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group{option:ddmNumberFormatError} has-error{/option:ddmNumberFormatError}">
                      <label for="numberFormat" class="control-label">{$lblNumberFormat|ucfirst}</label>
                      {$ddmNumberFormat} {$ddmNumberFormatError}
                    </div>
                  </div>
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
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group{option:ddmCsvSplitCharacterError} has-error{/option:ddmCsvSplitCharacterError}">
                      <label for="csvSplitCharacter" class="control-label">{$lblSplitCharacter|ucfirst}</label>
                      {$ddmCsvSplitCharacter} {$ddmCsvSplitCharacterError}
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group{option:ddmCsvLineEndingError} has-error{/option:ddmCsvLineEndingError}">
                      <label for="csvLineEnding" class="control-label">{$lblLineEnding|ucfirst}</label>
                      {$ddmCsvLineEnding} {$ddmCsvLineEndingError}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabPermissions">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group{option:chkActiveError} has-error{/option:chkActiveError}{option:chkApiAccessError} has-error{/option:chkApiAccessError}">
                  <ul class="list-unstyled">
                    <li class="checkbox">
                      <label for="active">{$chkActive} {$msgHelpActive}</label> {$chkActiveError}
                    </li>
                    <li class="checkbox">
                      <label for="apiAccess">{$chkApiAccess} {$msgHelpAPIAccess}</label> {$chkApiAccessError}
                    </li>
                  </ul>
                </div>
                <div class="form-group{option:chkGroupsError} has-error{/option:chkGroupsError}">
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
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-success">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
