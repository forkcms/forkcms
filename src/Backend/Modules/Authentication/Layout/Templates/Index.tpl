{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
<body id="login">
  {include:{$BACKEND_MODULES_PATH}/{$MODULE}/Layout/Templates/Ie6.tpl}
  <div class="page-header text-center">
    <h1>{$SITE_TITLE}</h1>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        {option:hasError}
        <div class="alert alert-danger">
          <p>{$errInvalidEmailPasswordCombination}</p>
        </div>
        {/option:hasError}
        {option:hasTooManyAttemps}
        <div class="alert alert-danger">
          <p>{$errTooManyLoginAttempts}</p>
        </div>
        {/option:hasTooManyAttemps}
        {option:txtBackendEmailForgotError}
        <div class="alert alert-danger">
          <p>{$txtBackendEmailForgotError}</p>
        </div>
        {/option:txtBackendEmailForgotError}
        {option:isForgotPasswordSuccess}
        <div class="alert alert-success">
          <p>{$msgLoginFormForgotPasswordSuccess}</p>
        </div>
        {/option:isForgotPasswordSuccess}
        {form:authenticationIndex}
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="form-group{option:txtBackendEmailError} has-error{/option:txtBackendEmailError}">
                <label for="backendEmail" class="control-label">{$lblEmail|ucfirst}</label>
                {$txtBackendEmail} {$txtBackendEmailError}
              </div>
              <div class="form-group{option:txtBackendPasswordError} has-error{/option:txtBackendPasswordError}">
                <label for="backendPassword" class="control-label">{$lblPassword|ucfirst}</label>
                {$txtBackendPassword} {$txtBackendPasswordError}
              </div>
              <div class="form-group">
                <div class="btn-toolbar pull-right">
                  <div class="btn-group">
                    <a href="#" id="forgotPasswordLink" class="btn" data-toggle="modal" data-target="#forgotPasswordHolder">{$msgForgotPassword}</a>
                  </div>
                  <div class="btn-group">
                    <button name="login" type="submit" class="btn btn-primary">
                      {$lblSignIn|ucfirst}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        {/form:authenticationIndex}
        <div class="modal fade" id="forgotPasswordHolder" tabindex="-1" role="dialog" aria-labelledby="{$msgHelpForgotPassword}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              {form:forgotPassword}
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">{$msgForgotPassword}</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="backendEmailForgot" class="control-label">{$lblEmail|ucfirst}</label>
                    {$txtBackendEmailForgot}
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">
                    {$lblCancel|ucfirst}
                  </button>
                  <button id="send" name="send" type="submit" class="btn btn-success">
                    {$lblSend|ucfirst}
                  </button>
                </div>
              {/form:forgotPassword}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
