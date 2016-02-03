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
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabGeneral">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
                  <label for="name" class="control-label">
                    {$lblName|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtName} {$txtNameError}
                </div>
                <div class="form-group{option:ddmMethodError} has-error{/option:ddmMethodError}">
                  <label for="method" class="control-label">
                    {$lblMethod|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$ddmMethod} {$ddmMethodError}
                </div>
                <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
                  <label for="email" class="control-label">
                    {$lblRecipient|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtEmail} {$txtEmailError}
                </div>
                <div class="form-group{option:txtSuccessMessageError} has-error{/option:txtSuccessMessageError}">
                  <label for="successMessage" class="control-label">
                    {$lblSuccessMessage|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtSuccessMessage} {$txtSuccessMessageError}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabExtra">
            <div class="row">
              <div class="col-md-12">
                <label for="identifier" class="control-label">
                  {$lblIdentifier|ucfirst}
                  <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpIdentifier}"></abbr>
                </label>
                {$txtIdentifier} {$txtIdentifierError}
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
