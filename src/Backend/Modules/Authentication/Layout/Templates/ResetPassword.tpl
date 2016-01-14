{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
<body id="login">
  {include:{$BACKEND_MODULES_PATH}/{$MODULE}/Layout/Templates/Ie6.tpl}
  <div class="page-header text-center">
    <h1>{$SITE_TITLE}</h1>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        {option:debug}
        <div class="alert alert-warning">
          <p>{$msgWarningDebugMode}</p>
        </div>
        {/option:debug}
        {form:authenticationResetPassword}
          <div class="panel panel-default">
            <div class="panel-body">
              <p class=help-block>{$msgHelpResetPassword}</p>
              <div class="form-group{option:txtBackendNewPasswordError} has-error{/option:txtBackendNewPasswordError}">
                <label for="backendNewPassword" class="control-label">{$lblNewPassword|ucfirst}</label>
                {$txtBackendNewPassword} {$txtBackendNewPasswordError}
              </div>
              <div class="form-group{option:txtBackendNewPasswordRepeatedError} has-error{/option:txtBackendNewPasswordRepeatedError}">
                <label for="backendNewPasswordRepeated" class="control-label">{$lblRepeatPassword|ucfirst}</label>
                {$txtBackendNewPasswordRepeated} {$txtBackendNewPasswordRepeatedError}
              </div>
              <div class="form-group">
                <div class="btn-toolbar pull-right">
                  <div class="btn-group">
                    <button id="resetPassword" name="reset" type="submit" class="btn btn-primary">
                      {$lblResetAndSignIn|ucfirst}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        {/form:authenticationResetPassword}
      </div>
    </div>
  </div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
