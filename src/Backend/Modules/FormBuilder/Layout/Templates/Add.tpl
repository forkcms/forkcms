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
            <a href="#tabGeneral" aria-controls="general" role="tab" data-toggle="tab">{$lblGeneral|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabExtra" aria-controls="extra" role="tab" data-toggle="tab">{$lblExtra|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabConfirmationMail" aria-controls="confirmationMail" role="tab" data-toggle="tab">{$lblConfirmationMail|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabGeneral">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblGeneral|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="name">
                    {$lblName|ucfirst}
                    <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                  </label>
                  {$txtName} {$txtNameError}
                </div>
                <div class="form-group">
                  <label for="method">
                    {$lblMethod|ucfirst}
                    <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                  </label>
                  {$ddmMethod} {$ddmMethodError}
                </div>
                <div class="form-group">
                  <label for="email">
                    {$lblRecipient|ucfirst}
                    <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                  </label>
                  {$txtEmail} {$txtEmailError}
                </div>
                <div class="form-group">
                  <label for="successMessage">
                    {$lblSuccessMessage|ucfirst}
                    <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                  </label>
                  {$txtSuccessMessage} {$txtSuccessMessageError}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabExtra">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblExtra|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <label for="identifier">
                  {$lblIdentifier|ucfirst}
                  <abbr class="glyphicon glyphicon-info-sign" title="{$msgHelpIdentifier}"></abbr>
                </label>
                {$txtIdentifier} {$txtIdentifierError}
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabConfirmationMail" >
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblConfirmationMail|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <p>{$msgConfirmationMail}</p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {$chkSendConfirmationMail} <label for="sendConfirmationMail">{$lblSendConfirmationMail|ucfirst}</label>
                </div>
                <div class="form-group jsConfirmationEmailContainer">
                  <label for="confirmationMailSubject">
                    {$lblConfirmationMailSubject|ucfirst}
                  </label>
                  {$txtConfirmationMailSubject} {$txtConfirmationMailSubjectError}
                </div>
                <div class="form-group jsConfirmationEmailContainer">
                  <label for="confirmationMailContent">
                    {$lblConfirmationMailContent|ucfirst}
                  </label>
                  {$txtConfirmationMailContent} {$txtConfirmationMailContentError}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="glyphicon glyphicon-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
